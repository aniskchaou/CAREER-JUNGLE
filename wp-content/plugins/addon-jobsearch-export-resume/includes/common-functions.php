<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
//
function jobsearch_resume_export_files_upload_dir($dir = '')
{
    $cus_dir = 'jobsearch-resume-export-temp';
    $dir_path = array(
        'path' => $dir['basedir'] . '/' . $cus_dir,
        'url' => $dir['baseurl'] . '/' . $cus_dir,
        'subdir' => $cus_dir,
    );
    return $dir_path + $dir;
}

$jobsearch_pdf_temp_upload_file = true;
add_filter('upload_dir', 'jobsearch_resume_export_files_upload_dir', 10, 1);
$wp_upload_dir = wp_upload_dir();
define('JOBSEARCH_RESUME_PDF_TEMP_DIR_PATH', $wp_upload_dir['path'] . "/");
remove_filter('upload_dir', 'jobsearch_resume_export_files_upload_dir', 10, 1);
$jobsearch_pdf_temp_upload_file = false;

function jobsearch_resume_export_job_get_all_skills($job_id, $seprator = '', $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $listype = 'job')
{
    global $jobsearch_plugin_options;
    $search_list_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';

    if ($listype == 'candidate') {
        $search_list_page = isset($jobsearch_plugin_options['jobsearch_cand_result_page']) ? $jobsearch_plugin_options['jobsearch_cand_result_page'] : '';
    }

    $search_page_obj = $search_list_page != '' ? get_page_by_path($search_list_page, 'OBJECT', 'page') : '';
    $skills = wp_get_post_terms($job_id, 'skill');
    ob_start();
    $html = '';
    if (!empty($skills)) {

        $flag = 0;
        foreach ($skills as $term) :
            echo($before_tag);
            if ($flag > 0) {
                echo $seprator;
            }
            $skill_page_url = '';
            if (isset($search_page_obj->ID)) {
                $skill_page_url = add_query_arg(array('skill_in' => $term->slug), get_permalink($search_page_obj->ID));
            } ?>

            <a <?php echo($skill_page_url != '' ? 'href="' . $skill_page_url . ' " ' : '') ?>class="<?php echo($link_class) ?>">
                <?php
                echo($before_title);
                echo esc_html($term->name);
                echo($after_title);
                ?>
            </a>
            <?php
            echo($after_tag);
            $flag++;
        endforeach;
    }
    $html .= ob_get_clean();
    return $html;
}

add_filter('jobsearch_resume_export_section', 'jobsearch_resume_export_section_callback', 10, 1);
function jobsearch_resume_export_section_callback($settings)
{

    $resume_export_sec = array();
    $resume_export_sec[] = array(
        'id' => 'my_resume_box_export_switch',
        'type' => 'button_set',
        'title' => __('My Resume Box on/off', 'jobsearch-resume-export'),
        'subtitle' => __('Enable/Disable My Resume Box in Candidate dashboard => My Resume', 'jobsearch-resume-export'),
        'desc' => '',
        'options' => array(
            'on' => __('On', 'jobsearch-resume-export'),
            'off' => __('Off', 'jobsearch-resume-export'),
        ),
        'default' => 'on',
    );

    $templates_arr_list = [
        'default' => 'Default',
        'Template 1' => 'Template 1',
        'Template 2' => 'Template 2',
        'Template 3' => 'Template 3',
        'Template 4' => 'Template 4',
        'Template 5' => 'Template 5',
        'Template 6' => 'Template 6',
        'Template 7' => 'Template 7',
        'Template 8' => 'Template 8',
        'Template 9' => 'Template 9',
        'Template 10' => 'Template 10',
        'Template 11' => 'Template 11',
        'Template 12' => 'Template 12',
        'Template 13' => 'Template 13',
        'Template 14' => 'Template 14',
        'Template 15' => 'Template 15',
        'Template 16' => 'Template 16',
        'Template 17' => 'Template 17',
        'Template 18' => 'Template 18',
        'Template 19' => 'Template 19',
        'Template 20' => 'Template 20',
    ];

    $resume_export_sec[] = array(
        'id' => 'cand_default_resume',
        'type' => 'select',
        'title' => __('Default Template', 'wp-jobsearch'),
        'subtitle' => __('Select default template for Resume PDF.', 'wp-jobsearch'),
        'options' => $templates_arr_list,
        'default' => 'Template 1',
        'desc' => '',
    );

    $resume_export_sec[] = array(
        'id' => 'resume-export-text',
        'type' => 'editor',
        'title' => __('My Resume Box', 'jobsearch-resume-export'),
        'desc' => __('The text will show above the packages list in user dashboard in my resume.', 'jobsearch-resume-export'),
        'indent' => true,
        'default' => '<h2>Fill it online, and download in seconds. Build a professional Design C.V, add ready-to-use suggestions, and get the job.</h2>
                        <strong>Choose a CV template, fill it out, and download in seconds. Create a professional curriculum vitae in a few clicks. Just pick one of designed CV templates below, add ready-to-use suggestions, and get the job.</strong>
                        <ul>
                            <li>Select design and click on generate PDF in A4 size.</li>
                            <li>Can print your resume with your printer.</li>
                            <li>Can use while applying on new jobs.</li>
                            <li>Can upload to CV manager as a default for your applied jobs.</li>
                        </ul>'
                    );

    $section_settings = array(
        'title' => __('Resume Export Settings', 'jobsearch-resume-export'),
        'id' => 'resume-export-section',
        'desc' => __('Add Resume Introduction', 'jobsearch-resume-export'),
        'icon' => 'el el-file-alt',
        'fields' => $resume_export_sec,
    );
    return $section_settings;
}

