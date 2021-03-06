jQuery(document).ready(addDamsBrowser);
jQuery(document).ready(implementSearch);
jQuery(document).ready(warnDeleteSchema);
var url = 'nuxeo-link/index/folders/uid/';
var docUrl = 'nuxeo-link/index/documents?path=';
var searchUrl = 'nuxeo-link/index/search/uid/';

function implementSearch() {
    jQuery('#nuxeo-search-button').click(function(e){
	e.preventDefault();
	jQuery('#nuxeo-preview').html('<h3>Searching...</h3><p>Retrieving documents containing your search terms. For large datasets, this could take a minute. Thanks for your patience.</p>');
	selectedNodes = jQuery('#tree').jstree(true).get_top_selected(false);
	if(selectedNodes.length > 0)
	    id=selectedNodes[0];
	else
	    id = 'd0ba6352-44c6-4f25-b9f9-667131d3eaf2';
	searchTerm = jQuery('#nuxeo-search-box').val();
	var url = searchUrl+id+'/search/'+searchTerm;
	jQuery.get(
	    url,
	    function(jsonData) {
		data = jQuery.parseJSON(jsonData);
		if(typeof(data)!="object") {
		    jQuery('#nuxeo-preview').html('<h3>Search Failed!</h3><p>Something went wrong in your search. Is something wrong with your server internet connection perhaps?</p>');
		    return;
		}
		thumbBase = data.thumbBase;
		entries = data.entries;
		//console.log(data);
		if(entries.length > 0) {
		    jQuery('#nuxeo-preview').html('<div id="select-buttons"><button id="select-all" class="select-button">Select All</button><button id="select-none" class="select-button">Select None</button></div><label id="numDocLi">'+entries.length+' Documents <div style="font-size:0.8em">(not displaying audio/video)</div></label><br><ul id="preview-list"></ul>');
		}else {
		    jQuery('#nuxeo-preview').html('<h3>No Results</h3><p>Your search returned no results.</p>');
		    return;
		}

		jQuery.each(entries,function(index,value) {
		    thumbUrl = thumbBase+value['uid']+"";
//		    console.log('thumburl: '+thumbUrl);
		    if((jQuery.inArray("Picture",value.facets)) < 0)
		    {
//			console.log(value.facets);
//			console.log('skipping non image file');
			return true;
		    }
		    prevLi = '<li id="preview-'+value['uid']+'">';
		    prevLi += '<input type="checkbox" class="import-check" checked="checked" name="toImport[]" value="'+value['path']+'" />';
		    prevLi += '<img src="'+encodeURI(thumbUrl)+'?path=%2Fviews%2Fitem%5B3%5D%2Fcontent" />';
		    prevLi += "<p>"+value['title']+"</p>";
		    prevLi += "</li>";
		    jQuery('#preview-list').append(prevLi);
		});
		bindButtonActions();
	    }
	);
	
    });
}

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

    jQuery('.nuxeo-link #fieldset-fields').append(jQuery("#nuxeo-search-div"));
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
	path = jQuery('#'+id).attr('title');
	jQuery('#nuxeo-preview').html('<h3>Loading...</h3><p>Retrieving data from Nuxeo. For large datasets, this could take a minute. Thanks for your patience.</p>');
	jQuery.get(
	    //docUrl+id,
	    docUrl+encodeURIComponent(path),
	    function(jsonData) {
		if(jsonData=="file not found")
		    data={};
		else
		    data = jQuery.parseJSON(jsonData);
		thumbBase = data['thumbBase'];
		data = data['docs'];
		if(data.length > 0)
		    jQuery('#nuxeo-preview').html('<div id="select-buttons"><button id="select-all" class="select-button">Select All</button><button id="select-none" class="select-button">Select None</button></div><label id="numDocLi">'+data.length+' Documents <div style="font-size:0.8em">(Not displaying audio/video)</div></label><br><ul id="preview-list"></ul>');
		else 
		    jQuery('#nuxeo-preview').html('<h3>No documents found</h3><p>There are no documents directly inside the folder you have selected on the left. There may be documents inside subfolders.</p>');


		jQuery.each(data,function(index,value) {
		    if(!('thumb' in value))
			return true;
		    thumbUrl = encodeURI(thumbBase+value['id'])+"?path=%2Fviews%2Fitem%5B3%5D%2Fcontent";
		    prevLi = '<li id="preview-'+value['id']+'">';
		    prevLi += '<input type="checkbox" class="import-check" checked="checked" name="toImport[]" value="'+value['path']+'" />';
		    prevLi += '<img src="'+thumbUrl+'" />';
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

function getPath(id) {

}
