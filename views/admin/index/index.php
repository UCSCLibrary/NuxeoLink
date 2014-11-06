<?php

$head = array('bodyclass' => 'nuxeo-link primary', 
              'title' => html_escape(__('NuxeoLink | Import documents')));
echo head($head);
echo flash(); 
echo $form;
?>
<div id="nuxeo-search-div" class="field">
<input type="text" id="nuxeo-search-box" />
<button type="notsubmit" id="nuxeo-search-button">Search</button>
<p id="nuxeo-search-blurb">
    Full text search of all metadata fields. Search is conducted within documents contained by the folder selected on the document tree on the left of your screen, or in all collections if no selection has been made.
</p>
</div>
<?
echo foot(); 
?>