<?php

/**
 * Careerfy functions and definitions.
 *
 * @package Careerfy
 */
if (!function_exists('careerfy_setup')) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function careerfy_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Careerfy, use a find and replace
         * to change 'careerfy' to the name of your theme in all the template files.
         */
        load_theme_textdomain('careerfy');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'primary' => esc_html__('Primary', 'careerfy'),
            'footer' => esc_html__('Footer', 'careerfy'),
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('careerfy_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        /*
         * This theme styles the visual editor to resemble the theme style,
         * specifically font, colors, icons, and column width.
         */
        add_editor_style(array('css/editor-style.css', careerfy_google_fonts_url()));

        add_theme_support('editor-style');

        // add custom image sizes

        add_image_size('careerfy-img1', 364, 214, true); // blog grid, blog list
        add_image_size('careerfy-img2', 825, 430, true); // blog detail
        add_image_size('careerfy-small-thumb', 112, 70, true); // blog detail
        add_image_size('careerfy-img4', 255, 202, true); // blog related
        add_image_size('careerfy-medium', 175, 347, true); // blog medium related
        add_image_size('careerfy-view6', 300, 225, true); // blog for view4
    }

endif;
add_action('after_setup_theme', 'careerfy_setup');

/**
 * Theme Config File.
 */
require trailingslashit(get_template_directory()) . 'inc/config.php';

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function careerfy_content_width() {
    $GLOBALS['content_width'] = apply_filters('careerfy_content_width', 980);
}

add_action('after_setup_theme', 'careerfy_content_width', 0);


