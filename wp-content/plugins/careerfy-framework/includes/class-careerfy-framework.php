<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 */
class Careerfy_framework {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    public static $version;

    //
    public function __construct() {

        $this->plugin_name = 'careerfy-frame';
        self::$version = '5.5.0';
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->load_shortcodes();
        $this->load_widgets();
        $this->load_mega_menu();
        add_action('init', array($this, 'set_plugin_locale'), 0);
    }
    
    public function set_plugin_locale() {
        $this->set_locale();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     * - Careerfy_framework_i18n. Defines internationalization functionality.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for defining options functionality
         * of the plugin.
         */
        include plugin_dir_path(dirname(__FILE__)) . 'envato_setup/envato_setup.php';
        include plugin_dir_path(dirname(__FILE__)) . 'envato_setup/envato_setup_init.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/class-form-fields.php';
        // common functions file
        include plugin_dir_path(dirname(__FILE__)) . 'includes/common-functions.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/careerfy-detail-pages.php';
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/careerfyframe-end-jsfile.php';

        // redux frameworks extension loader files
        include plugin_dir_path(dirname(__FILE__)) . 'admin/redux-ext/loader.php';

        // icons manager
        include plugin_dir_path(dirname(__FILE__)) . 'icons-manager/icons-manager.php';

        // visual composer files
        include plugin_dir_path(dirname(__FILE__)) . 'includes/vc-support/vc-actions.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/vc-support/vc-shortcodes.php';
        // visual icon files
        include plugin_dir_path(dirname(__FILE__)) . 'includes/vc-icons/icons.php';
        // Mailchimp
        include plugin_dir_path(dirname(__FILE__)) . 'includes/mailchimp/vendor/autoload.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/mailchimp/mailchimp-functions.php';

        // post types
        include plugin_dir_path(dirname(__FILE__)) . 'includes/post-types/faq.php';

        // meta box file
        include plugin_dir_path(dirname(__FILE__)) . 'admin/meta-boxes.php';

        // Custom Typography
        include plugin_dir_path(dirname(__FILE__)) . 'includes/custom-typography.php';

        // twitter oauth
        include plugin_dir_path(dirname(__FILE__)) . 'includes/twitter-tweets/twitteroauth.php';
        // maintenace mode
        include plugin_dir_path(dirname(__FILE__)) . 'includes/maintenance-mode/maintenance-mode.php';

        // redux frameworks files
        include plugin_dir_path(dirname(__FILE__)) . 'admin/ReduxFramework/class-redux-framework-plugin.php';
        include plugin_dir_path(dirname(__FILE__)) . 'admin/ReduxFramework/careerfy-options/options-config.php';

        include plugin_dir_path(dirname(__FILE__)) . 'admin/user/user-custom-fields.php';

        // instagram admin actions
        include plugin_dir_path(dirname(__FILE__)) . 'admin/instagram.php';
        // load Elementor Extension
        require plugin_dir_path(dirname(__FILE__)) . 'includes/class-careerfy-elementor.php';

    }

    /**
     * For Demo data redirect.
     *
     */
    public function get_to_demo_data() {
        $demo_data_key = get_option('careerfy_demo_data_install');
        if ($demo_data_key != '1') {
            update_option('careerfy_demo_data_install', '1');
            wp_redirect(admin_url('themes.php?page=careerfy-setup'));
            exit();
        }
    }

    public function load_mega_menu() {
        /**
         * The function responsible for mega menu
         * of the plugin.
         */
        include plugin_dir_path(dirname(__FILE__)) . 'includes/mega-menu/menu-functions.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/mega-menu/custom-walker.php';
    }

    /**
     * Load the shortcodes for this plugin.
     *
     * describe shortcodes markup
     *
     * @since    1.0.0
     * @access   public
     */
    public function load_shortcodes() {

        /**
         * The class responsible for loading shortcodes
         * of the plugin.
         */
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/blog-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/section-heading.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/left-title.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/button-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/advance-search.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/job-categories.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/job-types.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/jobs-simple-listing.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/call-to-action.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/about-company.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/block-text-box.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/simple-block-text.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/find-question.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/image-banner.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/google-map-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/contact-information.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/about-information.php';

        //
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/counters-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/services-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/vid-testimonial-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/simple-employers-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/careerfy-slider-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/image-services.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/our-team.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/testimonials-with-image.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/sign-up-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/recent-questions.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/faqs-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/cv-packages-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/candidate-packages-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/job-packages-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/all-packages-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/our-partners.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/help-links.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/jobs-listing-tabs-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/featured-jobs.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/process-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/candidate-slider-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/top-jobs-slider.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/careerfy-app-promo.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/careerfy-explore-jobs-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/jobs-by-categories.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/jobs-simple-listing-multi.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/promo-stats-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/how-it-works-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/banner-caption-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'shortcodes/vc-shortcodes/careerfy-news-letter-shortcode.php';
    }

