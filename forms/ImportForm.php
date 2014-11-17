<?php
/**
 * NuxeoLink import form 
 *
 * @package  NuxeoLink
 * @copyright   2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * NuxeoLink form class
 */
class Nuxeo_Form_Import extends Omeka_Form
{
    /**
     * Construct the import form.
     *
     *@return void
     */
    public function init()
    {
        parent::init();
        $this->_registerElements();
    }

    /**
     * Define the form elements.
     *
     *@return void
     */
    private function _registerElements()
    {
              
	// Collection:
        $this->addElement('select', 'nuxeocollection', array(
							'label'         => __('Collection'),
							'description'   => __('To which collection would you like to add the Nuxeo document(s)?'),
							'value'         => '0',
							'order'         => 4,
							'multiOptions'       => $this->_getCollectionOptions()
							)
			  );

        // Visibility (public vs private):
        $this->addElement('checkbox', 'nuxeopublic', array(
            'label'         => __('Public Visibility'),
            'description'   => __('Would you like to make the imported photo(s) public on your Omeka site?'),
            'checked'         => 'checked',
	    'order'         => 6
        )
        );

        // Submit:
        $this->addElement('submit', 'nuxeoimportsubmit', array(
            'label' => __('Import Item(s)')
        ));

	//Display Groups:
        $this->addDisplayGroup(
			       array(
				     'nuxeocollection',
				     'nuxeopublic'
				     ),
			       'fields'
			       );
	
        $this->addDisplayGroup(
			       array(
				     'nuxeoimportsubmit'
				     ), 
			       'submit_buttons'
			       );
	

    }

    /**
     *Process the form data and import the photos as necessary
     *
     *@return bool $success true if successful 
     */
    public static function ProcessPost()
    {
        try {

            if(self::_import())
                return('Your Nuxeo documents are now being imported. This process may take a few minutes. You may continue to work while the photos are imported in the background. You may notice some strange behavior while the photos are uploading, but it will all be over soon.');

        } catch(Exception $e) 
                {
                    throw new Exception('Error initializing nuxeo import: '.$e->getMessage());
                }

        return(true);

    }

    private static function _import()
    {
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . 'import.php');

        //process optional values
        if(isset($_REQUEST['nuxeocollection']))
            $collection = $_REQUEST['nuxeocollection'];
        else
            $collection = 0;

        if(isset($_REQUEST['nuxeopublic']))
            $public = $_REQUEST['nuxeopublic'];
        else 
            $public = false;

        if(isset($_REQUEST['toImport']))
            $docPaths = $_REQUEST['toImport'];
        else 
            $docPaths = array();

        //set up options to pass to background job
        $options = array(
            'collection'=>$collection,
            'public'=>$public,
            'paths'=>serialize($docPaths)
        );

        //print_r($options);
        //die();

        //attempt to start the job
        try{
            $dispacher = Zend_Registry::get('job_dispatcher');
            $dispacher->sendLongRunning('NuxeoLink_ImportJob',$options);
        } catch (Exception $e) {
            throw($e);
        }

        return(true);
 
    }

    /**
     * Get an array to be used in formSelect() containing all collections.
     * 
     * @return array $options An associative array mapping collection IDs
     *to their titles for display in a dropdown menu
     */
    private function _getCollectionOptions()
    {
        $collectionTable = get_db()->getTable('Collection');
        //$options = array('-1'=>'Create New Collection','0'=>'Assign No Collection'); TODO set up autocreate collection
        $options = array('0'=>'Assign No Collection');
        $pairs = $collectionTable->findPairsForSelectForm();
        foreach($pairs as $key=>$value)
            $options[$key]=$value;
        return $options;
    }


}