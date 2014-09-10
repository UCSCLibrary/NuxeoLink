
<div class="field">
    <div id="nuxeo-url-label" class="two columns alpha">
        <label for="nuxeo-url"><?php echo __('Nuxeo instance url'); ?></label>
    </div>
    <div class="inputs five columns omega">
<?php echo get_view()->formText('nuxeoUrl',get_option('nuxeoUrl'),array()); ?>
        <p class="explanation"><?php echo __('Enter the URL of your nuxeo instance'); ?></p>
    </div>
</div>

<div class="field">
    <div id="nuxeo-user-label" class="two columns alpha">
        <label for="nuxeo-user"><?php echo __('Nuxeo username'); ?></label>
    </div>
    <div class="inputs five columns omega">
<?php echo get_view()->formText('nuxeoUser',get_option('nuxeoUser'),array()); ?>
        <p class="explanation"><?php echo __('Enter your username for authentication for the Nuxeo rest API'); ?></p>
    </div>
</div>

<div class="field">
    <div id="nuxeo-pass-label" class="two columns alpha">
        <label for="nuxeo-pass"><?php echo __('Nuxeo password'); ?></label>
    </div>
    <div class="inputs five columns omega">
<?php echo get_view()->formText('nuxeoPass',get_option('nuxeoPass'),array()); ?>
        <p class="explanation"><?php echo __('Enter your password for authentication for the Nuxeo rest API'); ?></p>
    </div>
</div>

<div class="field">
    <div id="nuxeo-ucldc-schema-label" class="two columns alpha">
        <label for="nuxeo-ucldc-schema"><?php echo __('Use UCLDC schema?'); ?></label>
    </div>
    <div class="inputs five columns omega">
<?php  
     $props= array();
     if(get_option('nuxeoUcldcSchema')=='installed')
         $props=array('checked'=>'checked');
     echo get_view()->formCheckbox('nuxeoUcldcSchema', 'installed',$props); ?>
        <p class="explanation"><?php echo __(
            'Would you like to implement the UCLDC metadata schema in Omeka?' 
          . ' Required to capture all metadata from items in the UCLDC DAMS.'
        ); 
?></p>
    </div>
</div>
<!--
<div class="field">
    <div id="nuxeo-autocreate-schema-label" class="two columns alpha">
        <label for="nuxeo-autocreate-schema"><?php echo __('Autocreate new schema?'); ?></label>
    </div>
    <div class="inputs five columns omega">
   <?php echo get_view()->formCheckbox('nuxeoAutoCreateSchema','installed', 
        array('disabled'=>'disabled')); ?>
        <p class="explanation"><?php echo __(
            'Would you like to automatically create new schema in Omeka' 
          . 'to match any unknown schema in nuxeo containing item level metadata?'
        ); ?></p>
    </div>
</div>
-->