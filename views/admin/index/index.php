\<?php

$head = array('bodyclass' => 'nuxeo-link primary', 
              'title' => html_escape(__('NuxeoLink | Import documents')));
echo head($head);
echo flash(); 
echo $form;
echo foot(); 
?>