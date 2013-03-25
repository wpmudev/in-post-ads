function wdca_openReelEditor () {
	jQuery(document).trigger('wdca_selector_open');
	return false;
}

(function ($) {
$(function () {

	
/**
 * Inserts marker into regular (textarea) editor.
 */
function insertAtCursor(fld, text) {
    // IE
    if (document.selection && !window.opera) {
    	fld.focus();
        sel = window.opener.document.selection.createRange();
        sel.text = text;
    }
    // Rest
    else if (fld.selectionStart || fld.selectionStart == '0') {
        var startPos = fld.selectionStart;
        var endPos = fld.selectionEnd;
        fld.value = fld.value.substring(0, startPos)
        + text
        + fld.value.substring(endPos, fld.value.length);
    } else {
    	fld.value += text;
    }
}
	
	
//Create the needed editor container HTML
$('body').append(
	'<div id="wdca_ad_container" style="display:none">' + 
		'<div id="wdca_ads">' +
		'</div>' +
	'</div>'
);

// Bind events
$(document).bind('wdca_selector_open', function () {
	$("#wdca_ads").html("loading");
	$.post(ajaxurl, {"action": "wdca_list_ads"}, function (data) {
		var html = '';
		
		// Appearance
		html += '<h4>' + l10nWdca.appearance + '</h4>';
		html += '<div>';
		html += '<label for="wdca_size">' + 
			l10nWdca.ad_size +
			'<select id="wdca_size">' +
				'<option value="">' + l10nWdca.dflt + '</option>' +
				'<option value="small">' + l10nWdca.small + '</option>' +
				'<option value="medium">' + l10nWdca.medium + '</option>' +
				'<option value="large">' + l10nWdca.large + '</option>' +
			'</select>' +
		'</label>';
		html += '<label for="wdca_position">' + 
			l10nWdca.ad_position +
			'<select id="wdca_position">' +
				'<option value="">' + l10nWdca.dflt + '</option>' +
				'<option value="left">' + l10nWdca.left + '</option>' +
				'<option value="right">' + l10nWdca.right + '</option>' +
			'</select>' +
		'</label>';
		html += '</div>';
		
		html += '<div>';
		html += '<a href="#" class="wdca_insert_ad">' + l10nWdca.add_blank + ' <input type="hidden" value="" /></a>';
		html += l10nWdca.or_select_below;
		html += '</div>';
		
		// Existing ads
		html += '<table class="widefat" border="1">';
		
		html += '<thead><tr> <th>' + l10nWdca.ad_title + '</th> <th>' + l10nWdca.ad_date + '</th> <th></th> </tr></thead>';
		html += '<tfoot><tr> <th>' + l10nWdca.ad_title + '</th> <th>' + l10nWdca.ad_date + '</th> <th></th> </tr></tfoot>';
		
		$.each(data, function (idx, ad) {
			html += '<tr>';
			html += '<td>' + ad.post_title + '</td>';
			html += '<td>' + ad.post_date + '</td>';
			html += '<td><a href="#" class="wdca_insert_ad">' + l10nWdca.add_ad + ' <input type="hidden" value="' + ad.ID + '" /></a></td>';
			html += '</tr>';
		});
		
		html += '</table>';
		
		$("#wdca_ads").html(html);
	});
});

$(".wdca_insert_ad").live('click', function () {
	var id = parseInt($(this).find("input:hidden").val());
	var id_str = id ? 'id="' + id + '" ' : '';
	var size = $("#wdca_size").val();
	var pos = $("#wdca_position").val();
	var appearance = '';
	appearance += size ? size : '';
	appearance += pos ? ' ' + pos : '';
	var app_str = appearance ? 'appearance="' + appearance + '"' : '';
	var marker = ' [wdca_ad ' + id_str + ' ' + app_str + '] ';
	if (window.tinyMCE && ! $('#content').is(':visible')) window.tinyMCE.execCommand("mceInsertContent", true, marker);
	else insertAtCursor($("#content").get(0), marker);
	tb_remove();
	return false;
});

// Find Media Buttons strip and add the new one
var mbuttons_container = $('#media-buttons').length ? /*3.2*/ $('#media-buttons') : /*3.3*/ $("#wp-content-media-buttons");
if (!mbuttons_container.length) return;

mbuttons_container.append('' + 
	'<a onclick="return wdca_openReelEditor();" title="' + l10nWdca.add_ad + '" class="thickbox" id="add_map" href="#TB_inline?width=640&height=594&inlineId=wdca_ad_container">' +
		'<img onclick="return false;" alt="' + l10nWdca.add_ad + '" src="' + _wdca_data.root_url + '/img/ad.png">' +
	'</a>'
);

});
})(jQuery);