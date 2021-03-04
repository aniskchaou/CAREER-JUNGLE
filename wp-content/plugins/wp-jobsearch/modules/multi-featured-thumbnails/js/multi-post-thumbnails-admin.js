window.JobSearchMultiPostThumbnails = {
    
    setThumbnailHTML: function(html, id, post_type){
	    jQuery('.inside', '#' + post_type + '-' + id).html(html);
    },

    setThumbnailID: function(thumb_id, id, post_type){
	    var field = jQuery('input[value=_' + post_type + '_' + id + '_thumbnail_id]', '#list-table');
	    if ( field.length > 0 ) {
		    jQuery('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(thumb_id);
	    }
    },

    removeThumbnail: function(id, post_type, nonce){
	    jQuery.post(ajaxurl, {
		    action:'set-' + post_type + '-' + id + '-thumbnail', post_id: jQuery('#post_ID').val(), thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	    }, function(str){
		    if ( str == '0' ) {
			    alert( 'Error!' );
		    } else {
			    JobSearchMultiPostThumbnails.setThumbnailHTML(str, id, post_type);
		    }
	    }
	    );
    },


    setAsThumbnail: function(thumb_id, id, post_type, nonce){
	    var $link = jQuery('a#' + post_type + '-' + id + '-thumbnail-' + thumb_id);
		$link.data('thumbnail_id', thumb_id);
	    $link.text( 'Saving' );
	    jQuery.post(ajaxurl, {
		    action:'set-' + post_type + '-' + id + '-thumbnail', post_id: post_id, thumbnail_id: thumb_id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	    }, function(str){
		    var win = window.dialogArguments || opener || parent || top;
		    $link.text( 'Set Thumbnail' );
		    if ( str == '0' ) {
			    alert( 'Error!' );
		    } else {
			    $link.show();
			    $link.text( 'Done' );
			    $link.fadeOut( 2000, function() {
				    jQuery('tr.' + post_type + '-' + id + '-thumbnail').hide();
			    });
			    win.JobSearchMultiPostThumbnails.setThumbnailID(thumb_id, id, post_type);
			    win.JobSearchMultiPostThumbnails.setThumbnailHTML(str, id, post_type);
		    }
	    }
	    );
    }
}