<?php

/**
 * Careerfy Theme Dynamic Colors.
 *
 * @package Careerfy
 */
function careerfy_dynamic_colors()
{
    $careerfy__options = careerfy_framework_options();

    $careerfy_theme_color = isset($careerfy__options['careerfy-main-color']) && $careerfy__options['careerfy-main-color'] != '' ? $careerfy__options['careerfy-main-color'] : '#13b5ea';
    ob_start();
    ?>


    /* Plugin ThemeColor */
    .jobsearch-color,.jobsearch-colorhover:hover,.widget_nav_manu ul li:hover a,.widget_nav_manu ul li:hover a:before,
    .categories-list ul li i,li:hover .jobsearch-blog-grid-text h2 a,li:hover .jobsearch-read-more,.jobsearch-filterable ul li:hover a,.jobsearch-job-tag a,.jobsearch-list-option ul li a:hover,.jobsearch-jobdetail-postinfo,
    .jobsearch-jobdetail-options li i,.jobsearch-jobdetail-btn,.jobsearch-jobdetail-services i,.jobsearch-list-style-icon li i,.widget_view_jobs_btn,.jobsearch-employer-list small,.jobsearch-employer-list-btn,
    .jobsearch-employer-grid figcaption small,.jobsearch-fileUpload span,.jobsearch-managejobs-appli,.jobsearch-transactions-tbody small,.jobsearch-resumes-subtitle a,.jobsearch-employer-confitmation a,
    .jobsearch-candidate-default-text h2 i,.jobsearch-candidate-default-btn,.jobsearch-candidate-grid figure figcaption p a,.jobsearch_candidate_info p a,.jobsearch-candidate-download-btn,.show-toggle-filter-list,
    .jobsearch-employer-dashboard-nav ul li:hover a,.jobsearch-employer-dashboard-nav ul li.active a,.jobsearch-candidate-savedjobs tbody td span,.jobsearch-cvupload-file span,.jobsearch-modal .modal-close:hover,
    .jobsearch-box-title span,.jobsearch-user-form-info p a,.jobsearch-services-classic span i,.widget_faq ul li:hover a,.grab-classic-priceplane ul li.active i,.jobsearch-classic-priceplane.active .jobsearch-classic-priceplane-btn,
    .jobsearch-plain-services i,.jobsearch-packages-priceplane ul li i,.jobsearch-additional-priceplane-btn,.contact-service i,.jobsearch-filterable ul li:hover a i,.jobsearch-login-box form a:hover,.jobsearch-resume-addbtn:hover,.img-icons a,.jobsearch-description ul li:before,.jobsearch-employer-list small a,.jobsearch-employer-wrap-section .jobsearch-candidate-grid figure figcaption span,.jobsearch-load-more a,.jobsearch-jobdetail-btn:hover,.tabs-list li:hover a,
    .tabs-list li.active a,.sort-list-view a.active,.sort-list-view a:hover,.jobsearch-applied-job-btns .more-actions:hover,.jobsearch-applied-job-btns .more-actions:hover i,.jobsearch-profile-title h2,
    .jobsearch-typo-wrap .jobsearch-findmap-btn, .jobsearch-employer-profile-form .jobsearch-findmap-btn,.jobsearch-filterable-select select, #top .jobsearch-filterable-select select,.jobsearch-candidate-title i,
    .jobsearch-candidate-timeline-text span,.jobsearch-candidate-timeline small,.jobsearch_candidate_info small,.careerfy-employer-grid-btn,.jobsearch-employerdetail-btn,.jobsearch-typo-wrap .main-tab-section .jobsearch-employer-profile-submit:hover,.jobsearch-applied-jobs-text span,.jobsearch-employer-dashboard-nav ul li:hover i,.jobsearch-employer-dashboard-nav ul li.active i,.jobsearch-applied-job-btns .more-actions.open-options,
    .jobsearch-applied-job-btns .more-actions.open-options i,.restrict-candidate-sec a:hover,.skills-perc small,.get-skill-detail-btn:hover,.percent-num,.jobsearch-filterable-select .selectize-control.single .selectize-input input,
    .jobsearch-filterable-select .item,.jobsearch-list-option ul li.job-company-name a,.adv-srch-toggler a,.post-secin a,.jobsearch-banner-search ul li.jobsearch-banner-submit:hover i,.show-all-results a:hover,
    .jobsearch-typo-wrap .jobsearch-add-review-con input[type="submit"]:hover,.careerfy-contact-form input[type="submit"]:hover,.jobsearch-user-form input[type="submit"]:hover,
    .jobsearch-employer-profile-submit:hover,.widget_contact_form input[type="submit"]:hover,.careerfy-company-name a,.careerfy-joblisting-wrap:hover .careerfy-joblisting-text h2 a,.careerfy-more-view4-btn a:hover,
    .careerfy-banner-search-eight input[type="submit"]:hover,.careerfy-blog-view6-btn:hover,.careerfy-view7-priceplane-btn:hover,.jobsearch-subs-detail,.careerfy-candidatedetail-services ul li i,
    .careerfy-footernine-newslatter-inner input[type="submit"]:hover, .careerfy-backto-top:hover, .careerfy-loadmore-listingsbtn a, .careerfy-loadmore-ninebtn a,.careerfy-thirteen-banner-caption h2 small,
    .careerfy-explore-jobs-links ul li:hover a,.careerfy-jobslatest-list li:hover figcaption h2 a,.careerfy-headerfifteen-user > li > a:hover,.careerfy-headersixteen-btn:hover,.careerfy-sixteen-jobs-links ul li:hover a,.careerfy-sixteen-jobs-links ul li.active a,.careerfy-sixteen-candidate-grid-bottom .careerfy-featured-candidates-loc i,.careerfy-sixteen-priceplan.active span,.careerfy-footer-sixteen .widget_nav_menu ul li:hover a,.copyright-sixteen p a,
    #JobSearchNonuserApplyModal i.jobsearch-icon.jobsearch-upload,.jobsearch-drpzon-con .upload-icon-con i,.jobsearch-drpzon-con .jobsearch-drpzon-btn,.jobsearch-drag-dropcustom .jobsearch-drpzon-btn i,/*Update*/
.jobsearch-sort-section .selectize-input,.careerfy-jobdetail-btn,a.careerfy-jobdetail-btn:hover,.jobsearch-send-email-popup-btn,.jobsearch-drpzon-con .jobsearch-drpzon-btn,
.jobsearch-drag-dropcustom .jobsearch-drpzon-btn i,.jobsearch-user-form i.jobsearch-icon,.jobsearch-candidate-timeline-text span,.jobsearch-candidate-timeline small,.jobsearch-candidate-title i,
.jobsearch_candidate_info small,.jobsearch-employerdetail-btn,.jobsearch-profile-title h2,.jobsearch-typo-wrap .jobsearch-findmap-btn, .jobsearch-employer-profile-form .jobsearch-findmap-btn,
.jobsearch-employer-dashboard-nav ul li:hover i, .jobsearch-employer-dashboard-nav ul li.active i, .jobsearch-employer-dashboard-nav ul li:hover a, .jobsearch-employer-dashboard-nav ul li.active a,
.jobsearch-typo-wrap .main-tab-section .jobsearch-employer-profile-submit:hover, .other-lang-translate-post:hover,.jobsearch-employer-profile-form .upload-port-img-btn,.jobsearch-resume-education span,
.jobsearch-applied-jobs-text span,.jobsearch-empmember-add-popup:hover,.categories-list ul li i,.jobsearch-filterable ul li:hover a,.jobsearch-filterable ul li:hover a i,.jobsearch-filterable-select:after,
.show-toggle-filter-list:hover,.jobsearch-seemore,.show-toggle-filter-list,.jobsearch-jobdetail-postinfo,.jobsearch-jobdetail-options li i,.jobsearch-jobdetail-btn:hover,.jobsearch-jobdetail-btn,
.jobsearch-jobdetail-services i,.jobsearch-description ul li:before,.jobsearch-description ul li i,.jobsearch_apply_job span,.jobsearch_box_view_jobs_btn,.jobsearch-employer-list small,.jobsearch-employer-list-btn,
.jobsearch-payments-checkbox input[type="checkbox"]:checked + label span:before,
.jobsearch-payments-checkbox input[type="checkbox"] + label:hover span:before,
.jobsearch-payments-checkbox input[type="radio"]:checked + label span:before,
.jobsearch-payments-checkbox input[type="radio"] + label:hover span:before,.jobsearch-candidate-default-text ul li a.jobsearch-candidate-default-studio,
.jobsearch-candidate-grid figure figcaption p a a,.aplicants-grid-inner-con p a,.short-li-icons li a,.register-form:hover,.careerfy-candidatedetail-text3-btn:hover,
.careerfy-candidate-timeline-two-text span,.candidate-detail4-timeline-thumb small,.careerfy-candidate-title i,li:hover .careerfy-services-twentytwo-btn,.lodmore-notifics-btnsec a:hover,.lodmore-jobs-btnsec a:hover,
.careerfy-candidate-style8-title,.careerfy-candidate-style8-loc i,li:hover .careerfy-candidate-style8-detail,.hder-notifics-count,.hdernotifics-after-con a:hover,.hdernotifics-after-con a,.careerfy-jobs-style9-title,
.careerfy-jobs-style9-options,.careerfy-jobs-box2 .shortlist_job_btn:hover,.careerfy-jobdetail-style5-content-list h2,.careerfy-jobdetail-btn-style5,.jobsearch-employer-followin-btn,.careerfy-content-title-style5 h2,
.careerfy-candidate-cta-btn a,.careerfy-candidate-info-inner-style5 h2,.careerfy-candidate-detail5-tablink ul li.active a,.careerfy-candidate-title-style5 h2,.careerfy-candiate-services-style5 i,
.careerfy-candidate-style5-contact-form input[type="submit"]:hover,.careerfy-twentyone-search-tabs .careerfy-search-twentyone-tabs-nav li a,.careerfy-services-twentyone li:hover i,.jobsearch-style9-custom-fields li,
#jobsearch-chat-container .jobsearch-chat-nav li.active a,#jobsearch-chat-container .jobsearch-chat-nav li a:hover,.careerfy-header-twenty-user>li:hover>a,.addnew-aplyquestbtn-con button:hover,.jobsearch-showpass-btn,
.jobsearch-add-job-to-favourite.jobsearch-job-like i,.jobsearch-add-job-to-favourite.careerfy-job-like i,.jobsearch-add-job-to-favourite.featured-jobs-grid-like i {
    color: <?php echo esc_html($careerfy_theme_color) ?>;
    }
    .jobsearch-bgcolor,.jobsearch-bgcolorhover:hover,.jobsearch-banner-search ul li.jobsearch-banner-submit i,.jobsearch-plain-btn a,.jobsearch-testimonial-slider .slick-arrow:hover,
    .jobsearch-featured-label,.jobsearch-job-like:hover,.jobsearch-pagination-blog ul li:hover a, .jobsearch-pagination-blog ul li:hover span,.jobsearch-jobdetail-view,.jobsearch-jobdetail-tags a:hover,.jobsearch-employer-list-btn:hover,
    ul li:hover .jobsearch-employer-grid-btn,.widget_contact_form input[type="submit"],.jobsearch-fileUpload:hover span,.jobsearch-resumes-options li:hover a,.jobsearch-employer-jobnav ul li:hover i,.jobsearch-employer-jobnav ul li.active i,
    .jobsearch-employer-jobnav ul li.active ~ li.active:after,.jobsearch-employer-jobnav ul li.active:nth-child(2):after,.jobsearch-employer-jobnav ul li.active:nth-child(3):after,.jobsearch-employer-confitmation a:hover,
    .jobsearch-candidate-default-btn:hover,.jobsearch-candidate-download-btn:hover,.jobsearch-add-popup input[type="submit"],.jobsearch-user-form input[type="submit"],.jobsearch-classic-services ul li:hover i,
    .jobsearch-service-slider .slick-arrow:hover,.jobsearch-classic-priceplane-btn,.jobsearch-classic-priceplane.active,.active .jobsearch-simple-priceplane-basic a,.jobsearch-packages-priceplane-btn,
    .jobsearch-additional-priceplane.active h2,.jobsearch-additional-priceplane.active .jobsearch-additional-priceplane-btn,.jobsearch-contact-info-sec,.jobsearch-contact-form input[type="submit"],.contact-service a,
    .jobsearch-employer-profile-form .jobsearch-findmap-btn:hover,.jobsearch-login-box form input[type="submit"],.jobsearch-login-box form .jobsearch-login-submit-btn, .jobsearch-login-box form .jobsearch-reset-password-submit-btn,
    .jobsearch-login-box form .jobsearch-register-submit-btn,.jobsearch-radio-checkbox input[type="radio"]:checked+label,.jobsearch-radio-checkbox input[type="radio"]:hover+label,.jobsearch-load-more a:hover,
    .jobsearch-typo-wrap .jobsearch-add-review-con input[type="submit"],.email-jobs-top,.jobalert-submit,.tabs-list li a:before,.sort-list-view a:before,.more-actions,.preview-candidate-profile:hover,
    .jobsearch-typo-wrap .ui-widget-header,.jobsearch-typo-wrap .ui-state-default, .jobsearch-typo-wrap .ui-widget-content .ui-state-default,.jobsearch-checkbox input[type="checkbox"]:checked + label span, .jobsearch-checkbox input[type="checkbox"] + label:hover span, .jobsearch-checkbox input[type="radio"]:checked + label span, .jobsearch-checkbox input[type="radio"] + label:hover span,.jobsearch-candidate-timeline small:after,
    .jobsearch_progressbar .bar,.jobsearch-employerdetail-btn:hover,.jobsearch-typo-wrap .jobsearch-employer-profile-submit,.sort-select-all label:after, .candidate-select-box label:after,
    .jobsearch-resume-addbtn,.jobsearch-cvupload-file:hover span,.restrict-candidate-sec a,.get-skill-detail-btn,.profile-improve-con ul li small,.complet-percent .percent-bar span,.wpcf7-form input[type="submit"],
    .jobsearch_searchloc_div .jobsearch_google_suggestions:hover,.jobsearch_searchloc_div .jobsearch_location_parent:hover,.show-all-results a,.jobsearch-jobdetail-btn.active:hover,.jobsearch-checkbox li:hover .filter-post-count,
    .careerfy-more-view4-btn a,.careerfy-banner-search-eight input[type="submit"],.careerfy-blog-view6-btn,.careerfy-view7-priceplane-btn,.jobsearch-addreview-form input[type="submit"],
    .careerfy-footernine-newslatter-inner input[type="submit"], .careerfy-footer-nine-social li a:after, .careerfy-backto-top, .careerfy-loadmore-listingsbtn a:hover, .careerfy-loadmore-ninebtn a:hover,
    .careerfy-categories-classic-slider .slick-arrow:hover,.jobsearch-banner-search ul li.jobsearch-banner-submit:hover i,.careerfy-headersixteen-btn,.careerfy-sixteen-banner form input[type="submit"],.careerfy-sixteen-banner-tags a:hover,
    .careerfy-sixteen-jobs-grid-like:hover,.careerfy-sixteen-jobs-slider .slick-dots li.slick-active button,.careerfy-sixteen-parallex-btn:hover,.careerfy-sixteen-parallex-btn.active,.careerfy-sixteen-topcompanies-slider .slick-dots li.slick-active button,li:hover .careerfy-sixteen-candidate-grid-like,.careerfy-sixteen-candidate-grid-btn.active,.careerfy-sixteen-candidate-slider .slick-dots li.slick-active button,
    .careerfy-sixteen-priceplan:hover .careerfy-sixteen-priceplan-btn,.careerfy-sixteen-priceplan.active .careerfy-sixteen-priceplan-btn,.jobsearch-drpzon-con:hover .jobsearch-drpzon-btn,/*Update*/
.jobsearch-checkbox input[type="checkbox"]:checked + label span,
.jobsearch-checkbox input[type="checkbox"] + label:hover span,
.jobsearch-checkbox input[type="radio"]:checked + label span,
.jobsearch-checkbox input[type="radio"] + label:hover span,
.jobsearch-pagination-blog ul li:hover a,
.jobsearch-pagination-blog ul li:hover span,
.jobsearch-pagination-blog ul li span.current,.jobsearch-typo-wrap .ui-widget-header,.jobsearch-typo-wrap .ui-state-default, .jobsearch-typo-wrap .ui-widget-content .ui-state-default,
.jobsearch-drpzon-con:hover .jobsearch-drpzon-btn,.jobsearch-candidate-timeline small:after,.jobsearch-employerdetail-btn:hover,.jobsearch-typo-wrap .jobsearch-employer-profile-submit,
.other-lang-translate-post,.jobsearch-resume-addbtn,.jobsearch-employer-profile-form .upload-port-img-btn:hover,.suggested-skills li:hover,.jobsearch-empmember-add-popup,.dash-hdtabchng-btn,
.addcand-databy-emp,.alret-submitbtn-con a.jobsearch-savejobalrts-sbtn,.jobsearch-jobdetail-view,.jobsearch-jobdetail-tags a:hover,.jobsearch-employer-list-btn:hover,.jobsearch-company-photo .jobsearch-fileUpload span,
.jobsearch-typo-wrap button:hover, .jobsearch-typo-wrap button:focus, .jobsearch-typo-wrap input[type="button"]:hover, .jobsearch-typo-wrap input[type="button"]:focus,
.jobsearch-typo-wrap input[type="submit"]:hover, .jobsearch-typo-wrap input[type="submit"]:focus,.jobsearch-cvupload-file:hover span,.salary-type-radio input[type="radio"]:checked + label span,
.demo-login-pbtns .active .jobsearch-demo-login-btn,.demo-login-pbtns .jobsearch-demo-login-btn:hover,.jobsearch-file-attach-sec li:hover .file-download-btn,.careerfy-candidatedetail-text3-btn,
.candidate-detail-two-subheader-btn:hover,.careerfy-candidate-detail4-subheader-btn,.careerfy-services-twentytwo-inner i,.careerfy-services-twentytwo-btn,.careerfy-services-twentytwo-style2-btn,
.careerfy-services-twentytwo-style2-content h2:before,.lodmore-notifics-btnsec a,.slick-current.slick-active .careerfy-testimonial-twentytwo-inner p,.lodmore-jobs-btnsec a,.careerfy-style8-candidate-like,.careerfy-candidate-style8-detail,.hdernotifics-after-con a:hover,.careerfy-jobs-box2 .shortlist_job_btn,.careerfy-jobdetail-style5-btns a:hover,.careerfy-jobdetail-style5-content-list ul li a.job-view-map,
.careerfy-jobdetail-btn-style5:hover,.careerfy-jobdetail-style5-email:hover,.careerfy-header-two li.jobsearch-usernotifics-menubtn > a,.jobsearch-employer-followin-btn:hover,
.careerfy-content-title-style5 h2:before,.careerfy-jobdetail-services-style5 i,.careerfy-candidate-cta-btn a:hover,.careerfy-candidate-detail5-tablink ul li.active a:before,
.careerfy-candidate-title-style5 h2:before,.careerfy-candidate-education-info:before,.careerfy-candidate-style5-contact-form input[type="submit"],
.careerfy-twentyone-search-tabs .careerfy-search-twentyone-tabs-nav li a:before,#jobsearch-chat-container .jobsearch-chat-nav li a:before,.jobsearch-chat-users-list li:before,
.jobsearch-chat-typing-wrapper input[type="submit"],.quest-typecon-leftsec i,.addnew-questtypes-btnsiner a:hover i,.addnew-aplyquestbtn-con button,.addnew-questtypes-btnsiner a.active-type-itm i,
.quests-item-answer p a,.jobsearch-applics-filterscon input.applics-filter-formbtn,.jobsearch-job-like,.careerfy-job-like,.featured-jobs-grid-like {
    background-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    .jobsearch-bordercolor,.jobsearch-bordercolorhover:hover,.jobsearch-jobdetail-btn,.jobsearch-employer-list-btn,.jobsearch-fileUpload span,.jobsearch-employer-confitmation a,.jobsearch-candidate-default-btn,
    .jobsearch-candidate-download-btn,.jobsearch-cvupload-file span,.active .jobsearch-simple-priceplane-basic a,.jobsearch-additional-priceplane-btn,.jobsearch-resume-addbtn,.jobsearch-load-more a,
    .more-actions,.jobsearch-typo-wrap .ui-state-default, .jobsearch-typo-wrap .ui-widget-content .ui-state-default,.jobsearch-typo-wrap .jobsearch-findmap-btn, .jobsearch-employer-profile-form .jobsearch-findmap-btn,
    .jobsearch-checkbox input[type="checkbox"]:checked + label span, .jobsearch-checkbox input[type="checkbox"] + label:hover span, .jobsearch-checkbox input[type="radio"]:checked + label span, .jobsearch-checkbox input[type="radio"] + label:hover span,.jobsearch-jobdetail-btn.active,.jobsearch-employerdetail-btn,.jobsearch-typo-wrap .jobsearch-employer-profile-submit,.restrict-candidate-sec a,.get-skill-detail-btn,
    .jobsearch-banner-search .adv-search-options .ui-widget-content .ui-state-default,.jobsearch-banner-search ul li.jobsearch-banner-submit i,.jobsearch-typo-wrap .jobsearch-add-review-con input[type="submit"],
    .careerfy-contact-form input[type="submit"],.jobsearch-jobdetail-btn.active:hover,.jobsearch-user-form input[type="submit"]:hover,.widget_contact_form input[type="submit"],.gform_wrapper input[type="text"]:focus,
    .gform_wrapper textarea:focus,.careerfy-more-view4-btn a,.careerfy-banner-search-eight input[type="submit"], .careerfy-loadmore-listingsbtn a, .careerfy-loadmore-ninebtn a,.careerfy-headersixteen-btn,.careerfy-sixteen-banner-tags a:hover,.careerfy-sixteen-parallex-btn.active,.careerfy-sixteen-priceplan:hover .careerfy-sixteen-priceplan-btn,.careerfy-sixteen-priceplan.active .careerfy-sixteen-priceplan-btn,.jobsearch-drpzon-con .jobsearch-drpzon-btn,
    /*Update*/
.jobsearch-checkbox input[type="checkbox"]:checked + label span,
.jobsearch-checkbox input[type="checkbox"] + label:hover span,
.jobsearch-checkbox input[type="radio"]:checked + label span,
.jobsearch-checkbox input[type="radio"] + label:hover span,.careerfy-jobdetail-btn,
.jobsearch-typo-wrap .ui-state-default, .jobsearch-typo-wrap .ui-widget-content .ui-state-default,.jobsearch-drpzon-con .jobsearch-drpzon-btn,.jobsearch-employerdetail-btn,
.jobsearch-typo-wrap .jobsearch-employer-profile-submit,.other-lang-translate-post,.jobsearch-typo-wrap .jobsearch-findmap-btn, .jobsearch-employer-profile-form .jobsearch-findmap-btn,
.jobsearch-employer-profile-form .upload-port-img-btn,.jobsearch-empmember-add-popup,.dash-hdtabchng-btn,.addcand-databy-emp,.jobsearch-jobdetail-btn.active,.jobsearch-employer-list-btn,
.jobsearch-company-photo .jobsearch-fileUpload span,.short-li-icons li a,.careerfy-candidatedetail-text3-btn,.candidate-detail-two-subheader-btn:hover,.careerfy-candidate-detail4-subheader-btn,
.careerfy-services-twentytwo-btn,.careerfy-services-twentytwo,.careerfy-services-twentytwo-style2-btn,.lodmore-notifics-btnsec a,.lodmore-jobs-btnsec a,.careerfy-candidate-style8-detail,.hder-notifics-count,
.hdernotifics-after-con a,.careerfy-jobs-box2 .shortlist_job_btn,.careerfy-jobdetail-btn-style5,.jobsearch-employer-followin-btn,.careerfy-candidate-cta-btn a,
.careerfy-candidate-style5-contact-form input[type="submit"],.careerfy-twentyone-search-tabs .careerfy-twentyone-loc-search input[type="text"],.careerfy-services-nineteen small,.addnew-aplyquestbtn-con button,
.jobsearch-job-like,.careerfy-job-like,.featured-jobs-grid-like {
    border-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }
    .jobsearch-read-more {
    box-shadow: 0px 0px 0px 2px <?php echo esc_html($careerfy_theme_color) ?> inset;
    }

    .jobsearch-typo-wrap button:hover, .jobsearch-typo-wrap button:focus, .jobsearch-typo-wrap input[type="button"]:hover, .jobsearch-typo-wrap input[type="button"]:focus,
    .jobsearch-typo-wrap input[type="submit"]:hover, .jobsearch-typo-wrap input[type="submit"]:focus {
    background-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    .sort-select-all label:after,.candidate-select-box label:after {
        background:<?php echo esc_html($careerfy_theme_color) ?>;
    }


    /* ThemeColor */
    .careerfy-color,.careerfy-colorhover:hover,.widget_nav_manu ul li:hover a,.widget_nav_manu ul li:hover a:before,
    .categories-list ul li i,li:hover .careerfy-blog-grid-text h2 a,li:hover .careerfy-read-more,.careerfy-filterable ul li:hover a,.careerfy-job-tag a,.careerfy-list-option ul li a,.careerfy-jobdetail-postinfo,
    .careerfy-jobdetail-options li i,.careerfy-jobdetail-btn,.careerfy-jobdetail-services i,.careerfy-list-style-icon li i,.widget_view_jobs_btn,.careerfy-employer-list small,.careerfy-employer-list-btn,
    .careerfy-employer-grid figcaption small,.careerfy-fileUpload span,.careerfy-managejobs-appli,.careerfy-transactions-tbody small,.careerfy-resumes-subtitle a,.careerfy-employer-confitmation a,
    .careerfy-candidate-default-text h2 i,.careerfy-candidate-default-btn,.careerfy-candidate-grid figure figcaption p a,.careerfy_candidate_info p a,.careerfy-candidate-download-btn,
    .careerfy-employer-dashboard-nav ul li:hover a,.careerfy-employer-dashboard-nav ul li.active a,.careerfy-candidate-savedjobs tbody td span,.careerfy-cvupload-file span,.careerfy-modal .modal-close:hover,
    .careerfy-box-title span,.careerfy-user-form-info p a,.careerfy-services-classic span i,.widget_faq ul li:hover a,.grab-classic-priceplane ul li.active i,.careerfy-classic-priceplane.active .careerfy-classic-priceplane-btn,
    .careerfy-plain-services i,.careerfy-packages-priceplane ul li.active i,.careerfy-additional-priceplane-btn,.contact-service i,.careerfy-blog-author .careerfy-authorpost span,.careerfy-prev-post .careerfy-prenxt-arrow ~ a,
    .careerfy-next-post .careerfy-prenxt-arrow ~ a,.comment-reply-link,.careerfy-banner-two-btn:hover,.careerfy-banner-search-two input[type="submit"],.careerfy-fancy-title.careerfy-fancy-title-two h2 span,.careerfy-modren-btn a,.careerfy-joblisting-plain-left ul li span,.careerfy-news-grid-text ul li a,.careerfy-partnertwo-slider .slick-arrow:hover,.careerfy-testimonial-styletwo span,.careerfy-fancy-title-three i,.careerfy-testimonial-nav li:hover i,.careerfy-fancy-title-four span i,.careerfy-featured-jobs-list-text small,.careerfy-parallax-text-btn,.careerfy-footer-four .widget_section_nav ul li a:hover,.widget_footer_contact_email,.careerfy-header-option ul li:hover a,.careerfy-range-slider form input[type="submit"],.careerfy-grid-info span,.careerfy-cart-button a,.careerfy-cart-button i,.woocommerce div.product ins span,.woocommerce-review-link,.product_meta span a,.woocommerce #reviews #comments ol.commentlist li .meta time,.careerfy-shop-list .careerfy-cart-button > span,.careerfy-archive-options li a:hover,.careerfy-continue-read,.careerfy-blog-other > li i,.detail-title h2,.careerfy-author-detail .post-by a,.careerfy-continue-reading,
    .careerfy-showing-result .careerfy-post-item:hover h5 a,.careerfy-showing-result .post-author:hover a,.careerfy-classic-services i,.careerfy-accordion .panel-heading a,
    .recent-post-text .read-more-btn,.careerfy-footer-four .widget_footer_contact .widget_footer_contact_email,.jobsearch-headeight-option > li.active a:hover,.contact-service a:hover,
    .jobsearch-user-form input[type="submit"]:hover,.woocommerce .place-order button.button:hover,.woocommerce button.button:hover,.send-contract-to-applicnt,
    .careerfy-header-six .careerfy-headfive-option li a,.careerfy-banner-six .slick-arrow:hover,.careerfy-team-parallex span,.careerfy-blog-medium-btn,.careerfy-banner-search-seven ul li:last-child:hover i,
    .careerfy-employer-slider-btn,.careerfy-employer-slider .slick-arrow,.careerfy-candidate-view4 p,.footer-register-btn:hover,.careerfy-headseven-option > li:hover > a,.careerfy-candidate-view4 li:hover h2 a,
    .careerfy-banner-search ul li.careerfy-banner-submit:hover i,.careerfy-banner-search-three li:hover input[type="submit"],
    .careerfy-banner-search-three ul li:last-child:hover i,.careerfy-banner-search-four input[type="submit"]:hover,.careerfy-banner-search-six li:hover input[type="submit"],
    .careerfy-banner-search-six li:last-child:hover i,.careerfy-header-nine .navbar-nav > li:hover > a,.careerfy-header-nine .navbar-nav > li.active > a,.careerfy-bannernine-caption a:hover,.careerfy-callaction-nine a,
    .careerfy-fancy-title-nine h2 span,.careerfy-loadmore-ninebtn a,.careerfy-fancy-title-nine small strong,.careerfy-services-nineview i,.careerfy-trending-categories i,
    .careerfy-trending-categories li:hover h2,.careerfy-browse-ninebtn a:hover,.careerfy-featuredjobs-list time strong,.careerfy-loadmore-listingsbtn a,
    .careerfy-popular-candidates-layer:hover .careerfy-popular-candidates-text h2 a,.careerfy-testimonial-slider-classic p i,li:hover .careerfy-blog-masonry-text h2 a,
    .careerfy-blog-masonry-btn,.careerfy-footernine-newslatter-inner input[type="submit"]:hover,.careerfy-backto-top:hover,.careerfy-stripuser li a:hover,.careerfy-topstrip p a:hover,
    .careerfy-header10-usersec > li > a:hover,.careerfy-search-ten form label:hover input[type="submit"],
    .careerfy-search-ten form label:hover i,.careerfy-recent-list-text ul li span,.show-morejobs-btn a,.careerfy-top-recruiters ul li span,
    .careerfy-top-recruiters-btn,.careerfy-speakers-grid-text ul li small,.quote-icon-style,.careerfy-testimonial-style10-slider .slick-arrow:hover,
    .careerfy-blog-grid-style10-text ul li i,.careerfy-blog-grid-style10-btn,.top-companies-list-text span,.top-companies-list-text-btn,
    .careerfy-build-action a:hover,.widget_abouttext_ten ul li i,.careerfy-footer-ten .widget_nav_menu ul li a:before,.careerfy-twelve-blog-grid-text ul li i,
    .careerfy-headereleven-btn:hover,.careerfy-banner-eleven-search input[type="submit"]:hover,.careerfy-fileupload-banner span i,.careerfy-explore-jobs-links ul li.morejobs-link a,
    .careerfy-popular-candidates-style11-social a:hover,.copyright-eleven-social li:hover a,.copyright-eleven p a,.careerfy-fifteen-banner-search input[type="submit"]:hover,.careerfy-fifteen-categories i,
    .careerfy-fifteen-recent-jobs time i,.careerfy-fifteen-recent-jobs small i,.careerfy-fifteen-browse-links ul li:hover a,.careerfy-fifteen-blog-medium-text span i,.careerfy-footer-fifteen .widget_nav_menu ul li:hover a,
    .copyright-fifteen-social li:hover a,.copyright-fifteen p a,.jobsearch-send-email-popup-btn,.widget_apply_job span,.careerfy-breadcrumb-style7 ul li,.jobsearch-box-application-countdown .countdown-amount,
    .careerfy-jobdetail-content-list ul li small,.careerfy-jobdetail-content-section strong small,.jobsearch_box_jobdetail_three_apply_wrap_view i,.careerfy-jobdetail-four-options li small,
    .careerfy-twelve-navigation .navbar-nav > li > a,.careerfy-fancy-title-twelve h2,.careerfy-section-title-btn:hover,
    .careerfy-top-sectors-category i,.careerfy-candidates-style11 figcaption span,.careerfy-autojobs-mobile-text h2,.careerfy-refejobs-list small i,
    .careerfy-autojobs-mobile-btn,.careerfy-twelve-blog-grid-text ul li i,.careerfy-twelve-blog-grid-btn,.careerfy-partner-twelve-slider .slick-arrow:hover,.copyright-twelve-social li:hover a,.copyright-twelve p a,.careerfy-headerthirteen-user > li:hover > a,.careerfy-headerthirteen-btn,.careerfy-thirteen-banner-search ul li i,.careerfy-thirteen-banner-search input[type="submit"]:hover,.careerfy-thirteen-banner-caption span small,.careerfy-browsejobs-links ul li:hover a,.careerfy-thirteen-browse-alljobs-btn a:hover,.copyright-thirteen-social li:hover a,.copyright-thirteen p a,.careerfy-headersixteen-user > li > a:hover,.copyright-sixteen-social li:hover a,.careerfy-header-seventeen-strip p i,.careerfy-headerseventeen-user > li:hover > a,.careerfy-seventeen-search input[type="submit"]:hover,.careerfy-seventeen-services i,.careerfy-refejobs-loadmore-btn a:hover,.careerfy-services-eighteen i,.careerfy-services-eighteen a,.careerfy-refejobs-list-two small i,.careerfy-eighteen-blog-grid-text span small,.careerfy-eighteen-parallex-text h2 span,.careerfy-eighteen-newslatter input[type="submit"]:hover,.careerfy-headereighteen-user > li:hover > a,
    .careerfy-refejobs-list small i,.careerfy-explorejobs-links ul li:hover a,.careerfy-explorejobs-link-btn,
.careerfy-howit-works-text small,.careerfy-howit-works-list figure figcaption span i,.careerfy-howit-works-list .careerfy-spam-list figure figcaption em,.careerfy-popular-candidates-style14-inner span,.careerfy-popular-candidates-style14-social li:hover a,.careerfy-popular-candidates-style14-slider .slick-arrow:hover,.careerfy-blog-style14-bottom ul li i,.careerfy-blog-style14-tag:hover,.careerfy-testimonial-style14-inner span,.careerfy-footer-fourteen .widget_nav_menu ul li:hover a,.copyright-fourteen p a,.careerfy-detail-editore p a,.jobsearch-open-signup-tab.active a,.careerfy-header-nineteen-strip p i,.careerfy-nineteen-loc-search input[type="submit"]:hover,
.careerfy-nineteen-category-list li:hover i,.careerfy-services-nineteen span,.careerfy-services-nineteen-style2 span,.careerfy-services-nineteen-style2 small i,.careerfy-services-nineteen-style3 small,
.careerfy-services-nineteen-style3 strong,.careerfy-nineteen-loc-search-style2 input[type="submit"]:hover,.careerfy-twenty-search h1 strong,.careerfy-twenty-loc-search i,.careerfy-services-twenty .top-icon,
.careerfy-twenty-testimonial h2,.careerfy-twenty-testimonial-wrapper:before,.widget_text_ten li span,.careerfy-footer-twenty .widget_nav_menu ul li a:before,.careerfy-search-twentyone-tabs-nav li i,
.careerfy-twentyone-loc-search input[type="submit"]:hover,.careerfy-services-twentyone-style3 li:hover i,.careerfy-header-twentytwo-wrapper .top-strip-social-links p i,.careerfy-header-twentytwo-user li a i,
.careerfy-twentytwo-loc-search .careerfy-pin,.instacands-btns-con .more-fields-act-btn a:hover,.careerfy-footer-twentyone .widget_nav_menu ul li a:before,.careerfy-services-nineteen li:hover small,
.careerfy-jobdetail-style5-save,.imag-resoultion-msg {
    color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    .careerfy-bgcolor,.careerfy-bgcolorhover:hover,.careerfy-banner-search ul li.careerfy-banner-submit i,.careerfy-plain-btn a,.careerfy-testimonial-slider .slick-arrow:hover,
    .careerfy-featured-label,.careerfy-job-like:hover,.careerfy-pagination-blog ul li:hover a, .careerfy-pagination-blog ul li:hover span,.careerfy-jobdetail-view,.careerfy-jobdetail-tags a:hover,.careerfy-employer-list-btn:hover,
    .careerfy-employer-grid-btn:hover,.widget_contact_form input[type="submit"],.careerfy-fileUpload:hover span,.careerfy-resumes-options li:hover a,.careerfy-employer-jobnav ul li:hover i,.careerfy-employer-jobnav ul li.active i,
    .careerfy-employer-jobnav ul li.active ~ li:nth-child(2):after,.careerfy-employer-jobnav ul li.active:nth-child(2):after,.careerfy-employer-jobnav ul li.active:nth-child(3):after,.careerfy-employer-confitmation a:hover,
    .careerfy-candidate-default-btn:hover,.careerfy-candidate-download-btn:hover,.careerfy-add-popup input[type="submit"],.careerfy-user-form input[type="submit"],.careerfy-classic-services ul li:hover i,
    .careerfy-service-slider .slick-arrow:hover,.careerfy-classic-priceplane-btn,.careerfy-classic-priceplane.active,.active .careerfy-simple-priceplane-basic a,.careerfy-packages-priceplane-btn,
    .careerfy-additional-priceplane.active h2,.careerfy-additional-priceplane.active .careerfy-additional-priceplane-btn,.careerfy-contact-info-sec,.careerfy-contact-form input[type="submit"],.contact-service a,
    .careerfy-tags a:hover,.widget_search input[type="submit"],.careerfy-banner-two-btn,.careerfy-banner-search-two,.careerfy-post-btn:hover,.careerfy-btn-icon,.careerfy-modren-service-link,.careerfy-modren-btn a:hover,.slick-dots li.slick-active button,.careerfy-footer-newslatter input[type="submit"],.careerfy-pagination-blog ul li.active a,.careerfy-banner-search-three input[type="submit"],.careerfy-fancy-left-title a:hover,.featured-jobs-grid-like:hover,.careerfy-services-stylethree ul li:hover span,.careerfy-priceplan-style5:hover .careerfy-priceplan-style5-btn,.active .careerfy-priceplan-style5-btn,.careerfy-banner-search-four input[type="submit"],.careerfy-parallax-text-btn:hover,.careerfy-header-option > li > a:hover,.careerfy-header-option > li.active > a,.careerfy-shop-grid figure > a:before,.careerfy-shop-grid figure > a:after,.careerfy-cart-button a:before,.careerfy-cart-button a:after,.woocommerce a.button,.woocommerce input.button,.careerfy-post-tags a:hover,.author-social-links ul li a:hover,.careerfy-static-btn,.careerfy-modren-counter ul li:after,
    .careerfy-services-classic li:hover span i,.widget_tag_cloud a:hover,.mc-input-fields input[type="submit"],.comment-respond p input[type="submit"],.jobsearch-pagination-blog ul li span.current,.careerfy-shop-label,
    .woocommerce .place-order button.button,.gform_page_footer .button,.gform_footer .gform_button.button,.careerfy-header-six .careerfy-headfive-option > li.active > a,.careerfy-banner-six-caption a,.careerfy-banner-search-six input[type="submit"],.careerfy-animate-filter ul li a.is-checked,.careerfy-services-fourtext h2:before,.careerfy-dream-packages.active .careerfy-dream-packagesplan,.careerfy-banner-search-seven ul li:last-child i,
    .careerfy-headsix-option > li:hover > a,.careerfy-headsix-option > li.active > a,.careerfy-candidate-view4-social li:hover a,.footer-register-btn,.careerfy-headseven-option > li > a,.careerfy-headernine-btn:hover,.careerfy-header-nine .navbar-nav > li > a:before,.careerfy-bannernine-caption a,.careerfy-callaction-nine a:hover,
    .careerfy-services-video .slick-arrow:hover,.careerfy-loadmore-ninebtn a:hover,.careerfy-categories-classic-slider .slick-arrow:hover,.careerfy-fancy-title-nine small:before,
    .careerfy-services-nineview li:hover i,.careerfy-trending-categories span,.careerfy-loadmore-listingsbtn a:hover,.careerfy-popular-candidates .slick-arrow:hover,
    .careerfy-testimonial-slider-classic .slick-arrow:hover,.careerfy-counter-nineview span:before,.careerfy-blog-masonry-tag a:hover,
    .careerfy-blog-masonry-like:hover,.careerfy-blog-masonry-btn:hover,.careerfy-testimonial-slider-classic-pera:before,.careerfy-footernine-newslatter-inner input[type="submit"],
    .careerfy-footer-nine-social li a:after,.careerfy-backto-top,.careerfy-testimonial-slider-classic-layer:hover p,.careerfy-search-ten form label,.show-morejobs-btn a:hover,.careerfy-top-recruiters-btn:hover,
    .careerfy-counter-style10-transparent,li:hover .careerfy-speakers-grid-wrap:before,li:hover .careerfy-blog-grid-style10-btn,li:hover .top-companies-list-text-btn,.careerfy-getapp-btn,
    li:hover .careerfy-twelve-blog-grid-btn,.careerfy-header-eleven .navbar-nav > li > a:before,.careerfy-headereleven-btn,.careerfy-banner-eleven-tabs-nav li a:before,
    .careerfy-banner-eleven-search input[type="submit"],li:hover .careerfy-services-style11-btn,.careerfy-recentjobs-list-btn:hover,
    .careerfy-recruiters-top-list-bottom a:hover,li:hover .careerfy-blog-grid-style11-btn,.careerfy-counter-elevenview h2:before,.careerfy-action-style11 a,.careerfy-footer-eleven .footer-widget-title:before,
    .careerfy-header-fifteen .navbar-nav > li > a:before,.careerfy-headerfifteen-btn:hover,.careerfy-fifteen-banner-search input[type="submit"],.careerfy-fancy-title-fifteen:before,.careerfy-fifteen-browse-btn a:hover,.careerfy-fifteen-parallex a:hover,.careerfy-fifteen-packages-plan-btn,.careerfy-fifteen-packages-plan:hover,.careerfy-fifteen-packages-plan.active,.careerfy-fifteen-parallex-style2-btn,.careerfy-footer-fifteen .footer-widget-title h2:before,
    .line-scale-pulse-out > div,.jobsearch-empmember-add-popup,.careerfy-jobdetail-content-list ul li a.job-view-map,
    .careerfy-banner-twelve-search input[type="submit"]:hover,.careerfy-browse-links-btn a:hover,
    .careerfy-section-title-btn,.careerfy-top-employers-slider .slick-arrow:hover,.careerfy-premium-jobs-slider .slick-arrow:hover,
    .careerfy-top-sectors-category small,.careerfy-top-sectors-category-slider .slick-arrow:hover,.careerfy-candidates-style11-top span:hover,
    .careerfy-candidates-style11-slider .slick-arrow:hover,.careerfy-priceplan-twelve:hover .careerfy-priceplan-twelve-btn a,
    .careerfy-priceplan-twelve:hover .careerfy-priceplan-twelve-top,.careerfy-priceplan-twelve:hover .careerfy-priceplan-twelve-top a,
    .careerfy-priceplan-twelve.active .careerfy-priceplan-twelve-btn a,li:hover .careerfy-twelve-blog-grid-btn,
    .careerfy-priceplan-twelve.active .careerfy-priceplan-twelve-top,.careerfy-priceplan-twelve.active .careerfy-priceplan-twelve-top a,.careerfy-headerthirteen-user > li > a:before,
    .careerfy-headerthirteen-btn:hover,.careerfy-thirteen-banner-search input[type="submit"],.careerfy-thirteen-banner-btn a:hover,
    .careerfy-fancy-title-thirteen:before,.careerfy-thirteen-browse-alljobs-btn a,.careerfy-priceplan-thirteen:hover .careerfy-priceplan-thirteen-btn a,
    .careerfy-priceplan-thirteen:hover .careerfy-priceplan-thirteen-top,.careerfy-priceplan-thirteen.active .careerfy-priceplan-thirteen-btn a,
    .careerfy-priceplan-thirteen.active .careerfy-priceplan-thirteen-top,.careerfy-footer-thirteen .footer-widget-title h2:before,.careerfy-header-seventeen-social li:hover a,.careerfy-headerseventeen-user > li > a,
    .careerfy-headerseventeen-btn:hover,.careerfy-seventeen-banner-btn:hover,.careerfy-seventeen-search input[type="submit"],.careerfy-fancy-title-seventeen small.active,.careerfy-seventeen-services li:hover i,.careerfy-refejobs-loadmore-btn a,li:hover .careerfy-refejobs-list-btn span,.careerfy-headereighteen-btn,.careerfy-eighteen-banner form input[type="submit"],
.careerfy-eighteen-search-radio .form-radio:checked:before,.careerfy-header-eighteen .navbar-nav > li > a:before,
.careerfy-services-eighteen [class*="col-md-"]:hover a,.careerfy-refejobs-list-two li:hover .careerfy-refejobs-list-btn span,
.careerfy-eighteen-blog-grid figure a:before,.careerfy-eighteen-newslatter input[type="submit"],.careerfy-header-eighteen .careerfy-headerfifteen-btn,.careerfy-headerfourteen-btn:hover,.careerfy-fourteen-caption form input[type="submit"]:hover,.careerfy-jobs-btn-links a:hover,li:hover .careerfy-refejobs-list-btn span,.careerfy-fancy-title-fourteen:before,.careerfy-popular-candidates-style14 strong,.careerfy-blog-style14-like:hover,.careerfy-testimonial-style14-btn,
.widget_about_text_fourteen_btn,.careerfy-footer-fourteen .footer-widget-title h2:before,.jobsearch-open-signup-tab.active a:hover,.careerfy-headerninteen-user > li > a,.careerfy-search-nineteen-tabs-nav li.active i,
.careerfy-nineteen-loc-search input[type="submit"],.careerfy-services-nineteen small,.careerfy-nineteen-loc-search-style2 input[type="submit"],.careerfy-twenty-search-tabs .tab-content,
.careerfy-search-twenty-tabs-nav li.active a,.careerfy-services-twenty strong,.careerfy-services-twenty-style2-counter,.careerfy-accordion-style2 .panel-heading a,
.careerfy-services-twenty-img:before,.careerfy-services-twenty-img:after,.careerfy-footer-title-style18 h2:before,.careerfy-header-twentyone-wrapper .navbar-nav > li > a:after,
.careerfy-header-twentyone-social li:hover a,.careerfy-twentyone-loc-search input[type="submit"],.careerfy-search-twentyone-tabs-nav li.active a:before,.careerfy-search-twentyone-tabs-nav li.active a i,
.careerfy-services-twentyone-style3 i,.careerfy-header-twentytwo-strip,.careerfy-search-twentytwo-tabs-nav li i,.careerfy-twentytwo-loc-search i.careerfy-search-o,.instacands-btns-con .more-fields-act-btn a,
.candskills-list li span.insta-match-skill,.careerfy-footer-twentyone .careerfy-footer-widget a.social-icon-footer-twenty:hover {
    background-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    .careerfy-bordercolor,.careerfy-bordercolorhover:hover,.careerfy-jobdetail-btn,.careerfy-employer-list-btn,.careerfy-fileUpload span,.careerfy-employer-confitmation a,.careerfy-candidate-default-btn,
    .careerfy-candidate-download-btn,.careerfy-cvupload-file span,.active .careerfy-simple-priceplane-basic a,.careerfy-additional-priceplane-btn,blockquote,.careerfy-banner-two-btn,.careerfy-post-btn,.careerfy-parallax-text-btn,
    .careerfy-cart-button a,.careerfy-classic-services i,.jobsearch-headeight-option > li.active > a,.contact-service a,.jobsearch-user-form input[type="submit"],.woocommerce .place-order button.button,.woocommerce button.button,
    .careerfy-header-six,.careerfy-banner-six .slick-arrow:hover,.careerfy-banner-search-seven ul li:last-child i,.careerfy-headsix-option li a,.footer-register-btn,.careerfy-headseven-option > li > a,
    .careerfy-banner-search-four input[type="submit"],.careerfy-banner-search-six li input[type="submit"],.careerfy-banner-search ul li.careerfy-banner-submit i,.careerfy-banner-search-three input[type="submit"],
    .careerfy-bannernine-caption a,.careerfy-loadmore-ninebtn a,.careerfy-loadmore-listingsbtn a,.careerfy-blog-masonry-btn,.careerfy-search-ten form label,.show-morejobs-btn a,.careerfy-top-recruiters-btn,.careerfy-blog-grid-style10-btn,.top-companies-list-text-btn,.careerfy-headereleven-btn,.careerfy-banner-eleven-search input[type="submit"],.careerfy-services-style11-btn,.careerfy-jobdetail-btn.active,
    .careerfy-recentjobs-list-btn,.careerfy-recruiters-top-list-bottom a,.careerfy-blog-grid-style11-btn,.careerfy-action-style11 a,.careerfy-footernine-newslatter-inner input[type="submit"],
    .careerfy-headerfifteen-btn,.careerfy-banner-eleven-tabs-nav li a,.careerfy-fifteen-banner-search input[type="submit"],.careerfy-fifteen-browse-btn a,.jobsearch-empmember-add-popup,
    .careerfy-section-title-btn,.careerfy-top-employers-slider .slick-arrow:hover,.careerfy-twelve-blog-grid-btn,.careerfy-headerthirteen-btn,.careerfy-thirteen-banner-search input[type="submit"],.careerfy-thirteen-banner-btn a:hover,
    .careerfy-thirteen-browse-alljobs-btn a,.careerfy-headerseventeen-user > li > a,.careerfy-headerseventeen-btn:hover,.careerfy-seventeen-search input[type="submit"],
    .careerfy-refejobs-loadmore-btn a,.careerfy-headereighteen-btn,.careerfy-services-eighteen a,.careerfy-refejobs-list-two .careerfy-refejobs-list-btn span,.careerfy-eighteen-newslatter input[type="submit"],
    .careerfy-headerfourteen-btn,.careerfy-refejobs-list-btn span,.jobsearch-open-signup-tab.active a,.careerfy-nineteen-category-list li:hover i,.careerfy-twentyone-loc-search input[type="submit"],
    .instacands-btns-con .more-fields-act-btn a,.careerfy-twentyone-search .selectize-control,.careerfy-jobdetail-style5-save {
    border-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }
    .careerfy-read-more {
    box-shadow: 0px 0px 0px 2px <?php echo esc_html($careerfy_theme_color) ?> inset;
    }
    .careerfy-partner-slider a:hover {
        box-shadow: 0px 0px 0px 3px <?php echo esc_html($careerfy_theme_color) ?> inset;
    }
    .careerfy-seventeen-services i {
        box-shadow: inset 0px 0px 0px 2px <?php echo esc_html($careerfy_theme_color) ?>, 0 0px 15px rgba(0,0,0,0.15);
    }

    .careerfy-services-twenty-style3 i,.careerfy-services-twenty-style3 a:before,.careerfy-services-twentyone-style3 i {
        border-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    .careerfy-testimonial-slider-classic-layer:hover p:after,.careerfy-testimonial-slider-classic-layer.active p:after {
    border-top-color: <?php echo esc_html($careerfy_theme_color) ?>;
    }

    <?php
    // header colors
    $header_bg_color = isset($careerfy__options['header-bg-color']['rgba']) && $careerfy__options['header-bg-color']['rgba'] != '' ? $careerfy__options['header-bg-color']['rgba'] : '';
    $menu_link_color = isset($careerfy__options['menu-link-color']) && $careerfy__options['menu-link-color'] != '' ? $careerfy__options['menu-link-color'] : '';

    // header Button section colors
    $header_btn_bg_colors = isset($careerfy__options['header-btn-bg-color']['color']) && $careerfy__options['header-btn-bg-color']['color'] != '' ? $careerfy__options['header-btn-bg-color']['color'] : '';
    $header_btn_txt_colors = isset($careerfy__options['header-btn-text-color']['color']) && $careerfy__options['header-btn-text-color']['color'] != '' ? $careerfy__options['header-btn-text-color']['color'] : '';
    $header_link_colors = isset($careerfy__options['header-btn-link-color']['color']) && $careerfy__options['header-btn-link-color']['color'] != '' ? $careerfy__options['header-btn-link-color']['color'] : '';


    $submenu_bg_color = isset($careerfy__options['submenu-bg-color']) && $careerfy__options['submenu-bg-color'] != '' ? $careerfy__options['submenu-bg-color'] : '';
    $submenu_border_color = isset($careerfy__options['submenu-border-color']) && $careerfy__options['submenu-border-color'] != '' ? $careerfy__options['submenu-border-color'] : '';
    $submenu_link_color = isset($careerfy__options['submenu-link-color']) && $careerfy__options['submenu-link-color'] != '' ? $careerfy__options['submenu-link-color'] : '';
    $submenu_link_bg_color = isset($careerfy__options['submenu-link-bg-color']) && $careerfy__options['submenu-link-bg-color'] != '' ? $careerfy__options['submenu-link-bg-color'] : '';

    // Body background Color
    $body_background_color = isset($careerfy__options['careerfy-body-color']) && $careerfy__options['careerfy-body-color'] != '' ? $careerfy__options['careerfy-body-color'] : '';

    // footer colors

    $footer_bg_color = isset($careerfy__options['footer-bg-color']) && $careerfy__options['footer-bg-color'] != '' ? $careerfy__options['footer-bg-color'] : '';
    $footer_text_color = isset($careerfy__options['footer-text-color']) && $careerfy__options['footer-text-color'] != '' ? $careerfy__options['footer-text-color'] : '';
    $footer_link_color = isset($careerfy__options['footer-link-color']) && $careerfy__options['footer-link-color'] != '' ? $careerfy__options['footer-link-color'] : '';
    $footer_border_color = isset($careerfy__options['footer-border-color']) && $careerfy__options['footer-border-color'] != '' ? $careerfy__options['footer-border-color'] : '';
    $footer_copyright_bgcolor = isset($careerfy__options['footer-copyright-bgcolor']) && $careerfy__options['footer-copyright-bgcolor'] != '' ? $careerfy__options['footer-copyright-bgcolor'] : '';
    $footer_copyright_color = isset($careerfy__options['footer-copyright-color']) && $careerfy__options['footer-copyright-color'] != '' ? $careerfy__options['footer-copyright-color'] : '';
    $footer_background = isset($careerfy__options['footer-background']['url']) ? $careerfy__options['footer-background']['url'] : '';

    // megamenu colors
    $megamenu_text_color = isset($careerfy__options['megamenu-text-color']) && $careerfy__options['megamenu-text-color'] != '' ? $careerfy__options['megamenu-text-color'] : '';
    $megamenu_bg_color = isset($careerfy__options['megamenu-bg-color']) && $careerfy__options['megamenu-bg-color'] != '' ? $careerfy__options['megamenu-bg-color'] : '';
    $megamenu_border_color = isset($careerfy__options['megamenu-border-color']) && $careerfy__options['megamenu-border-color'] != '' ? $careerfy__options['megamenu-border-color'] : '';
    $megamenu_sublink_color = isset($careerfy__options['megamenu-sublink-color']) && $careerfy__options['megamenu-sublink-color'] != '' ? $careerfy__options['megamenu-sublink-color'] : '';
    // sticy header colors
    $sticky_bg_color = isset($careerfy__options['sticky-bg-color']['rgba']) && $careerfy__options['sticky-bg-color']['rgba'] != '' ? $careerfy__options['sticky-bg-color']['rgba'] : '';
    $sticky_bg_colorr = isset($careerfy__options['sticky-bg-color']) && $careerfy__options['sticky-bg-color'] != '' ? $careerfy__options['sticky-bg-color'] : '';
    $sticky_menu_link_color = isset($careerfy__options['sticky-menu-link-color']) && $careerfy__options['sticky-menu-link-color'] != '' ? $careerfy__options['sticky-menu-link-color'] : '';
    $top_menu_link_color = isset($careerfy__options['top-menu-link-color']) && $careerfy__options['top-menu-link-color'] != '' ? $careerfy__options['top-menu-link-color'] : '';

    if (!empty($careerfy__options)) {

        /*
         *  Header Buttons Background color
         */
        if (isset($header_btn_bg_colors) && $header_btn_bg_colors != '') {
            ?>
            .careerfy-headseven-option > li > a,.careerfy-simple-btn,.careerfy-post-btn:hover,.careerfy-btn-icon,.careerfy-user-log li a.active,
            .careerfy-header-option > li > a:hover,.careerfy-header-option > li.active > a,.careerfy-header-six .careerfy-headfive-option > li.active > a,
            .careerfy-headsix-option > li:hover > a,.careerfy-headsix-option > li.active > a,.careerfy-headseven-option > li > a,.careerfy-headerten-btn,.careerfy-headerfifteen-btn:hover,
            .careerfy-header-twenty-user > li > a,.careerfy-header-two .careerfy-user-option > li.jobsearch-usernotifics-menubtn > a,.jobsearch-usernotifics-menubtn a span,.careerfy-header-four .careerfy-header-option > li.jobsearch-usernotifics-menubtn > a,.careerfy-header-twelve .careerfy-user-section > li.jobsearch-usernotifics-menubtn > a,.careerfy-headertwelve-btn {
            background-color: <?php echo esc_html($header_btn_bg_colors) ?> !important; }
            <?php
        }
        if (isset($header_btn_bg_colors) && $header_btn_bg_colors != '') {
            ?>
            .careerfy-post-btn,.careerfy-header-option > li > a,.careerfy-headsix-option > li > a,.careerfy-headseven-option > li > a,.careerfy-headerten-btn,.careerfy-headerfifteen-btn,
            .careerfy-header-twenty-user > li > a {
            border-color: <?php echo esc_html($header_btn_bg_colors) ?> !important; }
            <?php
        }
        /*
         *  Header Buttons Text color
         */
        if (isset($header_btn_txt_colors) && $header_btn_txt_colors != '') {
            ?>
            .careerfy-headseven-option > li > a,.careerfy-simple-btn,.careerfy-header-two .careerfy-user-option li a.careerfy-post-btn,.careerfy-btn-icon,
            .careerfy-header-two .careerfy-user-option > li > a,.careerfy-user-log li a.active,.careerfy-header-option > li > a,.careerfy-header-six .careerfy-headfive-option > li.active > a,
            .careerfy-headsix-option > li > a,.careerfy-headseven-option > li > a,.careerfy-headerten-btn,.careerfy-headerfifteen-user li a,.careerfy-headerfifteen-btn,.careerfy-header-twenty-user > li:hover > a,
            .jobsearch-usernotifics-menubtn a span {
            color: <?php echo esc_html($header_btn_txt_colors) ?> !important; }

            <?php
        }

        if (isset($header_btn_txt_colors) && $header_btn_txt_colors != '') {
            ?>
            .careerfy-headerten-btn:hover {
            color: <?php echo esc_html($header_btn_bg_colors) ?> !important; }
            <?php
        }

        /*
         *  Header LINK Text color
         */
        if (isset($header_link_colors) && $header_link_colors != '') {
            ?>
            .careerfy-user-section > li > a,.careerfy-user-log > li > a,.careerfy-header-six .careerfy-headfive-option > li > a {
            color: <?php echo esc_html($header_link_colors) ?> !important; }
            <?php
        }

        /*
         * Sicky Header color
         */
        if (isset($sticky_bg_color) && $sticky_bg_color != '') {
            ?>
            .careerfy-sticky-header .careerfy-header-one, .careerfy-sticky-header .careerfy-header-three, .careerfy-sticky-header .careerfy-header-two,
            .careerfy-sticky-header .careerfy-header-six,.careerfy-sticky-header .careerfy-header-seven,.careerfy-sticky-header .careerfy-header-eight,
            .careerfy-sticky-header .careerfy-headerten-mainnav,
            .careerfy-sticky-header .careerfy-header-eleven
            {background-color: <?php echo esc_html($sticky_bg_color) ?>;}
            <?php
        }
        if (isset($sticky_menu_link_color) && $sticky_menu_link_color != '') {
            ?>
            .careerfy-sticky-header .navbar-nav > li > a{color: <?php echo esc_html($sticky_menu_link_color['regular']); ?> ! important;}
            <?php
        }
        if (isset($sticky_menu_link_color) && $sticky_menu_link_color != '') {
            ?>
            .careerfy-sticky-header .navbar-nav > li > a:active,.careerfy-sticky-header .navbar-nav > li.current-menu-item > a,
            .careerfy-sticky-header .navbar-nav > li.current_page_parent > a {color: <?php echo esc_html($sticky_menu_link_color['active']); ?> ! important;}
            <?php
        }
        if (isset($sticky_menu_link_color) && $sticky_menu_link_color != '') {
            ?>
            .careerfy-sticky-header .navbar-nav > li > a:visited{color: <?php echo esc_html($sticky_menu_link_color['visited']); ?> ! important;}
            <?php
        }
        if (isset($sticky_menu_link_color) && $sticky_menu_link_color != '') {
            ?>
            .careerfy-sticky-header .navbar-nav > li > a:hover{color: <?php echo esc_html($sticky_menu_link_color['hover']); ?> ! important;}
            <?php
        }
    }

    /*
     * Top header Menu Color on for header style 12
     * */
    if (isset($top_menu_link_color) && $top_menu_link_color != '') { ?>
        .careerfy-headertwelve-user > li > a { color: <?php echo esc_html($top_menu_link_color['regular']); ?> ! important; }
    <?php }

    if (isset($top_menu_link_color) && $top_menu_link_color != '') { ?>
        .careerfy-headertwelve-user > li.current_page_item > a {color: <?php echo esc_html($top_menu_link_color['active']); ?> ! important;}
        <?php
    }
    if (isset($top_menu_link_color) && $top_menu_link_color != '') { ?>
        .careerfy-headertwelve-user > li > a:visited {color: <?php echo esc_html($top_menu_link_color['visited']); ?> ! important;}
        <?php
    }
    if (isset($top_menu_link_color) && $top_menu_link_color != '') { ?>
        .careerfy-headertwelve-user > li > a:hover {color: <?php echo esc_html($top_menu_link_color['hover']); ?> ! important;}
        <?php
    }
    if (!empty($careerfy__options)) {
        /*
         * Body Background color
         */

        if (isset($body_background_color) && $body_background_color != '') {
            ?>
            body{background-color: <?php echo esc_html($body_background_color) ?>;}
            <?php
        }
    }

    if (!empty($careerfy__options)) {

        /*
         * megamenu Paragraph colors
         */

        if (isset($megamenu_text_color) && $megamenu_text_color != '') {
            ?>
            .careerfy-mega-text p{color: <?php echo esc_html($megamenu_text_color) ?>;}
            <?php
        }

        /*
         * megamenu background colors
         */
        if (isset($megamenu_bg_color) && $megamenu_bg_color != '') {
            ?>
            .navbar-nav > li.current-menu-item > a, .navbar-nav > li.current_page_item  > a,.navbar-nav > li.active > a  {color: <?php echo esc_html($megamenu_bg_color) ?>;}
            .careerfy-megamenu {background-color: <?php echo esc_html($megamenu_bg_color) ?>;}


            <?php
        }

        /*
         * megamenu border colors
         */
        if (isset($megamenu_border_color) && $megamenu_border_color != '') {
            ?>
            .careerfy-megalist li {border-color: <?php echo esc_html($megamenu_border_color) ?>;}

            <?php
        }

        /*
         * megamenu SubLink colors
         */

        if (isset($megamenu_sublink_color['regular']) && $megamenu_sublink_color['regular'] != '') {
            ?>
            .careerfy-megalist li a  {color: <?php echo esc_html($megamenu_sublink_color['regular']) ?>;}
            <?php
        }
        if (isset($megamenu_sublink_color['hover']) && $megamenu_sublink_color['hover'] != '') {
            ?>
            .careerfy-megalist li:hover a {color: <?php echo esc_html($megamenu_sublink_color['hover']) ?>;}
            .careerfy-header-nineteen .navbar-nav > li:hover > a:after { background-color: <?php echo esc_html($megamenu_sublink_color['hover']) ?>; }
            <?php
        }
        if (isset($megamenu_sublink_color['visited']) && $megamenu_sublink_color['visited'] != '') {
            ?>
            .careerfy-megalist li:hover a:visited {color: <?php echo esc_html($megamenu_sublink_color['visited']) ?>;}
            <?php
        }
        if (isset($megamenu_sublink_color['active']) && $megamenu_sublink_color['active'] != '') {
            ?>
            .careerfy-megalist > li.current-menu-item > a, .careerfy-megalist > li.current_page_item  > a,.careerfy-megalist > li.active > a{color: <?php echo esc_html($megamenu_sublink_color['active']) ?>;}
            <?php
        }
    }


    if (!empty($careerfy__options)) {

        if ($header_bg_color != '') { ?>
            .careerfy-header-one, .careerfy-main-header, .careerfy-main-header .careerfy-bgcolor-three, .careerfy-main-strip:before, .careerfy-header-three, .careerfy-header-six, .careerfy-header-seven,
            .careerfy-header-two,.careerfy-header-four,.careerfy-header-eight,.careerfy-header-eleven,.careerfy-headerten-mainnav,.careerfy-header-sixteen,.careerfy-header-twenty-wrapper {
                background-color: <?php echo esc_html($header_bg_color) ?>;}
            <?php
        }
        if (isset($menu_link_color['regular']) && $menu_link_color['regular'] != '') {
            ?>
            .navbar-nav > li > a,.navbar-default .navbar-nav > li > a,.careerfy-headereleven-user > li > a,.careerfy-header-fifteen .navbar-nav > li.submenu-addicon:after,
            .careerfy-header-sixteen .navbar-nav > li.submenu-addicon > a:after  {color: <?php echo esc_html($menu_link_color['regular']) ?>;}
            <?php
        }
        if (isset($menu_link_color['hover']) && $menu_link_color['hover'] != '') {
            ?>
            .navbar-nav > li:hover > a,.navbar-nav > li.active > a {color: <?php echo esc_html($menu_link_color['hover']) ?>;}
            .navbar-nav > li:hover > a:before { background-color: <?php echo esc_html($menu_link_color['hover']) ?>; }
            <?php
        }
        if (isset($menu_link_color['visited']) && $menu_link_color['visited'] != '') {
            ?>
            .navbar-nav > li > a:visited {color: <?php echo esc_html($menu_link_color['visited']) ?>;}
            <?php
        }
        if (isset($menu_link_color['active']) && $menu_link_color['active'] != '') {
            ?>
            .navbar-nav > li.current-menu-item > a, .navbar-nav > li.current_page_item  > a,.navbar-nav > li.active > a {color: <?php echo esc_html($menu_link_color['active']) ?>;}
            .careerfy-header-nineteen .navbar-nav > li.current_page_item > a:after { background-color: <?php echo esc_html($menu_link_color['active']) ?>; }

            <?php
        }
        if ($submenu_bg_color != '') {
            ?>
            .navbar-nav .sub-menu, .navbar-nav .children {background-color: <?php echo esc_html($submenu_bg_color) ?>;}
            <?php
        }
        if ($submenu_border_color != '') {
            ?>
            .navbar-nav .sub-menu li a, .navbar-nav .children li a {border-bottom-color: <?php echo esc_html($submenu_border_color) ?>;}
            <?php
        }
        if (isset($submenu_link_color['regular']) && $submenu_link_color['regular'] != '') {
            ?>
            .navbar-nav .sub-menu li a, .navbar-nav .children li a {color: <?php echo esc_html($submenu_link_color['regular']) ?>;}
            <?php
        }
        if (isset($submenu_link_color['hover']) && $submenu_link_color['hover'] != '') {
            ?>
            .navbar-nav .sub-menu > li:hover > a, .navbar-nav .children > li:hover > a {color: <?php echo esc_html($submenu_link_color['hover']) ?>;}
            <?php
        }
        if (isset($submenu_link_color['visited']) && $submenu_link_color['visited'] != '') {
            ?>
            .navbar-nav .sub-menu li a:visited, .navbar-nav .children li a:visited {color: <?php echo esc_html($submenu_link_color['hover']) ?>;}
            <?php
        }
        if (isset($submenu_link_color['active']) && $submenu_link_color['active'] != '') { ?>
            .navbar-nav .sub-menu > li.current-menu-item > a, .navbar-nav .children li.current-menu-item a, .careerfy-megalist li.current-menu-item a {color: <?php echo esc_html($submenu_link_color['active']) ?>;}
            <?php
        }
        if (isset($submenu_link_bg_color['hover']) && $submenu_link_bg_color['hover'] != '') {
            ?>
            .navbar-nav .sub-menu li:hover, .navbar-nav .children li:hover {background-color: <?php echo esc_html($submenu_link_bg_color['hover']) ?>;}
            <?php
        }
        if (isset($submenu_link_bg_color['active']) && $submenu_link_bg_color['active'] != '') {
            ?>
            .navbar-nav .sub-menu li.current-menu-item, .navbar-nav .children li.current-menu-item {background-color: <?php echo esc_html($submenu_link_bg_color['active']) ?>;}
            <?php
        }
        ///////// Footer background image will effect only on style 10 ////////////
        if ($footer_background != '') { ?>
            .careerfy-footer-eleven {
            background:  url('<?php echo $footer_background ?>');
            }
        <?php }
        if ($footer_bg_color != '') {
            ?>
            .careerfy-footer-one,.careerfy-footer-four,.careerfy-footer-three,.careerfy-footer-two,.careerfy-footer-five,.careerfy-footer-six, .jobsearch-footer-eight , .careerfy-footer-ten , .careerfy-footer-eleven,
            .careerfy-footer-sixteen,.careerfy-footer-thirteen,.careerfy-footer-ninteen,.careerfy-footer-twenty,.careerfy-footer-twentyone {background-color: <?php echo esc_html($footer_bg_color) ?>;}
            <?php
        }
        if ($footer_text_color != '') {
            ?>
            .careerfy-footer-one .text,.careerfy-footer-widget p,.careerfy-footer-widget ul li,.careerfy-footer-widget table > tbody > tr > td,
            .careerfy-footer-widget table > thead > tr > th,.careerfy-footer-widget table caption,.careerfy-footer-widget i,.careerfy-footer-widget ul li p,
            .careerfy-footer-widget time,.careerfy-footer-widget span,.careerfy-footer-widget strong,.careerfy-footer-widget .widget_contact_info a,
            .careerfy-footer-two .widget_archive ul li:before {color: <?php echo esc_html($footer_text_color) ?>;}
            <?php
        }
        if (isset($footer_link_color['regular']) && $footer_link_color['regular'] != '') { ?>
            .careerfy-footer-one .links,.careerfy-footer-widget a,.careerfy-footer-widget .widget_product_categories li span,.careerfy-footer-widget .widget.widget_categories ul li,
            .careerfy-footer-widget .widget.widget_archive ul li,.careerfy-footer-widget .careerfy-futurecourse li a,#careerfy-footer .widget_nav_menu ul li a {
            color: <?php echo esc_html($footer_link_color['regular']) ?>;}
            <?php
        }
        if (isset($footer_link_color['hover']) && $footer_link_color['hover'] != '') {
            ?>
            .careerfy-footer-one .links,.careerfy-footer-widget a:hover,#careerfy-footer .widget_nav_menu ul li a:hover {color: <?php echo esc_html($footer_link_color['hover']) ?>;}
            <?php
        }
        if (isset($footer_link_color['visited']) && $footer_link_color['visited'] != '') {
            ?>
            .careerfy-footer-one .links,.careerfy-footer-widget a:visited, #careerfy-footer .widget_nav_menu ul li a:visited {color: <?php echo esc_html($footer_link_color['visited']) ?>;}
            <?php
        }
        if (isset($footer_link_color['active']) && $footer_link_color['active'] != '') {
            ?>
            .careerfy-footer-one .links,#careerfy-footer .widget_nav_menu ul li.current-menu-item a {color: <?php echo esc_html($footer_link_color['active']) ?>;}
            <?php
        }
        if ($footer_border_color != '') {
            ?>
            .careerfy-footer-one .border,.careerfy-footer-widget *,.careerfy-footer-widget .woocommerce.widget *,.careerfy-footer-widget .widget_articles ul li,.careerfy-footer-four .careerfy-footer-widget,.careerfy-footer-partner,.careerfy-footer-two .widget_courses-program ul li,.careerfy-copyright,.copyright-three,.careerfy-copyright-wrap, .careerfy-footer-ten .copyright-ten,.copyright-thirteen,.copyright-sixteen {border-color: <?php echo esc_html($footer_border_color) ?>;}

            .widget_archive ul li:before { background-color: <?php echo esc_html($footer_border_color) ?>; }
            <?php
        }
        if ($footer_copyright_bgcolor != '') {
            ?>
            .careerfy-copyright,.jobsearch-footer-eight .jobsearch-copyright,.copyright-five,.copyright-ten,.copyright-sixteen,.copyright-thirteen,.copyright-nineteen {
            background-color: <?php echo esc_html($footer_copyright_bgcolor) ?>;}
            <?php
        }
        if ($footer_copyright_color != '') {
            ?>
            .jobsearch-copyright, .jobsearch-copyright p, .careerfy-copyright, .careerfy-copyright p, .careerfy-copyright span,.careerfy-copyright-two p,.copyright-three p,.careerfy-copyright-two p a,
            .copyright-ten p,.copyright-nineteen p,.copyright-nineteen-social li a,.copyright-twenty p,.copyright-twentyone p {
            color: <?php echo esc_html($footer_copyright_color) ?>;}
            <?php
        }
        
        $responsive_hder_bg = isset($careerfy__options['mobile_header_bg_color']['color']) ? $careerfy__options['mobile_header_bg_color']['color'] : '';
        $responsive_hder_menubg = isset($careerfy__options['mobile_header_menubg_color']['color']) ? $careerfy__options['mobile_header_menubg_color']['color'] : '';
        $resphder_hder_close_iconclr = isset($careerfy__options['mobile_hder_close_iconcolor']['color']) ? $careerfy__options['mobile_hder_close_iconcolor']['color'] : '';
        
        $resphder_menu_itmclr = isset($careerfy__options['mobile_hder_menuitm_color']['color']) ? $careerfy__options['mobile_hder_menuitm_color']['color'] : '';
        $resphder_menu_itm_bgclr = isset($careerfy__options['mobile_hder_menuitm_bgclr']['color']) ? $careerfy__options['mobile_hder_menuitm_bgclr']['color'] : '';
        $resphder_menu_itm_brderclr = isset($careerfy__options['mobile_hder_menuitm_borderclr']['color']) ? $careerfy__options['mobile_hder_menuitm_borderclr']['color'] : '';
        $resphder_activmenu_itmclr = isset($careerfy__options['mobile_hder_activ_menuitm_color']['color']) ? $careerfy__options['mobile_hder_activ_menuitm_color']['color'] : '';
        $resphder_activmenu_itm_bgclr = isset($careerfy__options['mobile_hder_activ_menuitm_bgclr']['color']) ? $careerfy__options['mobile_hder_activ_menuitm_bgclr']['color'] : '';
        $resphder_submenu_itmclr = isset($careerfy__options['mobile_hder_submenuitm_color']['color']) ? $careerfy__options['mobile_hder_submenuitm_color']['color'] : '';
        $resphder_submenu_itmbgclr = isset($careerfy__options['mobile_hder_submenuitm_bgclr']['color']) ? $careerfy__options['mobile_hder_submenuitm_bgclr']['color'] : '';
        $resphder_submenu_itmbordrclr = isset($careerfy__options['mobile_hder_submenuitm_borderclr']['color']) ? $careerfy__options['mobile_hder_submenuitm_borderclr']['color'] : '';
        
        $responsive_hder_menu_btnclr = isset($careerfy__options['mobile_hder_menubtn_color']['color']) ? $careerfy__options['mobile_hder_menubtn_color']['color'] : '';
        $responsive_hder_notify_clr = isset($careerfy__options['mobile_hder_notify_color']['color']) ? $careerfy__options['mobile_hder_notify_color']['color'] : '';
        $responsive_hder_notify_bgclr = isset($careerfy__options['mobile_hder_notify_bgcolor']['color']) ? $careerfy__options['mobile_hder_notify_bgcolor']['color'] : '';
        $responsive_hder_userbtn_clr = isset($careerfy__options['mobile_hder_userlogin_color']['color']) ? $careerfy__options['mobile_hder_userlogin_color']['color'] : '';
        $responsive_hder_userbtn_bgclr = isset($careerfy__options['mobile_hder_userlogin_bgcolor']['color']) ? $careerfy__options['mobile_hder_userlogin_bgcolor']['color'] : '';
        
        $responsive_hder_cusbtn_clr = isset($careerfy__options['mobile_hder_cusbtn_color']['color']) ? $careerfy__options['mobile_hder_cusbtn_color']['color'] : '';
        $responsive_hder_cusbtn_bgclr = isset($careerfy__options['mobile_hder_cusbtn_bgcolor']['color']) ? $careerfy__options['mobile_hder_cusbtn_bgcolor']['color'] : '';
        if ($responsive_hder_bg != '') {
            ?>
            .careerfy-mobilehder-strip {
            background-color: <?php echo esc_html($responsive_hder_bg) ?>;}
            <?php
        }
        if ($responsive_hder_menubg != '') {
            ?>
            .careerfy-mobile-hdr-sidebar, .careerfy-mobile-hdr-sidebar a.mobile-navclose-btn {
            background-color: <?php echo esc_html($responsive_hder_menubg) ?>;}
            <?php
        }
        if ($resphder_hder_close_iconclr != '') {
            ?>
            .careerfy-mobile-hdr-sidebar a.mobile-navclose-btn {
            color: <?php echo esc_html($resphder_hder_close_iconclr) ?>;}
            <?php
        }
        
        if ($resphder_menu_itmclr != '') {
            ?>
            .careerfy-mobile-navbar > li > a, .careerfy-mobile-navbar > li > .child-navitms-opner {
            color: <?php echo esc_html($resphder_menu_itmclr) ?>;}
            <?php
        }
        if ($resphder_menu_itm_bgclr != '') {
            ?>
            .careerfy-mobile-navbar > li > a {
            background-color: <?php echo esc_html($resphder_menu_itm_bgclr) ?>;}
            <?php
        }
        if ($resphder_menu_itm_brderclr != '') {
            ?>
            .careerfy-mobile-navbar > li > a {
            border-color: <?php echo esc_html($resphder_menu_itm_brderclr) ?>;}
            <?php
        }
        if ($resphder_activmenu_itmclr != '') {
            ?>
            .careerfy-mobile-navbar > li.active > a, .careerfy-mobile-navbar > li.active > .child-navitms-opner {
            color: <?php echo esc_html($resphder_activmenu_itmclr) ?>;}
            <?php
        }
        if ($resphder_activmenu_itm_bgclr != '') {
            ?>
            .careerfy-mobile-navbar > li.active > a {
            background-color: <?php echo esc_html($resphder_activmenu_itm_bgclr) ?>;}
            <?php
        }
        if ($resphder_submenu_itmclr != '') {
            ?>
            .careerfy-mobile-navbar .sidebar-submenu > li > a, .careerfy-mobile-navbar .sidebar-submenu > li > .child-navitms-opner {
            color: <?php echo esc_html($resphder_submenu_itmclr) ?>;}
            <?php
        }
        if ($resphder_submenu_itmbgclr != '') {
            ?>
            .careerfy-mobile-navbar .sidebar-submenu {
            background-color: <?php echo esc_html($resphder_submenu_itmbgclr) ?>;}
            <?php
        }
        if ($resphder_submenu_itmbordrclr != '') {
            ?>
            .careerfy-mobile-navbar .sidebar-submenu > li > a {
            border-color: <?php echo esc_html($resphder_submenu_itmbordrclr) ?>;}
            <?php
        }
        
        if ($responsive_hder_menu_btnclr != '') {
            ?>
            .mobile-right-btnscon a.mobile-navigation-togglebtn {
            color: <?php echo esc_html($responsive_hder_menu_btnclr) ?>;}
            <?php
        }
        if ($responsive_hder_notify_clr != '') {
            ?>
            .mobile-right-btnscon a.mobile-usernotifics-btn {
            color: <?php echo esc_html($responsive_hder_notify_clr) ?>;}
            <?php
        }
        if ($responsive_hder_notify_bgclr != '') {
            ?>
            .mobile-right-btnscon a.mobile-usernotifics-btn {
            background-color: <?php echo esc_html($responsive_hder_notify_bgclr) ?>;}
            <?php
        }
        if ($responsive_hder_userbtn_clr != '') {
            ?>
            .mobile-right-btnscon a.jobsearch-useracount-hdrbtn {
            color: <?php echo esc_html($responsive_hder_userbtn_clr) ?>;}
            <?php
        }
        if ($responsive_hder_userbtn_bgclr != '') {
            ?>
            .mobile-right-btnscon a.jobsearch-useracount-hdrbtn {
            background-color: <?php echo esc_html($responsive_hder_userbtn_bgclr) ?>;}
            <?php
        }
        if ($responsive_hder_cusbtn_clr != '') {
            ?>
            a.mobile-hdr-custombtn, a.mobile-hdr-custombtn i {
            color: <?php echo esc_html($responsive_hder_cusbtn_clr) ?>;}
            <?php
        }
        if ($responsive_hder_cusbtn_bgclr != '') {
            ?>
            a.mobile-hdr-custombtn {
            background-color: <?php echo esc_html($responsive_hder_cusbtn_bgclr) ?>;}
            <?php
        }
    }

    $custom_styles = ob_get_clean();
    return apply_filters('careerfy_theme_colors_styles', $custom_styles, $careerfy__options);
}
