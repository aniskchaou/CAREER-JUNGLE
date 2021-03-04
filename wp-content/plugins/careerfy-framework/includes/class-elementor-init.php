<?php

namespace CareerfyElementor;

class CareerfyElementorPlugin
{
    private static $_instance = null;

    public function __construct()
    {
        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);
        // Register widgets
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
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
        wp_enqueue_script('careerfy-elementor', careerfy_framework_get_url('js/careerfy-elementor.js'), ['jquery'], false, true);
        $eyecix_arr = array(
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        wp_localize_script('careerfy-elementor', 'eyecix_func_vars', $eyecix_arr);
    }

    private function include_widgets_files()
    {
        /*
         *
         * WP Jobsearch Elements files
         *
         * */
        if (class_exists('JobSearch_plugin')) {
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/jobs-listing.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/candidate-listing.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/employee-listing.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/login-registeration.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/job-categories.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/simple-jobs-listing.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/all-packages.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/jobsearch-features.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/explore-jobs.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/jobs-by-categories.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/simple-job-listing-multi.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/post-new-job.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/banner-advertisement.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/advance-search.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/simple-employer-listing.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/job-types.php');
            require_once(__DIR__ . '/elementor-widget/wp-jobsearch-elements/login-popup.php');
        }
        /*
         *
         * Careerfy Framework Elements files
         *
         * */
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/blog.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/section-heading.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/left-title.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/careerfy-button.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/call-to-action.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/banner-caption.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/about-company.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/block-text.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/simple-text.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/find-question.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/careerfy-maps.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/contact-info.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/about-info.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/image-banner.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/faq.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/counters.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/services.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/home-page-slider.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/video-testimonial-slider.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/image-services.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/our-partners.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/our-team.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/help-links.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/testimonials.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/recent-questions.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/jobs-listings-tabs.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/process.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/candidate-slider.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/top-job-slider.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/app-promo.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/promo-stats.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/news-letter.php');
        require_once(__DIR__ . '/elementor-widget/careerfy-framework-elements/how-it-works.php');
    }

    public function register_widgets()
    {
        $this->include_widgets_files();
        /*
        *
        * Careerfy framework Elements
        *
        * */
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Blog());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SectionHeading());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\LeftTitle());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CareerfyButton());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CallToAction());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BannerCaption());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AboutCompany());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BlockTextWithVideo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SimpleText());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FindQuestion());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CareerfyMaps());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ContactInfo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AboutInfo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FAQ());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Counters());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Services());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\HomePageSlider());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\VideoTestimonialSlider());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ImageServices());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\OurPartners());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\OurTeam());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\HelpLinks());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\testimonials());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\RecentQuestions());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobsListingsTabs());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Process());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CandidateSlider());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\TopJobSlider());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AppPromo());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PromoStats());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\NewsLetter());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\HowItWorks());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SimpleEmployerListings());
        /*
         *
         * WP Jobsearch Elements
         *
         * */
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobsListings());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CandidateListings());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\EmployeeListings());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\LoginRegisteration());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobCategories());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobsByCategories());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SimpleJobsListings());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AllPackages());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobFeatured());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ExploreJobs());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SimpleJobsListingsMulti());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostNewJob());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BannerAdvertisement());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AdvanceSearch());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\JobTypes());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\LoginPopup());
    }
}

CareerfyElementorPlugin::instance();