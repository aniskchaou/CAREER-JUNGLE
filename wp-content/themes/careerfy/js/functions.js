// Multi-Toggle Navigation
var $ = jQuery;

jQuery(function ($) {

    'use strict';

    $('body').addClass('js');

    $(".navbar li").each(function () {
        var each_li = $(this);
        if (each_li.find('ul').length > 0) {
            each_li.append("<span class='has-subnav'><i class='fa fa-angle-down'></i></span>");
        }
    });
});

$(document).on("click", '.menu-link', function (e) {
    e.preventDefault();
    var $ = jQuery;
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
    } else {
        $(this).addClass('active');
    }
    if ($('#menu').hasClass('active')) {
        $('#menu').removeClass('active');
    } else {
        $('#menu').addClass('active');
    }
});

$(document).on("click", '.has-subnav', function (e) {
    e.preventDefault();
    var $this = jQuery(this);
    $this.parent('li').find('> ul').toggleClass('active');
});

jQuery(document).on('click', '.careerfy-nav-toogle', function () {
    'use strict';
    var _this = jQuery(this);
    var nav_bar = jQuery('.careerfy-nav-area');
    if (nav_bar.hasClass('nav-active')) {
        nav_bar.removeClass('nav-active');
        _this.find('img').attr('src', careerfy_funnc_vars.nav_open_img);
        nav_bar.hide('slide', {direction: 'left'}, 500);
    } else {
        nav_bar.addClass('nav-active');
        _this.find('img').attr('src', careerfy_funnc_vars.nav_close_img);
        nav_bar.show("slide", {direction: "left"}, 500);
    }
});


jQuery(".navbar-nav .sub-menu").parent("li").addClass("submenu-addicon");

//
jQuery(document).on('click', '.careerfy-wrapper', function(e) {
    if (jQuery('.careerfy-mobile-hdr-sidebar').length > 0) {
        var this_dom = e.target;
        var thisdom_obj = jQuery(this_dom);

        if (thisdom_obj.hasClass('mobile-navigation-togglebtn') || thisdom_obj.parents('a').hasClass('mobile-navigation-togglebtn')) {
            return false;
        }
        if (thisdom_obj.hasClass('jobsearch-useracount-hdrbtn') || thisdom_obj.parents('a').hasClass('jobsearch-useracount-hdrbtn')) {
            return false;
        }
        if (thisdom_obj.hasClass('mobile-usernotifics-btn') || thisdom_obj.parents('a').hasClass('mobile-usernotifics-btn')) {
            return false;
        }

        var hdr_sidebar_con = jQuery('.careerfy-mobile-hdr-sidebar');
        if (hdr_sidebar_con.hasClass('animate-menu-open')) {
            hdr_sidebar_con.removeClass('animate-menu-open');
        }
    }
});

jQuery.careerfySidebarMenu = function (menu) {
    var animationSpeed = 300,
            subMenuSelector = '.sidebar-submenu';

    jQuery(menu).on('click', 'li .child-navitms-opner', function (e) {
        var $this = jQuery(this);
        var checkElement = $this.next();

        if (checkElement.is(subMenuSelector) && checkElement.is(':visible')) {
            checkElement.slideUp(animationSpeed, function () {
                checkElement.removeClass('menu-open');
            });
            checkElement.parent("li").removeClass("active");
        }

        //If the menu is not visible
        else if ((checkElement.is(subMenuSelector)) && (!checkElement.is(':visible'))) {
            //Get the parent menu
            var parent = $this.parents('ul').first();
            //Close all open menus within the parent
            var ul = parent.find('ul:visible').slideUp(animationSpeed);
            //Remove the menu-open class from the parent
            ul.removeClass('menu-open');
            //Get the parent li
            var parent_li = $this.parent("li");

            //Open the target menu and add the menu-open class
            checkElement.slideDown(animationSpeed, function () {
                //Add the class active to the parent li
                checkElement.addClass('menu-open');
                parent.find('li.active').removeClass('active');
                parent_li.addClass('active');
            });
        }
        //if this isn't a link, prevent the page from being redirected
        if (checkElement.is(subMenuSelector)) {
            e.preventDefault();
        }
    });
}

jQuery.careerfySidebarMenu(jQuery('.careerfy-mobile-navbar'));

jQuery('#careerfy-mobile-navbtn, .mobile-navclose-btn').click(function () {
    if (!jQuery('.careerfy-mobile-hdr-sidebar').hasClass('animate-menu-open')) {
        jQuery('.careerfy-inmobile-itemsgen').hide();
        jQuery('.careerfy-sidebar-navigation').removeAttr('style');
    }
    jQuery('.careerfy-mobile-hdr-sidebar').toggleClass('animate-menu-open');
});

jQuery(document).ready(function() {
    jQuery("body").fitVids();
});

jQuery(window).on("load", function () {
    jQuery("body").addClass("active");
    jQuery("body").removeClass("careerfy-page-loading");
});