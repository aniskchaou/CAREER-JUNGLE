<?php
if (!defined('ABSPATH')) {
    die;
}

global $empemail_applicants_handle;
if (!class_exists('jobsearch_empemail_applicants_handle')) {

    class jobsearch_empemail_applicants_handle {

        // hook things up
        public function __construct() {
            global $empemail_applicants_handle;
            add_action('wp_ajax_jobsearch_empdash_load_single_eapswith_job_inlist', array($this, 'load_single_apswith_job_inlist'));
            //
            add_action('wp_ajax_jobsearch_empdash_load_email_apswith_job_posts', array($this, 'load_all_jobs_post_data'));
            add_action('wp_ajax_jobsearch_empdash_load_email_apswith_job_apps', array($this, 'load_more_apswith_job_apps'));
            add_action('wp_ajax_jobsearch_empdash_load_email_apswith_apps_lis', array($this, 'load_more_apswith_apps_lis'));
            //
            add_action('wp_ajax_jobsearch_empdash_emailjobs_apps_count_loadboxes', array($this, 'alljobs_apps_count_loadboxes'));
        }

        public function applicants_list() {

            global $jobsearch_plugin_options;
            
            $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
            
            $user_id = get_current_user_id();
            $user_id = apply_filters('jobsearch_in_fromdash_all_emailjobaplics_user_id', $user_id);
            $employer_id = jobsearch_get_user_employer_id($user_id);

            if ($employer_id > 0) {
                $args = array(
                    'post_type' => 'job',
                    'posts_per_page' => 5,
                    'post_status' => array('publish', 'draft'),
                    'fields' => 'ids',
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_posted_by',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'jobsearch_job_emailapps_list',
                            'value' => '',
                            'compare' => '!=',
                        ),
                    ),
                );
                $get_job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';
                if ($get_job_id > 0 && get_post_type($get_job_id) == 'job') {
                    $args['post__in'] = array($get_job_id);
                }
                $jobs_query = new WP_Query($args);
                $totl_found_jobs = $jobs_query->found_posts;
                $jobs_posts = $jobs_query->posts;
                ?>

                <div class="jobsearch-typo-wrap">
                    <script>
                        jQuery(document).ready(function () {
                            jobsearch_alljobs_apps_count_load_email();
                        });
                    </script>
                    <div class="jobsearch-employer-box-section">
                        <div class="jobsearch-profile-title">
                            <h2><?php esc_html_e('Applied by Email Applicants', 'wp-jobsearch') ?></h2>
                            <a href="<?php echo add_query_arg(array('tab' => 'all-applicants'), $page_url) ?>" class="applicnts-view-btn"><?php esc_html_e('Internal Applicants', 'wp-jobsearch') ?></a>
                        </div>
                        <div class="jobsearch-allaplicants-holder jobsearch-emailaplicants-holder" data-uid="<?php echo ($user_id) ?>" data-eid="<?php echo ($employer_id) ?>">

                            <div class="select-appsjob-con">
                                <div class="allapps-selctcounts-holdr">
                                    <div class="allapps-job-label"><h2><?php esc_html_e('Filter by Job', 'wp-jobsearch') ?></h2></div>
                                    <div class="allapps-jobselct-con" style="display: inline-block; position: relative;">
                                        <?php
                                        $job_selcted_by = '';
                                        self::get_custom_post_field($job_selcted_by, 'job', esc_html__('Jobs', 'wp-jobsearch'), 'email_jobs_wapps_selctor');
                                        ?>
                                    </div>
                                </div>
                                <div class="overall-appcreds-con">
                                    <ul>
                                        <li><span class="tot-apps"><?php esc_html_e('Total Applicants ', 'wp-jobsearch') ?></span><div class="applicnt-count-box tot-apps"> <a class="overall-site-aplicnts">0</a></div></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            if (!empty($jobs_posts)) {
                                ?>
                                <div class="jobsearch-all-aplicantslst">
                                    <?php
                                    self::load_wapp_jobs_posts($jobs_posts, $employer_id);
                                    ?>
                                </div>
                                <?php
                                if ($totl_found_jobs > 5) {
                                    $total_pages = ceil($totl_found_jobs / 5);
                                    ?>
                                    <div class="lodmoreemil-apps-btnsec">
                                        <a href="javascript:void(0);" class="lodmoreemil-apps-btn" data-tpages="<?php echo ($total_pages) ?>" data-gtopage="2"><?php esc_html_e('Load More Jobs', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <p><?php esc_html_e('No job found with applicants.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
            } else {
                ?>
                <div class="jobsearch-employer-dasboard jobsearch-typo-wrap">
                    <div class="jobsearch-employer-box-section">
                        <div class="jobsearch-profile-title">
                            <h2><?php esc_html_e('Applied by Email Applicants', 'wp-jobsearch') ?></h2>
                        </div>
                        <p><?php esc_html_e('No Applicants found.', 'wp-jobsearch') ?></p>
                    </div>
                </div>
                <?php
            }
        }

        public static function get_custom_post_field($selected_id, $custom_post_slug, $field_label, $field_name, $custom_name = '') {
            global $jobsearch_form_fields;
            $custom_post_first_element = esc_html__('All ', 'wp-jobsearch');
            $custom_posts = array(
                '' => $custom_post_first_element . $field_label,
            );
            if ($selected_id) {
                $this_custom_posts = get_the_title($selected_id);
                $custom_posts[$selected_id] = $this_custom_posts;
            }

            $rand_num = rand(1234568, 6867867);
            $field_params = array(
                'classes' => 'job_postemil_cajax_field',
                'id' => 'custom_post_field_' . $rand_num,
                'name' => $field_name,
                'cus_name' => $field_name,
                'options' => $custom_posts,
                'force_std' => $selected_id,
                'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_id . '" data-loaded="false" data-posttype="' . $custom_post_slug . '"',
            );
            if (isset($custom_name) && $custom_name != '') {
                $field_params['cus_name'] = $custom_name;
            }
            echo '<div class="jobsearch-profile-select">';
            $jobsearch_form_fields->select_field($field_params);
            echo '</div>';
            ?>
            <span class="jobsearch-field-loader custom_post_loader_<?php echo absint($rand_num); ?>"></span>
            <?php
        }

        public static function list_job_all_apps($_job_id, $employer_id, $apps_start = 0) {
            global $jobsearch_plugin_options, $jobsearch_uploding_resume;
            
            $jobsearch_uploding_resume = true;
            
            $job_cver_ltrs = get_post_meta($_job_id, 'jobsearch_job_apply_cvrs', true);
            
            $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_emailapps_list', true);
            arsort($job_applicants_list);

            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }

            //
            $apps_offset = 6;
            if ($apps_start > 0) {
                $apps_start = ($apps_start - 1) * ($apps_offset);
            }
            $job_applicants_list = array_slice($job_applicants_list, $apps_start, $apps_offset);

            if (!empty($job_applicants_list)) {
                foreach ($job_applicants_list as $apply_data) {
                    $app_id = isset($apply_data['app_id']) ? $apply_data['app_id'] : '';
                    $app_obj = get_post($app_id);
                    $app_post_date = isset($app_obj->post_date) ? $app_obj->post_date : '';
                    $job_id = isset($apply_data['id']) ? $apply_data['id'] : '';
                    $email = isset($apply_data['email']) ? $apply_data['email'] : '';
                    $user_email = isset($apply_data['user_email']) ? $apply_data['user_email'] : '';
                    
                    //
                    $first_name = isset($apply_data['username']) ? $apply_data['username'] : '';
                    $last_name = isset($apply_data['user_surname']) ? $apply_data['user_surname'] : '';
                    $user_phone = isset($apply_data['user_phone']) ? $apply_data['user_phone'] : '';
                    $user_msg = isset($apply_data['user_msg']) ? $apply_data['user_msg'] : '';
                    $job_title = isset($apply_data['job_title']) ? $apply_data['job_title'] : '';
                    $current_salary = isset($apply_data['current_salary']) ? $apply_data['current_salary'] : '';
                    $att_file_path = isset($apply_data['att_file_path']) ? $apply_data['att_file_path'] : '';
                    $att_file_args = isset($apply_data['att_file_args']) ? $apply_data['att_file_args'] : '';
                    
                    $current_salary = jobsearch_get_price_format($current_salary);
                    
                    $user_def_avatar_url = get_avatar_url($user_email, array('size' => 69));
                    $_candidate_id = '';
                    $user_page_url = 'javascript:void(0);';
                    $candidate_title = $first_name . ' ' . $last_name;

                    if (email_exists($user_email)) {
                        $_user_obj = get_user_by('email', $user_email);
                        $_user_id = isset($_user_obj->ID) ? $_user_obj->ID : '';
                        if (jobsearch_user_is_candidate($_user_id)) {
                            $_candidate_id = jobsearch_get_user_candidate_id($_user_id);
                            $user_page_url = get_permalink($_candidate_id);
                            $candidate_title = get_the_title($_candidate_id);
                            
                        }
                    }
                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                    $_rand_id = rand(1000000, 9999999);
                    ?>
                    <li class="jobsearch-column-12">
                        <script>
                            jQuery(document).on('click', '.jobsearch-modelcvrltr-btn-<?php echo ($_rand_id) ?>', function () {
                                jobsearch_modal_popup_open('JobSearchCandCovershwModal<?php echo ($_rand_id) ?>');
                            });
                        </script>
                        <div class="jobsearch-applied-jobs-wrap">

                            <a href="<?php echo ($user_page_url) ?>" class="jobsearch-applied-jobs-thumb">
                                <img src="<?php echo ($user_def_avatar_url) ?>" alt="">
                            </a>
                            <div class="jobsearch-applied-jobs-text">
                                <div class="jobsearch-applied-jobs-left">
                                    <h2 class="jobsearch-pst-title">
                                        <a href="<?php echo ($user_page_url) ?>"><?php echo ($candidate_title) ?></a>
                                        <?php
                                        if ($user_phone != '') {
                                            ?>
                                            <small><a href="tel:<?php echo ($user_phone) ?>"><?php printf(esc_html__('Phone: %s', 'wp-jobsearch'), $user_phone) ?></a></small>
                                            <?php
                                        }
                                        ?>
                                    </h2>
                                    <?php
                                    if ($job_title != '') {
                                        ?>
                                        <span> <?php echo ($job_title) ?></span>
                                        <?php
                                    }
                                    ?>
                                    <ul>
                                        <?php
                                        if ($app_post_date != '') {
                                            $aply_date_time = strtotime($app_post_date);
                                            ?>
                                            <li><i class="jobsearch-icon jobsearch-calendar"></i> <?php printf(esc_html__('Applied at: %s', 'wp-jobsearch'), (date_i18n(get_option('date_format'), $aply_date_time) . ' ' . date_i18n(get_option('time_format'), $aply_date_time))) ?></li>
                                            <?php
                                        }
                                        if ($current_salary != '') {
                                            ?>
                                            <li><i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $current_salary) ?></li>
                                            <?php
                                        }
                                        if ($user_email != '') {
                                            ?>
                                            <li><i class="fa fa-envelope"></i> <a href="mailto:<?php echo ($user_email) ?>"><?php printf(esc_html__('Email: %s', 'wp-jobsearch'), $user_email) ?></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="jobsearch-applied-job-btns">
                                    <ul>
                                        <?php
                                        if ($_candidate_id > 0 && isset($job_cver_ltrs[$_candidate_id]) && $job_cver_ltrs[$_candidate_id] != '') {
                                            ?>
                                            <li><a href="javascript:void(0);" class="preview-candidate-profile jobsearch-modelcvrltr-btn-<?php echo ($_rand_id) ?>"><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?></a></li>
                                            <?php
                                        }
                                        if (isset($att_file_args['file_url']) && $att_file_args['file_url'] != '' && $app_id > 0) {
                                            $file_attach_id = $att_file_args['file_id'];
                                            $filename = $att_file_args['file_name'];
                                            $att_file_path = $att_file_args['file_url'];
                                            
                                            $file_url = apply_filters('wp_jobsearch_email_cvfile_downlod_url', $att_file_path, $file_attach_id, $app_id);
                                            ?>
                                            <li><a href="<?php echo ($file_url) ?>" class="preview-candidate-profile btn-downlod-cvbtn" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a></li>
                                            <?php
                                        }
                                        echo apply_filters('indash_email_apps_acts_list_after_download_link', '', $app_id, $job_id);
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                    $popup_args = array(
                        'job_id' => $_job_id,
                        'rand_num' => $_rand_id,
                        'candidate_id' => $_candidate_id,
                    );
                    add_action('wp_footer', function () use ($popup_args) {

                        global $jobsearch_plugin_options;

                        extract(shortcode_atts(array(
                            'job_id' => '',
                            'rand_num' => '',
                            'candidate_id' => '',
                        ), $popup_args));

                        $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                        if (isset($job_cver_ltrs[$candidate_id]) && $job_cver_ltrs[$candidate_id] != '') {
                            ?>
                            <div class="jobsearch-modal jobsearch-typo-wrap jobsearch-candcover-popup fade"
                                 id="JobSearchCandCovershwModal<?php echo($rand_num) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <p><?php echo($job_cver_ltrs[$candidate_id]) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }, 11, 1);
                }
            }
        }

        public static function load_wapp_jobs_posts($jobs_posts, $employer_id) {
            if (!empty($jobs_posts)) {
                foreach ($jobs_posts as $_job_id) {
                    $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_emailapps_list', true);
                    $job_aplly_email = get_post_meta($_job_id, 'jobsearch_field_job_apply_email', true);

                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }

                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                    ?>

                    <div class="sjob-aplicants-list">
                        <div class="thjob-title">
                            <h2>
                                <?php echo get_the_title($_job_id) ?>
                                <span class="email-sento"><?php esc_html_e('Email Sent To: ', 'wp-jobsearch') ?> <a href="mailto:<?php echo ($job_aplly_email) ?>"><?php echo ($job_aplly_email) ?></a></span>
                            </h2>
                            <div class="total-appcreds-con">
                                <ul>
                                    <li><div class="applicnt-count-box tot-apps"><span><?php esc_html_e('Total Applicants: ', 'wp-jobsearch') ?></span> <?php echo absint($job_applicants_count) ?></div></li>
                                </ul>
                            </div>
                        </div>
                        <div class="jobsearch-applied-jobs">
                            <?php
                            if (!empty($job_applicants_list)) {
                                ?>
                                <ul id="job-apps-list<?php echo ($_job_id) ?>" class="jobsearch-row">
                                    <?php
                                    self::list_job_all_apps($_job_id, $employer_id);
                                    ?>
                                </ul>
                                <?php
                                if ($job_applicants_count > 6) {
                                    $total_apps_pages = ceil($job_applicants_count / 6);
                                    ?>
                                    <div class="lodmoreemil-jobapps-btnsec">
                                        <a href="javascript:void(0);" class="lodmoreemil-jobapps-btn" data-jid="<?php echo ($_job_id) ?>" data-tpages="<?php echo ($total_apps_pages) ?>" data-gtopage="2"><?php esc_html_e('Load More Applicants', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <p><?php esc_html_e('No applicant found.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }

        public function load_all_jobs_post_data() {
            $force_std = $_POST['force_std'];
            $posttype = $_POST['posttype'];

            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $args = array(
                'posts_per_page' => "-1",
                'post_type' => $posttype,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_emailapps_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );

            $custom_query = new WP_Query($args);
            $all_records = $custom_query->posts;

            $html = "<option value=\"\">" . esc_html__('Please select job', 'wp-jobsearch') . "</option>" . "\n";
            if (isset($all_records) && !empty($all_records)) {
                foreach ($all_records as $user_var) {
                    $selected = $user_var == $force_std ? ' selected="selected"' : '';
                    $post_title = get_the_title($user_var);
                    $html .= "<option{$selected} value=\"{$user_var}\">{$post_title}</option>" . "\n";
                }
            }
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_more_apswith_job_apps() {
            $page_num = $_POST['page_num'];
            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => 5,
                'paged' => $page_num,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_emailapps_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $jobs_posts = $jobs_query->posts;

            ob_start();
            self::load_wapp_jobs_posts($jobs_posts, $employer_id);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_more_apswith_apps_lis() {
            $page_num = absint($_POST['page_num']);
            $_job_id = absint($_POST['_job_id']);
            
            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            ob_start();
            self::list_job_all_apps($_job_id, $employer_id, $page_num);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_single_apswith_job_inlist() {

            $_job_id = absint($_POST['_job_id']);
            $jobs_posts = array($_job_id);
            ob_start();
            self::load_wapp_jobs_posts($jobs_posts, $employer_id);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function alljobs_apps_count_loadboxes() {

            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $appcounts = $shappcounts = $rejappcounts = 0;

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => -1,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_emailapps_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $jobs_posts = $jobs_query->posts;

            if (!empty($jobs_posts)) {
                foreach ($jobs_posts as $_job_id) {
                    $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_emailapps_list', true);

                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }

                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                    $appcounts += $job_applicants_count;
                }
            }

            echo json_encode(array('appcounts' => $appcounts));

            wp_die();
        }

    }

    $empemail_applicants_handle = new jobsearch_empemail_applicants_handle();
}
