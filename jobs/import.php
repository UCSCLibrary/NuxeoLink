<?php
/**
 * NuxeoLink import job
 *
 * @package NuxeoLink
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The NuxeoLink import job class.
 *
 * @package NuxeoLink
 */
class NuxeoLink_ImportJob extends Omeka_Job_AbstractJob
{
    private $_docPaths;

    private $_collection;

    private $_public;
    
    private $_email;

    public function setCollection($collection) {
        $this->_collection = $collection;
    }

    public function setPublic($public) {
        $this->_public = $public;
    }

    public function setPaths($paths) {
        $this->_docPaths = $paths;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function perform()
    {
        Zend_Registry::get('bootstrap')->bootstrap('Acl');
      
        //require the helpers
        require_once(dirname(dirname(__FILE__)).'/helpers/APIfunctions.php');
      
        //set up a session
        //$client = new NuxeoPhpAutomationClient($url);
        $client = new NuxeoOmekaImportClient(get_option('nuxeoUrl'));
        $session = $client->getSession(get_option('nuxeoUser'),get_option('nuxeoPass'));

        foreach(unserialize($this->_docPaths) as $path) {
            $session->addDoc($path,$this->_collection,$this->_public);            
        }

        //email user to let them know it is finished
        $subject = "Nuxeo -> Omeka: Import Completed";
        $message = "Your import from Nuxeo into Omeka has completed successfully. Your new items are ready to view in your Omeka dashboard. Have a nice day!";
        mail($this->_email,$subject,$message);
    
    }

}