require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'careerfy_register_required_plugins');

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function careerfy_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array();
    // This is an example of how to include a plugin bundled with a theme.
    $plugins[] = array(
        'name' => esc_html__('Careerfy Framework', 'careerfy'), // The plugin name.
        'slug' => 'careerfy-framework', // The plugin slug (typically the folder name).
        'source' => get_template_directory() . '/inc/activation-plugins/careerfy-framework.zip', // The plugin source.
        'required' => true, // If false, the plugin is only 'recommended' instead of required.
        'version' => CAREERFY_VERSION, // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
        'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        'external_url' => '', // If set, overrides default API URL and points to an external URL.
        'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
    );
    if (class_exists('Careerfy_framework')) {
        $plugins[] = array(
            'name' => esc_html__('Careerfy Demo Data', 'careerfy'), // The plugin name.
            'slug' => 'careerfy-demo-data', // The plugin slug (typically the folder name).
            'source' => 'http://careerfy.net/download-plugins/careerfy-demo-data.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '2.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('WP JobSearch', 'careerfy'), // The plugin name.
            'slug' => 'wp-jobsearch', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/wp-jobsearch.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => WP_JOBSEARCH_VERSION, // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Envato Market', 'careerfy'), // The plugin name.
            'slug' => 'envato-market', // The plugin slug (typically the folder name).
            'source' => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('WPBakery Page Builder', 'careerfy'), // The plugin name.
            'slug' => 'js_composer', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/js_composer.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '6.5.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Elemetor', 'careerfy'),
            'slug' => 'elementor',
            'required' => false,
            'version' => '',
            'force_activation' => false,
            'force_deactivation' => false,
            'external_url' => '',
        );
        $plugins[] = array(
            'name' => esc_html__('Revolution Slider', 'careerfy'), // The plugin name.
            'slug' => 'revslider', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/revslider.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '6.3.9', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('WooCommerce', 'careerfy'),
            'slug' => 'woocommerce',
            'required' => true,
            'version' => '',
            'force_activation' => false,
            'force_deactivation' => false,
            'external_url' => '',
        );
        $plugins[] = array(
            'name' => esc_html__('Addon Jobsearch Scheduled Meetings', 'careerfy'), // The plugin name.
            'slug' => 'aaddon-jobsearch-scheduled-meetings', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/addon-jobsearch-scheduled-meetings.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Addon Jobsearch Resume Export', 'careerfy'), // The plugin name.
            'slug' => 'addon-jobsearch-export-resume', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/addon-jobsearch-export-resume.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.8', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Addon Jobsearch Chat', 'careerfy'), // The plugin name.
            'slug' => 'addon-jobsearch-chat', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/addon-jobsearch-chat.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.9', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Loco Translate', 'careerfy'),
            'slug' => 'loco-translate',
            'required' => true,
            'version' => '',
            'force_activation' => false,
            'force_deactivation' => false,
            'external_url' => '',
        );
        $plugins[] = array(
            'name' => esc_html__('WP All Import', 'careerfy'),
            'slug' => 'wp-all-import',
            'required' => false,
            'version' => '',
            'force_activation' => false,
            'force_deactivation' => false,
            'external_url' => '',
        );
        $plugins[] = array(
            'name' => esc_html__('WP All Import Wp Jobsearch Add-On', 'careerfy'), // The plugin name.
            'slug' => 'wp-all-import-jobsearch', // The plugin slug (typically the folder name).
            'source' => get_template_directory() . '/inc/activation-plugins/wp-all-import-jobsearch.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            'is_callable' => '', // If set, this callable will be checked for availability to determine if a plugin is active.
        );
        $plugins[] = array(
            'name' => esc_html__('Login as User', 'careerfy'),
            'slug' => 'login-as-user',
            'required' => false,
            'version' => '',
            'force_activation' => false,
            'force_deactivation' => false,
            'external_url' => '',
        );
    }

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id' => 'careerfy', // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '', // Default absolute path to bundled plugins.
        'menu' => 'tgmpa-install-plugins', // Menu slug.
        'has_notices' => true, // Show admin notices or not.
        'dismissable' => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message' => '', // Message to output right before the plugins table.
        'strings' => array(
            'page_title' => esc_html__('Install Required Plugins', 'careerfy'),
            'menu_title' => esc_html__('Install Plugins', 'careerfy'),
            /* translators: %s: plugin name. */
            'installing' => esc_html__('Installing Plugin: %s', 'careerfy'),
            /* translators: %s: plugin name. */
            'updating' => esc_html__('Updating Plugin: %s', 'careerfy'),
            'oops' => esc_html__('Something went wrong with the plugin API.', 'careerfy'),
            'notice_can_install_required' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'careerfy'
            ),
            'notice_can_install_recommended' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'careerfy'
            ),
            'notice_ask_to_update' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'careerfy'
            ),
            'notice_ask_to_update_maybe' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'There is an update available for: %1$s.', 'There are updates available for the following plugins: %1$s.', 'careerfy'
            ),
            'notice_can_activate_required' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'careerfy'
            ),
            'notice_can_activate_recommended' => _n_noop(
                    /* translators: 1: plugin name(s). */
                    'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'careerfy'
            ),
            'install_link' => _n_noop(
                    'Begin installing plugin', 'Begin installing plugins', 'careerfy'
            ),
            'update_link' => _n_noop(
                    'Begin updating plugin', 'Begin updating plugins', 'careerfy'
            ),
            'activate_link' => _n_noop(
                    'Begin activating plugin', 'Begin activating plugins', 'careerfy'
            ),
            'return' => esc_html__('Return to Required Plugins Installer', 'careerfy'),
            'plugin_activated' => esc_html__('Plugin activated successfully.', 'careerfy'),
            'activated_successfully' => esc_html__('The following plugin was activated successfully:', 'careerfy'),
            /* translators: 1: plugin name. */
            'plugin_already_active' => esc_html__('No action taken. Plugin %1$s was already active.', 'careerfy'),
            /* translators: 1: plugin name. */
            'plugin_needs_higher_version' => esc_html__('Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'careerfy'),
            /* translators: 1: dashboard link. */
            'complete' => esc_html__('All plugins installed and activated successfully. %1$s', 'careerfy'),
            'dismiss' => esc_html__('Dismiss this notice', 'careerfy'),
            'notice_cannot_install_activate' => esc_html__('There are one or more required or recommended plugins to install, update or activate.', 'careerfy'),
            'contact_admin' => esc_html__('Please contact the administrator of this site for help.', 'careerfy'),
            'nag_type' => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
        ),
    );

    tgmpa($plugins, $config);
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function careerfy_widgets_init() {
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'careerfy'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'careerfy'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="careerfy-widget-title"><h2>',
        'after_title' => '</h2></div>',
    ));
}

