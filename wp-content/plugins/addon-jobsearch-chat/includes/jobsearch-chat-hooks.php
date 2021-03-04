<?php
if (!class_exists('addon_jobsearch_chat_settings_handle')) {

    class addon_jobsearch_chat_settings_handle
    {
        public $jobsearch_candidate;
        public $jobsearch_employer;
        public $jobsearch_admin;
        public $jobsearch_chat_support;
        public $query;
        public $args;
        public $user_login_flag;
        public $current_user;
        public $get_friends;
        public $result;
        public $emojis;

        public function __construct()
        {
            $this->load_auto_load_files();
            add_action('wp_footer', array($this, 'jobsearch_chat_floating_chat'));
            add_action('admin_menu', array($this, 'jobsearch_chat_settings_create_menu'));
            add_action('jobsearch_chat_with_candidate', array($this, 'start_chat_with_candidate_callback'), 10, 1);
            add_action('jobsearch_chat_with_employer', array($this, 'start_chat_with_employer_callback'), 10, 1);
            //
            add_filter('jobsearch_cand_dash_menu_in_opts', array($this, 'dashboard_cand_menu_items_inopts_arr'), 10, 1);
            add_filter('jobsearch_cand_dash_menu_in_opts_swch', array($this, 'dashboard_cand_menu_items_inopts_swch_arr'), 10, 1);
            add_filter('jobsearch_cand_menudash_link_user_chat_item', array($this, 'dashboard_cand_menu_items_in_fmenu'), 10, 5);
            //
            add_filter('jobsearch_emp_dash_menu_in_opts', array($this, 'dashboard_emp_menu_items_inopts_arr'), 10, 1);
            add_filter('jobsearch_emp_dash_menu_in_opts_swch', array($this, 'dashboard_emp_menu_items_inopts_swch_arr'), 10, 1);
            add_filter('jobsearch_emp_menudash_link_user_chat_item', array($this, 'dashboard_emp_menu_items_in_fmenu'), 10, 5);
            add_filter('jobsearch_dashboard_tab_content_ext', array($this, 'dashboard_tab_content_add'), 10, 2);
            //
            add_filter('jobsearch_api_settings_section', array($this, 'php_pusher_sdk_api_settings'), 999, 1);
            //
            add_action('wp_ajax_wp_jobsearch_get_cand_profile_img_content', array($this, 'wp_jobsearch_get_cand_profile_img_content'));
            add_action('wp_ajax_nopriv_wp_jobsearch_get_cand_profile_img_content', array($this, 'wp_jobsearch_get_cand_profile_img_content'));

            add_filter('jobsearch_cand_dash_side_menulinks_html', array($this, 'jobsearch_cand_dash_side_menulinks_html_callback'), 10, 4);
            $this->save_chatsettings();
            $this->emojis = _wp_emoji_list();
        }

        public function load_auto_load_files()
        {
            include plugin_dir_path(dirname(__FILE__)) . '/vendor/autoload.php';
        }

        public function jobsearch_cand_dash_side_menulinks_html_callback($menu_items_html, $get_tab, $page_url, $candidate_id)
        {
            $this->current_user = wp_get_current_user();
            if ($this->current_user->roles[0] == 'jobsearch_chat_support') {
                $menu_items_html = '';
            }
            return $menu_items_html;
        }

        public function jobsearch_chat_floating_chat()
        {
            $this->current_user = wp_get_current_user();
            if (!is_user_logged_in() || $this->current_user->roles[0] == 'administrator' || (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'emp-chat') || (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'cand-chat')) {
                return;
            }
            include dirname(__FILE__) . '/front/jobsearch-chat-toggle-view.php';
        }

        public function get_user_last_msg_info($id)
        {
            global $wpdb;
            $this->current_user = wp_get_current_user();
            $this->query = "select * from {$wpdb->prefix}chatmessages where sender_id = {$id} and reciever_id = {$this->current_user->ID}";
            return $wpdb->get_results($this->query);
        }

        public function get_fiends_list()
        {
            global $wpdb;
            $this->current_user = wp_get_current_user();
            //if no user is logged in
            if ($this->current_user->ID != 0) {
                if (jobsearch_user_is_candidate($this->current_user->ID)) {
                    $this->query = "select employer_id,is_active,f_id  from {$wpdb->prefix}chatfriendlist where candidate_id = {$this->current_user->ID}";
                }
                //
                if (jobsearch_user_is_employer($this->current_user->ID)) {
                    $this->query = "select candidate_id,is_active,f_id from {$wpdb->prefix}chatfriendlist where employer_id = {$this->current_user->ID}";
                }
                return $wpdb->get_results($this->query);
            }
        }

        public function limit_text($text, $limit)
        {
            if (str_word_count($text, 0) > $limit) {
                $words = str_word_count($text, 2);
                $pos = array_keys($words);
                $text = substr($text, 0, $pos[$limit]) . '...';
            }
            return $text;
        }

        public function get_day_name_and_time($timestamp)
        {
            $date = date('d/m/Y', strtotime($timestamp));
            if ($date == date('d/m/Y')) {
                $date = 'Today at ' . date('g:ia', strtotime($timestamp));
            } else if ($date == date('d/m/Y', strtotime("-1 days"))) {
                $date = 'Yesterday at ' . date('g:ia', strtotime($timestamp));
            } else {
                $date = date('g:ia l,F Y', strtotime($timestamp));
            }
            return $date;
        }

        public function get_unviewed_messages($reciever_id)
        {
            global $wpdb;
            $current_user = get_current_user_id();
            $this->query = "SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$reciever_id} AND reciever_id = {$current_user} and viewed = 0";
            return $wpdb->get_results($this->query);
        }

        public function jobsearch_chat_settings_create_menu()
        {
            global $current_user;
            if (in_array($current_user->roles[0], array('jobsearch_chat_support', 'administrator'))) {
                add_menu_page(esc_html__('Chat Box', 'jobsearch-ajchat'), esc_html__('Chat Box', 'jobsearch-ajchat'), $current_user->roles[0], 'jobsearch-chat-box', function () {
                    include dirname(__FILE__) . '/backend/jobsearch-chat-backend-view.php';
                }, '', 20);
            }
            //
            add_submenu_page(
                'jobsearch-chat-box',
                esc_html__('Chat Box Settings', 'jobsearch-ajchat'), //page title
                esc_html__('Chat Box Settings', 'jobsearch-ajchat'), //menu title
                'administrator',
                'jobsearch-chat-box-settings',
                array($this, 'jobsearch_chat_settings_editor')
            );
        }

        public function jobsearch_chat_settings_editor()
        {
            include dirname(__FILE__) . '/backend/jobsearch-chat-backend-settings.php';
        }

        public function save_chatsettings()
        {
            if (isset($_POST['jobsearch_chat_setingsubmit'])) {
                $data_arr_list = array();
                foreach ($_POST as $post_key => $post_val) {
                    $data_arr_list[$post_key] = $post_val;
                }
                update_option('jobsearch_chat_settings', $data_arr_list);

            }
        }

        public function dashboard_cand_menu_items_inopts_arr($opts_arr = array())
        {
            $opts_arr['user_chat'] = __('Chat', 'wp-jobsearch');
            return $opts_arr;
        }

        public function dashboard_cand_menu_items_inopts_swch_arr($opts_arr = array())
        {
            $opts_arr['user_chat'] = true;
            return $opts_arr;
        }

        public function dashboard_cand_menu_items_in_fmenu($opts_item = '', $cand_menu_item, $get_tab, $page_url, $candidate_id)
        {
            $jobsearch__options = get_option('jobsearch_plugin_options');

            ob_start();
            $dashmenu_links_cand = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
            $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);

            $link_item_switch = isset($dashmenu_links_cand['user_chat']) ? $dashmenu_links_cand['user_chat'] : '';

            if ($cand_menu_item == 'user_chat' && $link_item_switch == '1') { ?>
                <li<?php echo($get_tab == 'user_chat' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'cand-chat'), $page_url) ?>">
                        <i class="chat-icon chat-bubble-line"></i>
                        <?php esc_html_e('Message / Chat', 'jobsearch-ajchat') ?>
                    </a>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
            return $opts_item;
        }

        public function dashboard_emp_menu_items_inopts_arr($opts_arr = array())
        {
            $opts_arr['user_chat'] = __('Chat', 'wp-jobsearch');
            return $opts_arr;
        }

        public function dashboard_emp_menu_items_inopts_swch_arr($opts_arr = array())
        {
            $opts_arr['user_chat'] = true;
            return $opts_arr;
        }

        public function dashboard_emp_menu_items_in_fmenu($opts_item = '', $emp_menu_item, $get_tab, $page_url, $emp_id)
        {
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $dashmenu_links_empp = isset($jobsearch__options['emp_dashbord_menu']) ? $jobsearch__options['emp_dashbord_menu'] : '';

            ob_start();
            $link_item_switch = isset($dashmenu_links_empp['user_chat']) ? $dashmenu_links_empp['user_chat'] : '';
            if ($emp_menu_item == 'user_chat' && $link_item_switch == '1') { ?>
                <li<?php echo($get_tab == 'user_chat' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'emp-chat'), $page_url) ?>">
                        <i class="chat-icon chat-bubble-line"></i>
                        <?php esc_html_e('Message / Chat', 'jobsearch-ajchat') ?>
                    </a>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
            return $opts_item;
        }

        public function start_chat_with_candidate_callback($args = array())
        {
            $logged_in_user_id = get_current_user_id();
            $employer_id = jobsearch_user_is_employer($logged_in_user_id) == true ? jobsearch_get_user_employer_id($logged_in_user_id) : '';
            $employer_user_id = jobsearch_user_is_employer($logged_in_user_id) == true ? $logged_in_user_id : '';
            $jobsearch_chat_settings = get_option('jobsearch_chat_settings');
            $u_jobsearch_chat_settings_emp = isset($jobsearch_chat_settings['cusjob_chat_disable_for_emp']) ? $jobsearch_chat_settings['cusjob_chat_disable_for_emp'] : '';
            $candidate_id = isset($args['candidate_id']) ? $args['candidate_id'] : '';
            $class = isset($args['class']) ? $args['class'] : '';
            $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
            $cand_jobsearch_chat_settings = get_option('jobsearch_chat_chat_status_' . $cand_user_id);

            $is_emp_applicant = jobsearch_is_employer_job_aplicant($candidate_id, $employer_id);
            //
            $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
            $employer_resumes_list = explode(',', $employer_resumes_list);
            $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($employer_user_id);
            if (!$user_cv_pkg) {
                $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($employer_user_id, 'cvs');
            }
            //
            $jobsearch_chat_settings = get_option('jobsearch_chat_settings');

            $add_to_chat_class = '';
            if ($this->check_if_candidate_exists($candidate_id) == 1) {
                $add_to_chat_class = 'jobsearch-chat-candidate-openbox';
            } else if ($is_emp_applicant) {

                $add_to_chat_class = 'jobsearch-chat-candidate-add';
            } else if (in_array($candidate_id, $employer_resumes_list)) {
                $add_to_chat_class = 'jobsearch-chat-candidate-add';
            } else {
                if ($jobsearch_chat_settings['chat_pkg'] == 'free') {
                    if (!in_array($candidate_id, $employer_resumes_list)) {
                        $employer_resumes_list[] = $candidate_id;
                    }
                    $employer_resumes_list = implode(',', $employer_resumes_list);
                    update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
                    $add_to_chat_class = 'jobsearch-chat-candidate-add';

                } else {
                    if ($user_cv_pkg) {
                        if (!in_array($candidate_id, $employer_resumes_list)) {
                            $employer_resumes_list[] = $candidate_id;
                        }
                        $employer_resumes_list = implode(',', $employer_resumes_list);
                        update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
                        $add_to_chat_class = 'jobsearch-chat-candidate-add';
                    } else {
                        $add_to_chat_class = 'jobsearch-open-dloadres-popup';
                    }
                }
            }
            //
            if (is_user_logged_in()) {
                if ((jobsearch_user_is_employer($logged_in_user_id) == true && $u_jobsearch_chat_settings_emp == 'off') ||  $u_jobsearch_chat_settings_emp == '') {
                    if ($cand_jobsearch_chat_settings != 1) { ?>
                        <div class="jobsearch-chat-start-btn <?php echo($class) ?>">
                            <a href="javascript:void(0)" data-cand-id="<?php echo($candidate_id) ?>"
                               data-id="<?php echo($candidate_id) ?>"
                               data-cand-user-id="<?php echo jobsearch_get_candidate_user_id($candidate_id) ?>"
                               class="<?php echo($add_to_chat_class) ?>">
                                <i class="chat-icon chat-bubble"></i>
                                <small><?php esc_html_e('Send Message', 'jobsearch-ajchat') ?></small>
                            </a>
                        </div>
                        <?php
                    }
                }
            }
        }

        public function start_chat_with_employer_callback($args = array())
        {
            $employer_id = isset($args['employer_id']) ? $args['employer_id'] : '';

            $class = isset($args['class']) ? $args['class'] : '';
            $jobsearch_chat_settings = get_option('jobsearch_chat_settings');
            $u_jobsearch_chat_settings_cand = isset($jobsearch_chat_settings['cusjob_chat_disable_for_cand']) ? $jobsearch_chat_settings['cusjob_chat_disable_for_cand'] : '';

            if (is_user_logged_in()) {
                $logged_in_user_id = get_current_user_id();
                if ((jobsearch_user_is_candidate($logged_in_user_id) && $u_jobsearch_chat_settings_cand == 'off') || $u_jobsearch_chat_settings_cand == '') {
                    $emp_user_id = jobsearch_get_employer_user_id($employer_id);
                    $emp_jobsearch_chat_settings = get_option('jobsearch_chat_chat_status_' . $emp_user_id);
                    if ($emp_jobsearch_chat_settings != 1) { ?>
                        <div class="jobsearch-chat-start-btn <?php echo($class) ?>">
                            <a href="javascript:void(0)" data-emp-id="<?php echo($employer_id) ?>"
                               data-emp-user-id="<?php echo jobsearch_get_employer_user_id($employer_id) ?>"
                               class="<?php echo $this->check_if_employer_exists($employer_id) != 1 ? 'jobsearch-chat-emp-add' : 'jobsearch-chat-employer-openbox' ?>">
                                <i class="chat-icon chat-bubble"></i>
                                <small><?php esc_html_e('Send Message', 'jobsearch-ajchat') ?></small>
                            </a>
                        </div>
                    <?php }
                }
            }
        }

        public function check_if_candidate_exists($candidate_id)
        {
            global $wpdb;
            $current_user = get_current_user_id();
            $this->result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatfriendlist WHERE employer_id = {$current_user} and candidate_id = " . jobsearch_get_candidate_user_id($candidate_id) . " ");
            return count($this->result);
        }

        public function check_if_employer_exists($employer_id)
        {
            global $wpdb;
            $current_user = get_current_user_id();
            $emp_user_id = jobsearch_get_employer_user_id($employer_id);

            $this->query = "SELECT * FROM {$wpdb->prefix}chatfriendlist WHERE employer_id = {$emp_user_id} and candidate_id = {$current_user}";
            $this->result = $wpdb->get_results($this->query);
            return count($this->result);
        }

        public function php_pusher_sdk_api_settings($section_settings = array())
        {
            if (isset($section_settings['fields'])) {
                // PHP pusher
                $section_settings['fields'][] = array(
                    'id' => 'php-pusher-section',
                    'type' => 'section',
                    'title' => __('Chat Pusher API settings section.', 'jobsearch-ajchat'),
                    'indent' => true,
                );
                //
                $section_settings['fields'][] = array(
                    'id' => 'jobsearch-php-pusher-app-id',
                    'type' => 'text',
                    'transparent' => false,
                    'title' => __('App ID', 'wp-jobsearch'),
                    'subtitle' => __('Please enter the app id of your chat Pusher app.', 'jobsearch-ajchat'),
                    'desc' => '',
                    'default' => ''
                );
                //
                $section_settings['fields'][] = array(
                    'id' => 'jobsearch-php-pusher-auth-key',
                    'type' => 'text',
                    'transparent' => false,
                    'title' => __('Auth Key', 'wp-jobsearch'),
                    'subtitle' => __('Please enter the auth key of your chat Pusher app.', 'jobsearch-ajchat'),
                    'desc' => '',
                    'default' => ''
                );
                $section_settings['fields'][] = array(
                    'id' => 'jobsearch-php-pusher-auth-secret',
                    'type' => 'text',
                    'transparent' => false,
                    'title' => __('Auth Secret', 'wp-jobsearch'),
                    'subtitle' => __('Please enter the auth secret of your chat Pusher app.', 'jobsearch-ajchat'),
                    'desc' => '',
                    'default' => ''
                );
                $section_settings['fields'][] = array(
                    'id' => 'jobsearch-php-pusher-auth-cluster',
                    'type' => 'text',
                    'transparent' => false,
                    'title' => __('cluster Secret', 'wp-jobsearch'),
                    'subtitle' => __('Please enter the app cluster of your chat Pusher app.', 'jobsearch-ajchat'),
                    'desc' => '',
                    'default' => ''
                );

            }
            return $section_settings;
        }

        public function wp_jobsearch_get_cand_profile_img_content()
        {
            $candidate_id = isset($_GET['cand_id']) ? $_GET['cand_id'] : '';
            $size = isset($_GET['dimen']) ? $_GET['dimen'] : '';
            $error_page_url = home_url('/404_error');

            if (get_post_type($candidate_id) == 'candidate') {

                $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);
                if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {

                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    WP_Filesystem();
                    global $wp_filesystem;

                    $folder_path = $user_avatar_dburl['file_path'];
                    $user_def_avatar_url = isset($user_avatar_dburl['orig_file_url']) ? $user_avatar_dburl['orig_file_url'] : '';

                    $file_name = $user_avatar_dburl['file_name'];
                    $filetype = $user_avatar_dburl['mime_type'];
                    $file_mimetype = $filetype['type'];
                    $file_ext = $filetype['ext'];
                    if (!$file_ext) {
                        $file_ext = 'jpg';
                    }

                    if ($attach_size == 'full') {
                        $file_path = $folder_path . '/' . $file_name;
                        if ($user_def_avatar_url != '') {
                            $file_path = str_replace(get_site_url() . '/', ABSPATH, $user_def_avatar_url);
                        }
                    } else {
                        $file_path = $folder_path . '/user-img-150.' . $file_ext;
                        if ($user_def_avatar_url != '') {
                            $orig_file_path = str_replace(get_site_url() . '/', ABSPATH, $user_def_avatar_url);
                            $file_path = str_replace($file_name, 'user-img-150.' . $file_ext, $orig_file_path);
                        }
                    }
                }

                if (isset($file_path)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: ' . $file_mimetype);
                    header('Content-Dispositon: attachment; filename="' . basename($file_path) . '"');
                    header('Content-Transfer-Encoding: Binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . @filesize($file_path));

                    ob_clean();
                    flush();
                    @readfile($file_path);
                    exit;
                }
            } else {
                wp_redirect($error_page_url);
                exit;
            }
            die;
        }

        public function jobsearch_cand_img_url_wout_encode($candidate_id, $dimen = '150')
        {
            $user_id = jobsearch_get_candidate_user_id($candidate_id);
            $user_gravatar_url = get_avatar_url($user_id, array('size' => 132));
            $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);

            $user_def_avatar_url = '';
            if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
                $user_img_name = $user_avatar_dburl['file_name'];
                $user_img_path = $user_avatar_dburl['file_path'];
                $img_full_path = $user_img_path . '/' . $user_img_name;
                $user_def_avatar_url = isset($user_avatar_dburl['orig_file_url']) ? $user_avatar_dburl['orig_file_url'] : '';
                if ($user_def_avatar_url != '') {
                    $img_full_path = str_replace(get_site_url() . '/', ABSPATH, $user_def_avatar_url);
                }
                if (file_exists($img_full_path)) {
                    $user_def_avatar_url = esc_url(wp_nonce_url(add_query_arg(array('action' => 'wp_jobsearch_get_cand_profile_img_content', 'cand_id' => $candidate_id, 'dimen' => $dimen), admin_url('admin-ajax.php')), 'jobsearch_cand_img_nonce', '_cand_img_nonce'));
                } else {
                    $user_def_avatar_url = '';
                }
            } else {
                $user_avatar_id = get_post_thumbnail_id($candidate_id);
                if ($user_avatar_id > 0) {
                    $user_has_cimg = true;
                    $def_img_size = 'thumbnail';
                    $def_img_size = apply_filters('jobsearch_cand_dashside_pimg_size', $def_img_size);
                    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, $def_img_size);
                    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                }
            }
            if ($user_def_avatar_url == '') {
                $user_def_avatar_url = $user_gravatar_url != '' ? $user_gravatar_url : jobsearch_candidate_image_placeholder();
            }
            return $user_def_avatar_url;
        }

        public function dashboard_tab_content_add($html = '', $get_tab = '')
        {
            //Chat front Full view for emplyer and candidate
            global $jobsearch_plugin_options;
            $user_id = get_current_user_id();
            wp_enqueue_style('jobsearch-chat-app');
            wp_enqueue_script('jobsearch-selectize');
            //
            $php_pusher_auth = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-key']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-key'] : '';
            $php_pusher_secret = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-secret']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-secret'] : '';
            $php_pusher_app_id = isset($jobsearch_plugin_options['jobsearch-php-pusher-app-id']) ? $jobsearch_plugin_options['jobsearch-php-pusher-app-id'] : '';
            //
            if ($get_tab == 'emp-chat' || $get_tab == 'cand-chat') {
                $this->args = array(
                    'role' => 'administrator',
                    'orderby' => 'user_nicename',
                    'order' => 'ASC',
                );
                $this->jobsearch_admin = get_users($this->args);
                //
                $this->args = array(
                    'role' => 'jobsearch_chat_support',
                    'orderby' => 'user_nicename',
                    'order' => 'ASC',
                );
                $this->jobsearch_chat_support = get_users($this->args);

                //
                $user_id = get_current_user_id();

                if (jobsearch_user_is_employer($user_id)) {
                    $employer_user_id = $user_id;
                    $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                    $current_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                    $user_avatar_id = get_post_thumbnail_id($employer_id);
                    if ($user_avatar_id > 0) {
                        $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                        $current_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                    } else {
                        $current_user_def_avatar_url = jobsearch_employer_image_placeholder();
                    }
                } else {
                    $cand_user_id = $user_id;
                    $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                    $current_user_def_avatar_url = $this->jobsearch_cand_img_url_wout_encode($candidate_user_id);
                }
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery('.chat-selectize').selectize({
                            sortField: 'text'
                        });
                    });
                </script>

                <div id="jobsearch-chat-container" class="jobsearch-chat-main-container jobsearch-chat-front-full">
                    <div class="jobsearch-profile-title jobsearch-chat-heading">
                        <?php if (empty($php_pusher_auth) || empty($php_pusher_secret) || empty($php_pusher_app_id)) { ?>
                            <h2><?php esc_html_e('Please add API credentials in Jobsearch options, API settings in order to use chat.', 'wp-jobsearch') ?></h2>
                            <?php return;
                        } ?>
                        <h2><?php esc_html_e('Messages', 'wp-jobsearch') ?></h2>
                    </div>
                    <aside>
                        <ul class="nav nav-tabs jobsearch-chat-nav">
                            <li class="active">
                                <a href="javascript:void(0)" data-list="jobsearch-chat-user-employer"
                                   class="jobsearch-chat-list"><?php echo esc_html__('All', 'jobsearch-ajchat') ?></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" data-list="jobsearch-chat-user-candidate"
                                   class="jobsearch-chat-list"><?php echo esc_html__(jobsearch_user_is_employer($user_id) ? 'Candidate' : 'Employer', 'jobsearch-ajchat') ?></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" data-list="jobsearch-chat-user-groups"
                                   class="jobsearch-chat-list"><?php echo esc_html__('Groups', 'jobsearch-ajchat') ?></a>
                            </li>
                        </ul>
                        <div class="jobsearch-chat-sort-list">
                            <div class="jobsearch-chat-selectize">
                                <select class="chat-selectize sort-contacts">
                                    <option value="0"><?php echo esc_html__('Sort Contacts', 'jobsearch-ajchat') ?></option>
                                    <option value="sort_by_recent"><?php echo esc_html__('Sort by recent', 'jobsearch-ajchat') ?></option>
                                    <option value="sort_by_unread"><?php echo esc_html__('Sort by unread', 'jobsearch-ajchat') ?></option>
                                </select>
                            </div>
                            <div class="jobsearch-chat-filter-wrapper">
                                <div class="jobsearch-chat-filter-input">
                                    <a href="javascript:void(0)" class="jobsearch-chat-filter-toggle"><i
                                                class="chat-icon chat-search"></i></a>
                                </div>
                                <div class="jobsearch-chat-filter-input-field">
                                    <input type="text" name="filter-name" id="search-field-front-full-view"
                                           data-user-type="emp"
                                           placeholder="<?php echo esc_html__('Search', 'jobsearch-ajchat') ?>">
                                    <i class="jobsearch-chat-filter-toggle chat-icon chat-close"></i>
                                </div>
                            </div>
                        </div>
                        <div class="jobsearch-chat-user-list">
                            <input type="hidden" name="employer-user-offset" value="7">
                            <input type="hidden" name="candidate-user-offset" value="7">
                            <input type="hidden" name="scrollTop-emp" value="600">
                            <input type="hidden" name="scrollTop-cand" value="600">
                            <div class="jobsearch-chat-users-list jobsearch-chat-user-employer">
                                <ul>
                                    <?php
                                    $user_def_avatar_url = '';
                                    if (count($this->jobsearch_chat_support) > 0) {
                                        foreach ($this->jobsearch_chat_support as $admin) {
                                            $total_msgs = $this->get_unviewed_messages($admin->ID);
                                            $user_img = get_avatar_url($admin->ID);
                                            $user_msgs = $this->get_user_last_msg_info($admin->ID);
                                            $last_msg = end($user_msgs);
                                            ?>
                                            <li class="jobsearch-chat-user-<?php echo($admin->ID) ?>">
                                                <img src="<?php echo($user_img) ?>">
                                                <span class="status status-with-thumb orange"></span>
                                                <div class="jobsearch-load-user-chat user-info"
                                                     data-user-chat="<?php echo($admin->ID) ?>">
                                                    <h2 class="name"><?php echo($admin->display_name) ?>
                                                        (<?php echo esc_html__('Support', 'jobsearch-ajchat') ?>)
                                                        <small><?php echo isset($last_msg) && !empty($last_msg) ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                                    </h2>
                                                    <p> <?php echo isset($last_msg) && !empty($last_msg) ? $this->limit_text($last_msg->message, 5) : '' ?>
                                                        <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                                    </p>
                                                </div>
                                            </li>
                                        <?php }
                                    }
                                    //
                                    if (count($this->jobsearch_admin) > 0) {
                                        foreach ($this->jobsearch_admin as $admin) {
                                            $total_msgs = $this->get_unviewed_messages($admin->ID);
                                            $user_img = get_avatar_url($admin->ID);
                                            $user_msgs = $this->get_user_last_msg_info($admin->ID);
                                            $last_msg = end($user_msgs);
                                            ?>
                                            <li class="jobsearch-chat-user-<?php echo($admin->ID) ?>">
                                                <img src="<?php echo($user_img) ?>">
                                                <span class="status status-with-thumb orange"></span>
                                                <div class="jobsearch-load-user-chat user-info"
                                                     data-user-chat="<?php echo($admin->ID) ?>">
                                                    <h2 class="name"><?php echo($admin->display_name) ?>
                                                        <small><?php echo isset($last_msg) && !empty($last_msg) ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                                    </h2>
                                                    <p> <?php echo isset($last_msg) && !empty($last_msg) ? $this->limit_text($last_msg->message, 5) : '' ?>
                                                        <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                                    </p>
                                                </div>
                                            </li>
                                        <?php }
                                    }
                                    //
                                    foreach ($this->get_fiends_list() as $friends) {
                                        $user_data = isset($friends->candidate_id) ? get_userdata($friends->candidate_id) : get_userdata($friends->employer_id);
                                        if (isset($friends->employer_id)) {
                                            $employer_user_id = $friends->employer_id;
                                            $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                                            $user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                                            $user_avatar_id = get_post_thumbnail_id($employer_id);
                                            if ($user_avatar_id > 0) {
                                                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                            } else {
                                                $user_def_avatar_url = jobsearch_employer_image_placeholder();
                                            }
                                            $user_displayname = isset($user_data->display_name) ? $user_data->display_name : '';
                                        } else {
                                            $cand_user_id = $friends->candidate_id;
                                            $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                                            $user_def_avatar_url = $this->jobsearch_cand_img_url_wout_encode($candidate_user_id);

                                            $user_data = get_user_by('ID', $friends->candidate_id);
                                            $user_id = isset($user_data->ID) ? $user_data->ID : '';
                                            $user_displayname = isset($user_data->display_name) ? $user_data->display_name : '';
                                            $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_data);
                                        }

                                        $total_msgs = $this->get_unviewed_messages($user_id);
                                        $user_msgs = $this->get_user_last_msg_info($user_id);
                                        $last_msg = end($user_msgs);
                                        ?>
                                        <li class="jobsearch-chat-user-<?php echo($user_id) ?>">
                                            <img src="<?php echo($user_def_avatar_url) ?>">
                                            <div class="jobsearch-chat-user-events">
                                                <a href="javascript:void(0)"><i
                                                            class="chat-icon chat-clock-fill"></i></a>
                                                <a href="javascript:void(0)"><i
                                                            class="chat-icon chat-heart-o"></i></a>
                                                <a href="javascript:void(0)" class="jobsearch-chat-delete-user"
                                                   data-selector-id="<?php echo($user_id) ?>"
                                                   data-user-id="<?php echo($friends->f_id) ?>"><i
                                                            class="chat-icon chat-trash-fill"></i></a>
                                            </div>
                                            <span class="status status-with-thumb orange"></span>
                                            <div class="jobsearch-load-user-chat name"
                                                 data-user-chat="<?php echo($user_id) ?>">
                                                <h2><?php echo($user_displayname) ?>
                                                    <small><?php echo isset($last_msg) && !empty($last_msg) ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                                </h2>
                                                <p><?php echo isset($last_msg) && !empty($last_msg) ? $this->limit_text($last_msg->message, 5) : '' ?>
                                                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                                </p>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="jobsearch-chat-users-list jobsearch-chat-user-candidate hidden">
                                <ul>
                                    <?php foreach ($this->get_fiends_list() as $friends) {
                                        $user_data = isset($friends->candidate_id) ? get_userdata($friends->candidate_id) : get_userdata($friends->employer_id);
                                        if (isset($friends->employer_id)) {
                                            $employer_user_id = $friends->employer_id;
                                            $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                                            $user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                                            $user_avatar_id = get_post_thumbnail_id($employer_id);
                                            if ($user_avatar_id > 0) {
                                                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                            } else {
                                                $user_def_avatar_url = jobsearch_employer_image_placeholder();
                                            }
                                            $user_displayname = $user_data->display_name;
                                        } else {
                                            $cand_user_id = $friends->candidate_id;
                                            $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                                            $user_def_avatar_url = $this->jobsearch_cand_img_url_wout_encode($candidate_user_id);
                                            //
                                            $user_data = get_user_by('ID', $friends->candidate_id);
                                            $user_id = isset($user_data->ID) ? $user_data->ID : '';
                                            $user_displayname = isset($user_data->display_name) ? $user_data->display_name : '';
                                            $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_data);
                                        }
                                        //
                                        $total_msgs = $this->get_unviewed_messages($user_id);
                                        $user_msgs = $this->get_user_last_msg_info($user_id);
                                        $last_msg = end($user_msgs);
                                        ?>
                                        <li class="jobsearch-chat-user-<?php echo($user_id) ?>">
                                            <img src="<?php echo($user_def_avatar_url) ?>">
                                            <div class="jobsearch-chat-user-events">
                                                <a href="javascript:void(0)"><i
                                                            class="chat-icon chat-clock-fill"></i></a>
                                                <a href="javascript:void(0)"><i
                                                            class="chat-icon chat-heart-o"></i></a>
                                                <a href="javascript:void(0)"><i
                                                            class="chat-icon chat-trash-fill"></i></a>
                                            </div>
                                            <span class="status status-with-thumb orange"></span>
                                            <div class="user-info">
                                                <h2 class="jobsearch-load-user-chat name"
                                                    data-user-chat="<?php echo($user_id) ?>"><?php echo($user_displayname) ?>
                                                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                                </h2>
                                                <p><?php echo $last_msg != '' ? $this->limit_text($last_msg->message, 5) : '' ?>
                                                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                                </p>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="jobsearch-chat-users-list jobsearch-chat-user-groups hidden">
                                <ul>
                                    <li class="feature-coming-soon">
                                        <span class="jobsearch-chat-group-title "><?php echo esc_html__('Group Conversation', 'jobsearch-ajchat') ?></span>
                                        <a href="javascript:void(0)">
                                            <span><img src="<?php echo plugin_dir_url(__DIR__) . '/img/group-icon.png'; ?>"></span>
                                            <small><?php echo esc_html__('Create New Group', 'jobsearch-ajchat') ?></small>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                    <div class="jobsearch-user-chat-content jobsearch-user-chat-messages hidden">
                        <div class="jobsearch-user-chat-header">
                            <div class="jobsearch-user-detail">
                                <img src="#" alt="">
                                <span class="status status-with-thumb orange"></span>
                                <div class="jobsearch-user-status-wrapper">
                                    <h2></h2>
                                    <span><?php echo esc_html__('Offline', 'jobsearch-ajchat') ?></span>
                                </div>
                            </div>
                            <div class="jobsearch-user-file-content">
                                <ul>
                                    <li><a href="javascript:void(0)" class="feature-coming-soon"><i
                                                    class="chat-icon chat-icon-plus-circle"></i></a></li>
                                    <li><a href="javascript:void(0)" class="feature-coming-soon"><i
                                                    class="chat-icon chat-icon-ellipsis-v"></i></a></li>
                                </ul>
                            </div>
                        </div>

                        <ul id="chat" class="jobsearch-chat-messages-list" onscroll="loadMessages(this)"></ul>

                        <div class="jobsearch-chat-form-wrapper">
                            <form class="jobsearch-chat-form" method="post" enctype="multipart/form-data">
                                <textarea placeholder="Type your message" name="message"></textarea>
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" class="typing" name="typing" value="false">
                                <input type="hidden" name="sender_image"
                                       value="<?php echo($current_user_def_avatar_url) ?>">
                                <input type="hidden" name="receiver_image" value="">
                                <input type="file" class="jobsearch-chat-share-file hidden" value="sharefile">
                                <div class="jobsearch-chat-share-file-wrapper">
                                    <div class="jobsearch-chat-emojis-box">
                                        <?php foreach ($this->emojis as $key => $info) {
                                            if ($key >= 1777 && $key <= 1821) {
                                                echo '<span class="jobsearch-emoji" data-val="' . $info . '">' . $info . '</span>';
                                            }
                                        } ?>
                                    </div>
                                    <div class="jobsearch-chat-share-file">
                                        <a href="javascript:void(0)" class="jobsearch-tooltipcon"
                                           title="<?php echo esc_html__('Upload File', 'jobsearch-ajchat') ?>"
                                           onclick="triggerFile()"><i class="chat-icon chat-link"></i></a>
                                        <a href="javascript:void(0)"
                                           class="jobsearch-chat-emoji-picker-select-full-view"><i
                                                    class="chat-icon chat-smile"></i></a>
                                    </div>
                                    <div class="jobsearch-chat-typing-wrapper">
                                    <span class="jobsearch-chat-user-typing bounce">
                                         <img src="<?php echo plugin_dir_url(__DIR__) . '../img/userIsTyping.gif'; ?>"/>
                                    </span>
                                        <input type="submit" class="jobsearch-chat-send-message"
                                               value="<?php echo esc_html__('Send', 'jobsearch-ajchat') ?>">
                                    </div>
                                </div>
                            </form>
                            <form hidden enctype="multipart/form-data" name="fileForm" method="post"
                                  onsubmit="uploadFileForm(this);return false;">
                                <input type="file" class="file" name="userfile" required/>
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="sender_image"
                                       value="<?php echo($current_user_def_avatar_url) ?>">
                                <input type="hidden" name="receiver_image" value="">
                                <input type="hidden" name="action" value="uploadFile">
                                <input type="submit" name="submit" class="jobsearch-chat-save" value="save"/>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                $html .= ob_get_clean();
                return $html;
            }
        }
    }
}
//
global $jobsearch_chat_settings;
$jobsearch_chat_settings = new addon_jobsearch_chat_settings_handle();