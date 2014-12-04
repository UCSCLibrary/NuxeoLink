<?php
/**
 * NuxeoLink import helpers
 *
 * @package NuxeoLink
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

//require the api
require_once(dirname(dirname(__FILE__)).'/libraries/NuxeoAutomationAPI.php');

/**
 * The NuxeoLink import client class, extending Nuxeo's php automation class.
 *
 * @package NuxeoLink
 */
class NuxeoOmekaImportClient extends NuxeoPhpAutomationClient {

    private $url;
    private $session;

    public function NuxeoOmekaImportClient($url = 'http://localhost:8080/nuxeo/site/automation') {
        $this->url = $url;
    }

    public function getSession($username = 'Administrator', $password = 'Administrator') {
        $this->session = $username . ":" . $password;
$session = new NuxeoOmekaSession($this->url, $this->session);
        return $session;
    }
}

/**
 * The NuxeoLink import client class, extending Nuxeo's php automation class.
 *
 * @package NuxeoLink
 */
class NuxeoOmekaSession extends NuxeoSession {

    public function __construct($url, $session, $headers = "Content-Type: application/json+nxrequest") {
        parent::__construct($url, $session, $headers = "Content-Type: application/json+nxrequest");
        $this->_setPropertyMaps( get_option('nuxeoUcldcSchema') == 'installed');
    }

    private $_propertyMaps = array();

    private function _setPropertyMaps($use_UCLDC){
        $this->_propertyMaps['dc'] = array(
//            'contributors'=>'Contributor',
            'contributors'=>'ignored',
            'created'=>'Date Created',
            'modified'=>'Date Modified',
            'creator'=>'ignored'
        );
        $this->_propertyMaps['ucldc_schema'] = array(
            'rightsstatus' => $use_UCLDC ? 'Copyright Status' : 'dc:Rights',
            'campusunit' => $use_UCLDC ? 'Campus' : 'dc:Publisher',
            'localidentifier' => $use_UCLDC ? 'Local Identifier' : 'dc:Identifier',
            'rightsnotice' => $use_UCLDC ? 'Copyright Notice' : 'dc:Rights',
            'language' => $use_UCLDC ? 'Language' : 'dc:Language',
            'relatedresource' => $use_UCLDC ? 'Related Resource' : 'dc:Relation',
            'source' => $use_UCLDC ? 'Source' : 'dc:Source',
            'alternativetitle' => $use_UCLDC ? 'Alternative Title' : 'dc:Title',
            'collection' => $use_UCLDC ? 'Collection' : 'dc:Relation',
            'formgenre' => $use_UCLDC ? 'Form / Genre' : 'dc:Type',
            'identifier' => $use_UCLDC ? 'Identifier' : 'dc:Identifier',
            'rightsstatement' => $use_UCLDC ? 'Copyright Statement' : 'dc:Rights',
            'type' => $use_UCLDC ? 'Type' : 'dc:Type',
            'physlocation' => $use_UCLDC ? 'Physical Location' : 'dc:Coverage',
            'temporalcoverage' => $use_UCLDC ? 'Temporal Coverage' : 'dc:Coverage',
            'date' => $use_UCLDC ? 'Date' : 'dc:Date',
            'subjecttopic' => $use_UCLDC ? 'Subject (Topic)' : 'dc:Subject',
            'rightsholder' => $use_UCLDC ? 'Copyright Holder' : 'dc:Rights',
            'creator' => $use_UCLDC ? 'Creator' : 'dc:Creator',
            'rightsjurisdiction' => $use_UCLDC ? 'Copyright Jurisdiction' : 'dc:Rights',
            'rightscontact' => $use_UCLDC ? 'Copyright Contact' : 'dc:Rights',
            'publisher' => $use_UCLDC ? 'Publisher' : 'dc:Publisher',
            'contributor' => $use_UCLDC ? 'Contributor' : 'dc:Contributor',
            'subjectname' => $use_UCLDC ? 'Subject (Name)' : 'dc:Subject',
            'rightsnote' => $use_UCLDC ? 'Copyright Note' : 'dc:Rights',
            'provenance' => $use_UCLDC ? 'Provenance' : 'dc:Source',
            'description' => $use_UCLDC ? 'Description' : 'dc:Description',
            'place' => $use_UCLDC ? 'Place' : 'dc:Coverage',
            'physdesc'=> $use_UCLDC ? 'Physical Description' : 'dc:format',
            //'rightsdeterminationdate' => $use_UCLDC ? 'Rights Determination Date' : '', //***
            //'rightsenddate' => $use_UCLDC ? 'Rights End Date' : '',//**,
            //'rightsstartdate' => $use_UCLDC ? 'Rights Start Date' : '', //***
//            'accessrestrict' => $use_UCLDC ? 'AccessRestrict' : '',
        );
    }