    /**
     * Load Widgets.
     *
     * Widgets markup
     *
     * @since    1.0.0
     * @access   public
     */
    public function load_widgets() {

        /**
         * The function responsible for Widgets
         * of the plugin.
         */
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/recent-posts.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/newsletter.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/simple-newsletter.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/flickr.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/instagram.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/twitter.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/contact-info.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/contact-info2.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/contact-info3.php';
        include plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/about-info.php';
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Careerfy_framework_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }
        $locale = apply_filters('plugin_locale', $locale, 'careerfy-frame');
        
        unload_textdomain('careerfy-frame');
        load_textdomain('careerfy-frame', WP_LANG_DIR . '/plugins/careerfy-frame-' . $locale . '.mo');
        load_plugin_textdomain('careerfy-frame', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        add_action('init', array($this, 'image_sizes'), 10, 0);
        add_action('admin_init', array($this, 'get_to_demo_data'), 10);

        add_action('admin_enqueue_scripts', array($this, 'admin_style_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_style_scripts'), 95);

        add_action('add_meta_boxes', 'careerfy_page_header_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_page_subheader_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_page_view_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_page_title_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_page_layout_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_post_layout_meta_boxes');
        add_action('add_meta_boxes', 'careerfy_post_settings_meta_boxes');
    }

    private function define_public_hooks() {
        
    }

