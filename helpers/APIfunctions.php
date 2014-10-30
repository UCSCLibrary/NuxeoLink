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

    private $_propertyMaps = array(
        'dc'=>array(
            'contributors'=>'Contributor',
            'created'=>'Date Created',
            'modified'=>'Date Modified'
        ),
        'ucldc_schema'=>array(
            'rightsstatus' =>'Rights Status',
            'campusunit' => 'Campus Unit',
            'localidentifier' => 'Local Identifier',
            'rightsnotice' => 'Rights Notice',
            'language' => 'Language',
            'relatedresource' => 'Related Resource',
            'source' => 'Source',
            'alternativetitle' => 'Alternative Title',
            'collection' => 'Collection',
            'formgenre' => 'Form Genre',
            'identifier' => 'Identifier',
            'rightsstartdate' => 'Rights Start Date',
            'rightsstatement' => 'Rights Statement',
            'type' => 'Type',
            'physlocation' => 'Physical Location',
            'temporalcoverage' => 'Temporal Coverage',
            'rightsdeterminationdate' => 'Rights Determination Date',
            'date' => 'Date',
            'subjecttopic' => 'Subject Topic',
            'rightsholder' => 'Rights Holder',
            'creator' => 'Creator',
            'rightsjurisdiction' => 'Rights Jurisdiction',
            'rightscontact' => 'Rights Contact',
            'accessrestrict' => 'AccessRestrict',
            'publisher' => 'Publisher',
            'contributor' => 'Contributor',
            'subjectname' => 'Subject Name',
            'rightsnote' => 'Rights Note',
            'provenance' => 'Provenance',
            'rightsenddate' => 'Rights End Date',
            'description' => 'Description',
            'place' => 'Place',
            'physdesc'=>'Physical Description'
        )
    );

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
        //TODO set this option on startup to include Dublin Core and UCLDC_Schema
        $knownSchema = unserialize(get_option('nuxeoKnownSchema'));
        $elementTable = get_db()->getTable('Element');   
        $properties = array();
        $props = $doc->getProperties();
        
        foreach($props as $propkey=>$propval) {

            $propkeys = explode(':',$propkey);
            $schema=$propkeys[0];
            if(!isset($knownSchema[$schema]))
                continue;

            $property=$propkeys[1];
                
            $property = $this->_filterProperty($schema,$property);

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

    private function _filterProperty($schema,$property){
        $maps = $this->_propertyMaps;
        if(!isset($maps[$schema]))
            return $property;
        if(array_key_exists($property,$maps[$schema])) 
            return $maps[$schema][$property];
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