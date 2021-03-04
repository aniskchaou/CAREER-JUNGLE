(function() {
    if (typeof joblistin_caned_msgs !== 'undefined' && joblistin_caned_msgs.length > 0) {
        var jobsearch_btn_menu = [];
        
        tinymce.PluginManager.add('caned_resp_button', function( editor, url ) {
            if (editor.id == 'job_detail') {
                jQuery.each(joblistin_caned_msgs, function(cmsg_index, cmsg_val) {
                    jobsearch_btn_menu.push({
                        text: cmsg_val.title,
                        onclick: function() {
                            editor.setContent('');
                            editor.insertContent(cmsg_val.desc);
                        }
                    });
                });
                editor.addButton( 'caned_resp_button', {
                    text: joblistin_caned_msgstitl,
                    icon: false,
                    type: 'listbox',
                    menu: jobsearch_btn_menu
                });
            }
        });
    }
})();
