<?php
/**
 * Directory Plus Shortlists Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_Shortlist')) {

    class Jobsearch_Shortlist
    {

        /**
         * Start construct Functions
         */
        public function __construct()
        {

            // Initialize Addon
            add_action('init', array($this, 'init'));
        }

        /**
         * Initialize application, load text domain, enqueue scripts and bind hooks
         */
        public function init()
        {

            // Add actions
            add_action('jobsearch_job_shortlist_button_frontend', array($this, 'jobsearch_job_shortlist_button_callback'), 11, 1);
            add_action('wp_ajax_jobsearch_add_candidate_job_to_favourite', array($this, 'jobsearch_shortlist_submit_callback'), 11);
            add_action('wp_ajax_jobsearch_remv_candidate_job_frm_favourite', array($this, 'jobsearch_removed_shortlist_callback'), 11);
            add_action('wp_enqueue_scripts', array($this, 'jobsearch_shortlist_enqueue_scripts'), 10);
        }


        public function jobsearch_shortlist_enqueue_scripts()
        {
            global $sitepress;

            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }

            $is_page = is_page();
            $page_content = '';
            if ($is_page) {
                $page_id = get_the_ID();
                $page_post = get_post($page_id);
                $page_content = isset($page_post->post_content) ? $page_post->post_content : '';
            }
            $is_jobs_elemnt_page = $is_cands_elemnt_page = $is_emps_elemnt_page = false;
            if (strpos($page_content, 'job_short_counter')) {
                $is_jobs_elemnt_page = true;
            }
            if (strpos($page_content, 'candidate_short_counter')) {
                $is_cands_elemnt_page = true;
            }
            if (strpos($page_content, 'employer_short_counter')) {
                $is_emps_elemnt_page = true;
            }

            // Enqueue JS 
            wp_register_script('jobsearch-shortlist-functions-script', plugins_url('assets/js/shortlist-functions.js', __FILE__), '', '', true);
            wp_localize_script('jobsearch-shortlist-functions-script', 'jobsearch_shortlist_vars', array(
                'admin_url' => $admin_ajax_url,
                'plugin_url' => jobsearch_plugin_get_url(),
            ));

            if ($is_page && (has_shortcode($page_content, 'jobsearch_job_shortcode') || $is_jobs_elemnt_page)) {
                wp_enqueue_script('jobsearch-shortlist-functions-script');
            }
            if (is_singular('job')) {
                wp_enqueue_script('jobsearch-shortlist-functions-script');
            }
        }

        /**
         * Member Shortlists Frontend Button
         * @ shortlists frontend buuton based on job id
         */
        public function jobsearch_job_shortlist_button_callback($args = '')
        {
            wp_enqueue_script('jobsearch-shortlist-script');

            $job_id = isset($args['job_id']) ? $args['job_id'] : '';
            $before_icon = isset($args['before_icon']) ? $args['before_icon'] : '';
            $after_icon = isset($args['after_icon']) ? $args['after_icon'] : '';
            $before_label = isset($args['before_label']) ? $args['before_label'] : '';
            $after_label = isset($args['after_label']) ? $args['after_label'] : '';
            $container_class = isset($args['container_class']) ? $args['container_class'] : '';
            $anchor_class = isset($args['anchor_class']) ? $args['anchor_class'] : '';
            $view = isset($args['view']) ? $args['view'] : '';

            if ($anchor_class == '' && $view != 'job_detail') {
                $anchor_class = 'jobsearch-job-like';
            }
            if ($view == 'job_detail_3') {
                if (!is_user_logged_in()) { ?>
                    <a href="javascript:void(0);" class="<?php echo($anchor_class); ?> jobsearch-open-signin-tab"><i
                                class="<?php echo $before_icon; ?>"></i> <?php echo($before_label); ?></a>
                <?php } else {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_fav_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                        $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    }
                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo($after_label); ?>" data-view="job3"
                       class="<?php echo in_array($job_id, $candidate_fav_jobs_list) ? '' : 'jobsearch-add-job-to-favourite' ?> <?php echo($anchor_class); ?>"
                       data-id="<?php echo($job_id); ?>" data-before-icon='<?php echo($before_icon); ?>'
                       data-after-icon='<?php echo($after_icon); ?>'>
                        <i class="<?php echo in_array($job_id, $candidate_fav_jobs_list) ? $before_icon : $after_icon ?>"></i>
                        <?php echo in_array($job_id, $candidate_fav_jobs_list) ? ($after_label) : ($before_label); ?>
                    </a>
                    <span class="job-to-fav-msg-con"></span>

                    <?php
                }
            } elseif ($view == 'job_detail') {
                if (!is_user_logged_in()) { ?>
                    <a href="javascript:void(0);"
                       class="shortlist_job_btn jobsearch-open-signin-tab"><i
                                class="<?php echo $before_icon; ?>"></i><?php echo($before_label); ?></a>
                <?php } else {

                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_fav_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                        $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    }
                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo($after_label); ?>" data-view="job2"
                       class="shortlist_job_btn <?php echo in_array($job_id, $candidate_fav_jobs_list) ? '' : 'jobsearch-add-job-to-favourite' ?>"
                       data-id="<?php echo($job_id); ?>" data-before-icon="<?php echo($before_icon); ?>"
                       data-after-icon="<?php echo($after_icon); ?>">
                        <?php echo '<i class="' . $before_icon . '"></i>';
                        echo in_array($job_id, $candidate_fav_jobs_list) ? ($after_label) : ($before_label); ?>
                    </a>
                    <span class="job-to-fav-msg-con"></span>

                    <?php
                }
            } elseif ($view == 'style9') {
                if (!is_user_logged_in()) { ?>
                    <a href="javascript:void(0);"
                       class="shortlist_job_btn jobsearch-open-signin-tab"><i
                                class="fa fa-heart-o"></i> <?php echo($before_label); ?></a>
                <?php } else {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_fav_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                        $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    }
                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo($after_label); ?>" data-view="style9"
                       class="shortlist_job_btn <?php echo in_array($job_id, $candidate_fav_jobs_list) ? '' : 'jobsearch-add-job-to-favourite' ?>"
                       data-id="<?php echo($job_id); ?>" data-before-icon="<?php echo($before_icon); ?>"
                       data-after-icon="<?php echo($after_icon); ?>">
                        <?php
                        echo in_array($job_id, $candidate_fav_jobs_list) ? '<i class="' . $after_icon . '"></i>' : '<i class="' . $before_icon . '"></i>';
                        echo in_array($job_id, $candidate_fav_jobs_list) ? ($after_label) : ($before_label); ?>

                    </a>
                    <span class="job-to-fav-msg-con"></span>
                <?php } ?>
            <?php } else if ($view == 'job_detail_5') {

                if (!is_user_logged_in()) { ?>
                    <a href="javascript:void(0);" class="<?php echo($anchor_class); ?> jobsearch-open-signin-tab"><i
                                class="<?php echo $before_icon; ?>"></i> <?php echo($before_label); ?></a>
                    <?php
                } else {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_fav_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                        $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    }


                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo($after_label); ?>" data-view="job3"
                       class="<?php echo in_array($job_id, $candidate_fav_jobs_list) ? '' : 'jobsearch-add-job-to-favourite-style-5' ?> <?php echo($anchor_class); ?>"
                       data-id="<?php echo($job_id); ?>" data-before-icon='<?php echo($before_icon); ?>'
                       data-after-icon='<?php echo($after_icon); ?>'>
                        <i class="<?php echo in_array($job_id, $candidate_fav_jobs_list) ? $before_icon : $after_icon ?>"></i>
                        <?php echo in_array($job_id, $candidate_fav_jobs_list) ? ($after_label) : ($before_label); ?>
                    </a>
                    <span class="job-to-fav-msg-con"></span>

                    <?php
                }

            } else {

                if (!is_user_logged_in()) {

                    ?>
                    <div class="like-btn <?php echo($container_class) ?>">
                        <a href="javascript:void(0);"
                           class="shortlist jobsearch-open-signin-tab <?php echo($anchor_class) ?>">
                            <i class="jobsearch-icon jobsearch-heart"></i>
                        </a>
                    </div>
                    <?php
                } else {

                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_fav_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                        $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    }

                    ob_start();
                    ?>
                    <div class="like-btn <?php echo($container_class) ?>">
                        <a href="javascript:void(0);"
                           class="shortlist <?php echo in_array($job_id, $candidate_fav_jobs_list) ? '' : 'jobsearch-add-job-to-favourite' ?> <?php echo($anchor_class) ?>"
                           data-id="<?php echo($job_id) ?>" data-before-icon="<?php echo($before_icon) ?>"
                           data-after-icon="<?php echo($after_icon) ?>">
                            <i class="<?php echo in_array($job_id, $candidate_fav_jobs_list) ? 'fa fa-heart' : 'jobsearch-icon jobsearch-heart' ?>"></i>
                        </a>
                        <span class="job-to-fav-msg-con"></span>
                    </div>
                    <?php
                    $btn_html = ob_get_clean();
                    echo apply_filters('jobsearch_addtofav_shortlist_btn_html', $btn_html, $job_id, $args);
                }
            }
        }

        /**
         * Member Shortlists
         * @ added member shortlists based on job id
         */
        public
        function jobsearch_shortlist_submit_callback()
        {
            if (!is_user_logged_in()) {
                echo json_encode(array('msg' => esc_html__('You are not logged in.', 'wp-jobsearch'), 'error' => '1'));
                die;
            }
            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '0';
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);

                if ($candidate_fav_jobs_list != '') {
                    $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                    if (!in_array($job_id, $candidate_fav_jobs_list)) {
                        $candidate_fav_jobs_list[] = $job_id;
                    }
                    $candidate_fav_jobs_list = implode(',', $candidate_fav_jobs_list);
                } else {
                    $candidate_fav_jobs_list = $job_id;
                }
                update_post_meta($candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list);
                echo json_encode(array('msg' => esc_html__('Job added to the list.', 'wp-jobsearch')));
                die;
            } else {
                $msgva = esc_html__('You are not a candidate.', 'wp-jobsearch');
                $msgva = apply_filters('jobsearch_favjob_cand_notalowerr_msg', $msgva);
                echo json_encode(array('msg' => $msgva, 'error' => '1'));
                die;
            }
        }

        /**
         * Member Removed Shortlist
         * @ removed member shortlists based on job id
         */
        public
        function jobsearch_removed_shortlist_callback()
        {
            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '0';
            $user_id = get_current_user_id();
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
            $candidate_fav_jobs_list = $candidate_fav_jobs_list != '' ? explode(',', $candidate_fav_jobs_list) : array();

            if (!empty($candidate_fav_jobs_list)) {

                if (($key = array_search($job_id, $candidate_fav_jobs_list)) !== false) {
                    unset($candidate_fav_jobs_list[$key]);

                    $candidate_fav_jobs_list = implode(',', $candidate_fav_jobs_list);
                    update_post_meta($candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list);

                    echo json_encode(array('msg' => esc_html__('Job removed.', 'wp-jobsearch')));
                    die;
                }
            }
            echo json_encode(array('error' => '1'));
            die;
        }

    }

    global $jobsearch_shortlist;
    $jobsearch_shortlist = new Jobsearch_Shortlist();
}
