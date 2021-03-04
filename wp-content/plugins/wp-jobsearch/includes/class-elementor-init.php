<?php

namespace WP_JobsearchElementor;

class WP_JobsearchElementorPlugin
{

    private static $_instance = null;

    public function __construct()
    {
        // Register widgets
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        // Register Categories
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
        add_action('elementor/editor/before_enqueue_scripts', function () {
            wp_enqueue_style('jobsearch-elementor', jobsearch_plugin_get_url('css/jobsearch-elementor.css'), array(), \JobSearch_plugin::get_version(), true);
           // wp_enqueue_script(...);
        });
    }


    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function include_widgets_files()
    {
        require_once(__DIR__ . '/elementor-templates/single-job/job-info.php');
        require_once(__DIR__ . '/elementor-templates/single-job/job-logo.php');
        require_once(__DIR__ . '/elementor-templates/single-job/cover-image.php');
        require_once(__DIR__ . '/elementor-templates/single-job/custom-fields.php');
        require_once(__DIR__ . '/elementor-templates/single-job/job-description.php');
        require_once(__DIR__ . '/elementor-templates/single-job/job-skills.php');
        require_once(__DIR__ . '/elementor-templates/single-job/related-jobs.php');
        require_once(__DIR__ . '/elementor-templates/single-job/job-attachments.php');
        require_once(__DIR__ . '/elementor-templates/single-job/apply-job.php');
        require_once(__DIR__ . '/elementor-templates/single-job/employer-contact.php');
        require_once(__DIR__ . '/elementor-templates/single-job/location-map.php');
        require_once(__DIR__ . '/elementor-templates/single-job/employer-jobs.php');
        /*
         * Employer Single Widgets
         * */
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-info.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-logo.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/cover-image.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-custom-fields.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-description.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-team-members.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-reviews.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-active-jobs.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-contact-form.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-map.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-chat.php');
        require_once(__DIR__ . '/elementor-templates/single-employer/emp-comp-gallery.php');
    }

    public function register_widgets()
    {
        $this->include_widgets_files();
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobInfo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobLogo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobCoverImage());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobCustomFields());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobDescription());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobSkills());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobRelatedJobs());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobAttachments());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobApplyButtons());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobEmployerContact());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobMap());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleJobEmployerJobs());
        /*
        * Employer Single Widgets
        * */
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpInfo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpLogo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpCoverImage());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmployerCustomFields());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpDescription());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpTeamMembers());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpReviews());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpActiveJobs());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpContactForm());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpMap());
        if (class_exists('Addon_Jobsearch_Chat')) {
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpChat());
        }
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleEmpCompGallery());
        //
    }

    public function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'jobsearch-job-single',
            [
                'title' => __('Job Single', 'careerfy-frame'),
                'icon' => 'fa fa-plug',
            ]
        );
        $elements_manager->add_category(
            'jobsearch-emp-single',
            [
                'title' => __('Employer Single', 'careerfy-frame'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

}

WP_JobsearchElementorPlugin::instance();
