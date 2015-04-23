function insertDeck(){
	var deckid = jQuery('#lmslider_tinymce_dialog tbody tr.selected')[0].id.split("_")[2];
        var width = jQuery('#lmslider_tinymce_dimension_w').val();
        var height = jQuery('#lmslider_tinymce_dimension_h').val();
    
        var lmslider_str = " [lmslider deckid='" + deckid + "'";
        if(width.replace(/^\s+|\s+$/g,"") != ""){
            lmslider_str += " width='" + width + "'";
        }
        if(height.replace(/^\s+|\s+$/g,"") != ""){
            lmslider_str += " height='" + height + "'";
        }
        lmslider_str += "] ";

        if (typeof(tinyMCE) != 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden()) {
            ed.focus();
            if (tinymce.isIE) {
                ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
            }
            ed.execCommand('mceInsertContent', false, lmslider_str);
        } else {
            edInsertContent(edCanvas, lmslider_str);
        }
        
}

jQuery(document).ready(function(){
	jQuery('#lmslider_tinymce_dialog').dialog({
        autoOpen: false,
        width: 450,
        height: 'auto',
        draggable: false,
        resizable: false,
        title: 'Ajouter animation',
        dialogClass: 'lmslider_tinymce_dialog',
        buttons: {
            "Ajouter": function(){
                insertDeck();
            },
            "Annuler": function(){
                jQuery(this).dialog('close');
            }
        },

    }).find('tbody tr').click(function(event){
		event.preventDefault();
        jQuery('#lmslider_tinymce_dialog tbody tr').removeClass('selected');
        jQuery(this).addClass('selected');
    })

})