    function fullTextSearch($parentUid,$searchTerm) {
        $url = $this->getUrlLoggedIn();
        if(strpos($url,"/automation"))
            $url = str_replace("/automation","",$url);
        $searchUrl = $url."/id/".$parentUid."/@search?fullText=".urlencode($searchTerm)."&orderBy=dc:title";

        //$data = json_decode($this->curl_download($searchUrl));
        $data = json_decode($this->stream_download($searchUrl));
        if(empty($data))
            return false;
        $data->thumbBase = $this->getUrlLoggedIn()."/files/";
        return $data;
        
    }

    function stream_download($Url) {
        $context_options = array(
            'http' => array(
                'method'=>'GET',
                'header'=>'Accept-language: en\r\n'
            )
        );
        $context = stream_context_create($context_options);
        $contents = file_get_contents($Url,NULL,$context);
        return $contents;
    }

    function curl_download($Url){
        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }



    function getChildFolders($uid) {
        $query="SELECT * FROM Folder WHERE ecm:parentId = '".$uid."' AND dc:title like '%'";
        return $this->_getDocs($query,$uid);
    }

    function getChildDocuments($uid) {
        $query="SELECT * FROM Document WHERE ecm:parentId = '".$uid."' AND ecm:mixinType = 'Asset' ";
        return $this->_getDocs($query,$uid);
    }

    public function getFullDoc($docPath) {

        $answer = $this->newRequest("Document.Fetch")->set('params', 'value', $docPath )->setSchema()->sendRequest();

        return($answer);

    }

    public function getPicDoc($docPath) {

        $answer = $this->newRequest("Document.Fetch")->set('params', 'value', $docPath )->setSchema('picture')->sendRequest();

        return($answer);

    }
    
    public function addDoc($docPath,$collection,$public) {

        $answer = $this->newRequest("Document.Fetch")->set('params', 'value', $docPath )->setSchema()->sendRequest();

        foreach($answer->getDocumentList() as $doc) {
            $postElements = $this->_getPostElements($doc);
            $post = array(
			 'Elements'=>$postElements,
			 'item_type_id'=>'6',      //a still image TODO
			 'tags-to-add'=>'',
			 'tags-to-delete'=>''
            );

            if($collection>0)
                $post['collection_id']=$collection;

            if($public)
                $post['public']="1";
            //todo make new item and add the post
            $item = new Item();
            
            $item->setPostData($post);
            $item->save();
            //This returns the primary file associated with the nuxeo item, which is bad because those are sometimes sensitive. 
            //$fileInfo = $doc->getProperty('file:content');
            $fileInfo = $doc->getImageInfo();
            $filePath = $fileInfo['data'];
            $fileName = $fileInfo['name'];

            $this->addFile($filePath,$fileName,$item);
        }
    }

    public function addFile($filePath,$filename,$item) {
        $urlblob = $this->getUrlLoggedIn().'/'.$filePath ;

        $remoteFile = fopen($urlblob, 'r');
        $tmpPath = sys_get_temp_dir().'/'.$filename;
        $localFile = fopen($tmpPath,'w');
        
        //While we still have content to read, append it
        //1k at a time
        while ($line = fread($remoteFile, 8184)) {
            //fwrite($localFile,$line);
            echo(fwrite($localFile,$line)." bytes written<br>");
        }

        $metaDatas = stream_get_meta_data($localFile);
        $tmpFilename = $metaDatas['uri'];
        insert_files_for_item($item,'Filesystem',$tmpPath);
        fclose($localFile);
        unlink($tmpPath);
    }

