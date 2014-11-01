jQuery(document).ready(addDamsBrowser);
jQuery(document).ready(warnDeleteSchema);
var url = 'nuxeo-link/index/folders/uid/';
var docUrl = 'nuxeo-link/index/documents/uid/';

function warnDeleteSchema() {
    jQuery('#nuxeoUcldcSchema').click(function(e) {

	if(!jQuery('#nuxeoUcldcSchema').prop('checked')) {
	    
	    jQuery("#content").append('<div id="dialog">Are you sure you want to stop using the UCLDC Schema? Unchecking this option will delete the UCLDC schema from this Omeka instance, which will also delete any metadata stored therein.</div>');
	    jQuery("#dialog").dialog({
		resizable: false,
		height:250,
		width:350,
		modal: true,
		buttons: {
		    "Proceed": function() {
			jQuery( this ).dialog( "close" );
		    },
		    Cancel: function() {
			jQuery('#nuxeoUcldcSchema').prop('checked',true);
			jQuery( this ).dialog( "close" );
		    }
		}
	    });
	}
    });
}

function addDamsBrowser() {

    jQuery('.nuxeo-link #fieldset-fields').append('<div id="tree_div"><div id="tree" style="float:left;clear:left;"></div><div id="nuxeo-preview"></div></div>');
    var topUid = 'd0ba6352-44c6-4f25-b9f9-667131d3eaf2';

    jQuery('#tree').jstree({
	'core' : {
	    'data' : {
		'url' : function (node) {
		    return node.id === '#' ? 
			url+topUid : 
			url+node.id;
		},
		'data' : function (node) {
		    return { 'id' : node.id };
		},
		'dataType':'json'
	    }
	}
    });

    jQuery('#tree').on('select_node.jstree',function(event,selectParams){
	id = selectParams['selected'];
	//console.log(id);
	jQuery('#nuxeo-preview').html('<h3>Loading...</h3><p>Retrieving data from Nuxeo. For large datasets, this could take a minute. Thanks for your patience.</p>');
	jQuery.get(
	    docUrl+id,
	    function(jsonData) {
		data = jQuery.parseJSON(jsonData);

		if(data.length > 0)
		    jQuery('#nuxeo-preview').html('<div id="select-buttons"><button id="select-all" class="select-button">Select All</button><button id="select-none" class="select-button">Select None</button></div><label>'+data.length+' Documents (displaying images only)</label><br><ul id="preview-list"></ul>');

		jQuery.each(data,function(index,value) {
		    if(!('thumb' in value))
			next;
		    prevLi = '<li id="preview-'+value['id']+'">';
		    prevLi += '<input type="checkbox" class="import-check" checked="checked" name="toImport[]" value="'+value['path']+'" />';
		    prevLi += '<img src="'+value['thumb']+'" />';
		    prevLi += "<p>"+value['text']+"</p>";
		    prevLi += "</li>";
		    jQuery('#preview-list').append(prevLi);
		});
		bindButtonActions();
	    }
	);
	jQuery('#tree').jstree('open_node',selectParams['node']);
    });
    
}

function bindButtonActions() {
    jQuery('#select-all').click(function(e) {
	e.preventDefault();
	jQuery('.import-check').prop('checked',true);
    });
    jQuery('#select-none').click(function(e) {
	e.preventDefault();
	jQuery('.import-check').attr('checked',false);
    });

}