function jobsearch_get_font_code($font = '')
{
    $fonts_list = array(
        'jobsearch-icon jobsearch-resume-document' => '&#xe900',
        'jobsearch-icon jobsearch-paper' => '&#xe901',
        'jobsearch-icon jobsearch-coding' => '&#xe902',
        'jobsearch-icon jobsearch-24-hours' => '&#xe904',
        'jobsearch-icon jobsearch-company-workers' => '&#xe905',
        'jobsearch-icon jobsearch-graphic' => '&#xe906',
        'jobsearch-icon jobsearch-support' => '&#xe907',
        'jobsearch-icon jobsearch-pen' => '&#xe908',
        'jobsearch-icon jobsearch-search-1' => '&#xe909',
        'jobsearch-icon jobsearch-handshake' => '&#xe90a',
        'jobsearch-icon jobsearch-office2' => '&#xe90b',
        'jobsearch-icon jobsearch-check-square' => '&#xe90c',
        'jobsearch-icon jobsearch-arrows-4' => '&#xe90d',
        'jobsearch-icon jobsearch-arrows-3' => '&#xe90e',
        'jobsearch-icon jobsearch-building' => '&#xe90f',
        'jobsearch-icon jobsearch-curriculum' => '&#xe910',
        'jobsearch-icon jobsearch-user-2' => '&#xe911',
        'jobsearch-icon jobsearch-discuss-issue' => '&#xe912',
        'jobsearch-icon jobsearch-newspaper' => '&#xe913',
        'jobsearch-icon jobsearch-fax' => '&#xe914',
        'jobsearch-icon jobsearch-placeholder' => '&#xe915',
        'jobsearch-icon jobsearch-linkedin-logo' => '&#xe916',
        'jobsearch-icon jobsearch-facebook-logo-1' => '&#xe917',
        'jobsearch-icon jobsearch-summary' => '&#xe918',
        'jobsearch-icon jobsearch-calendar-1' => '&#xe919',
        'jobsearch-icon jobsearch-valentines-heart' => '&#xe91a',
        'jobsearch-icon jobsearch-check-sign-in-a-rounded-black-square' => '&#xe91b',
        'jobsearch-icon jobsearch-check-box-empty' => '&#xe91c',
        'jobsearch-icon jobsearch-checked' => '&#xe91d',
        'jobsearch-icon jobsearch-linkedin-1' => '&#xe91e',
        'jobsearch-icon jobsearch-user-1' => '&#xe91f',
        'jobsearch-icon jobsearch-star-1' => '&#xe920',
        'jobsearch-icon jobsearch-upload' => '&#xe921',
        'jobsearch-icon jobsearch-plus' => '&#xe922',
        'jobsearch-icon jobsearch-credit-card-1' => '&#xe923',
        'jobsearch-icon jobsearch-star' => '&#xe924',
        'jobsearch-icon jobsearch-right-arrow-1' => '&#xe925',
        'jobsearch-icon jobsearch-credit-card' => '&#xe926',
        'jobsearch-icon jobsearch-time' => '&#xe927',
        'jobsearch-icon jobsearch-folder' => '&#xe928',
        'jobsearch-icon jobsearch-group' => '&#xe929',
        'jobsearch-icon jobsearch-linkedin' => '&#xe92a',
        'jobsearch-icon jobsearch-twitter-circular-button' => '&#xe92b',
        'jobsearch-icon jobsearch-facebook-logo-in-circular-button-outlined-social-symbol' => '&#xe92c',
        'jobsearch-icon jobsearch-internet' => '&#xe92d',
        'jobsearch-icon jobsearch-calendar' => '&#xe92e',
        'jobsearch-icon jobsearch-view' => '&#xe92f',
        'jobsearch-icon jobsearch-rubbish' => '&#xe930',
        'jobsearch-icon jobsearch-edit' => '&#xe931',
        'jobsearch-icon jobsearch-resume-1' => '&#xe932',
        'jobsearch-icon jobsearch-logout' => '&#xe933',
        'jobsearch-icon jobsearch-multimedia' => '&#xe934',
        'jobsearch-icon jobsearch-id-card' => '&#xe935',
        'jobsearch-icon jobsearch-alarm' => '&#xe936',
        'jobsearch-icon jobsearch-briefcase-1' => '&#xe937',
        'jobsearch-icon jobsearch-resume' => '&#xe938',
        'jobsearch-icon jobsearch-google-plus-logo-button' => '&#xe939',
        'jobsearch-icon jobsearch-add' => '&#xe93a',
        'jobsearch-icon jobsearch-download-arrow' => '&#xe93b',
        'jobsearch-icon jobsearch-technology' => '&#xe93c',
        'jobsearch-icon jobsearch-mail' => '&#xe93d',
        'jobsearch-icon jobsearch-trophy' => '&#xe93e',
        'jobsearch-icon jobsearch-design-skills' => '&#xe93f',
        'jobsearch-icon jobsearch-mortarboard' => '&#xe940',
        'jobsearch-icon jobsearch-network' => '&#xe941',
        'jobsearch-icon jobsearch-user' => '&#xe942',
        'jobsearch-icon jobsearch-briefcase' => '&#xe943',
        'jobsearch-icon jobsearch-social-media' => '&#xe944',
        'jobsearch-icon jobsearch-salary' => '&#xe945',
        'jobsearch-icon jobsearch-check-mark' => '&#xe946',
        'jobsearch-icon jobsearch-sort' => '&#xe947',
        'jobsearch-icon jobsearch-envelope' => '&#xe948',
        'jobsearch-icon jobsearch-add-list' => '&#xe949',
        'jobsearch-icon jobsearch-squares' => '&#xe94a',
        'jobsearch-icon jobsearch-list' => '&#xe94b',
        'jobsearch-icon jobsearch-up-arrow-1' => '&#xe94c',
        'jobsearch-icon jobsearch-right-arrow' => '&#xe94d',
        'jobsearch-icon jobsearch-instagram-logo' => '&#xe94e',
        'jobsearch-icon jobsearch-linkedin-button' => '&#xe94f',
        'jobsearch-icon jobsearch-dribbble-logo' => '&#xe950',
        'jobsearch-icon jobsearch-twitter-logo' => '&#xe951',
        'jobsearch-icon jobsearch-facebook-logo' => '&#xe952',
        'jobsearch-icon jobsearch-play-button' => '&#xe953',
        'jobsearch-icon jobsearch-arrows-1' => '&#xe954',
        'jobsearch-icon jobsearch-arrows' => '&#xe955',
        'jobsearch-icon jobsearch-two-quotes' => '&#xe956',
        'jobsearch-icon jobsearch-left-quote' => '&#xe957',
        'jobsearch-icon jobsearch-filter-tool-black-shape' => '&#xe958',
        'jobsearch-icon jobsearch-maps-and-flags' => '&#xe959',
        'jobsearch-icon jobsearch-heart' => '&#xe95a',
        'jobsearch-icon jobsearch-business' => '&#xe95b',
        'jobsearch-icon jobsearch-fast-food' => '&#xe95c',
        'jobsearch-icon jobsearch-books' => '&#xe95d',
        'jobsearch-icon jobsearch-antenna' => '&#xe95e',
        'jobsearch-icon jobsearch-hospital' => '&#xe95f',
        'jobsearch-icon jobsearch-accounting' => '&#xe960',
        'jobsearch-icon jobsearch-car' => '&#xe961',
        'jobsearch-icon jobsearch-engineer' => '&#xe962',
        'jobsearch-icon jobsearch-search' => '&#xe963',
        'jobsearch-icon jobsearch-down-arrow' => '&#xe964',
        'jobsearch-icon jobsearch-location' => '&#xe965',
        'jobsearch-icon jobsearch-portfolio' => '&#xe966',
        'jobsearch-icon jobsearch-up-arrow' => '&#xe967',
        'jobsearch-icon jobsearch-arrows-2' => '&#xe968',
        'jobsearch-icon jobsearch-signs22' => '&#xeb44',
        'jobsearch-icon jobsearch-squares2' => '&#xeb45',
        'jobsearch-icon jobsearch-map-location' => '&#xeb6e',
        'jobsearch-icon jobsearch-mark2' => '&#xeb73',
        'jobsearch-icon jobsearch-map3' => '&#xeb76',
        'jobsearch-icon jobsearch-tool4' => '&#xeb7d',
        'jobsearch-icon jobsearch-buildings2' => '&#xeb92',
        'jobsearch-icon jobsearch-arrows32' => '&#xeb60',
        'jobsearch-icon jobsearch-arrows22' => '&#xeb2d',
        'jobsearch-icon jobsearch-arrows4' => '&#xeaef',
        'jobsearch-icon jobsearch-office' => '&#xe903',
        'jobsearch-icon jobsearch-arrow-right2' => '&#xea3c',
        'jobsearch-icon jobsearch-arrow-left2' => '&#xea40',
        "careerfy-icon careerfy-forgot" => "&#xe900",
        "careerfy-icon careerfy-edit" => "&#xe901",
        "careerfy-icon careerfy-self-esteem" => "&#xe902",
        "careerfy-icon careerfy-available" => "&#xe903",
        "careerfy-icon careerfy-newborn" => "&#xe904",
        "careerfy-icon careerfy-cost" => "&#xe905",
        "careerfy-icon careerfy-shield" => "&#xe906",
        "careerfy-icon careerfy-insight" => "&#xe907",
        "careerfy-icon careerfy-back" => "&#xe909",
        "careerfy-icon careerfy-wash-toub" => "&#xe90a",
        "careerfy-icon careerfy-injection" => "&#xe90b",
        "careerfy-icon careerfy-dog" => "&#xe90c",
        "careerfy-icon careerfy-note" => "&#xe90d",
        "careerfy-icon careerfy-dog-line" => "&#xe90e",
        "careerfy-icon careerfy-search-o" => "&#xe90f",
        "careerfy-icon careerfy-stopwatch" => "&#xe910",
        "careerfy-icon careerfy-map-marker" => "&#xe911",
        "careerfy-icon careerfy-woman" => "&#xe912",
        "careerfy-icon careerfy-work" => "&#xe913",
        "careerfy-icon careerfy-student" => "&#xe914",
        "careerfy-icon careerfy-user-filled" => "&#xe915",
        "careerfy-icon careerfy-back-arrow" => "&#xe916",
        "careerfy-icon careerfy-swap" => "&#xe917",
        "careerfy-icon careerfy-rss" => "&#xe918",
        "careerfy-icon careerfy-work-line" => "&#xe919",
        "careerfy-icon careerfy-check" => "&#xe91a",
        "careerfy-icon careerfy-heart-o" => "&#xe91b",
        "careerfy-icon careerfy-youtube" => "&#xe91c",
        "careerfy-icon careerfy-whatsapp" => "&#xe91d",
        "careerfy-icon careerfy-graduation-cap" => "&#xe91e",
        "careerfy-icon careerfy-skills" => "&#xe91f",
        "careerfy-icon careerfy-experience" => "&#xe920",
        "careerfy-icon careerfy-age" => "&#xe921",
        "careerfy-icon careerfy-eye" => "&#xe922",
        "careerfy-icon careerfy-link" => "&#xe923",
        "careerfy-icon careerfy-goal" => "&#xe924",
        "careerfy-icon careerfy-work-bold-line" => "&#xe925",
        "careerfy-icon careerfy-linkedin" => "&#xe926",
        "careerfy-icon careerfy-man" => "&#xe927",
        "careerfy-icon careerfy-back-arrow-line" => "&#xe928",
        "careerfy-icon careerfy-arrow" => "&#xe929",
        "careerfy-icon careerfy-cost-paper" => "&#xe92a",
        "careerfy-icon careerfy-father" => "&#xe92c",
        "careerfy-icon careerfy-help" => "&#xe92d",
        "careerfy-icon careerfy-household" => "&#xe92e",
        "careerfy-icon careerfy-instagram-circular-fill" => "&#xe92f",
        "careerfy-icon careerfy-maid" => "&#xe930",
        "careerfy-icon careerfy-shield-double" => "&#xe931",
        "careerfy-icon careerfy-submit" => "&#xe932",
        "careerfy-icon careerfy-tie" => "&#xe933",
        "careerfy-icon careerfy-user-profiles" => "&#xe934",
        "careerfy-icon careerfy-vacuum-cleaner" => "&#xe935",
        "careerfy-icon careerfy-woman-line" => "&#xe936",
        "careerfy-icon careerfy-circle-fill" => "&#xe939",
        "careerfy-icon careerfy-people" => "&#xe93a",
        "careerfy-icon careerfy-target" => "&#xe93b",
        "careerfy-icon careerfy-human-resources" => "&#xe93c",
        "careerfy-icon careerfy-arrow-left-long" => "&#xe93d",
        "careerfy-icon careerfy-twitter" => "&#xe93e",
        "careerfy-icon careerfy-hospital" => "&#xe93f",
        "careerfy-icon careerfy-money" => "&#xe940",
        "careerfy-icon careerfy-apple" => "&#xe941",
        "careerfy-icon careerfy-plus-light" => "&#xe942",
        "careerfy-icon careerfy-user-plus" => "&#xe943",
        "careerfy-icon careerfy-diamond" => "&#xe944",
        "careerfy-icon careerfy-play-circular" => "&#xe946",
        "careerfy-icon careerfy-down-arrow" => "&#xe947",
        "careerfy-icon careerfy-plus-fill-circle" => "&#xe948",
        "careerfy-icon careerfy-arrow-pointing-to-right" => "&#xe949",
        "careerfy-icon careerfy-arrow-pointing-to-left" => "&#xe94a",
        "careerfy-icon careerfy-user-new" => "&#xe94b",
        "careerfy-icon careerfy-shoping-bag" => "&#xe94c",
        "careerfy-icon careerfy-logout-line" => "&#xe94d",
        "careerfy-icon careerfy-android-logo" => "&#xe94e",
        "careerfy-icon careerfy-resume-document" => "&#xe94f",
        "careerfy-icon careerfy-paper" => "&#xe950",
        "careerfy-icon careerfy-coding" => "&#xe951",
        "careerfy-icon careerfy-office-o" => "&#xe952",
        "careerfy-icon careerfy-clock-hours" => "&#xe953",
        "careerfy-icon careerfy-company-workers" => "&#xe954",
        "careerfy-icon careerfy-graphic" => "&#xe955",
        "careerfy-icon careerfy-support" => "&#xe956",
        "careerfy-icon careerfy-pen" => "&#xe957",
        "careerfy-icon careerfy-search-papper" => "&#xe958",
        "careerfy-icon careerfy-handshake" => "&#xe959",
        "careerfy-icon careerfy-office" => "&#xe95a",
        "careerfy-icon careerfy-check-square" => "&#xe95b",
        "careerfy-icon careerfy-chef-plate" => "&#xe95c",
        "careerfy-icon careerfy-arrow-right-circular-fill" => "&#xe95d",
        "careerfy-icon careerfy-arrow-down-circular-fill" => "&#xe95e",
        "careerfy-icon careerfy-building" => "&#xe95f",
        "careerfy-icon careerfy-curriculum" => "&#xe960",
        "careerfy-icon careerfy-user-line-double" => "&#xe961",
        "careerfy-icon careerfy-discuss-issue" => "&#xe962",
        "careerfy-icon careerfy-newspaper" => "&#xe963",
        "careerfy-icon careerfy-fax" => "&#xe964",
        "careerfy-icon careerfy-pin-line" => "&#xe965",
        "careerfy-icon careerfy-linkedin-o" => "&#xe966",
        "careerfy-icon careerfy-gps-o" => "&#xe967",
        "careerfy-icon careerfy-edit-square" => "&#xe968",
        "careerfy-icon careerfy-facebook" => "&#xe969",
        "careerfy-icon careerfy-summary" => "&#xe96a",
        "careerfy-icon careerfy-calendar-line" => "&#xe96b",
        "careerfy-icon careerfy-check-box-fill" => "&#xe96d",
        "careerfy-icon careerfy-check-box-empty" => "&#xe96e",
        "careerfy-icon careerfy-checked" => "&#xe96f",
        "careerfy-icon careerfy-user-circular" => "&#xe971",
        "careerfy-icon careerfy-star-empty" => "&#xe972",
        "careerfy-icon careerfy-upload-cloud" => "&#xe973",
        "careerfy-icon careerfy-plus-circular" => "&#xe974",
        "careerfy-icon careerfy-credit-card-line" => "&#xe975",
        "careerfy-icon careerfy-star-fill" => "&#xe976",
        "careerfy-icon careerfy-right-arrow-long" => "&#xe977",
        "careerfy-icon careerfy-credit-card" => "&#xe978",
        "careerfy-icon careerfy-time" => "&#xe979",
        "careerfy-icon careerfy-folder" => "&#xe97a",
        "careerfy-icon careerfy-group" => "&#xe97b",
        "careerfy-icon careerfy-linkedin-circular" => "&#xe97c",
        "careerfy-icon careerfy-twitter-circular" => "&#xe97d",
        "careerfy-icon careerfy-facebook-circular" => "&#xe97e",
        "careerfy-icon careerfy-internet" => "&#xe97f",
        "careerfy-icon careerfy-calendar-filled" => "&#xe980",
        "careerfy-icon careerfy-view" => "&#xe981",
        "careerfy-icon careerfy-rubbish" => "&#xe982",
        "careerfy-icon careerfy-edit-outline" => "&#xe983",
        "careerfy-icon careerfy-resume-user" => "&#xe984",
        "careerfy-icon careerfy-logout" => "&#xe985",
        "careerfy-icon careerfy-multimedia" => "&#xe986",
        "careerfy-icon careerfy-responsive" => "&#xe987",
        "careerfy-icon careerfy-id-card" => "&#xe988",
        "careerfy-icon careerfy-alarm" => "&#xe989",
        "careerfy-icon careerfy-briefcase-time" => "&#xe98a",
        "careerfy-icon careerfy-resume" => "&#xe98b",
        "careerfy-icon careerfy-google-plus-circular-fill" => "&#xe98c",
        "careerfy-icon careerfy-add" => "&#xe98d",
        "careerfy-icon careerfy-download-arrow" => "&#xe98e",
        "careerfy-icon careerfy-technology" => "&#xe98f",
        "careerfy-icon careerfy-envelope-line" => "&#xe990",
        "careerfy-icon careerfy-trophy" => "&#xe991",
        "careerfy-icon careerfy-design-skills" => "&#xe992",
        "careerfy-icon careerfy-degree-cap" => "&#xe993",
        "careerfy-icon careerfy-network" => "&#xe994",
        "careerfy-icon careerfy-user-line" => "&#xe995",
        "careerfy-icon careerfy-briefcase" => "&#xe996",
        "careerfy-icon careerfy-social-media" => "&#xe997",
        "careerfy-icon careerfy-salary" => "&#xe998",
        "careerfy-icon careerfy-check-circular-fill" => "&#xe999",
        "careerfy-icon careerfy-sort" => "&#xe99a",
        "careerfy-icon careerfy-envelope" => "&#xe99b",
        "careerfy-icon careerfy-user-blank" => "&#xe99c",
        "careerfy-icon careerfy-add-list" => "&#xe99d",
        "careerfy-icon careerfy-squares" => "&#xe99e",
        "careerfy-icon careerfy-list" => "&#xe99f",
        "careerfy-icon careerfy-up-arrow" => "&#xe9a0",
        "careerfy-icon careerfy-right-arrow" => "&#xe9a1",
        "careerfy-icon careerfy-dribbble-circular-fill" => "&#xe9a4",
        "careerfy-icon careerfy-twitter-circular-fill" => "&#xe9a5",
        "careerfy-icon careerfy-facebook-circular-fill" => "&#xe9a6",
        "careerfy-icon careerfy-play-button" => "&#xe9a7",
        "careerfy-icon careerfy-arrow-right-circular" => "&#xe9a8",
        "careerfy-icon careerfy-arrow-left-circular-fill" => "&#xe9a9",
        "careerfy-icon careerfy-two-quotes" => "&#xe9aa",
        "careerfy-icon careerfy-left-quote" => "&#xe9ab",
        "careerfy-icon careerfy-filter-tool-black-shape" => "&#xe9ac",
        "careerfy-icon careerfy-map-pin" => "&#xe9ad",
        "careerfy-icon careerfy-business" => "&#xe9af",
        "careerfy-icon careerfy-fast-food" => "&#xe9b0",
        "careerfy-icon careerfy-books" => "&#xe9b1",
        "careerfy-icon careerfy-antenna" => "&#xe9b2",
        "careerfy-icon careerfy-accounting" => "&#xe9b4",
        "careerfy-icon careerfy-car" => "&#xe9b5",
        "careerfy-icon careerfy-engineer" => "&#xe9b6",
        "careerfy-icon careerfy-search" => "&#xe9b7",
        "careerfy-icon careerfy-down-arrow-line" => "&#xe9b8",
        "careerfy-icon careerfy-location" => "&#xe9b9",
        "careerfy-icon careerfy-briefcase-line" => "&#xe9ba",
        "careerfy-icon careerfy-arrow-up-circular" => "&#xe9bb",
        "careerfy-icon careerfy-upload-arrow" => "&#xe9bc",
        "careerfy-icon careerfy-edit-pencil" => "&#xe9bd",
        "careerfy-icon careerfy-user-support" => "&#xe9cc",
        "careerfy-icon careerfy-arrow-right-fill" => "&#xea3c",
        "careerfy-icon careerfy-arrow-left" => "&#xea40",
        "careerfy-icon careerfy-pin" => "&#xea41",
        "careerfy-icon careerfy-calendar" => "&#xea42",
        "careerfy-icon careerfy-next" => "&#xea43",
        "careerfy-icon careerfy-briefcase-work" => "&#xea44",
        "careerfy-icon careerfy-avatar" => "&#xea45",
        "careerfy-icon careerfy-portfolio" => "&#xea46",
        "careerfy-icon careerfy-visit" => "&#xea47",
        "careerfy-icon careerfy-quote" => "&#xea48",
        "careerfy-icon careerfy-next-long" => "&#xea49",
        "careerfy-icon careerfy-gear" => "&#xea4a",
        "careerfy-icon careerfy-comment" => "&#xea4b",
        "careerfy-icon careerfy-heart" => "&#xea4c",
        "careerfy-icon careerfy-phone" => "&#xea4d",
        "careerfy-icon careerfy-google-play" => "&#xea4e",
        "careerfy-icon careerfy-mail" => "&#xea50",
        "careerfy-icon careerfy-plane" => "&#xea55",
        "careerfy-icon careerfy-linkedin-circular-fill" => "&#xea56",
        "careerfy-icon careerfy-pinterest" => "&#xea57",
        "careerfy-icon careerfy-logout-o" => "&#xea58",
        "careerfy-icon careerfy-user" => "&#xea59",
        "careerfy-icon careerfy-upload" => "&#xea5a",
        "careerfy-icon careerfy-user-outline" => "&#xea80",
        "careerfy-icon careerfy-search-outline" => "&#xea98",
        "careerfy-icon careerfy-phrase" => "&#xea9a",
        "careerfy-icon careerfy-play" => "&#xea9b",
        "careerfy-icon careerfy-star-fill-smoth" => "&#xea9c",
        "careerfy-icon careerfy-clock" => "&#xea9d",
        "careerfy-icon careerfy-comment-outline" => "&#xea9e",
        "careerfy-icon careerfy-like" => "&#xea9f",
        "careerfy-icon careerfy-user-shield" => "&#xeaa0",
        "careerfy-icon careerfy-login" => "&#xeaa1",
        "careerfy-icon careerfy-password" => "&#xeaa9",
        "careerfy-icon careerfy-arrow-right-bold" => "&#xeaef",
        "careerfy-icon careerfy-arrow-right" => "&#xeb2d",
        "careerfy-icon careerfy-list-sign" => "&#xeb44",
        "careerfy-icon careerfy-grid-square" => "&#xeb45",
        "careerfy-icon careerfy-arrow-right-light" => "&#xeb60",
        "careerfy-icon careerfy-map-location-pin" => "&#xeb6e",
        "careerfy-icon careerfy-check-mark" => "&#xeb73",
        "careerfy-icon careerfy-pin-on-map" => "&#xeb76",
        "careerfy-icon careerfy-edit-tool" => "&#xeb7d",
        "careerfy-icon careerfy-building-office" => "&#xeb92"
    );

    foreach ($fonts_list as $key => $info) {
        if ($font == $key) {
            return $info;
        }
    }
    return null;
}

