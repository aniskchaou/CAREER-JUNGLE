function jobsearch_import_indeed_jobs_submit(adminajax) {
    "use strict";
    var $ = jQuery;
    var publisher_number = $('#publisher_number').val();
    if (publisher_number == '') {
        $("#error_msg").show();
        scroll_to_top();
    } else {
        $("#loading").show();
        $('input[type="submit"]').attr('disabled', true);
        var datastring = $("#jobsearch-import-indeed-jobs").serialize() + "&action=jobsearch_import_indeed_jobs";
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
                $("#invalid_publisher_number").html("<p><strong>" + response.message + "</strong></p>");
                $("#success_msg").hide();
                $("#invalid_publisher_number").show();
                scroll_to_top();
            } else {
                $("#invalid_publisher_number").hide();
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
            $("#invalid_publisher_number").hide();
        });
    }

}

function scroll_to_top() {
    jQuery('html, body').animate({
        scrollTop: jQuery("#wrapper").offset().top
    }, 500);
}