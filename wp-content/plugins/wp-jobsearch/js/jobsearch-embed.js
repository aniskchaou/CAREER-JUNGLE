window.embeddable_job_embarg = function () {
    var embeddable_job_embarg_page = 1;
    var embeddable_job_embarg_script;
    return {
        get_jobs: function (page) {
            var head = document.getElementsByTagName("head")[0];
            embeddable_job_embarg_script = document.createElement("script");
            //embeddable_job_embarg_script.async = true;
            var var_atts_str = '';
            if (typeof jobsearch_embeddable_job_options === 'object') {
                var atts_counter = 1;
                for (var key_index of Object.keys(jobsearch_embeddable_job_options)) {
                    var elem_val = jobsearch_embeddable_job_options[key_index];
                    if (atts_counter == 1) {
                        var_atts_str += elem_val;
                    } else {
                        var_atts_str += '&' + key_index + '=' + elem_val;
                    }
                    atts_counter++;
                }
                if (var_atts_str != '') {
                    var_atts_str += '&page=' + page;
                }
                //console.log(var_atts_str);
            }
            
            embeddable_job_embarg_script.src = var_atts_str;
            head.appendChild(embeddable_job_embarg_script);
            return false
        },
        show_jobs: function (target_id, content) {
            var target = document.getElementById(target_id);
            if (target) {
                target.innerHTML = this.decode_html(content);
            }
        },
        decode_html: function (html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        },
        prev_page: function () {
            embeddable_job_embarg_script.parentNode.removeChild(embeddable_job_embarg_script);
            embeddable_job_embarg_page = embeddable_job_embarg_page - 1;

            if (embeddable_job_embarg_page < 1) {
                embeddable_job_embarg_page = 1;
            }

            this.get_jobs(embeddable_job_embarg_page)
        },
        next_page: function () {
            embeddable_job_embarg_script.parentNode.removeChild(embeddable_job_embarg_script);
            embeddable_job_embarg_page = embeddable_job_embarg_page + 1;
            this.get_jobs(embeddable_job_embarg_page)
        }
    }
}();

window.embeddable_job_embarg.get_jobs(1);