add_filter('jobsearch_pkgs_admin_columns_title', 'jobsearch_pkgs_admin_columns_title_callback', 10, 2);
function jobsearch_pkgs_admin_columns_title_callback($type, $post_id)
{
    $pckg_type = get_post_meta($post_id, 'jobsearch_field_package_type', true);
    if ($pckg_type == 'cand_resume') {
        $type = esc_html__('Candidate PDF Resume', 'jobsearch-resume-export');
    }
    return $type;
}

function jobsearch_pdf_pckges_list()
{
    $user_id = get_current_user_id();
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => array('wc-completed'),
        'order' => 'DESC',
        'orderby' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_transaction_type',
                'value' => 'paid',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'cand_resume',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $trans_query = new WP_Query($args);
    $total_trans = $trans_query->posts;
    return $total_trans;
}

function jobsearch_pdf_pckg_pdf_templates($arr = array(), $template)
{
    $result = false;
    if (in_array($template, $arr)) {
        $result = true;
    }
    return $result;
}

// check if user package subscribed
function jobsearch_pdf_pckg_is_subscribed($pckg_id = 0, $user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'cand_resume',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);
    $pkgs_query_posts = $pkgs_query->posts;

    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            return $order_post_id;
        }
    }
    return false;
}

//
add_action('wp_ajax_jobsearch_user_pdf_pckg_subscribe', 'user_pdf_pckg_subscribe');
add_action('wp_ajax_nopriv_jobsearch_user_pdf_pckg_subscribe', 'user_pdf_pckg_subscribe');

