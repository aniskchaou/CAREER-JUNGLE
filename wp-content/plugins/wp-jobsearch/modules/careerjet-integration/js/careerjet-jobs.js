function jobsearch_import_careerjet_jobs_submit(adminajax) {
    "use strict";
    var $ = jQuery;
    $("#loading").show();
    $('input[type="submit"]').attr('disabled', true);
    var datastring = $("#jobsearch-import-careerjet-jobs").serialize() + "&action=jobsearch_import_careerjet_jobs";
    var request = $.ajax({
        type: 'POST',
        url: adminajax,
        data: datastring,
        dataType: "json",
    });
    request.done(function (response) {
        $("#loading").hide();
        $("#error_msg").hide();
        $('input[type="submit"]').removeAttr('disabled');
        if (response.type == 'error') {
            $("#error_msg").find('p').find('strong').html(response.message);
            $("#error_msg").show();
            scroll_to_top();
        } else {
            $("#success_msg").find('p').find('strong').html(response.msg);
            $("#success_msg").show();
            scroll_to_top();
        }
    });
    request.fail(function (jqXHR, textStatus) {
        $("#loading").hide();
        $("#error_msg").hide();
        $('input[type="submit"]').removeAttr('disabled');
        $("#success_msg").hide();
    });

}

function scroll_to_top() {
    jQuery('html, body').animate({
        scrollTop: jQuery("#wrapper").offset().top
    }, 500);
}