    /**
     * Register all of the front styles and scripts
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function front_style_scripts() {
        global $careerfy_framework_options;

        $is_page = is_page();
        $page_content = '';
        if ($is_page) {
            $page_id = get_the_ID();
            $page_post = get_post($page_id);
            $page_content = isset($page_post->post_content) ? $page_post->post_content : '';
        }

        $google_api_key = isset($careerfy_framework_options['careerfy-google-api-key']) ? $careerfy_framework_options['careerfy-google-api-key'] : '';
        $sticky_header = isset($careerfy_framework_options['careerfy-sticky-header']) ? $careerfy_framework_options['careerfy-sticky-header'] : '';

        $is_front_page = 'false';
        if (is_front_page()) {
            $is_front_page = 'true';
        }

        if (class_exists('JobSearch_plugin') && $is_page && (has_shortcode($page_content, 'jobsearch_job_shortcode') || has_shortcode($page_content, 'careerfy_google_map') || has_shortcode($page_content, 'careerfy_advance_search'))) {
            JobSearch_plugin::map_styles_for_header();
        }

        wp_enqueue_style('fancybox', careerfy_framework_get_url('css/fancybox.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('careerfy-slick-slider', careerfy_framework_get_url('css/slick-slider.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('careerfy-mediaelementplayer', careerfy_framework_get_url('build/mediaelementplayer.css'), array(), Careerfy_framework::get_version());
        wp_register_style('careerfy-mapbox-style', 'https://api.tiles.mapbox.com/mapbox-gl-js/v1.6.0/mapbox-gl.css', array(), Careerfy_framework::get_version());
        wp_enqueue_style('careerfy-styles', careerfy_framework_get_url('css/careerfy-styles.css'), array(), Careerfy_framework::get_version());
        wp_register_script('careerfy-counters', careerfy_framework_get_url('js/counter.js'), array('jquery'), Careerfy_framework::get_version(), true);
        wp_register_script('careerfy-slick', careerfy_framework_get_url('js/slick-slider.js'), array('jquery'), Careerfy_framework::get_version(), true);
        wp_register_script('careerfy-mediaelement', careerfy_framework_get_url('build/mediaelement-and-player.js'), array(), Careerfy_framework::get_version(), true);
        wp_enqueue_script('careerfy-slick');
        wp_register_script('careerfy-countdown', careerfy_framework_get_url('js/jquery.countdown.js'), array('jquery'), Careerfy_framework::get_version(), true);

        wp_enqueue_script('careerfy-frame-common', careerfy_framework_get_url('js/careerfy-common.js'), array('jquery'), Careerfy_framework::get_version(), true);
        $careerfy_framework_arr = array(
            'plugin_url' => careerfy_framework_get_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'error_msg' => esc_html__('There is some problem.', 'careerfy-frame'),
            'blank_field_msg' => esc_html__('This field should not be blank.', 'careerfy-frame'),
            'is_sticky' => $sticky_header,
            'is_front_page' => $is_front_page,
        );
        wp_localize_script('careerfy-frame-common', 'careerfy_framework_vars', $careerfy_framework_arr);

        wp_register_script('careerfy-google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places', array(), Careerfy_framework::get_version(), true);
        wp_register_script('careerfy-addthis', 'https://s7.addthis.com/js/250/addthis_widget.js', array(), Careerfy_framework::get_version(), true);
    }

    /**
     * Register all of the admin styles and scripts
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function admin_style_scripts() {
        global $careerfy_framework_options, $pagenow;
        wp_enqueue_style('wp-color-picker');
        
        $jobsearch_post_pages = false;
        if ($pagenow == 'post.php') {
            $the_post_type = get_post_type();
            if ($the_post_type == 'job' || $the_post_type == 'employer' || $the_post_type == 'candidate') {
                $jobsearch_post_pages = true;
            }
        }
        $is_options_page = false;
        if (isset($_GET['page']) && $_GET['page'] == 'jobsearch_plugin_options') {
            $is_options_page = true;
        }
        $theme_options_page = false;
        if (isset($_GET['page']) && $_GET['page'] == 'careerfy_framework_options') {
            $theme_options_page = true;
        }

        $jobsearch_plugin_options = get_option('jobsearch_plugin_options');

        $google_api_key = isset($careerfy_framework_options['careerfy-google-api-key']) ? $careerfy_framework_options['careerfy-google-api-key'] : '';

        if (isset($jobsearch_plugin_options['jobsearch-google-api-key']) && $jobsearch_plugin_options['jobsearch-google-api-key'] != '') {
            $google_api_key = $jobsearch_plugin_options['jobsearch-google-api-key'];
        }

        wp_enqueue_style('font-awesome', careerfy_framework_get_url('icon-picker/css/font-awesome.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('fonticonpicker', careerfy_framework_get_url('icon-picker/font/jquery.fonticonpicker.min.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('fonticonpicker.bootstrap', careerfy_framework_get_url('icon-picker/theme/bootstrap-theme/jquery.fonticonpicker.bootstrap.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('datetimepicker', careerfy_framework_get_url('admin/css/jquery.datetimepicker.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_style('careerfy-frame', careerfy_framework_get_url('admin/css/admin.css'), array(), Careerfy_framework::get_version());
        wp_enqueue_script('fitvideo', careerfy_framework_get_url('js/fitvideo.js'), array('jquery'), Careerfy_framework::get_version());
        // Localize the script
        $careerfy_icons_arr = array(
            'plugin_url' => careerfy_framework_get_url(),
        );
        wp_localize_script('wp-jobsearch-icons', 'icons_vars', $careerfy_icons_arr);
        wp_enqueue_script('careerfy-frame-admin', careerfy_framework_get_url('admin/js/admin.js'), array('jquery'), Careerfy_framework::get_version(), true);
        // Localize the script
        $careerfy_framework_arr = array(
            'plugin_url' => careerfy_framework_get_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'are_you_sure' => __('Are you sure!', 'careerfy-frame'),
            'require_fields' => __('Please fill the required fields.', 'careerfy-frame'),
        );
        wp_localize_script('careerfy-frame-admin', 'careerfy_framework_vars', $careerfy_framework_arr);
        wp_enqueue_script('careerfy-frame-common', careerfy_framework_get_url('js/careerfy-common.js'), array('jquery'), Careerfy_framework::get_version(), true);
        wp_enqueue_script('fonticonpicker', careerfy_framework_get_url('icon-picker/js/jquery.fonticonpicker.min.js'), array(), Careerfy_framework::get_version(), true);
        if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
            wp_enqueue_script('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js', array('jquery'), Careerfy_framework::get_version());
        }
        wp_enqueue_script('datetimepicker', careerfy_framework_get_url('admin/js/jquery.datetimepicker.full.min.js'), array(), Careerfy_framework::get_version(), true);

        wp_register_script('careerfy-google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places', array(), Careerfy_framework::get_version(), true);
        // enqueue style
        // enqueue scripts
        if (!$jobsearch_post_pages && !$is_options_page && !$theme_options_page) {
            wp_enqueue_script('wp-color-picker-alpha', careerfy_framework_get_url('admin/js/wp-color-picker-alpha.min.js'), array('wp-color-picker'), Careerfy_framework::get_version(), true);
        }
    }

    /**
     * Register all image sizes required
     * for the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function image_sizes() {
        add_image_size('careerfy-job-medium', 358, 204, true); // posts for team grid and medium
        add_image_size('careerfy-posts-msmal', 85, 58, true); // posts for team grid and medium
        add_image_size('careerfy-emp-msmal', 132, 47, true); //
        add_image_size('careerfy-candidate-2', 350, 450, true); // posts for team grid and medium
        add_image_size('careerfy-testimonial-thumb', 268, 268, true); // posts for team grid and medium
        add_image_size('careerfy-service', 247, 252, true); // posts for team grid and medium
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public static function get_version() {
        return self::$version;
    }

}