add_action('widgets_init', 'careerfy_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function careerfy_front_scripts() {
    global $careerfy_framework_options;
    wp_enqueue_style('bootstrap', trailingslashit(get_template_directory_uri()) . 'css/bootstrap.css', array(), CAREERFY_VERSION);
    wp_enqueue_style('font-awesome', trailingslashit(get_template_directory_uri()) . 'css/font-awesome.css', array(), CAREERFY_VERSION);
    wp_enqueue_style('careerfy-flaticon', trailingslashit(get_template_directory_uri()) . 'css/flaticon.css', array(), CAREERFY_VERSION);

    if (class_exists('WooCommerce')) {
        wp_enqueue_style('woocommerce-styles', trailingslashit(get_template_directory_uri()) . 'css/woocommerce.css', array(), CAREERFY_VERSION);
    }
    wp_enqueue_style('careerfy-style', get_stylesheet_uri());
    wp_enqueue_style('wp-jobsearch-plugin', trailingslashit(get_template_directory_uri()) . 'css/wp-jobsearch-plugin.css', array(), CAREERFY_VERSION);
    wp_enqueue_style('careerfy-common-detail', trailingslashit(get_template_directory_uri()) . 'css/common-detail.css', array(), CAREERFY_VERSION);
    // job details
    wp_register_style('careerfy-job-detail-two', trailingslashit(get_template_directory_uri()) . 'css/job-detail-two.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-job-detail-three', trailingslashit(get_template_directory_uri()) . 'css/job-detail-three.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-job-detail-four', trailingslashit(get_template_directory_uri()) . 'css/job-detail-four.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-job-detail-five', trailingslashit(get_template_directory_uri()) . 'css/job-detail-five.css', array(), CAREERFY_VERSION);
    // candidate deatails
    wp_register_style('careerfy-candidate-detail-two', trailingslashit(get_template_directory_uri()) . 'css/candidate-detail-two.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-candidate-detail-two', trailingslashit(get_template_directory_uri()) . 'css/candidate-detail-two.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-candidate-detail-three', trailingslashit(get_template_directory_uri()) . 'css/candidate-detail-three.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-candidate-detail-four', trailingslashit(get_template_directory_uri()) . 'css/candidate-detail-four.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-candidate-detail-five', trailingslashit(get_template_directory_uri()) . 'css/candidate-detail-five.css', array(), CAREERFY_VERSION);

    // register progress bars
    wp_register_script('careerfy-progress-circle', trailingslashit(get_template_directory_uri()) . 'js/progress-circle.js', array('jquery'), CAREERFY_VERSION, true);
    wp_register_script('careerfy-progressbar', trailingslashit(get_template_directory_uri()) . 'js/progressbar.js', array('jquery'), CAREERFY_VERSION, true);
    wp_register_script('careerfy-progressbar-two', trailingslashit(get_template_directory_uri()) . 'js/progressbar-two.js', array('jquery'), CAREERFY_VERSION, true);
    // employer views stylesheets
    wp_register_style('careerfy-emp-detail-two', trailingslashit(get_template_directory_uri()) . 'css/employer-detail-two.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-emp-detail-three', trailingslashit(get_template_directory_uri()) . 'css/employer-detail-three.css', array(), CAREERFY_VERSION);
    wp_register_style('careerfy-emp-detail-four', trailingslashit(get_template_directory_uri()) . 'css/employer-detail-four.css', array(), CAREERFY_VERSION);
    // common-detail css

    wp_enqueue_style('careerfy-responsive', trailingslashit(get_template_directory_uri()) . 'css/responsive.css', array(), CAREERFY_VERSION);
    // RTL
    if (is_rtl()) {
        wp_enqueue_style('careerfy-rtl', trailingslashit(get_template_directory_uri()) . 'css/rtl.css', array(), CAREERFY_VERSION);
    }
    // scripts
    wp_enqueue_script('bootstrap', trailingslashit(get_template_directory_uri()) . 'js/bootstrap.js', array('jquery'), CAREERFY_VERSION, true);
    wp_enqueue_script('fitvideo', trailingslashit(get_template_directory_uri()) . 'js/fitvideo.js', array(), CAREERFY_VERSION, true);
    wp_enqueue_script('careerfy-functions', trailingslashit(get_template_directory_uri()) . 'js/functions.js', array('jquery'), CAREERFY_VERSION, true);
    // Localize the script
    $careerfy_arr = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nav_open_img' => get_template_directory_uri() . '/images/nav-list-icon.png',
        'nav_close_img' => get_template_directory_uri() . '/images/cross.png',
    );
    wp_localize_script('careerfy-functions', 'careerfy_funnc_vars', $careerfy_arr);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'careerfy_front_scripts', 99);

