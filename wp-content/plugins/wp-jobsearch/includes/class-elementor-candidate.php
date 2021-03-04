<?php

namespace WP_JobsearchCandElementor;

class WP_JobsearchCandElementorPlugin
{

    private static $_instance = null;

    public function __construct()
    {
        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);
        // Register widgets
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        // Register Categories
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function widget_scripts()
    {

    }

    private function include_widgets_files()
    {

        require_once(__DIR__ . '/elementor-templates/single-candidate/candidate-info.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/candidate-logo.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/cover-image.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/review-total.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/candidate-contact-form.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/candidate-map.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/custom-fields.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/candidate-description.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/education.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/experience.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/expertise.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/portfolio.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/awards.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/skills.php');
        require_once(__DIR__ . '/elementor-templates/single-candidate/reviews.php');
    }

    public function register_widgets()
    {
        $this->include_widgets_files();

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateInfo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateLogo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateCoverImage());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateReviewTotal());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateContact());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateMap());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateCustomFields());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateDescription());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateEducation());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateExperience());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateExpertise());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidatePortfolio());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateAwards());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateSkills());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SingleCandidateReviews());
    }

    public function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'jobsearch-cand-single',
            [
                'title' => __('Candidate Single', 'wp-jobsearch'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

}

WP_JobsearchCandElementorPlugin::instance();
