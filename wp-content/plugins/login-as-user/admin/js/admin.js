/* ======================================================
# Login as User WordPress Plugin! - v1.3.0 (Free)
# -------------------------------------------------------
# For WordPress
# Author: Web357
# Copyright (©) 2009-2021 Web357. All rights reserved.
# License: GPLv2 or later, http://www.gnu.org/licenses/gpl-2.0.html
# Website: https:/www.web357.com/
# Demo: https://demo.web357.com/wordpress/login-as-user
# Support: support@web357.com
# Last modified: 09 Jan 2021, 02:18:12
========================================================= */

jQuery(function ($) {
	'use strict';

    $('.w357-login-as-user-btn').bind('contextmenu', function(e) {
		alert('The right click is disabled. Please, just click on the button.');
		return false;
    }); 

    // Do not show the button (activate License key) on typing
    $('#license_key').on('input', function() {
        $("#apikey-container").html('<p style="color: red; margin-top: 15px;">Please, save the plugin settings.</p>');
    });

	// Restore to Defaults
	$(document).on("click", ".web357-activate-api-key-btn", function(e){
        e.preventDefault();

        var nonce = $(this).data('nonce');
        var key = $(this).data('key');
        var domain = $(this).data('domain');

        $.ajax({
            type : "POST",
            dataType : "json",
            cache: false,
            url : loginasuserAjax.loginasuser_ajaxurl,
            data : {action: "web357_license_key_validation", key : key, domain: domain, nonce: nonce},

            success: function (response) {
                console.log( "RESPONSE TYPE: " + response.type );
                if(response.type == "success") {
                    $('.web357_apikey_activation_html').html(response.message);
                }
                else {
                    alert("There is a problem. Your key could not be validated. Please, contact us at support@web357.com")
                }
			},
			error: function(response) {

                $('.web357_apikey_activation_html').html(response.message);
            },
            beforeSend: function () {

                $(".web357-loading-gif").show();
                
               $('#w357-activated-successfully-msg').hide();
               $('#w357-activated-successfully-msg-ajax').hide();

			},
			complete: function () {

				$(".web357-loading-gif").hide();
                $('#w357-activated-successfully-msg').css('display', 'none');
                $('#w357-activated-successfully-msg-ajax').css('display', 'block');

            }
        })          
    });

});