/**
 * Enqueue scripts and styles.
 */
function careerfy_custom_front_scripts() {
    wp_enqueue_style('careerfy-custom-styles', trailingslashit(get_template_directory_uri()) . 'css/custom-styles.css');
    $custom_css = careerfy_dynamic_colors();
    wp_add_inline_style('careerfy-custom-styles', $custom_css);
}

add_action('wp_enqueue_scripts', 'careerfy_custom_front_scripts', 9999);

/*
 * Custom google fonts.
 * @return
 */

function careerfy_google_fonts_enqueue() {

    $google_fonts_url = careerfy_google_fonts_url(
            array(
        'Merriweather' => '400,300,300italic,400italic,700,700italic,900italic,900',
        'Ubuntu' => '400,300,300italic,400italic,500,500italic,700,700italic',
            ), 'latin'
    );

    wp_enqueue_style('careerfy-google-fonts', $google_fonts_url, array(), CAREERFY_VERSION);
}

add_action('wp_enqueue_scripts', 'careerfy_google_fonts_enqueue', 0);

// REMOVE UPDATE NOTICE FOR VISUAL COMPOSER
add_filter('site_transient_update_plugins', 'careerfy_remove_update_notifications');

function careerfy_remove_update_notifications($value) {
    if (isset($value) && is_object($value)) {
        unset($value->response['js_composer_theme/js_composer.php']);
    }
    return $value;
}

/**
 * Implement the theme common colors.
 */
require trailingslashit(get_template_directory()) . 'inc/theme-colors.php';

/**
 * Implement the theme common functions.
 */
require trailingslashit(get_template_directory()) . 'inc/theme-functions.php';

/**
 * Implement the header functions.
 */
require trailingslashit(get_template_directory()) . 'inc/header-functions.php';

/**
 * Implement the footer functions.
 */
require trailingslashit(get_template_directory()) . 'inc/footer-functions.php';

/**
 * Implement the Custom Header feature.
 */
require trailingslashit(get_template_directory()) . 'inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require trailingslashit(get_template_directory()) . 'inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require trailingslashit(get_template_directory()) . 'inc/extras.php';

/**
 * Customizer additions.
 */
require trailingslashit(get_template_directory()) . 'inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require trailingslashit(get_template_directory()) . 'inc/jetpack.php';

/**
 * Load woocommerce config.
 */
if (class_exists('WooCommerce')) {
    require_once trailingslashit(get_template_directory()) . 'inc/woocommerce-config.php';
}