function user_pdf_pckg_subscribe()
{
    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_candidate($user_id);
    if ($user_is_employer) {
        $pkg_id = isset($_POST['pkg_id']) ? $_POST['pkg_id'] : '';

        if (jobsearch_pdf_pckg_is_subscribed($pkg_id, $user_id)) {
            $msgva = esc_html__('You have already subscribed to this package.', 'jobsearch-resume-export');
            $msgva = apply_filters('jobsearch_buypkg_emp_alredybuy_msg', $msgva);
            echo json_encode(array('msg' => $msgva, 'error' => '1'));
            die;
        }
        if (!class_exists('WooCommerce')) {
            echo json_encode(array('msg' => esc_html__('WooCommerce Plugin not exist.', 'jobsearch-resume-export'), 'error' => '1'));
            die;
        }
        $pkg_charges_type = get_post_meta($pkg_id, 'jobsearch_field_charges_type', true);
        $pkg_attach_product = get_post_meta($pkg_id, 'jobsearch_package_product', true);
        //
        if ($pkg_charges_type == 'paid') {
            $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

            if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                $product_id = $package_product_obj->ID;
            } else {
                echo json_encode(array('msg' => esc_html__('Selected Package Product not found.', 'jobsearch-resume-export'), 'error' => '1'));
                die;
            }
            // add to cart and checkout
            ob_start();
            do_action('jobsearch_woocommerce_payment_checkout', $pkg_id, 'checkout_url');
            $checkout_url = ob_get_clean();
            echo json_encode(array('msg' => esc_html__('redirecting...', 'jobsearch-resume-export'), 'redirect_url' => $checkout_url));
            die;
        } else {
            do_action('jobsearch_create_free_packg_order', $pkg_id);
            echo json_encode(array('msg' => esc_html__('Package Subscribed Successfully.', 'jobsearch-resume-export')));
            die;
        }
        //
    } else {
        $msgva = esc_html__('You are not a candidate.', 'jobsearch-resume-export');
        $msgva = apply_filters('jobsearch_buyjobpkg_emp_notalowerr_msg', $msgva);
        echo json_encode(array('msg' => $msgva, 'error' => '1'));
        die;
    }
}