    private function _getPostElements($doc) {
        $knownSchema = unserialize(get_option('nuxeoKnownSchema'));
        $elementTable = get_db()->getTable('Element');   
        $properties = array();
        $props = $doc->getProperties();

        foreach($props as $propkey=>$propval) {
            
            $propkeys = explode(':',$propkey);
            $schema=$propkeys[0];
            $property=$propkeys[1];
            
            if($property == 'type' && $propval != 'image')
                continue;
          
            if($property == 'accessrestrict' && $propval!='public')
                continue;
            
            $beforeSchema = $schema;
            //echo '   --   beforeschema: '.$schema;
            $property = $this->_filterProperty($schema,$property);
            
            if($schema !== $beforeSchema)
                echo '  afterschema: '.$schema;
            
            if(!isset($knownSchema[$schema]))
                continue;

            $element = $elementTable->findByElementSetNameAndElementName($knownSchema[$schema],ucfirst($property));

            if(is_array($propval)) {
                
                $propval = array_filter($propval);
                if(empty($propval))
                    continue;
            }elseif(!is_null($propval)) {
                $propval = array($propval);
            }else{ //null
                continue;
            }

            foreach($propval as $val) {
                if(is_array($val)) {
                    if(array_key_exists('date',$val))
                        $val=$val['date'];
                    
                    else if(array_key_exists('name',$val))
                        $val=$val['name'];
                    
                    else if(array_key_exists('heading',$val))
                        $val=$val['heading'];
                    
                    else if(array_key_exists('item',$val))
                        $val=$val['item'];
                }

                $html = false;
                if($val != strip_tags($val)) 
                    $html = true;

                $postArray = array('text'=>$val,'html'=>$html);
                if(!is_null($element->id)) {
                    if(!in_array($postArray,$properties[$element->id]))
                        $properties[$element->id][]=$postArray;
                }

                if($dcElement = $elementTable->findByElementSetNameAndElementName($knownSchema[$schema],ucfirst($property))) {
                    if(!in_array($postArray,$properties[$dcElement->id]))
                        $properties[$dcElement->id][]=$postArray;
                }

            }
        }
        return($properties);
    }

    private function _filterProperty(&$schema,$property){
        $maps = $this->_propertyMaps;
        
        if(!isset($maps[$schema]))
            return $property;
        if(array_key_exists($property,$maps[$schema])) {
            
            if(!strpos($prop = $maps[$schema][$property],':')) {
                return $prop;
            } else {
                echo ' !- schema:'.$schema.' Property: '.$property.' -!';
                $ps = explode(':',$prop);
                $schema = $ps[0];
                return $ps[1];
            }
        }
        return $property;

    }

    private function _getDocs($query,$parent='#') {

        $answer = $this->newRequest("Document.Query")->set('params', 'query', $query )->setSchema('picture')->sendRequest();
        //$answer = $this->newRequest("Document.Query")->set('params', 'query', $query )->setSchema('ucldc_schema')->sendRequest();
        $docs = array();
        $list = $answer->getDocumentList();

        if(count($list)==0)
            die();
        foreach($list as $doc) {
            $newDoc = array(
                'text'=> $doc->getTitle(),
                'id'=> $doc->getUid(),
                'children'=>true,
                'path'=> $doc->getPath(),
                'type'=>$doc->getType()
            );
            if(in_array('Thumbnail',$doc->getFacets())) {
                $thumbpath =  $doc->getThumbPath();
                if(!is_array($thumbpath))
                    $newDoc['thumb'] = htmlspecialchars($this->getUrlLoggedIn().'/'.$thumbpath);
            }
            $docs[] = $newDoc;
        }
        //return 'properties do not exist anywhere';
        return $docs;
    }
}

?>