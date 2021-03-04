jQuery(document).ready(function ($) {
    
    $('#embarg-get-code').click(function () {
        var all_var_atts = '';
        
        var this_form = jQuery(this).parents('form');
        this_form.find('input,select').each(function() {
            var _this_field = jQuery(this);
            var _this_field_get = _this_field.get(0);
            var attr_name = '';
            if (_this_field_get.hasAttribute('id')) {
                attr_name = _this_field.attr('id');
            }
            if (_this_field_get.hasAttribute('name')) {
                attr_name = _this_field.attr('name');
            }
            if (attr_name != '' && attr_name.indexOf('-selectized') < 0) {
                var attr_field_val = _this_field.val();
                //console.log(attr_name);
                //console.log(attr_field_val);
                all_var_atts += "'" + attr_name + "' : '" + attr_field_val + "',";
            }
        });

        var embed_code = "<script type='text/javascript'>\n\
	var jobsearch_embeddable_job_options = {\n\
            'script_url' : '" + jobsearch_embeddable_jobs_form_args.script_url + "',\
            " + (all_var_atts) + "\
	};\n\
        </script>\n" + jobsearch_embeddable_jobs_form_args.css + "\n" + jobsearch_embeddable_jobs_form_args.code;

        $('#embarg-code').val(embed_code).focus().select();
        $('#embarg-code-preview iframe').remove();
        $('#embarg-code-preview > p').remove();
        var iframe = document.createElement('iframe');
        var html = '<!doctype html><html><head></head><body style="margin:0; padding: 0;">' + embed_code + '</body></html>';
        $('#embarg-code-preview').append(iframe);
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(html);
        iframe.contentWindow.document.close();
        $('#embarg-code-wrapper').slideDown();
    });
});