add_action('wp_ajax_jobsearch_user_pdf_type_save', 'jobsearch_user_pdf_type_save_callback');
add_action('wp_ajax_nopriv_jobsearch_user_pdf_type_save', 'jobsearch_user_pdf_type_save_callback');
function jobsearch_user_pdf_type_save_callback()
{
    $current_user = wp_get_current_user();
    $template = isset($_POST['template_name']) ? $_POST['template_name'] : '';
    update_option('jobsearch_selected_pdf_template_' . $current_user->ID, $template);
    echo json_encode(array('res' => true));
    wp_die();
}

add_action('wp_ajax_jobsearch_all_pckges_buy_checkout_call', 'jobsearch_all_pckges_buy_checkout_call');
add_action('wp_ajax_nopriv_jobsearch_all_pckges_buy_checkout_call', 'jobsearch_all_pckges_buy_checkout_call');

function jobsearch_all_pckges_buy_checkout_call() {
    $user_id = get_current_user_id();
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    if ($user_is_candidate) {
        $pkg_id = isset($_POST['pkg_id']) ? $_POST['pkg_id'] : '';
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $is_subscribed = false;
        if ($is_subscribed) {
            $msgva = esc_html__('You have already subscribed to this package.', 'wp-jobsearch');
            $msgva = apply_filters('jobsearch_buypkg_cand_alredybuy_msg', $msgva);
            echo json_encode(array('msg' => $msgva, 'error' => '1'));
            die;
        }
        if (!class_exists('WooCommerce')) {
            echo json_encode(array('msg' => esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch'), 'error' => '1'));
            die;
        }
        $pkg_charges_type = get_post_meta($pkg_id, 'jobsearch_field_charges_type', true);
        $pkg_attach_product = get_post_meta($pkg_id, 'jobsearch_package_product', true);
        if ($pkg_charges_type == 'paid') {
            $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

            if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                $product_id = $package_product_obj->ID;
            } else {
                echo json_encode(array('msg' => esc_html__('Selected Package Product not found.', 'wp-jobsearch'), 'error' => '1'));
                die;
            }

            // add to cart and checkout
            ob_start();
            do_action('jobsearch_woocommerce_payment_checkout', $pkg_id, 'checkout_url');
            $checkout_url = ob_get_clean();
            echo json_encode(array('msg' => esc_html__('redirecting...', 'wp-jobsearch'), 'redirect_url' => $checkout_url));
            die;
        } else {
            do_action('jobsearch_create_free_packg_order', $pkg_id);
            echo json_encode(array('msg' => esc_html__('Package Subscribed Successfully.', 'wp-jobsearch')));
            die;
        }
        //
    } else {
        $msgva = esc_html__('You are not a candidate.', 'wp-jobsearch');
        $msgva = apply_filters('jobsearch_buypdfresmpkg_cand_notalowerr_msg', $msgva);
        echo json_encode(array('msg' => $msgva, 'error' => '1'));
        die;
    }
}