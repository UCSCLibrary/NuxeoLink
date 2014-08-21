<?php
/**
 * NuxeoLink plugin
 *
 * @package     NuxeoLink
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * NuxeoLink plugin class
 * 
 * @package
 */
class NuxeoLinkPlugin extends Omeka_plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install','uninstall','initialize','config','config_form','admin_head','define_acl');

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main');

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'nuxeoKnownSchema' => '',
        'nuxeoUser' => '',
        'nuxeoPass' => '',
        'nuxeoUrl' => '',
        'nuxeoUcldcSchema' => 'false',
        'nuxeoAutocreateSchema' => 'false'
    );

    /**
     * Install the plugin's options
     *
     * @return void
     */
    public function hookInstall() {
        $this->_installOptions();
        set_option('nuxeoKnownSchema',serialize(array('dc'=>'Dublin Core')));
    }

    /**
     * Uninstall the options
     *
     * @return void
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }

    /**
     * Require the job and helper files
     *
     * @return void
     */
    public function hookInitialize()
    {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . 'import.php';
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'APIfunctions.php';
    }

    /**
     * Display the plugin config form.
     */
    public function hookConfig() { 

        if(!empty($_REQUEST['nuxeoUrl']))
            set_option('nuxeoUrl',$_REQUEST['nuxeoUrl']);
        if(!empty($_REQUEST['nuxeoUser']))
            set_option('nuxeoUser',$_REQUEST['nuxeoUser']);
        if(!empty($_REQUEST['nuxeoPass']))
            set_option('nuxeoPass',$_REQUEST['nuxeoPass']);

        $ucldcElementNames = array( 
            'Rights Status',
            'Campus Unit',
            'Local Identifier',
            'Rights Notice',
            'Language',
            'Related Resource',
            'Source',
            'Alternative Title',
            'Collection',
            'Form Genre',
            'Identifier',
            'Rights Start Date',
            'Rights Statement',
            'Type',
            'Physical Location',
            'Temporal Coverage',
            'Rights Determination Date',
            'Date',
            'Subject Topic',
            'Rights Holder',
            'Creator',
            'Rights Jurisdiction',
            'Rights Contact',
            'AccessRestrict',
            'Publisher',
            'Contributor',
            'Subject Name',
            'Rights Note',
            'Provenance',
            'Rights End Date',
            'Description',
            'Place',
            'Physical Description'      
        );
        
        if($_REQUEST['nuxeoUcldcSchema']=='installed' && get_option('nuxeoUcldcSchema')!=='installed') {
            //install schema
            $ucldcElementSet = new ElementSet();
            $ucldcElementSet->name="UCLDC Schema";
            $ucldcElementSet->save();
            
            $ucldcElements = array();
            foreach($ucldcElementNames as $elementName) {
                $element = new Element();
                $element->element_set_id = $ucldcElementSet->id;
                $element->name = $elementName;
                $element->save();
                $ucldcElements[] = $element;
            }
            $ucldcElementSet->addElements($ucldcElements);
            $ucldcElementSet->save();

            set_option('nuxeoUcldcSchema','installed');

            $knownSchema = unserialize(get_option('nuxeoKnownSchema'));
            $knownSchema['ucldc_schema']="UCLDC Schema";
            set_option('nuxeoKnownSchema',serialize($knownSchema));

        } else if($_REQUEST['nuxeoUcldcSchema'] !=="installed" && get_option('nuxeoUcldcSchema')=='installed' ) {
            
            $ucldcElementSet = get_db()->getTable('ElementSet')->findByName('UCLDC Schema');
            foreach($ucldcElementSet->getElements() as $element) 
                $element->delete();

            $ucldcElementSet->delete();

            set_option('nuxeoUcldcSchema','');

            $knownSchema = unserialize(get_option('nuxeoKnownSchema'));
            unset($knownSchema['ucldc_schema']);
            set_option('nuxeoKnownSchema',serialize($knownSchema));
                
        } 

    
//TODO Import or delete UCLDC schema as necessary
    }
    
    /**
     * Set the options from the config form input.
     */
    public function hookConfigForm() {
        require dirname(__FILE__) . '/forms/config_form.php';
    }

    /**
     * Queue the javascript and css files to help the form work.
     *
     * This function runs before the admin section of the sit loads.
     * It queues the javascript and css files which help the form work,
     * so that they are loaded before any html output.
     *
     * @return void
     */
    public function hookAdminHead() {
        queue_css_file('style.min');
        queue_css_file('nuxeoLink');
        queue_js_file('jstree.min');
        queue_js_file('nuxeoLink');
    }

    /**
     * Define the plugin's access control list.
     *
     * @param array $args This array contains a reference to
     * the zend ACL under it's 'acl' key.
     * @return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('NuxeoLink_Index');
    }

    /**
     * Add the NuxeoLink link to the admin main navigation.
     * 
     * @param array $nav Array of links for admin nav section
     * @return array $nav Updated array of links for admin nav section
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Nuxeo Link'),
            'uri' => url('nuxeo-link'),
            'resource' => 'NuxeoLink_Index',
            'privilege' => 'index'
        );
        return $nav;
    }
  

}