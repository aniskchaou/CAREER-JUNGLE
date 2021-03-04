<?php

/**
 * visual composer shortcodes mapping
 * @config
 */
/**
 * list all hooks adding
 * @return hooks
 */
add_action('vc_before_init', 'careerfy_vc_blog_shortcode');
add_action('vc_before_init', 'careerfy_vc_section_heading');
add_action('vc_before_init', 'careerfy_vc_left_title_shortcode');
add_action('vc_before_init', 'careerfy_vc_button_shortcode');
add_action('vc_before_init', 'careerfy_vc_advance_search');
add_action('vc_before_init', 'careerfy_vc_job_categories');
add_action('vc_before_init', 'careerfy_vc_job_types');
add_action('vc_before_init', 'careerfy_vc_simple_jobs_listing');
add_action('vc_before_init', 'careerfy_vc_jobs_listing');
add_action('vc_before_init', 'careerfy_vc_employer_listing');
add_action('vc_before_init', 'careerfy_vc_candidate_listing');
add_action('vc_before_init', 'careerfy_vc_call_to_action');
add_action('vc_before_init', 'careerfy_vc_banner_caption');
add_action('vc_before_init', 'careerfy_vc_about_company');
add_action('vc_before_init', 'careerfy_vc_block_text_box');
add_action('vc_before_init', 'careerfy_vc_simple_block_text');
add_action('vc_before_init', 'careerfy_vc_find_question');
add_action('vc_before_init', 'careerfy_vc_google_map_shortcode');
add_action('vc_before_init', 'careerfy_vc_contact_information');
add_action('vc_before_init', 'careerfy_vc_about_information');
add_action('vc_before_init', 'careerfy_vc_image_banner');
add_action('vc_before_init', 'careerfy_vc_faqs_shortcode');
add_action('vc_before_init', 'careerfy_vc_counters_shortcode');
add_action('vc_before_init', 'careerfy_vc_services_shortcode');
add_action('vc_before_init', 'careerfy_vc_slider_shortcode');
add_action('vc_before_init', 'careerfy_vc_video_testimonial_shortcode');
add_action('vc_before_init', 'careerfy_vc_image_services_shortcode');
add_action('vc_before_init', 'careerfy_vc_our_partners_shortcode');
add_action('vc_before_init', 'careerfy_vc_our_team_shortcode');
add_action('vc_before_init', 'careerfy_vc_help_links_shortcode');
add_action('vc_before_init', 'careerfy_vc_testimonials_with_image');
add_action('vc_before_init', 'careerfy_vc_recent_questions');
add_action('vc_before_init', 'careerfy_vc_cv_packages');
add_action('vc_before_init', 'careerfy_vc_job_packages');
add_action('vc_before_init', 'careerfy_vc_candidate_packages');
add_action('vc_before_init', 'careerfy_vc_all_packages');
add_action('vc_before_init', 'careerfy_vc_jobs_listings_tabs');
add_action('vc_before_init', 'jobsearch_vc_featured_jobs');
add_action('vc_before_init', 'careerfy_vc_simple_employers_shortcode');
add_action('vc_before_init', 'careerfy_vc_process_shortcode');
add_action('vc_before_init', 'careerfy_vc_candidate_slider_shortcode');
add_action('vc_before_init', 'careerfy_vc_top_recruiters_slider_shortcode');
add_action('vc_before_init', 'careerfy_vc_app_promo');
add_action('vc_before_init', 'careerfy_vc_explore_jobs');
add_action('vc_before_init', 'careerfy_vc_jobs_by_categories');
add_action('vc_before_init', 'careerfy_vc_simple_jobs_listing_multi');
add_action('vc_before_init', 'careerfy_vc_promo_statistics_shortcode');
add_action('vc_before_init', 'careerfy_vc_how_it_works');
add_action('vc_before_init', 'careerfy_vc_news_letter');
add_action('vc_before_init', 'careerfy_vc_sign_up');
add_action('vc_before_init', 'careerfy_vc_embeddable_jobs');
add_action('vc_before_init', 'careerfy_vc_user_job_shortcode');
add_action('vc_before_init', 'careerfy_vc_banner_advertisement');
add_action('vc_before_init', 'careerfy_vc_login_registration_shortcode');

/**
 * adding user job shortcode
 * @return markup
 */

function careerfy_vc_sign_up()
{
    $attributes = array(
        "name" => esc_html__("Sign Up Form", "careerfy-frame"),
        "base" => "careerfy_sign_up",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__("First Description", "careerfy-frame"),
                'param_name' => 'content',
                'description' => '',
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'srch_desc',
                'description' => esc_html__("Description will show under the first description", "careerfy-frame"),
            ),

        ),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding user job shortcode
 * @return markup
 */
function careerfy_vc_news_letter()
{
    global $careerfy_framework_options;
    $mailchimp_api_key = '';
    if (isset($careerfy_framework_options['careerfy-mailchimp-api-key'])) {
        $mailchimp_api_key = $careerfy_framework_options['careerfy-mailchimp-api-key'];
    }

    //$careerfy_mailchimp_list = array();
    $careerfy_mailchimp_list = array(esc_html__("Select List", "careerfy-frame") => '');
    $mailchimp_lists = careerfy_framework_mailchimp_list($mailchimp_api_key);
    if (is_array($mailchimp_lists) && isset($mailchimp_lists['data'])) {
        foreach ($mailchimp_lists['data'] as $mc_list) {
            $careerfy_mailchimp_list[$mc_list['name']] = $mc_list['id'];
        }
    }

    $attributes = array(
        "name" => esc_html__("Newsletter", "careerfy-frame"),
        "base" => "news_letter_shortcode",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'style1',
                    esc_html__("Style 2", "careerfy-frame") => 'style2',
                    esc_html__("Style 3", "careerfy-frame") => 'style3',
                    esc_html__("Style 4", "careerfy-frame") => 'style4',
                ),
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'news_letter_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('style1')
                ),
            ),
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'content',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('style2', 'style3')
                ),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'news_letter_desc',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('style1')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("List", "careerfy-frame"),
                'param_name' => 'news_letter_list',
                'value' => $careerfy_mailchimp_list,
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('style1')
                )
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("List", "careerfy-frame"),
                'param_name' => 'news_letter_list_multi',
                'value' => $careerfy_mailchimp_list,
                'description' => esc_html__("Select any two options.", "careerfy-frame"),
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('style2', 'style3', 'style4')
                )
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * adding user job shortcode
 * @return markup
 */
function jobsearch_vc_featured_jobs()
{
    $attributes = array(
        "name" => esc_html__("Jobsearch Featured", "careerfy-frame"),
        "base" => "jobsearch_featured_job",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'featured_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Featured", "careerfy-frame"),
                'param_name' => 'featured_section',
                'value' => array(
                    esc_html__("Jobs", "careerfy-frame") => 'jobs',
                    esc_html__("Employers", "careerfy-frame") => 'employer',
                    esc_html__("Candidate", "careerfy-frame") => 'candidate',
                ),
                'description' => '',
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("Locations in listing", "careerfy-frame"),
                'param_name' => 'job_list_loc_listing',
                'value' => array(
                    esc_html__("Country", "careerfy-frame") => 'country',
                    esc_html__("State", "careerfy-frame") => 'state',
                    esc_html__("City", "careerfy-frame") => 'city',
                ),
                'std' => 'country,city',
                'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Num of Posts", "careerfy-frame"),
                'param_name' => 'featured_job_num',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Non-Featured Items", "careerfy-frame"),
                'param_name' => 'non_featured_items',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => esc_html__("If Featured Items not found show other general items.", "careerfy-frame"),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Slide Position", "careerfy-frame"),
                'param_name' => 'job_slide_position',
                'value' => array(
                    esc_html__("Vertical", "careerfy-frame") => 'vertical',
                    esc_html__("Horizontal", "careerfy-frame") => 'horizontal',
                ),
                'description' => '',
                'group' => esc_html__("Slide Settings", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Num of Slides", "careerfy-frame"),
                'param_name' => 'featured_num_slide',
                'value' => '',
                'description' => esc_html__("Add num of Slides to scroll", "careerfy-frame"),
                'group' => esc_html__("Slide Settings", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Slide Speed Time", "careerfy-frame"),
                'param_name' => 'featured_slide_speed',
                'value' => '',
                'description' => esc_html__("Slide speed like 5000,7000", "careerfy-frame"),
                'group' => esc_html__("Slide Settings", "careerfy-frame"),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Slide Execution", "careerfy-frame"),
                'param_name' => 'slide_execution',
                'value' => array(
                    esc_html__("Linear", "careerfy-frame") => 'linear',
                    esc_html__("Ease", "careerfy-frame") => 'ease',
                    esc_html__("Ease In", "careerfy-frame") => 'ease-in',
                    esc_html__("Ease Out", "careerfy-frame") => 'ease-out',
                    esc_html__("Ease In Out", "careerfy-frame") => 'ease-in-out',
                    esc_html__("Step Start", "careerfy-frame") => 'step-start',
                    esc_html__("Step End", "careerfy-frame") => 'step-end',
                    esc_html__("Initial", "careerfy-frame") => 'initial',
                    esc_html__("Inherit", "careerfy-frame") => 'inherit',
                ),
                'description' => '',
                'group' => esc_html__("Slide Settings", "careerfy-frame"),
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

function careerfy_vc_jobs_listings_tabs()
{
    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));
    $cate_array = array();
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }
    //
    $attributes = array(
        "name" => esc_html__("Jobs Listing Tabs", "careerfy-frame"),
        "base" => "jobsearch_job_listin_tabs_shortcode",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("Sector", "careerfy-frame"),
                'param_name' => 'job_cats_filter',
                'value' => $cate_array,
                'description' => esc_html__("Select Sector.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("job per page", "careerfy-frame"),
                'param_name' => 'job_per_page',
                'value' => '',
                'description' => esc_html__("Set the number of jobs you want to show for each page.", "careerfy-frame")
            ),
        )
    );
    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding blog shortcode
 * @return markup
 */
function careerfy_vc_blog_shortcode()
{
    $categories = get_categories(array(
        'orderby' => 'name',
    ));

    $cate_array = array(esc_html__("Select Category", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->cat_name] = $category->slug;
        }
    }

    $attributes = array(
        "name" => esc_html__("Blog", "careerfy-frame"),
        "base" => "careerfy_blog_shortcode",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'blog_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                    esc_html__("Style 6", "careerfy-frame") => 'view7',
                    esc_html__("Style 7", "careerfy-frame") => 'view8',
                    esc_html__("Style 8", "careerfy-frame") => 'view9',
                    esc_html__("Style 9", "careerfy-frame") => 'view10',
                    esc_html__("Style 10", "careerfy-frame") => 'view11',
                    esc_html__("Style 11", "careerfy-frame") => 'view12',
                    esc_html__("Style 12", "careerfy-frame") => 'view13',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Category", "careerfy-frame"),
                'param_name' => 'blog_cat',
                'value' => $cate_array,
                'description' => esc_html__("Select Category.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Excerpt Length", "careerfy-frame"),
                'param_name' => 'blog_excerpt',
                'value' => '20',
                'description' => esc_html__("Set the number of words you want to show for post excerpt.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'blog_order',
                'value' => array(
                    esc_html__("Descending", "careerfy-frame") => 'DESC',
                    esc_html__("Ascending", "careerfy-frame") => 'ASC',
                ),
                'description' => esc_html__("Choose the blog list items order.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Orderby", "careerfy-frame"),
                'param_name' => 'blog_orderby',
                'value' => array(
                    esc_html__("Date", "careerfy-frame") => 'date',
                    esc_html__("Title", "careerfy-frame") => 'title',
                ),
                'description' => esc_html__("Choose blog list items orderby.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Pagination", "careerfy-frame"),
                'param_name' => 'blog_pagination',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'default' => 'no',
                'dependency' => array(
                    'element' => 'blog_view',
                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5', 'view9', 'view10', 'view11', 'view12', 'view13')
                ),
                'description' => esc_html__("Choose Yes if you want to show pagination for post items.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Posts per Page", "careerfy-frame"),
                'param_name' => 'blog_per_page',
                'value' => '10',
                'description' => esc_html__("Set number that how much posts you want to show per page. Leave it blank for all posts on a single page.", "careerfy-frame")
            )
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding app promo shortcode
 * @return markup
 */
function careerfy_vc_app_promo()
{
    $attributes = array(
        "name" => esc_html__("App Promo", "careerfy-frame"),
        "base" => "careerfy_app_promo",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'app_promo_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'h_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Title Color", "careerfy-frame"),
                'param_name' => 'hc_title_clr',
                'value' => '',
                'description' => esc_html__("This Color will apply to 'Color Title'.", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'h_desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Description Color", "careerfy-frame"),
                'param_name' => 'desc_clr',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link Text", "careerfy-frame"),
                'param_name' => 'link_text',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'app_promo_view',
                    'value' => array('view2')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link Text URL", "careerfy-frame"),
                'param_name' => 'link_text_url',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'app_promo_view',
                    'value' => array('view2')
                ),
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("First Image", "careerfy-frame"),
                'param_name' => 'careerfy_browse_img_1',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("First Image Links URL", "careerfy-frame"),
                'param_name' => 'first_img_link',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Second Image", "careerfy-frame"),
                'param_name' => 'careerfy_browse_img_2',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Second Image Links URL", "careerfy-frame"),
                'param_name' => 'second_img_link',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Third Image", "careerfy-frame"),
                'param_name' => 'careerfy_browse_img_3',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'app_promo_view',
                    'value' => array('view2')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Third Image Links URL", "careerfy-frame"),
                'param_name' => 'third_img_link',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'app_promo_view',
                    'value' => array('view2')
                ),
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Features", "careerfy-frame"),
                'param_name' => 'pckg_features',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Feature Detail', 'careerfy-frame'),
                        'param_name' => 'feat_name',
                    ),
                ),
                'dependency' => array(
                    'element' => 'app_promo_view',
                    'value' => array('view3')
                ),
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding section heading shortcode
 * @return markup
 */
function careerfy_vc_section_heading()
{
    $attributes = array(
        "name" => esc_html__("Section Heading", "careerfy-frame"),
        "base" => "careerfy_section_heading",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                    esc_html__("Style 6", "careerfy-frame") => 'view6',
                    esc_html__("Style 7", "careerfy-frame") => 'view7',
                    esc_html__("Style 8", "careerfy-frame") => 'view8',
                    esc_html__("Style 9", "careerfy-frame") => 'view9',
                    esc_html__("Style 10", "careerfy-frame") => 'view10',
                    esc_html__("Style 11", "careerfy-frame") => 'view11',
                    esc_html__("Style 12", "careerfy-frame") => 'view12',
                    esc_html__("Style 13", "careerfy-frame") => 'view13',
                    esc_html__("Style 14", "careerfy-frame") => 'view14',
                    esc_html__("Style 15", "careerfy-frame") => 'view15',
                    esc_html__("Style 16", "careerfy-frame") => 'view16',
                    esc_html__("Style 17", "careerfy-frame") => 'view17',
                    esc_html__("Style 18", "careerfy-frame") => 'view18',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Small Title", "careerfy-frame"),
                'param_name' => 's_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view6', 'view18')
                ),
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'heading_img',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view8', 'view15')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'h_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title Number", "careerfy-frame"),
                'param_name' => 'num_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view6')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Fancy Title", "careerfy-frame"),
                'param_name' => 'h_fancy_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Color Title", "careerfy-frame"),
                'param_name' => 'hc_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5')
                ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Small Title Color", "careerfy-frame"),
                'param_name' => 's_title_clr',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view6', 'view18')
                ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Title Color", "careerfy-frame"),
                'param_name' => 'hc_title_clr',
                'value' => '',
                'description' => esc_html__("This Color will apply to 'Color Title'.", "careerfy-frame"),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Description Color", "careerfy-frame"),
                'param_name' => 'desc_clr',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view6')
                ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Process Number Color", "careerfy-frame"),
                'param_name' => 'proc_num_clr',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view6')
                ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__("Icon", "careerfy-frame"),
                'param_name' => 'hc_icon',
                'value' => '',
                'description' => esc_html__("This will apply to heading style 3 only.", "careerfy-frame"),
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5')
                ),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'h_desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Description Color", "careerfy-frame"),
                'param_name' => 'hc_dcolor',
                'value' => '',
                'description' => esc_html__("This will apply to the description only.", "careerfy-frame"),
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5', 'view7', 'view8', 'view9', 'view10', 'view11', 'view12', 'view13', 'view14', 'view15', 'view16', 'view17', 'view18')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Text Align", "careerfy-frame"),
                'param_name' => 'text_align',
                'value' => array(
                    esc_html__("Center", "careerfy-frame") => 'center',
                    esc_html__("Left", "careerfy-frame") => 'left',
                ),
                'description' => '',
                'default' => 'center',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view7', 'view8', 'view17', 'view18')
                ),
            ),
            array(
                'type' => 'css_editor',
                'heading' => __('Css', 'careerfy-frame'),
                'param_name' => 'css',
                'group' => __('Design options', 'careerfy-frame'),
//                'dependency' => array(
//                    'element' => 'view',
//                    'value' => array('view1', 'view2', 'view3', 'view4', 'view5')
//                ),
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding Left Title shortcode
 * @return markup
 */
function careerfy_vc_left_title_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Left Title", "careerfy-frame"),
        "base" => "careerfy_left_title",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'h_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding button shortcode
 * @return markup
 */
function careerfy_vc_button_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Button", "careerfy-frame"),
        "base" => "careerfy_button",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'btn_styl',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding Advance Search shortcode
 * @return markup
 */
function careerfy_vc_advance_search()
{
    global $jobsearch_plugin_options;
    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

    $all_page = array();
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $all_page[$page->post_title] = $page->ID;
        }
    }

    $adv_srch_listsh_parms = [];
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Style", "careerfy-frame"),
        'param_name' => 'view',
        'value' => array(
            esc_html__("Style 1", "careerfy-frame") => 'view1',
            esc_html__("Style 2", "careerfy-frame") => 'view2',
            esc_html__("Style 3", "careerfy-frame") => 'view3',
            esc_html__("Style 4", "careerfy-frame") => 'view4',
            esc_html__("Style 5", "careerfy-frame") => 'view5',
            esc_html__("Style 6", "careerfy-frame") => 'view6',
            esc_html__("Style 7", "careerfy-frame") => 'view7',
            esc_html__("Style 8", "careerfy-frame") => 'view8',
            esc_html__("Style 9", "careerfy-frame") => 'view9',
            esc_html__("Style 10", "careerfy-frame") => 'view10',
            esc_html__("Style 11", "careerfy-frame") => 'view11',
            esc_html__("Style 12", "careerfy-frame") => 'view12',
            esc_html__("Style 13", "careerfy-frame") => 'view13',
            esc_html__("Style 14", "careerfy-frame") => 'view14',
            esc_html__("Style 15", "careerfy-frame") => 'view15',
            esc_html__("Style 16", "careerfy-frame") => 'view16',
            esc_html__("Style 17", "careerfy-frame") => 'view17',
            esc_html__("Style 18", "careerfy-frame") => 'view18',
            esc_html__("Style 19", "careerfy-frame") => 'view19',
            esc_html__("Style 20", "careerfy-frame") => 'view20',
        ),
        'description' => ''
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'careerfy_browse_img',
        'heading' => esc_html__("Background Image", "careerfy-frame"),
        'param_name' => 'bg_img',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view12')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Small Title", "careerfy-frame"),
        'param_name' => 'small_search_title',
        'value' => '',
        'description' => esc_html__("The Small title will show above the title.", "careerfy-frame"),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view18', 'view19', 'view20')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Title", "careerfy-frame"),
        'param_name' => 'srch_title',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'view11', 'view12', 'view13', 'view15', 'view16', 'view17')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Total Number of Job Types", "careerfy-frame"),
        'param_name' => 'no_total_jobtypes',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view14', 'view15')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textarea_html',
        'heading' => esc_html__("First Description", "careerfy-frame"),
        'param_name' => 'content',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view18', 'view19', 'view20')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("Description", "careerfy-frame"),
        'param_name' => 'srch_desc',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'view11', 'view12', 'view13', 'view15', 'view16', 'view17', 'view18', 'view19')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'param_group',
        'value' => '',
        'heading' => esc_html__("Images", "careerfy-frame"),
        'param_name' => 'adv_banner_images',
        'params' => array(
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'banner_img',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Image Link", "careerfy-frame"),
                'param_name' => 'img_link',
                'description' => ''
            ),
        ),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view11')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("Text Below Form 1", "careerfy-frame"),
        'param_name' => 'txt_below_forms_1',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => 'view9'
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Search Result Page by Jobs", "careerfy-frame"),
        'param_name' => 'result_page',
        'value' => $all_page,
        'description' => '',

    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Search Result Page by Employer", "careerfy-frame"),
        'param_name' => 'result_page_2',
        'value' => $all_page,
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => 'view9'
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("Text Below Form 2", "careerfy-frame"),
        'param_name' => 'txt_below_forms_2',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => 'view9'
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Search Result Page by Candidate", "careerfy-frame"),
        'param_name' => 'result_page_3',
        'value' => $all_page,
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view9', 'view12', 'view17', 'view18', 'view19', 'view20')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("Text Below Form 3", "careerfy-frame"),
        'param_name' => 'txt_below_forms_3',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => 'view9'
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Button 1 Text", "careerfy-frame"),
        'param_name' => 'btn1_txt',
        'value' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
        'description' => esc_html__("This will not show in Search Style 4.", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Button 1 URL", "careerfy-frame"),
        'param_name' => 'btn1_url',
        'value' => '',
        'description' => esc_html__("This will not show in Search Style 4.", "careerfy-frame"),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'iconpicker',
        'heading' => esc_html__("Button 1 Icon", "careerfy-frame"),
        'param_name' => 'btn_1_icon',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Button 2 Text", "careerfy-frame"),
        'param_name' => 'btn2_txt',
        'value' => '',
        'description' => esc_html__("This will only show in Search Style 1.", "careerfy-frame"),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Button 2 URL", "careerfy-frame"),
        'param_name' => 'btn2_url',
        'value' => '',
        'description' => esc_html__("This will only show in Search Style 1.", "careerfy-frame"),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'iconpicker',
        'heading' => esc_html__("Button 2 Icon", "careerfy-frame"),
        'param_name' => 'btn_2_icon',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Keyword Field", "careerfy-frame"),
        'param_name' => 'keyword_field',
        'value' => array(
            esc_html__("Show", "careerfy-frame") => 'show',
            esc_html__("Hide", "careerfy-frame") => 'hide',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),

    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Keyword Suggestions", "careerfy-frame"),
        'param_name' => 'autofill_keyword',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location Field", "careerfy-frame"),
        'param_name' => 'location_field',
        'value' => array(
            esc_html__("Show", "careerfy-frame") => 'show',
            esc_html__("Hide", "careerfy-frame") => 'hide',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location Radius", "careerfy-frame"),
        'param_name' => 'radius_field',
        'value' => array(
            esc_html__("Show", "careerfy-frame") => 'show',
            esc_html__("Hide", "careerfy-frame") => 'hide',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location Suggestions", "careerfy-frame"),
        'param_name' => 'autofill_location',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("AutoFill Geo Location", "careerfy-frame"),
        'param_name' => 'auto_geo_location',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Fields Settings", "careerfy-frame"),
    );
    if ($sectors_enable_switch == 'on') {
        $adv_srch_listsh_parms[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Sector Field", "careerfy-frame"),
            'param_name' => 'category_field',
            'value' => array(
                esc_html__("Show", "careerfy-frame") => 'show',
                esc_html__("Hide", "careerfy-frame") => 'hide',
            ),
            'description' => '',
            'group' => esc_html__("Fields Settings", "careerfy-frame"),
        );
    }
    $adv_srch_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Title Color", "careerfy-frame"),
        'param_name' => 'search_title_color',
        'value' => '',
        'description' => '',
        'group' => esc_html__("Color Settings", "careerfy-frame"),
        'dependency' => array(
            'element' => 'view',
            'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view17', 'view19')
        ),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Paragraph Color", "careerfy-frame"),
        'param_name' => 'search_paragraph_color',
        'value' => '',
        'description' => '',
        'group' => esc_html__("Color Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Link Color", "careerfy-frame"),
        'param_name' => 'search_link_color',
        'value' => '',
        'description' => '',
        'group' => esc_html__("Color Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Button Background Color", "careerfy-frame"),
        'param_name' => 'search_btn_bg_color',
        'value' => '',
        'description' => '',
        'group' => esc_html__("Color Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Button Text Color", "careerfy-frame"),
        'param_name' => 'search_btn_txt_color',
        'value' => '',
        'description' => '',
        'group' => esc_html__("Color Settings", "careerfy-frame"),
    );
    $adv_srch_listsh_parms[] = array(
        'type' => 'css_editor',
        'heading' => __('Css', 'careerfy-frame'),
        'param_name' => 'css',
        'group' => __('Design options', 'careerfy-frame'),
    );
    $attributes = array(
        "name" => esc_html__("Advance Search", "careerfy-frame"),
        "base" => "careerfy_advance_search",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "class" => "",
        "params" => apply_filters('careerfy_adv_search_vcsh_params', $adv_srch_listsh_parms)
    );
    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

}

/**
 * Adding job categories shortcode
 * @return markup
 */
function careerfy_vc_jobs_by_categories()
{

    $all_page = array();

    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $all_page[$page->post_title] = $page->ID;
        }
    }

    $attributes = array(
        "name" => esc_html__("Jobs by Categories", "careerfy-frame"),
        "base" => "careerfy_jobs_by_categories",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of Sectors", "careerfy-frame"),
                'param_name' => 'num_cats',
                'value' => '',
                'description' => esc_html__("Set the number of Sectors to show", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Result Page", "careerfy-frame"),
                'param_name' => 'result_page',
                'value' => $all_page,
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view1', 'view2', 'view3', 'view5')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'order_by',
                'value' => array(
                    esc_html__("By Jobs Count", "careerfy-frame") => 'jobs_count',
                    esc_html__("By Random", "careerfy-frame") => 'id',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Show Jobs Counts", "careerfy-frame"),
                'param_name' => 'sector_job_counts',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'cat_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text", "careerfy-frame"),
                'param_name' => 'cat_link_text',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text URL", "careerfy-frame"),
                'param_name' => 'cat_link_text_url',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
        )
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * Adding job categories shortcode
 * @return markup
 */
function careerfy_vc_job_categories()
{
    $all_page = array(esc_html__("Select Page", "careerfy-frame") => '');
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $all_page[$page->post_title] = $page->ID;
        }
    }

    $attributes = array(
        "name" => esc_html__("Job Sectors", "careerfy-frame"),
        "base" => "careerfy_job_categories",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'cats_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                    esc_html__("Style 6", "careerfy-frame") => 'view6',
                    esc_html__("Style 7", "careerfy-frame") => 'view7',
                    esc_html__("Style 8", "careerfy-frame") => 'view8',
                    esc_html__("Style 9", "careerfy-frame") => 'view9',
                    esc_html__("Style 10", "careerfy-frame") => 'view10',
                    esc_html__("Slider", "careerfy-frame") => 'slider',
                ),
                'description' => ''
            ),

            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of Sectors", "careerfy-frame"),
                'param_name' => 'num_cats',
                'value' => '',
                'description' => esc_html__("Set the number of Sectors to show", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of sub Sectors", "careerfy-frame"),
                'param_name' => 'num_cats_child',
                'value' => '',
                'description' => esc_html__("Set the number of sub Sectors to show", "careerfy-frame"),
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view10')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Include Sub Categories", "careerfy-frame"),
                'param_name' => 'sub_cats',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Show Jobs Counts", "careerfy-frame"),
                'param_name' => 'sector_job_counts',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'slider')
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Result Page", "careerfy-frame"),
                'param_name' => 'result_page',
                'value' => $all_page,
                'description' => '',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'order_by',
                'value' => array(
                    esc_html__("By Jobs Count", "careerfy-frame") => 'jobs_count',
                    esc_html__("By Title", "careerfy-frame") => 'title',
                    esc_html__("By Random", "careerfy-frame") => 'id',
                ),
                'description' => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Icon Color", "careerfy-frame"),
                'param_name' => 'icon_color',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view5')
                ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Jobs Number Background Color", "careerfy-frame"),
                'param_name' => 'job_bg_color',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view5')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'cat_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text", "careerfy-frame"),
                'param_name' => 'cat_link_text',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text URL", "careerfy-frame"),
                'param_name' => 'cat_link_text_url',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
        )
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * Adding job types shortcode
 * @return markup
 */
function careerfy_vc_job_types()
{

    $all_page = array(esc_html__("Select Page", "careerfy-frame") => '');
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $all_page[$page->post_title] = $page->ID;
        }
    }

    $attributes = array(
        "name" => esc_html__("Job Types", "careerfy-frame"),
        "base" => "careerfy_job_types",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'cats_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                    esc_html__("Style 6", "careerfy-frame") => 'view6',
                    esc_html__("Style 7", "careerfy-frame") => 'view7',
                    esc_html__("Style 8", "careerfy-frame") => 'view8',
                    esc_html__("Style 9", "careerfy-frame") => 'view9',
                    esc_html__("Style 10", "careerfy-frame") => 'view10',
                    esc_html__("Slider", "careerfy-frame") => 'slider',
                ),
                'description' => ''
            ),

            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of Types", "careerfy-frame"),
                'param_name' => 'num_cats',
                'value' => '',
                'description' => esc_html__("Set the number of Types to show", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Show Jobs Counts", "careerfy-frame"),
                'param_name' => 'sector_job_counts',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'slider')
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Result Page", "careerfy-frame"),
                'param_name' => 'result_page',
                'value' => $all_page,
                'description' => '',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'order_by',
                'value' => array(
                    esc_html__("By Jobs Count", "careerfy-frame") => 'jobs_count',
                    esc_html__("By Title", "careerfy-frame") => 'title',
                    esc_html__("By Random", "careerfy-frame") => 'id',
                ),
                'description' => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Icon Color", "careerfy-frame"),
                'param_name' => 'icon_color',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view5')
                ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Jobs Number Background Color", "careerfy-frame"),
                'param_name' => 'job_bg_color',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('view5')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'cat_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text", "careerfy-frame"),
                'param_name' => 'cat_link_text',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link text URL", "careerfy-frame"),
                'param_name' => 'cat_link_text_url',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'cats_view',
                    'value' => array('slider')
                ),
            ),
        )
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * Adding CV Packages shortcode
 * @return markup
 */
function careerfy_vc_cv_packages()
{

    $all_pckgs = array(esc_html__("Select Package", "careerfy-frame") => '');

    $args = array(
        'post_type' => 'package',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'ASC',
        'orderby' => 'title',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_package_type',
                'value' => 'cv',
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    if ($pkgs_query->found_posts > 0) {
        $pkgs_list = $pkgs_query->posts;

        if (!empty($pkgs_list)) {
            foreach ($pkgs_list as $pkg_item) {
                $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                    $cv_pkg_post = get_post($pkg_item);
                    $cv_pkg_post_name = isset($cv_pkg_post->post_name) ? $cv_pkg_post->post_name : '';
                    $all_pckgs[get_the_title($pkg_item)] = $cv_pkg_post_name;
                }
            }
        }
    }

    $attributes = array(
        "name" => esc_html__("CV Packages", "careerfy-frame"),
        "base" => "careerfy_cv_packages",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_cv_package_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("View", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("View 1", "careerfy-frame") => 'view1',
                    esc_html__("View 2", "careerfy-frame") => 'view2',
                ),
                'description' => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Package Item", "careerfy-frame"),
        "base" => "careerfy_cv_package_item",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_cv_packages'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Select Package", "careerfy-frame"),
                'param_name' => 'att_pck',
                'value' => $all_pckgs,
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Featured", "careerfy-frame"),
                'param_name' => 'featured',
                'value' => array(
                    esc_html__("No", "careerfy-frame") => 'no',
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Subtitle", "careerfy-frame"),
                'param_name' => 'subtitle',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Features", "careerfy-frame"),
                'param_name' => 'pckg_features',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Feature Name', 'careerfy-frame'),
                        'param_name' => 'feat_name',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__("Active", "careerfy-frame"),
                        'param_name' => 'feat_active',
                        'value' => array(
                            esc_html__("Yes", "careerfy-frame") => 'yes',
                            esc_html__("No", "careerfy-frame") => 'no',
                        ),
                        'description' => ''
                    ),
                ),
            ),
        ),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Cv_Packages extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Cv_Package_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Adding Job Packages shortcode
 * @return markup
 */
function careerfy_vc_job_packages()
{

    $all_pckgs = array(esc_html__("Select Package", "careerfy-frame") => '');

    $args = apply_filters('careerfy_job_pkgs_vcsh_args', array(
        'post_type' => 'package',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'ASC',
        'orderby' => 'title',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_package_type',
                'value' => 'job',
                'compare' => '=',
            ),
        ),
    ));
    $pkgs_query = new WP_Query($args);

    if ($pkgs_query->found_posts > 0) {
        $pkgs_list = $pkgs_query->posts;

        if (!empty($pkgs_list)) {
            foreach ($pkgs_list as $pkg_item) {
                $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                    $job_pkg_post = get_post($pkg_item);
                    $job_pkg_post_name = isset($job_pkg_post->post_name) ? $job_pkg_post->post_name : '';
                    $all_pckgs[get_the_title($pkg_item)] = $job_pkg_post_name;
                }
            }
        }
    }

    $job_styles = array(
        esc_html__("Style 1", "careerfy-frame") => 'view1',
        esc_html__("Style 2", "careerfy-frame") => 'view2',
        esc_html__("Style 3", "careerfy-frame") => 'view3',
        esc_html__("Style 4", "careerfy-frame") => 'view4',
        esc_html__("Style 6", "careerfy-frame") => 'view6',

    );


    $job_styles = apply_filters('geek_finder_job_package_style', $job_styles);

    $job_compare_fields = array();
    $job_compare_fields = apply_filters('geek_finder_job_package_compare_fields', $job_compare_fields);

    $attributes = array(
        "name" => esc_html__("Job Packages", "careerfy-frame"),
        "base" => "careerfy_job_packages",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_job_package_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => $job_styles,
                'description' => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Package Item", "careerfy-frame"),
        "base" => "careerfy_job_package_item",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_job_packages'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Select Package", "careerfy-frame"),
                'param_name' => 'att_pck',
                'value' => $all_pckgs,
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Featured", "careerfy-frame"),
                'param_name' => 'featured',
                'value' => array(
                    esc_html__("No", "careerfy-frame") => 'no',
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Duration", "careerfy-frame"),
                'param_name' => 'duration',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Features", "careerfy-frame"),
                'param_name' => 'pckg_features',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Feature Name', 'careerfy-frame'),
                        'param_name' => 'feat_name',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__("Active", "careerfy-frame"),
                        'param_name' => 'feat_active',
                        'value' => array(
                            esc_html__("Yes", "careerfy-frame") => 'yes',
                            esc_html__("No", "careerfy-frame") => 'no',
                        ),
                        'description' => ''
                    ),
                ),
            ),
        ),
    );
    $attributes = apply_filters('geek_finder_job_package_compare_fields', $attributes);
    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Job_Packages extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Job_Package_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Adding All Packages shortcode
 * @return markup
 */
function careerfy_vc_all_packages()
{
    $all_pckgs = array(esc_html__("Select Package", "careerfy-frame") => '');
    $args = apply_filters('careerfy_job_pkgs_vcsh_args', array(
        'post_type' => 'package',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'ASC',
        'orderby' => 'title',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_package_type',
                'value' => apply_filters('jobsearch_careerfy_allpkg_shtypes_list', array('job', 'featured_jobs', 'cv', 'emp_allin_one', 'feature_job', 'candidate', 'promote_profile', 'urgent_pkg', 'candidate_profile', 'employer_profile', 'cand_resume')),
                'compare' => 'IN',
            ),
        ),
    ));
    $pkgs_query = new WP_Query($args);
    if ($pkgs_query->found_posts > 0) {
        $pkgs_list = $pkgs_query->posts;
        if (!empty($pkgs_list)) {
            foreach ($pkgs_list as $pkg_item) {
                $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                    $job_pkg_post = get_post($pkg_item);
                    $job_pkg_post_name = isset($job_pkg_post->post_name) ? $job_pkg_post->post_name : '';
                    $all_pckgs[get_the_title($pkg_item)] = $job_pkg_post_name;
                }
            }
        }
    }
    $job_styles = array(
        esc_html__("Style 1", "careerfy-frame") => 'view1',
        esc_html__("Style 2", "careerfy-frame") => 'view2',
        esc_html__("Style 3", "careerfy-frame") => 'view3',
        esc_html__("Style 4", "careerfy-frame") => 'view4',
        esc_html__("Style 5", "careerfy-frame") => 'view5',
        esc_html__("Style 6", "careerfy-frame") => 'view6',
        esc_html__("Style 7", "careerfy-frame") => 'view7',
        esc_html__("Style 8", "careerfy-frame") => 'view8',
        esc_html__("Style 9", "careerfy-frame") => 'view9',
        esc_html__("Style 10", "careerfy-frame") => 'view10',
        esc_html__("Style 11", "careerfy-frame") => 'view11',
        esc_html__("Style 12", "careerfy-frame") => 'view12',
    );

    $attributes = array(
        "name" => esc_html__("All Packages", "careerfy-frame"),
        "base" => "careerfy_all_packages",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_all_package_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => $job_styles,
                'description' => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Package Item", "careerfy-frame"),
        "base" => "careerfy_all_package_item",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_all_packages'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Select Package", "careerfy-frame"),
                'param_name' => 'att_pck',
                'value' => $all_pckgs,
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Featured", "careerfy-frame"),
                'param_name' => 'featured',
                'value' => array(
                    esc_html__("No", "careerfy-frame") => 'no',
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Duration", "careerfy-frame"),
                'param_name' => 'duration',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Features", "careerfy-frame"),
                'param_name' => 'pckg_features',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Feature Name', 'careerfy-frame'),
                        'param_name' => 'feat_name',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__("Active", "careerfy-frame"),
                        'param_name' => 'feat_active',
                        'value' => array(
                            esc_html__("Yes", "careerfy-frame") => 'yes',
                            esc_html__("No", "careerfy-frame") => 'no',
                        ),
                        'description' => ''
                    ),
                ),
            ),
        ),
    );
    $attributes = apply_filters('geek_finder_job_package_compare_fields', $attributes);
    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_All_Packages extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_All_Package_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Explore Jobs
 * @return markup
 */
function careerfy_vc_explore_jobs()
{
    $all_page = array(esc_html__("Select Page", "careerfy-frame") => '');
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $all_page[$page->post_title] = $page->ID;
        }
    }

    $attributes = array(
        "name" => esc_html__("Explore Jobs", "careerfy-frame"),
        "base" => "careerfy_explore_jobs",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_explore_jobs_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "js_view" => 'VcColumnView',
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'list_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'style1',
                    esc_html__("Style 2", "careerfy-frame") => 'style2',
                    esc_html__("Style 3", "careerfy-frame") => 'style3',
                    esc_html__("Style 4", "careerfy-frame") => 'style4',
                ),
                'description' => esc_html__("Select a listing view.", "careerfy-frame")
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Title Color", "careerfy-frame"),
                'param_name' => 'title_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("List Color", "careerfy-frame"),
                'param_name' => 'list_items_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Button Color", "careerfy-frame"),
                'param_name' => 'btn_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Button Text Color", "careerfy-frame"),
                'param_name' => 'btn_text_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button text", "careerfy-frame"),
                'param_name' => 'button_text',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'button_url',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Load More", "careerfy-frame"),
                'param_name' => 'load_more',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ""
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Load More Text", "careerfy-frame"),
                'param_name' => 'load_more_text',
                'value' => '',
                'description' => ""
            )
        ),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $attributes = array(
        "name" => esc_html__("Explore Jobs Item", "careerfy-frame"),
        "base" => "careerfy_explore_jobs_item",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_explore_jobs'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Jobs by", "careerfy-frame"),
                'param_name' => 'jobs_by',
                'value' => array(
                    esc_html__("Job Type", "careerfy-frame") => 'jobtype',
                    esc_html__("Skills", "careerfy-frame") => 'skill',
                    esc_html__("Category", "careerfy-frame") => 'sector',
                    esc_html__("Top Companies", "careerfy-frame") => 'employer',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Result Page", "careerfy-frame"),
                'param_name' => 'result_page',
                'value' => $all_page,
                'description' => '',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Sector", "careerfy-frame"),
                'param_name' => 'employer_cat',
                'value' => $cate_array,
                'description' => esc_html__("Select Sector.", "careerfy-frame"),
                'dependency' => array(
                    'element' => 'jobs_by',
                    'value' => 'employer',
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'explore_job_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of jobs", "careerfy-frame"),
                'param_name' => 'jobs_numbers',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'job_order',
                'value' => array(
                    esc_html__("Descending", "careerfy-frame") => 'DESC',
                    esc_html__("Ascending", "careerfy-frame") => 'ASC',
                ),
                'description' => esc_html__("Choose job list items order.", "careerfy-frame")
            ),

        ),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Explore_Jobs extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Explore_Jobs_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Adding Candidate Applications Packages shortcode
 * @return markup
 */
function careerfy_vc_candidate_packages()
{

    $all_pckgs = array(esc_html__("Select Package", "careerfy-frame") => '');

    $args = array(
        'post_type' => 'package',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'ASC',
        'orderby' => 'title',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_package_type',
                'value' => 'candidate',
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    if ($pkgs_query->found_posts > 0) {
        $pkgs_list = $pkgs_query->posts;

        if (!empty($pkgs_list)) {
            foreach ($pkgs_list as $pkg_item) {
                $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                    $_pkg_post = get_post($pkg_item);
                    $_pkg_post_name = isset($_pkg_post->post_name) ? $_pkg_post->post_name : '';
                    $all_pckgs[get_the_title($pkg_item)] = $_pkg_post_name;
                }
            }
        }
    }

    $attributes = array(
        "name" => esc_html__("Candidate Packages", "careerfy-frame"),
        "base" => "careerfy_candidate_packages",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_candidate_package_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => apply_filters('careerfy_candidate_pkgs_vcsh_params', array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("View", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("View 1", "careerfy-frame") => 'view1',
                ),
                'description' => ''
            ),
        )),
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Package Item", "careerfy-frame"),
        "base" => "careerfy_candidate_package_item",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_candidate_packages'),
        "show_settings_on_create" => true,
        "params" => apply_filters('careerfy_candidate_pkg_item_vcsh_params', array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Select Package", "careerfy-frame"),
                'param_name' => 'att_pck',
                'value' => $all_pckgs,
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Featured", "careerfy-frame"),
                'param_name' => 'featured',
                'value' => array(
                    esc_html__("No", "careerfy-frame") => 'no',
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Subtitle", "careerfy-frame"),
                'param_name' => 'subtitle',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Features", "careerfy-frame"),
                'param_name' => 'pckg_features',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Feature Name', 'careerfy-frame'),
                        'param_name' => 'feat_name',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__("Active", "careerfy-frame"),
                        'param_name' => 'feat_active',
                        'value' => array(
                            esc_html__("Yes", "careerfy-frame") => 'yes',
                            esc_html__("No", "careerfy-frame") => 'no',
                        ),
                        'description' => ''
                    ),
                ),
            ),
        )),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Candidate_Packages extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode') && class_exists('JobSearch_plugin')) {

        class WPBakeryShortCode_Careerfy_Candidate_Package_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * adding jobs listing shortcode
 * @return markup
 */
function careerfy_vc_how_it_works()
{
    $attributes = array(
        "name" => esc_html__("How It Works", "careerfy-frame"),
        "base" => "jobsearch_how_it_works_shortcode",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'step_1_image',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Image Description", "careerfy-frame"),
                'param_name' => 'step_1_image_desc',
                'value' => '',
                'description' => esc_html__('Description will show below Image.', 'careerfy-frame'),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'step_1_title',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'step_1_desc',
                'value' => '',
                'description' => ''
            ),
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "step_1_icon",
                'value' => '',
                "description" => '',

            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Icon Color", "careerfy-frame"),
                'param_name' => 'step_1_icon_color',
                'value' => '',
                'description' => '',

            ),
            /////////// Step 2 Fields /////////////
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'step_2_image',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "step_2_icon",
                'value' => '',
                "description" => '',
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Icon Color", "careerfy-frame"),
                'param_name' => 'step_2_icon_color',
                'value' => '',
                'description' => esc_html__('Icon color will only effect on style 2.'),
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'step_2_image_desc',
                'value' => '',
                'description' => esc_html__('Description will show below Image.'),
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'step_2_title',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'step_2_desc',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 2", "careerfy-frame"),
            ),
            ///////////// Step 3 Fields //////////////////
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Step 3 Options", "careerfy-frame"),
                'param_name' => 'step_3_opts',
                'params' => array(
                    array(
                        'type' => 'iconpicker',
                        'value' => '',
                        'heading' => __('Icon', 'careerfy-frame'),
                        'param_name' => 'step_3_icon',
                    ),
                    array(
                        'type' => 'colorpicker',
                        'heading' => esc_html__("Icon Color", "careerfy-frame"),
                        'param_name' => 'step_3_icon_color',
                        'value' => '',
                        'description' => '',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__("Title", "careerfy-frame"),
                        'param_name' => 'step_3_title',
                        'value' => '',
                        'description' => '',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__("Checked", "careerfy-frame"),
                        'param_name' => 'step_3_checked_1',
                        'value' => array(
                            esc_html__("Yes", "careerfy-frame") => 'yes',
                            esc_html__("No", "careerfy-frame") => 'no',
                        ),
                        'description' => '',
                        'group' => esc_html__("Step 3", "careerfy-frame"),
                    ),
                ),
                'group' => esc_html__("Step 3", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'step_3_title',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 3", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'step_3_desc',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 3", "careerfy-frame"),
            ),
            ///////////// Step 4 Fields //////////////////
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'step_4_image',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 4", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'step_4_title',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 4", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'step_4_desc',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Step 4", "careerfy-frame"),
            ),
        ),
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding jobs listing shortcode
 * @return markup
 */
function careerfy_vc_jobs_listing()
{
    global $jobsearch_gdapi_allocation, $jobsearch_plugin_options;
    $jobsearch__options = get_option('jobsearch_plugin_options');
    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $jobsearch_job_cus_fields = get_option("jobsearch_custom_field_job");
    $job_cus_field_arr = array();
    if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
        foreach ($jobsearch_job_cus_fields as $key => $value) {
            $job_cus_field_arr[$value['label']] = $key;
        }
    }

    $job_listsh_parms = array();
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("View", "careerfy-frame"),
        'param_name' => 'job_view',
        'value' => array(
            esc_html__("Style 1", "careerfy-frame") => 'view-default',
            esc_html__("Style 2", "careerfy-frame") => 'view-medium',
            esc_html__("Style 3", "careerfy-frame") => 'view-listing2',
            esc_html__("Style 4", "careerfy-frame") => 'view-grid2',
            esc_html__("Style 5", "careerfy-frame") => 'view-medium2',
            esc_html__("Style 6", "careerfy-frame") => 'view-grid',
            esc_html__("Style 7", "careerfy-frame") => 'view-medium3',
            esc_html__("Style 8", "careerfy-frame") => 'view-grid3',
            esc_html__("Style 9", "careerfy-frame") => 'view-grid-4',
        ),
        'description' => esc_html__("Select jobs listing view.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'job_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Founds with display counts", "careerfy-frame"),
        'param_name' => 'display_per_page',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Display the per page jobs count at top of the listing.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Featured Only", "careerfy-frame"),
        'param_name' => 'featured_only',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Employer Base Jobs", "careerfy-frame"),
        'param_name' => 'jobs_emp_base',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Show only Selected Employer Jobs.", "careerfy-frame"),
        'group' => esc_html__("Employer Base Jobs", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Employer ID", "careerfy-frame"),
        'param_name' => 'jobs_emp_base_id',
        'value' => '',
        'description' => esc_html__("Put employer ID here.", "careerfy-frame"),
        'group' => esc_html__("Employer Base Jobs", "careerfy-frame"),
    );
    if ($all_locations_type == 'api') {

        $api_contries_list = array();
        if (class_exists('JobSearch_plugin')) {
            $api_contries_list = $jobsearch_gdapi_allocation::get_countries();
        }
        if (!empty($api_contries_list)) {
            foreach ($api_contries_list as $api_cntry_key => $api_cntry_val) {
                if (isset($api_cntry_val->code)) {
                    $contry_arr_list[$api_cntry_val->code] = $api_cntry_val->name;
                }
            }
        }

//        $contry_arr_list = array('' => esc_html__("Select Country", "careerfy-frame"));
//        if (!empty($api_contries_list)) {
//            foreach ($api_contries_list as $api_cntry_key => $api_cntry_val) {
//                if (isset($api_cntry_val['code'])) {
//                    $contry_arr_list[$api_cntry_val['code']] = $api_cntry_val['name'];
//                }
//            }
//        }

        $job_listsh_parms[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Selected Location Jobs", "careerfy-frame"),
            'param_name' => 'selct_loc_jobs',
            'value' => array(
                esc_html__("No", "careerfy-frame") => 'no',
                esc_html__("Yes", "careerfy-frame") => 'yes',
            ),
            'description' => esc_html__("Show only Selected Location Jobs.", "careerfy-frame"),
            'group' => esc_html__("Location Based Jobs", "careerfy-frame"),
        );
        $job_listsh_parms[] = array(
            'type' => 'jobsearch_gapi_locs',
            'heading' => esc_html__("Select Location", "careerfy-frame"),
            'param_name' => 'selct_gapiloc_str',
            'api_contry_list' => $contry_arr_list,
            'value' => '',
            'description' => '',
            'group' => esc_html__("Location Based Jobs", "careerfy-frame"),
        );
    }
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters", "careerfy-frame"),
        'param_name' => 'job_filters',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs searching filters switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Count", "careerfy-frame"),
        'param_name' => 'job_filters_count',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Sort by", "careerfy-frame"),
        'param_name' => 'job_filters_sortby',
        'value' => array(
            esc_html__("Default", "careerfy-frame") => 'default',
            esc_html__("Ascending", "careerfy-frame") => 'asc',
            esc_html__("Descending", "careerfy-frame") => 'desc',
            esc_html__("Alphabetical", "careerfy-frame") => 'alpha',
            esc_html__("Highest Count", "careerfy-frame") => 'count',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Keyword Search", "careerfy-frame"),
        'param_name' => 'job_filters_keyword',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Jobs filters 'Keyword Search' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations", "careerfy-frame"),
        'param_name' => 'job_filters_loc',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs searching filters 'Location' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations Filter Collapse", "careerfy-frame"),
        'param_name' => 'job_filters_loc_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations Filter Style", "careerfy-frame"),
        'param_name' => 'job_filters_loc_view',
        'value' => array(
            esc_html__("Checkbox List", "careerfy-frame") => 'checkboxes',
            esc_html__("Dropdown Fields", "careerfy-frame") => 'dropdowns',
            esc_html__("Input Field", "careerfy-frame") => 'input',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted", "careerfy-frame"),
        'param_name' => 'job_filters_date',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs searching filters 'Date Posted' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted Filter Collapse", "careerfy-frame"),
        'param_name' => 'job_filters_date_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Type", "careerfy-frame"),
        'param_name' => 'job_filters_type',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs searching filters 'Job Type' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Type Filter Collapse", "careerfy-frame"),
        'param_name' => 'job_filters_type_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'job_filters_sector',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs searching filters 'Sector' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector Filter Collapse", "careerfy-frame"),
        'param_name' => 'job_filters_sector_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Map", "careerfy-frame"),
        'param_name' => 'job_top_map',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Jobs top map switch.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Height", "careerfy-frame"),
        'param_name' => 'job_top_map_height',
        'value' => '450',
        'description' => esc_html__("Jobs top map height.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Zoom", "careerfy-frame"),
        'param_name' => 'job_top_map_zoom',
        'value' => '8',
        'description' => esc_html__("Jobs top map zoom.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Featured Jobs on Top", "careerfy-frame"),
        'param_name' => 'job_feat_jobs_top',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Featured jobs will display on top of listing.", "careerfy-frame"),
        'group' => esc_html__("Featured Jobs", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Number of Featured jobs", "careerfy-frame"),
        'param_name' => 'num_of_feat_jobs',
        'value' => '5',
        'description' => '',
        'group' => esc_html__("Featured Jobs", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Bar", "careerfy-frame"),
        'param_name' => 'job_top_search',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Jobs top search bar switch.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Style", "careerfy-frame"),
        'param_name' => 'job_top_search_view',
        'value' => array(
            esc_html__("Simple", "careerfy-frame") => 'simple',
            esc_html__("Advance Search", "careerfy-frame") => 'advance',
        ),
        'description' => esc_html__("Jobs top search style.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Radius", "careerfy-frame"),
        'param_name' => 'job_top_search_radius',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'default' => 'yes',
        'description' => esc_html__("Enable/Disable top search radius.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Title, Keywords, or Phrase", "careerfy-frame"),
        'param_name' => 'top_search_title',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location", "careerfy-frame"),
        'param_name' => 'top_search_location',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable location field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    if ($sectors_enable_switch == 'on') {
        $job_listsh_parms[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Sector", "careerfy-frame"),
            'param_name' => 'top_search_sector',
            'value' => array(
                esc_html__("Yes", "careerfy-frame") => 'yes',
                esc_html__("No", "careerfy-frame") => 'no',
            ),
            'description' => esc_html__("Enable/Disable Sector Dropdown field.", "careerfy-frame"),
            'group' => esc_html__("Top Search", "careerfy-frame"),
        );
    }
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("AutoFill Search Box", "careerfy-frame"),
        'param_name' => 'top_search_autofill',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable autofill in search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );

    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sort by Fields", "careerfy-frame"),
        'param_name' => 'job_sort_by',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Results search sorting section switch. When choosing option yes then jobs display counts will show.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Locations in listing", "careerfy-frame"),
        'param_name' => 'job_loc_listing',
        'value' => array(
            esc_html__("Country", "careerfy-frame") => 'country',
            esc_html__("State", "careerfy-frame") => 'state',
            esc_html__("City", "careerfy-frame") => 'city',
        ),
        'std' => 'country,city',
        'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("RSS Feed", "careerfy-frame"),
        'param_name' => 'job_rss_feed',
        'dependency' => array(
            'element' => 'job_sort_by',
            'value' => 'yes',
        ),
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => ''
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Excerpt Length", "careerfy-frame"),
        'param_name' => 'job_excerpt',
        'value' => '20',
        'description' => esc_html__("Set the number of words you want to show for excerpt.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'job_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Choose job list items order.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'job_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
        ),
        'description' => esc_html__("Choose job list items orderby.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Pagination", "careerfy-frame"),
        'param_name' => 'job_pagination',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Choose yes if you want to show pagination for job items.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Jobs per Page", "careerfy-frame"),
        'param_name' => 'job_per_page',
        'value' => '10',
        'description' => esc_html__("Set number that how much jobs you want to show per page. Leave it blank for all jobs on a single page.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Quick Apply job", "careerfy-frame"),
        'param_name' => 'quick_apply_job',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'off',
            esc_html__("Yes", "careerfy-frame") => 'on',
        ),
        'description' => esc_html__("By setting this option to yes, when user will click on job title or image, pop-up will be appear from the side.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Custom Fields", "careerfy-frame"),
        'param_name' => 'job_custom_fields_switch',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Enable / Disable job custom fields", "careerfy-frame"),
        'group' => esc_html__("Custom Fields", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Select Fields", "careerfy-frame"),
        'param_name' => 'job_elem_custom_fields',
        'value' => $job_cus_field_arr,
        'description' => '',
        'group' => esc_html__("Custom Fields", "careerfy-frame"),
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Deadline", "careerfy-frame"),
        'param_name' => 'job_deadline_switch',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'Yes',
        ),
        'description' => esc_html__("Enable / Disable jobs deadline date in listings", "careerfy-frame"),
    );

    //
    $attributes = array(
        "name" => esc_html__("Jobs Listing", "careerfy-frame"),
        "base" => "jobsearch_job_shortcode",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => apply_filters('jobsearch_job_listings_vcsh_params', $job_listsh_parms)
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding jobs simple listing shortcode
 * @return markup
 */
function careerfy_vc_simple_jobs_listing()
{
    global $jobsearch_gdapi_allocation;
    $jobsearch__options = get_option('jobsearch_plugin_options');

    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $job_listsh_parms = array();
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("View", "careerfy-frame"),
        'param_name' => 'job_list_style',
        'value' => array(
            esc_html__("Style 1", "careerfy-frame") => 'style1',
            esc_html__("Style 2", "careerfy-frame") => 'style2',
            esc_html__("Style 3", "careerfy-frame") => 'style3',
            esc_html__("Style 4", "careerfy-frame") => 'style4',
            esc_html__("Style 5", "careerfy-frame") => 'style5',
            esc_html__("Style 6", "careerfy-frame") => 'style6',
            esc_html__("Style 7", "careerfy-frame") => 'style7',
            esc_html__("Style 8", "careerfy-frame") => 'style8',
            esc_html__("Style 9", "careerfy-frame") => 'style9',
        ),
    );

    $job_listsh_parms[] = array(
        'type' => 'careerfy_browse_img',
        'heading' => esc_html__("Image", "careerfy-frame"),
        'param_name' => 'title_img',
        'value' => '',
        'description' => esc_html__("Image will show above title", "careerfy-frame"),
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => 'style4'
        ),
    );

    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Title", "careerfy-frame"),
        'param_name' => 'job_list_title',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => array('style1', 'style2', 'style3', 'style4')
        ),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Description", "careerfy-frame"),
        'param_name' => 'job_list_description',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => array('style2', 'style4')
        ),
    );
    $job_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Locations in listing", "careerfy-frame"),
        'param_name' => 'job_list_loc_listing',
        'value' => array(
            esc_html__("Country", "careerfy-frame") => 'country',
            esc_html__("State", "careerfy-frame") => 'state',
            esc_html__("City", "careerfy-frame") => 'city',
        ),
        'std' => 'country,city',
        'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'job_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Featured Only", "careerfy-frame"),
        'param_name' => 'featured_only',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'job_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Choose job list items order.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'job_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
        ),
        'description' => esc_html__("Choose job list items orderby.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Number of Jobs", "careerfy-frame"),
        'param_name' => 'job_per_page',
        'value' => '10',
        'description' => esc_html__("Set number that how many jobs you want to show.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Load More Jobs", "careerfy-frame"),
        'param_name' => 'job_load_more',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Choose yes if you want to show more job items.", "careerfy-frame"),
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => array('style1', 'style2', 'style7', 'style8', 'style4', 'style5', 'style6', 'style9')
        ),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Link text", "careerfy-frame"),
        'param_name' => 'job_link_text',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => array('style3')
        ),
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Link text URL", "careerfy-frame"),
        'param_name' => 'job_link_text_url',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'job_list_style',
            'value' => array('style3')
        ),
    );
    $attributes = array(
        "name" => esc_html__("Simple Jobs Listing", "careerfy-frame"),
        "base" => "jobsearch_simple_jobs_listing",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => $job_listsh_parms,
    );
    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding jobs simple listing multi shortcode
 * @return markup
 */
function careerfy_vc_simple_jobs_listing_multi()
{
    global $jobsearch_gdapi_allocation;

    $jobsearch__options = get_option('jobsearch_plugin_options');
    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));
    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $job_listsh_parms = array();
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'job_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'job_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Order dropdown will work for featured jobs only", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'job_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
        ),
        'description' => esc_html__("Choose job list items orderby.", "careerfy-frame")
    );
    $job_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Number of Jobs", "careerfy-frame"),
        'param_name' => 'job_per_page',
        'value' => '10',
        'description' => esc_html__("Set number that how many jobs you want to show.", "careerfy-frame")
    );

    //
    $attributes = array(
        "name" => esc_html__("Simple Jobs Listing Multi", "careerfy-frame"),
        "base" => "jobsearch_simple_jobs_listing_multi",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => $job_listsh_parms,
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding employer listing shortcode
 * @return markup
 */
function careerfy_vc_employer_listing()
{
    global $jobsearch_plugin_options;
    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));
    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }
    $emp_listsh_parms = [];
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("View", "careerfy-frame"),
        'param_name' => 'employer_view',
        'value' => array(
            esc_html__("Style 1", "careerfy-frame") => 'view-default',
            esc_html__("Style 2", "careerfy-frame") => 'view-grid',
            esc_html__("Style 3", "careerfy-frame") => 'view-slider',
        ),
        'description' => esc_html__("Select employers listing view.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'employer_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );

    $emp_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Locations in listing", "careerfy-frame"),
        'param_name' => 'employer_loc_listing',
        'value' => array(
            esc_html__("Country", "careerfy-frame") => 'country',
            esc_html__("State", "careerfy-frame") => 'state',
            esc_html__("City", "careerfy-frame") => 'city',
        ),
        'std' => 'country,city',
        'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Employer Founds with display counts", "careerfy-frame"),
        'param_name' => 'display_per_page',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Display the per page empoyers count at top of the listing.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters", "careerfy-frame"),
        'param_name' => 'employer_filters',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Employers searching filters switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Count", "careerfy-frame"),
        'param_name' => 'employer_filters_count',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Sort by", "careerfy-frame"),
        'param_name' => 'employer_filters_sortby',
        'value' => array(
            esc_html__("Default", "careerfy-frame") => 'default',
            esc_html__("Ascending", "careerfy-frame") => 'asc',
            esc_html__("Descending", "careerfy-frame") => 'desc',
            esc_html__("Alphabetical", "careerfy-frame") => 'alpha',
            esc_html__("Highest Count", "careerfy-frame") => 'count',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations", "careerfy-frame"),
        'param_name' => 'employer_filters_loc',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Employers searching filters 'Location' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations Filter Collapse", "careerfy-frame"),
        'param_name' => 'employer_filters_loc_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Locations Filter Style", "careerfy-frame"),
        'param_name' => 'employer_filters_loc_view',
        'value' => array(
            esc_html__("Checkbox List", "careerfy-frame") => 'checkboxes',
            esc_html__("Dropdown Fields", "careerfy-frame") => 'dropdowns',
            esc_html__("Input Field", "careerfy-frame") => 'input',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted", "careerfy-frame"),
        'param_name' => 'employer_filters_date',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Employers searching filters 'Date Posted' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted Collapse", "careerfy-frame"),
        'param_name' => 'employer_filters_date_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'employer_filters_sector',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Employers searching filters 'Sector' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector Filter Collapse", "careerfy-frame"),
        'param_name' => 'employer_filters_sector_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Team Size", "careerfy-frame"),
        'param_name' => 'employer_filters_team',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Employers searching filters 'Team Size' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Team Size Filter Collapse", "careerfy-frame"),
        'param_name' => 'employer_filters_team_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Map", "careerfy-frame"),
        'param_name' => 'emp_top_map',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Employers top map switch.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Height", "careerfy-frame"),
        'param_name' => 'emp_top_map_height',
        'value' => '450',
        'description' => esc_html__("Employers top map height.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Zoom", "careerfy-frame"),
        'param_name' => 'emp_top_map_zoom',
        'value' => '8',
        'description' => esc_html__("Employers top map zoom.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Bar", "careerfy-frame"),
        'param_name' => 'emp_top_search',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Employer&apos;s top search bar switch.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Style", "careerfy-frame"),
        'param_name' => 'emp_top_search_view',
        'value' => array(
            esc_html__("Simple", "careerfy-frame") => 'simple',
            esc_html__("Advance Search", "careerfy-frame") => 'advance',
        ),
        'description' => esc_html__("Employers top search style.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Radius", "careerfy-frame"),
        'param_name' => 'emp_top_search_radius',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable top search radius.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );

    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("AutoFill Search Box", "careerfy-frame"),
        'param_name' => 'top_search_autofill',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable autofill in search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Title, Keywords, or Phrase", "careerfy-frame"),
        'param_name' => 'emp_top_search_title',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location", "careerfy-frame"),
        'param_name' => 'emp_top_search_location',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable location field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    if ($sectors_enable_switch == 'on') {
        $emp_listsh_parms[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Sector", "careerfy-frame"),
            'param_name' => 'emp_top_search_sector',
            'value' => array(
                esc_html__("Yes", "careerfy-frame") => 'yes',
                esc_html__("No", "careerfy-frame") => 'no',
            ),
            'description' => esc_html__("Enable/Disable Sector Dropdown field.", "careerfy-frame"),
            'group' => esc_html__("Top Search", "careerfy-frame"),
        );
    }
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sort by Fields", "careerfy-frame"),
        'param_name' => 'employer_sort_by',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Results search sorting section switch.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Excerpt Length", "careerfy-frame"),
        'param_name' => 'employer_excerpt',
        'value' => '20',
        'description' => esc_html__("Set the number of words you want to show for excerpt.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'employer_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Choose job list items order.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'employer_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
            esc_html__("Promote Profile", "careerfy-frame") => 'promote_profile',
        ),
        'description' => esc_html__("Choose list items orderby.", "careerfy-frame")
    );
    $emp_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Pagination", "careerfy-frame"),
        'param_name' => 'employer_pagination',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Choose yes if you want to show pagination for employer items.", "careerfy-frame")
    );

    $emp_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Items per Page", "careerfy-frame"),
        'param_name' => 'employer_per_page',
        'value' => '10',
        'description' => esc_html__("Set number that how much employers you want to show per page. Leave it blank for all employers on a single page.", "careerfy-frame")
    );
    $attributes = array(
        "name" => esc_html__("Employer Listing", "careerfy-frame"),
        "base" => "jobsearch_employer_shortcode",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => apply_filters('jobsearch_employer_listings_vcsh_params', $emp_listsh_parms)
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * App Promo
 * @return markup
 */
function careerfy_vc_promo_statistics_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Promo Statistics", "careerfy-frame"),
        "base" => "jobsearch_promo_stats",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Title Color", "careerfy-frame"),
                'param_name' => 'title_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Description Color", "careerfy-frame"),
                'param_name' => 'desc_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title 1", "careerfy-frame"),
                'param_name' => 'promo_title_1',
                'value' => '',
                'description' => '',

            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description 1", "careerfy-frame"),
                'param_name' => 'promo_desc_1',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title 2", "careerfy-frame"),
                'param_name' => 'promo_title_2',
                'value' => '',
                'description' => '',

            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description 2", "careerfy-frame"),
                'param_name' => 'promo_desc_2',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Rating", "careerfy-frame"),
                'param_name' => 'promo_ranking',
                'value' => '',
                'description' => 'Enter the rating in numbers',

            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Rating Description", "careerfy-frame"),
                'param_name' => 'promo_rating_desc',
                'value' => '',
                'description' => '',
            ),
        )
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * Simple Employers
 * @return markup
 */
function careerfy_vc_simple_employers_shortcode()
{

    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $attributes = array(
        "name" => esc_html__("Simple Employers Listing", "careerfy-frame"),
        "base" => "jobsearch_simple_employers",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'employer_style',
                'value' => array(
                    esc_html__("Simple Style 1", "careerfy-frame") => 'simple',
                    esc_html__("Simple Style 2", "careerfy-frame") => 'style3',
                    esc_html__("Simple Style 3", "careerfy-frame") => 'style4',
                    esc_html__("Simple Style 4", "careerfy-frame") => 'style5',
                    esc_html__("Slider Style 1", "careerfy-frame") => 'slider',
                    esc_html__("Slider Style 2", "careerfy-frame") => 'slider2',
                    esc_html__("Slider Style 3", "careerfy-frame") => 'slider3',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'employer_title',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'employer_style',
                    'value' => array('slider2')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link Text", "careerfy-frame"),
                'param_name' => 'link_text',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'employer_style',
                    'value' => array('slider2')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Link Text URL", "careerfy-frame"),
                'param_name' => 'link_text_url',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'employer_style',
                    'value' => array('slider2')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Sector", "careerfy-frame"),
                'param_name' => 'employer_cat',
                'value' => $cate_array,
                'description' => esc_html__("Select Sector.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'employer_order',
                'value' => array(
                    esc_html__("Descending", "careerfy-frame") => 'DESC',
                    esc_html__("Ascending", "careerfy-frame") => 'ASC',
                ),
                'description' => esc_html__("Choose list items order.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Orderby", "careerfy-frame"),
                'param_name' => 'employer_orderby',
                'value' => array(
                    esc_html__("Date", "careerfy-frame") => 'date',
                    esc_html__("Title", "careerfy-frame") => 'title',
                    esc_html__("Promote Profile", "careerfy-frame") => 'promote_profile',
                    esc_html__("Active Jobs", "careerfy-frame") => 'meta_value_num',
                ),
                'description' => esc_html__("Choose list items orderby.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Number of Items", "careerfy-frame"),
                'param_name' => 'employer_per_page',
                'value' => '20',
                'description' => esc_html__("Set number that how many employers you want to show.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Load more Employers", "careerfy-frame"),
                'param_name' => 'employer_load_more',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'dependency' => array(
                    'element' => 'employer_style',
                    'value' => array('simple'),
                ),
                'description' => esc_html__("Enable/Disable Load more Employers button.", "careerfy-frame")
            ),
        )
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding candidate listing shortcode
 * @return markup
 */
function careerfy_vc_candidate_listing()
{
    global $jobsearch_plugin_options;
    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $jobsearch_job_cus_fields = get_option("jobsearch_custom_field_candidate");
    $job_cus_field_arr = array();
    if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
        foreach ($jobsearch_job_cus_fields as $key => $value) {
            $job_cus_field_arr[$value['label']] = $key;
        }
    }
    $cand_listsh_parms = [];
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("View", "careerfy-frame"),
        'param_name' => 'candidate_view',
        'value' => array(
            esc_html__("Style 1", "careerfy-frame") => 'view-default',
            esc_html__("Style 2", "careerfy-frame") => 'view-grid',
            esc_html__("Style 3", "careerfy-frame") => 'view-classic',
            esc_html__("Style 4", "careerfy-frame") => 'view-modern',
            esc_html__("Style 5", "careerfy-frame") => 'view-fancy',
            esc_html__("Style 6", "careerfy-frame") => 'view-fancy-2',
            esc_html__("Style 7", "careerfy-frame") => 'view-fancy-3',
            esc_html__("Style 8", "careerfy-frame") => 'view-fancy-4',
        ),
        'description' => esc_html__("Select candidates listing view.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'candidate_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Candidate Founds with display counts", "careerfy-frame"),
        'param_name' => 'display_per_page',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Display the per page candidates count at top of the listing.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("First Button Color", "careerfy-frame"),
        'param_name' => 'first_btn_color',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'candidate_view',
            'value' => array('view-fancy-3')
        ),
    );
    $cand_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Locations in listing", "careerfy-frame"),
        'param_name' => 'candidate_loc_listing',
        'value' => array(
            esc_html__("Country", "careerfy-frame") => 'country',
            esc_html__("State", "careerfy-frame") => 'state',
            esc_html__("City", "careerfy-frame") => 'city',
        ),
        'std' => 'country,city',
        'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Second Button Color", "careerfy-frame"),
        'param_name' => 'second_btn_color',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'candidate_view',
            'value' => array('view-fancy-3')
        ),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters", "careerfy-frame"),
        'param_name' => 'candidate_filters',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Candidates searching filters switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Count", "careerfy-frame"),
        'param_name' => 'candidate_filters_count',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Filters Sort by", "careerfy-frame"),
        'param_name' => 'candidate_filters_sortby',
        'value' => array(
            esc_html__("Default", "careerfy-frame") => 'default',
            esc_html__("Ascending", "careerfy-frame") => 'asc',
            esc_html__("Descending", "careerfy-frame") => 'desc',
            esc_html__("Alphabetical", "careerfy-frame") => 'alpha',
            esc_html__("Highest Count", "careerfy-frame") => 'count',
        ),
        'description' => esc_html__("Show result counts in front of every filter.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted", "careerfy-frame"),
        'param_name' => 'candidate_filters_date',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Candidates searching filters 'Date Posted' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Date Posted Collapse", "careerfy-frame"),
        'param_name' => 'candidate_filters_date_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'candidate_filters_sector',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Candidates searching filters 'Sector' switch.", "careerfy-frame"),
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector Filter Collapse", "careerfy-frame"),
        'param_name' => 'candidate_filters_sector_collapse',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => '',
        'group' => esc_html__("Filters Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Map", "careerfy-frame"),
        'param_name' => 'cand_top_map',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Candidates top map switch.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Height", "careerfy-frame"),
        'param_name' => 'cand_top_map_height',
        'value' => '450',
        'description' => esc_html__("Candidates top map height.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Map Zoom", "careerfy-frame"),
        'param_name' => 'cand_top_map_zoom',
        'value' => '8',
        'description' => esc_html__("Candidates top map zoom.", "careerfy-frame"),
        'group' => esc_html__("Map Settings", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Bar", "careerfy-frame"),
        'param_name' => 'cand_top_search',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Candidates top search bar switch.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Style", "careerfy-frame"),
        'param_name' => 'cand_top_search_view',
        'value' => array(
            esc_html__("Simple", "careerfy-frame") => 'simple',
            esc_html__("Advance Search", "careerfy-frame") => 'advance',
        ),
        'description' => esc_html__("Candidates top search style.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Top Search Radius", "careerfy-frame"),
        'param_name' => 'cand_top_search_radius',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Candidates top search radius.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Title, Keywords, or Phrase", "careerfy-frame"),
        'param_name' => 'cand_top_search_title',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location", "careerfy-frame"),
        'param_name' => 'cand_top_search_location',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable location field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    if ($sectors_enable_switch == 'on') {
        $cand_listsh_parms[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Sector", "careerfy-frame"),
            'param_name' => 'cand_top_search_sector',
            'value' => array(
                esc_html__("Yes", "careerfy-frame") => 'yes',
                esc_html__("No", "careerfy-frame") => 'no',
            ),
            'description' => esc_html__("Enable/Disable Sector Dropdown field.", "careerfy-frame"),
            'group' => esc_html__("Top Search", "careerfy-frame"),
        );
    }
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("AutoFill Search Box", "careerfy-frame"),
        'param_name' => 'top_search_autofill',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Enable/Disable autofill in search keyword field.", "careerfy-frame"),
        'group' => esc_html__("Top Search", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sort by Fields", "careerfy-frame"),
        'param_name' => 'candidate_sort_by',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Results search sorting section switch.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Excerpt Length", "careerfy-frame"),
        'param_name' => 'candidate_excerpt',
        'value' => '20',
        'description' => esc_html__("Set the number of words you want to show for excerpt.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'candidate_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Choose candidate list items order.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'candidate_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
            esc_html__("Promote Profile", "careerfy-frame") => 'promote_profile',
        ),
        'description' => esc_html__("Choose list items orderby.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Pagination", "careerfy-frame"),
        'param_name' => 'candidate_pagination',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => esc_html__("Choose yes if you want to show pagination for candidate items.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Items per Page", "careerfy-frame"),
        'param_name' => 'candidate_per_page',
        'value' => '10',
        'description' => esc_html__("Set number that how much candidates you want to show per page. Leave it blank for all candidates on a single page.", "careerfy-frame")
    );
    $cand_listsh_parms[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Custom Fields", "careerfy-frame"),
        'param_name' => 'candidate_custom_fields_switch',
        'value' => array(
            esc_html__("No", "careerfy-frame") => 'no',
            esc_html__("Yes", "careerfy-frame") => 'yes',
        ),
        'description' => esc_html__("Enable / Disable job custom fields", "careerfy-frame"),
        'group' => esc_html__("Custom Fields", "careerfy-frame"),
    );
    $cand_listsh_parms[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Select Fields", "careerfy-frame"),
        'param_name' => 'candidate_elem_custom_fields',
        'value' => $job_cus_field_arr,
        'description' => '',
        'group' => esc_html__("Custom Fields", "careerfy-frame"),
    );
    $attributes = array(
        "name" => esc_html__("Candidate Listing", "careerfy-frame"),
        "base" => "jobsearch_candidate_shortcode",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => apply_filters('jobsearch_candidate_listings_vcsh_params', $cand_listsh_parms)
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * Adding call to action shortcode
 * @return markup
 */
function careerfy_vc_banner_caption()
{
    $attributes = array(
        "name" => esc_html__("Banner Caption", "careerfy-frame"),
        "base" => "careerfy_banner_caption",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title ", "careerfy-frame"),
                'param_name' => 'banner_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'banner_desc',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Heading", "careerfy-frame"),
                'param_name' => 'btn_heading',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => esc_html__("Banner Buttons", "careerfy-frame"),
                'param_name' => 'banner_btn',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'value' => '',
                        'heading' => __('Button Text', 'careerfy-frame'),
                        'param_name' => 'btn_txt',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__("Button Link", "careerfy-frame"),
                        'param_name' => 'btn_link',
                        'value' => '',
                        'description' => ''
                    ),
                ),
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding call to action shortcode
 * @return markup
 */
function careerfy_vc_call_to_action()
{
    $attributes = array(
        "name" => esc_html__("Call to Action", "careerfy-frame"),
        "base" => "careerfy_call_to_action",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("View", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("View 1", "careerfy-frame") => 'view-1',
                    esc_html__("View 2", "careerfy-frame") => 'view-2',
                    esc_html__("View 3", "careerfy-frame") => 'view-3',
                    esc_html__("View 4", "careerfy-frame") => 'view-4',
                    esc_html__("View 5", "careerfy-frame") => 'view-5',
                ),
                'description' => ''
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'cta_img',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view-1', 'view-2')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title 1", "careerfy-frame"),
                'param_name' => 'cta_title1',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title 2", "careerfy-frame"),
                'param_name' => 'cta_title2',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view-1', 'view-2', 'view-4')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Small Title", "careerfy-frame"),
                'param_name' => 'cta_title_small',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view-3')
                ),

            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'cta_desc',
                'value' => '',
                'description' => '',
                'dependency' => array(
                    'element' => 'view',
                    'value' => array('view-1', 'view-2', 'view-5')
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text 2", "careerfy-frame"),
                'param_name' => 'btn_txt_2',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL 2", "careerfy-frame"),
                'param_name' => 'btn_url_2',
                'value' => '',
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding About Company shortcode
 * @return markup
 */
function careerfy_vc_about_company()
{

    $about_param_arr = array(
        array(
            'type' => 'dropdown',
            'heading' => esc_html__("Style", "careerfy-frame"),
            'param_name' => 'ab_view',
            'value' => array(
                esc_html__("Style 1", "careerfy-frame") => 'view1',
                esc_html__("Style 2", "careerfy-frame") => 'view2',
            ),
            'description' => ''
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__("Title", "careerfy-frame"),
            'param_name' => 'title',
            'value' => '',
            'description' => ''
        ),
        array(
            'type' => 'textarea',
            'heading' => esc_html__("Bold Text", "careerfy-frame"),
            'param_name' => 'bold_txt',
            'value' => '',
            'description' => ''
        ),
        array(
            'type' => 'textarea_html',
            'heading' => esc_html__("About Text", "careerfy-frame"),
            'param_name' => 'content',
            'value' => '',
            'description' => ''
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__("Title Color", "careerfy-frame"),
            'param_name' => 'title_color',
            'value' => '',
            'description' => '',
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__("Content Color", "careerfy-frame"),
            'param_name' => 'desc_color',
            'value' => '',
            'description' => '',
        ),
        array(
            'type' => 'careerfy_browse_img',
            'heading' => esc_html__("Image", "careerfy-frame"),
            'param_name' => 'about_img',
            'value' => '',
            'description' => ''
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__("Button Text", "careerfy-frame"),
            'param_name' => 'btn_txt',
            'value' => '',
            'description' => ''
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__("Button URL", "careerfy-frame"),
            'param_name' => 'btn_url',
            'value' => '',
            'description' => ''
        ),
    );

    $about_param_arr = apply_filters('careerfy_about_company_fields', $about_param_arr);

    $attributes = array(
        "name" => esc_html__("About the Company", "careerfy-frame"),
        "base" => "careerfy_about_company",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => $about_param_arr,
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding Block Text Box shortcode
 * @return markup
 */
function careerfy_vc_block_text_box()
{

    $attributes = array(
        "name" => esc_html__("Block Text with Video", "careerfy-frame"),
        "base" => "careerfy_block_text_box",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'title',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__("Text", "careerfy-frame"),
                'param_name' => 'content',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Background Image", "careerfy-frame"),
                'param_name' => 'bg_img',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Background Color", "careerfy-frame"),
                'param_name' => 'bg_color',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Block Text", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Video URL", "careerfy-frame"),
                'param_name' => 'video_url',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Video", "careerfy-frame"),
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Video Poster Image", "careerfy-frame"),
                'param_name' => 'poster_img',
                'value' => '',
                'description' => '',
                'group' => esc_html__("Video", "careerfy-frame"),
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding Simple Block Text shortcode
 * @return markup
 */
function careerfy_vc_simple_block_text()
{

    $attributes = array(
        "name" => esc_html__("Simple Block Text", "careerfy-frame"),
        "base" => "careerfy_simple_block_text",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'title',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__("Content Text", "careerfy-frame"),
                'param_name' => 'content',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Title Color", "careerfy-frame"),
                'param_name' => 'title_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Content Color", "careerfy-frame"),
                'param_name' => 'desc_color',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 1 Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 1 URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 2 Text", "careerfy-frame"),
                'param_name' => 'btn2_txt',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 2 URL", "careerfy-frame"),
                'param_name' => 'btn2_url',
                'value' => '',
                'description' => '',
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * adding Google Map shortcode
 * @return markup
 */
function careerfy_vc_google_map_shortcode()
{

    global $jobsearch_plugin_options;
    $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

    if ($location_map_type == 'mapbox') {
        $map_style_desc = __("Set map style URL here.", "careerfy-frame");
    } else {
        $map_style_desc = __("Set map styles. You can get predefined styles from <a href=\"https://snazzymaps.com/\" target=\"_blank\">snazzymaps.com</a>", "careerfy-frame");
    }

    $shortcode_params = array();
    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Box Address", "careerfy-frame"),
        'param_name' => 'map_box_address',
        'value' => '',
        'description' => esc_html__("Set infobox Address for map.", "careerfy-frame"),
    );
    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Latitude", "careerfy-frame"),
        'param_name' => 'map_latitude',
        'value' => '',
        'description' => esc_html__("Set Latitude of map.", "careerfy-frame"),
    );
    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Longitude", "careerfy-frame"),
        'param_name' => 'map_longitude',
        'value' => '',
        'description' => esc_html__("Set Longitude of map.", "careerfy-frame"),
    );
    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Zoom", "careerfy-frame"),
        'param_name' => 'map_zoom',
        'value' => '',
        'description' => esc_html__("Set Zoom for map.", "careerfy-frame"),
    );
    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Height", "careerfy-frame"),
        'param_name' => 'map_height',
        'value' => '350',
        'description' => esc_html__("Set Height for map.", "careerfy-frame"),
    );
    if ($location_map_type != 'mapbox') {
        $shortcode_params[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Street View", "careerfy-frame"),
            'param_name' => 'map_street_view',
            'value' => array(
                esc_html__("Yes", "careerfy-frame") => 'yes',
                esc_html__("No", "careerfy-frame") => 'no',
            ),
            'description' => '',
            'group' => __('Map Settings', 'careerfy-frame'),
        );
    }

    $shortcode_params[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Scroll Wheel control", "careerfy-frame"),
        'param_name' => 'map_scrollwheel',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
        'description' => '',
        'group' => __('Map Settings', 'careerfy-frame'),
    );
    if ($location_map_type != 'mapbox') {
        $shortcode_params[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Disable Map Type", "careerfy-frame"),
            'param_name' => 'map_default_ui',
            'value' => array(
                esc_html__("No", "careerfy-frame") => 'no',
                esc_html__("Yes", "careerfy-frame") => 'yes',
            ),
            'group' => __('Map Settings', 'careerfy-frame'),
            'description' => ''
        );
    }
    $shortcode_params[] = array(
        'type' => 'careerfy_browse_img',
        'heading' => esc_html__("Marker Icon", "careerfy-frame"),
        'param_name' => 'map_marker',
        'value' => '',
        'description' => esc_html__("Put custom marker icon for map.", "careerfy-frame"),
    );

    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Box Title", "careerfy-frame"),
        'param_name' => 'map_box_title',
        'value' => '',
        'description' => esc_html__("Set infobox title for map.", "careerfy-frame"),
    );


    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Box Telephone", "careerfy-frame"),
        'param_name' => 'map_box_phone',
        'value' => '',
        'description' => esc_html__("Set infobox Telephone for map.", "careerfy-frame"),
    );

    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Box Email", "careerfy-frame"),
        'param_name' => 'map_box_email',
        'value' => '',
        'description' => esc_html__("Set infobox Email for map.", "careerfy-frame"),
    );

    $shortcode_params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Box Website", "careerfy-frame"),
        'param_name' => 'map_box_website',
        'value' => '',
        'description' => esc_html__("Set infobox Website for map.", "careerfy-frame"),
    );

    $shortcode_params[] = array(
        'type' => ($location_map_type == 'mapbox' ? 'textfield' : 'textarea'),
        'heading' => ($location_map_type == 'mapbox' ? esc_html__("Style URL", "careerfy-frame") : esc_html__("Styles", "careerfy-frame")),
        'param_name' => 'map_styles',
        'value' => '',
        'description' => $map_style_desc,
        'group' => __('Map Settings', 'careerfy-frame'),
    );

    $attributes = array(
        "name" => esc_html__("Careerfy Map", "careerfy-frame"),
        "base" => "careerfy_google_map_shortcode",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_map_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => $shortcode_params,
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Map Marker", "careerfy-frame"),
        "base" => "careerfy_map_item",
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_google_map_shortcode'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Box Title", "careerfy-frame"),
                'param_name' => 'map_box_title',
                'value' => '',
                'description' => esc_html__("Set info box title for map.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Latitude", "careerfy-frame"),
                'param_name' => 'map_latitude',
                'value' => '',
                'description' => esc_html__("Set Latitude.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Longitude", "careerfy-frame"),
                'param_name' => 'map_longitude',
                'value' => '',
                'description' => esc_html__("Set Longitude.", "careerfy-frame"),
            ),
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Marker Icon", "careerfy-frame"),
                'param_name' => 'map_marker',
                'value' => '',
                'description' => esc_html__("Put custom marker icon for map.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Box Address", "careerfy-frame"),
                'param_name' => 'map_box_address',
                'value' => '',
                'description' => esc_html__("Set infobox Address for map.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Box Telephone", "careerfy-frame"),
                'param_name' => 'map_box_phone',
                'value' => '',
                'description' => esc_html__("Set infobox Telephone for map.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Box Email", "careerfy-frame"),
                'param_name' => 'map_box_email',
                'value' => '',
                'description' => esc_html__("Set infobox Email for map.", "careerfy-frame"),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Box Website", "careerfy-frame"),
                'param_name' => 'map_box_website',
                'value' => '',
                'description' => esc_html__("Set infobox Website for map.", "careerfy-frame"),
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Google_Map_Shortcode extends WPBakeryShortCodesContainer
        {

        }

    }
    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Map_Item extends WPBakeryShortCode
        {

        }

    }
}


/**
 * Adding Contact information shortcode
 * @return markup
 */
function careerfy_vc_contact_information()
{

    $cf7_posts = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'numberposts' => -1
    ));
    $cf7_arr = array(
        esc_html__("Select Form", "careerfy-frame") => ''
    );
    if (!empty($cf7_posts)) {
        foreach ($cf7_posts as $p) {
            $cf7_arr[$p->post_title] = $p->post_name;
        }
    }

    $params = array();
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Contact Info title", "careerfy-frame"),
        'param_name' => 'con_info_title',
        'value' => '',
        'description' => '',
    );
    if (class_exists('WPCF7_ContactForm')) {
        $params[] = array(
            'type' => 'dropdown',
            'heading' => esc_html__("Select Contact Form 7", "careerfy-frame"),
            'param_name' => 'con_form_7',
            'value' => $cf7_arr,
            'description' => '',
        );
    }
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Contact Form title", "careerfy-frame"),
        'param_name' => 'con_form_title',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("Description", "careerfy-frame"),
        'param_name' => 'con_desc',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Address", "careerfy-frame"),
        'param_name' => 'con_address',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Email", "careerfy-frame"),
        'param_name' => 'con_email',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Phone", "careerfy-frame"),
        'param_name' => 'con_phone',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Fax", "careerfy-frame"),
        'param_name' => 'con_fax',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'param_group',
        'value' => '',
        'heading' => esc_html__("Social Links", "careerfy-frame"),
        'param_name' => 'social_links',
        'params' => array(
            array(
                'type' => 'iconpicker',
                'value' => '',
                'heading' => __('Social Icon', 'careerfy-frame'),
                'param_name' => 'soc_icon',
            ),
            array(
                'type' => 'textfield',
                'value' => '',
                'heading' => __('Social Link', 'careerfy-frame'),
                'param_name' => 'soc_link',
            ),
        ),
    );
    $attributes = array(
        "name" => esc_html__("Contact Info", "careerfy-frame"),
        "base" => "careerfy_contact_info",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => $params
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding About information shortcode
 * @return markup
 */
function careerfy_vc_about_information()
{

    $params = array();
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Title", "careerfy-frame"),
        'param_name' => 'abt_info_title',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Subtitle", "careerfy-frame"),
        'param_name' => 'abt_sub_title',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textarea',
        'heading' => esc_html__("User Description", "careerfy-frame"),
        'param_name' => 'abt_desc',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("User Name", "careerfy-frame"),
        'param_name' => 'abt_name',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("User Experience", "careerfy-frame"),
        'param_name' => 'abt_experi',
        'value' => '',
        'description' => '',
    );
    $params[] = array(
        'type' => 'param_group',
        'value' => '',
        'heading' => esc_html__("Social Links", "careerfy-frame"),
        'param_name' => 'abt_social_links',
        'params' => array(
            array(
                'type' => 'iconpicker',
                'value' => '',
                'heading' => __('Social Icon', 'careerfy-frame'),
                'param_name' => 'abt_soc_icon',
            ),
            array(
                'type' => 'textfield',
                'value' => '',
                'heading' => __('Social Link', 'careerfy-frame'),
                'param_name' => 'abt_soc_link',
            ),
        ),
    );
    $attributes = array(
        "name" => esc_html__("About Info", "careerfy-frame"),
        "base" => "careerfy_about_info",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => $params
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding Image Banner shortcode
 * @return markup
 */
function careerfy_vc_image_banner()
{

    $attributes = array(
        "name" => esc_html__("Image Banner", "careerfy-frame"),
        "base" => "careerfy_image_banner",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Background Image", "careerfy-frame"),
                'param_name' => 'b_bgimg',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'b_title',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("SubTitle", "careerfy-frame"),
                'param_name' => 'b_subtitle',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'b_desc',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 1 Text", "careerfy-frame"),
                'param_name' => 'btn1_txt',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 1 URL", "careerfy-frame"),
                'param_name' => 'btn1_url',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 2 Text", "careerfy-frame"),
                'param_name' => 'btn2_txt',
                'value' => '',
                'description' => '',
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button 2 URL", "careerfy-frame"),
                'param_name' => 'btn2_url',
                'value' => '',
                'description' => '',
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Adding section heading shortcode
 * @return markup
 */
function careerfy_vc_find_question()
{

    $attributes = array(
        "name" => esc_html__("Find Question", "careerfy-frame"),
        "base" => "careerfy_find_question",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "class" => "",
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Search Box", "careerfy-frame"),
                'param_name' => 'search_box',
                'value' => array(
                    esc_html__("Show", "careerfy-frame") => 'show',
                    esc_html__("Hide", "careerfy-frame") => 'hide',
                ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'search_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textarea',
                'heading' => esc_html__("Description", "careerfy-frame"),
                'param_name' => 'search_desc',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button Text", "careerfy-frame"),
                'param_name' => 'btn_txt',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Button URL", "careerfy-frame"),
                'param_name' => 'btn_url',
                'value' => '',
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Counters shortcode
 * @return markup
 */
function careerfy_vc_counters_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Counters", "careerfy-frame"),
        "base" => "careerfy_counters",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_counters_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view-1',
                    esc_html__("Style 2", "careerfy-frame") => 'view-2',
                    esc_html__("Style 3", "careerfy-frame") => 'view-3',
                    esc_html__("Style 4", "careerfy-frame") => 'view-4',
                    esc_html__("Style 5", "careerfy-frame") => 'view-5',
                    esc_html__("Style 6", "careerfy-frame") => 'view-6',
                    esc_html__("Style 7", "careerfy-frame") => 'view-7',
                ),
                'description' => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Icon Color", "careerfy-frame"),
                "param_name" => "counter_icon_color",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Number Color", "careerfy-frame"),
                "param_name" => "counter_number_color",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Title Color", "careerfy-frame"),
                "param_name" => "counter_title_color",
                'value' => '',
                "description" => '',
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Counter Item", "careerfy-frame"),
        "base" => "careerfy_counters_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_counters'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "count_icon",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Number", "careerfy-frame"),
                "param_name" => "count_number",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => "count_title",
                'value' => '',
                "description" => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Counters extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Counters_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Services shortcode
 * @return markup
 */
function careerfy_vc_services_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Services", "careerfy-frame"),
        "base" => "careerfy_services",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_services_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view-1',
                    esc_html__("Style 2", "careerfy-frame") => 'view-2',
                    esc_html__("Style 3", "careerfy-frame") => 'view-3',
                    esc_html__("Style 4", "careerfy-frame") => 'view-4',
                    esc_html__("Style 5", "careerfy-frame") => 'view-5',
                    esc_html__("Style 6", "careerfy-frame") => 'view-6',
                    esc_html__("Style 7", "careerfy-frame") => 'view-7',
                    esc_html__("Style 8", "careerfy-frame") => 'view-8',
                    esc_html__("Style 9", "careerfy-frame") => 'view-9',
                    esc_html__("Style 10", "careerfy-frame") => 'view-10',
                    esc_html__("Style 11", "careerfy-frame") => 'view-11',
                    esc_html__("Style 12", "careerfy-frame") => 'view-12',
                    esc_html__("Style 13", "careerfy-frame") => 'view-13',
                    esc_html__("Style 14", "careerfy-frame") => 'view-14',
                    esc_html__("Style 15", "careerfy-frame") => 'view-15',
                    esc_html__("Style 16", "careerfy-frame") => 'view-16',
                    esc_html__("Style 17", "careerfy-frame") => 'view-17',
                    esc_html__("Style 18", "careerfy-frame") => 'view-18',
                    esc_html__("Style 19", "careerfy-frame") => 'view-19',
                    esc_html__("Style 20", "careerfy-frame") => 'view-20',
                    esc_html__("Style 21", "careerfy-frame") => 'view-21',
                    esc_html__("Style 22", "careerfy-frame") => 'view-22',
                ),
                'description' => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Title Color", "careerfy-frame"),
                "param_name" => "service_title_color",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Description Color", "careerfy-frame"),
                "param_name" => "service_text_color",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Icon Color", "careerfy-frame"),
                "param_name" => "service_icon_color",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "textfield",
                "heading" => __("Small Title", "careerfy-frame"),
                "param_name" => 'small_service_title',
                'value' => '',
                "description" => __("This option will apply to 'Service style 21' only.", "careerfy-frame"),
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Service Item", "careerfy-frame"),
        "base" => "careerfy_services_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_services'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'service_img',
                'value' => '',
                'description' => __("This option will not apply to 'Service style 4', 'Service style 8' ,'Service style 11' and style 21 as background image. For Style 21 first uploaded image size should be (width:940px), (height:670). Rest of the images should (width:467px), (height:330)", "careerfy-frame"),
            ),
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "service_icon",
                'value' => '',
                "description" => '',
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Background Color", "careerfy-frame"),
                "param_name" => "service_bg",
                'value' => '',
                "description" => __("This option will apply to 'Service style 4' only.", "careerfy-frame"),
            ),

            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => 'service_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Link", "careerfy-frame"),
                "param_name" => 'service_link',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textarea",
                "heading" => __("Description", "careerfy-frame"),
                "param_name" => "service_desc",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Button Text", "careerfy-frame"),
                "param_name" => "btn_txt",
                'value' => '',
                "description" => __("This option will apply on 'Service style 6, Service style 8, Service style 11 and can also be use as read more link in style 11' Buttons only.", "careerfy-frame"),
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Button Color", "careerfy-frame"),
                "param_name" => "btn_color",
                'value' => '',
                "description" => __("This option will apply on 'Service style 6,Service style 8 and Service style 11' Buttons only.", "careerfy-frame"),

            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Services extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Services_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Services Slider
 * @return markup
 */
function careerfy_vc_slider_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Home Page Slider", "careerfy-frame"),
        "base" => "careerfy_slider",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_slider_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "js_view" => 'VcColumnView',
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'slider_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                ),
                'description' => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Slider Item", "careerfy-frame"),
        "base" => "careerfy_slider_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_slider'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'slider_img',
                'value' => '',
                'description' => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Tiny Title", "careerfy-frame"),
                "param_name" => 'tiny_title',
                'value' => '',
                "description" => __("Will not affect on style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Small Title", "careerfy-frame"),
                "param_name" => 'small_title',
                'value' => '',
            ),
            array(
                "type" => "textfield",
                "heading" => __("Big Title", "careerfy-frame"),
                "param_name" => 'big_title',
                'value' => '',
                "description" => ''
            ),

            array(
                "type" => "textarea",
                "heading" => __("Description", "careerfy-frame"),
                "param_name" => "slider_desc",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Button Background Color", "careerfy-frame"),
                "param_name" => "button_bg_color",
                'value' => '',
                "description" => __("Select the background color for the slider button.", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Button Title", "careerfy-frame"),
                "param_name" => 'button_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Button URL", "careerfy-frame"),
                "param_name" => 'button_url',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Button Title Color", "careerfy-frame"),
                "param_name" => "button_title_color",
                'value' => '',
                "description" => __("Select color for the slider button title.", "careerfy-frame"),
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Slider extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Slider_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Top recruiters slider
 * @return markup
 */
function careerfy_vc_top_recruiters_slider_shortcode()
{

    global $jobsearch_gdapi_allocation;
    $jobsearch__options = get_option('jobsearch_plugin_options');

    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }


    $job_top_recruiter_slider = array();
    $job_top_recruiter_slider[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Syle", "careerfy-frame"),
        'param_name' => 'top_job_style',
        'value' => array(
            esc_html__("Slider Style 1", "careerfy-frame") => 'slider',
            esc_html__("Slider Style 2", "careerfy-frame") => 'slider2',
            esc_html__("Slider Style 3", "careerfy-frame") => 'slider3',
        ),
        'description' => esc_html__("Choose a team style.", "careerfy-frame")
    );

    $job_top_recruiter_slider[] = array(
        'type' => 'careerfy_browse_img',
        'heading' => esc_html__("Title Image", "careerfy-frame"),
        'param_name' => 'title_img',
        'value' => '',
        'description' => esc_html__("Image will show above title", "careerfy-frame"),
        'dependency' => array(
            'element' => 'top_job_style',
            'value' => 'slider2'
        ),
    );

    $job_top_recruiter_slider[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Title", "careerfy-frame"),
        'param_name' => 'top_recruiter_title',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'top_job_style',
            'value' => array('slider2')
        ),
    );
    $job_top_recruiter_slider[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Description", "careerfy-frame"),
        'param_name' => 'top_recruiter_desc',
        'value' => '',
        'description' => '',
        'dependency' => array(
            'element' => 'top_job_style',
            'value' => array('slider2')
        ),
    );

    $job_top_recruiter_slider[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector", "careerfy-frame"),
        'param_name' => 'top_recruiter_cat',
        'value' => $cate_array,
        'description' => esc_html__("Select Sector.", "careerfy-frame")
    );

    $job_top_recruiter_slider[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Featured", "careerfy-frame"),
        'param_name' => 'featured_only',
        'value' => array(
            esc_html__("Yes", "careerfy-frame") => 'yes',
            esc_html__("No", "careerfy-frame") => 'no',
        ),
    );

    $job_top_recruiter_slider[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Order", "careerfy-frame"),
        'param_name' => 'top_recruiter_order',
        'value' => array(
            esc_html__("Descending", "careerfy-frame") => 'DESC',
            esc_html__("Ascending", "careerfy-frame") => 'ASC',
        ),
        'description' => esc_html__("Choose the top recruiter items order.", "careerfy-frame")
    );
    $job_top_recruiter_slider[] = array(
        'type' => 'checkbox',
        'heading' => esc_html__("Locations in listing", "careerfy-frame"),
        'param_name' => 'job_list_loc_listing',
        'value' => array(
            esc_html__("Country", "careerfy-frame") => 'country',
            esc_html__("State", "careerfy-frame") => 'state',
            esc_html__("City", "careerfy-frame") => 'city',
        ),
        'std' => 'country,city',
        'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame")
    );
    $job_top_recruiter_slider[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Orderby", "careerfy-frame"),
        'param_name' => 'top_recruiter_orderby',
        'value' => array(
            esc_html__("Date", "careerfy-frame") => 'date',
            esc_html__("Title", "careerfy-frame") => 'title',
        ),
        'description' => esc_html__("Choose job list items orderby.", "careerfy-frame")
    );
    $job_top_recruiter_slider[] = array(
        'type' => 'textfield',
        'heading' => esc_html__("Number of top Jobs", "careerfy-frame"),
        'param_name' => 'top_recruiter_per_page',
        'value' => '10',
        'description' => esc_html__("Set a number of top jobs to show.", "careerfy-frame"),

    );

    $attributes = array(
        "name" => esc_html__("Top Jobs Slider", "careerfy-frame"),
        "base" => "careerfy_top_recruiters",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => $job_top_recruiter_slider
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

}

/**
 * Video Testimonial
 * @return markup
 */
function careerfy_vc_video_testimonial_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Video Testimonial", "careerfy-frame"),
        "base" => "careerfy_video_testimonial",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_video_testimonial_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "js_view" => 'VcColumnView',
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'vid_testimonial_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                ),
                'description' => ''
            ),
        ),
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Video Testimonial Item", "careerfy-frame"),
        "base" => "careerfy_video_testimonial_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_video_testimonial'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'careerfy_browse_img',
                'heading' => esc_html__("Image", "careerfy-frame"),
                'param_name' => 'vid_testimonial_img',
                'value' => '',
                'description' => ''
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Video URL", "careerfy-frame"),
                "param_name" => 'vid_url',
                'value' => '',
                "description" => esc_html__("Put Video URL of Vimeo, Youtube.", "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Client Name", "careerfy-frame"),
                "param_name" => 'client_title',
                'value' => '',
                "description" => esc_html__('client Title will  be applied on style 1 and style 3.', "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Client Rank", "careerfy-frame"),
                "param_name" => 'rank_title',
                'value' => '',
                "description" => esc_html__('Client Rank will be applied on style 1 and style 3.', "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Client Company", "careerfy-frame"),
                "param_name" => 'company_title',
                'value' => '',
                "description" => esc_html__("Client Company will be applied to style 1 and style 2.", "careerfy-frame")
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Description", "careerfy-frame"),
                "param_name" => 'testimonial_desc',
                'value' => '',
                "description" => esc_html__("Testimonial Description will be applied to style 3 only.", "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Client Location", "careerfy-frame"),
                "param_name" => 'client_location',
                'value' => '',
                "description" => esc_html__("Client Location will be applied to style 3 only.", "careerfy-frame")
            ),
        ),
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Video_Testimonial extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Video_Testimonial_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Candidates Slider
 * @return markup
 */
function careerfy_vc_candidate_slider_shortcode()
{
    $categories = get_terms(array(
        'taxonomy' => 'sector',
        'hide_empty' => false,
    ));

    $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $attributes = array(
        "name" => esc_html__("Candidate Slider", "careerfy-frame"),
        "base" => "careerfy_candidate_slider",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'slider_style',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'style1',
                    esc_html__("Style 2", "careerfy-frame") => 'style2',
                    esc_html__("Style 3", "careerfy-frame") => 'style3',
                    esc_html__("Style 4", "careerfy-frame") => 'style4',
                ),
                'description' => esc_html__("Choose a team style.", "careerfy-frame")
            ),
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Sector", "careerfy-frame"),
                'param_name' => 'candidate_cat',
                'value' => $cate_array,
                'description' => esc_html__("Select Sector.", "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Number of Candidates", "careerfy-frame"),
                "param_name" => 'candidate_nums',
                'value' => '',
                "description" => ''
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("First Button Color", "careerfy-frame"),
                'param_name' => 'first_btn_color',
                'value' => '',
                'description' => '',

            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Second Button Color", "careerfy-frame"),
                'param_name' => 'second_btn_color',
                'value' => '',
                'description' => '',
            ),

        ),
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Recruitment Process
 * @return markup
 */
function careerfy_vc_process_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Process", "careerfy-frame"),
        "base" => "careerfy_process",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_process_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "js_view" => 'VcColumnView'
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Process Item", "careerfy-frame"),
        "base" => "careerfy_process_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_process'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "process_icon",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Icon Color", "careerfy-frame"),
                "param_name" => "icon_color",
                'value' => '',
                "description" => __("Select colors for Icons.", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Process Title", "careerfy-frame"),
                "param_name" => 'process_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textarea",
                "heading" => __("Process Description", "careerfy-frame"),
                "param_name" => 'process_desc',
                'value' => '',
                "description" => ''
            ),

        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Process extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Process_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Image Services shortcode
 * @return markup
 */
function careerfy_vc_image_services_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Image Services", "careerfy-frame"),
        "base" => "careerfy_image_services",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_image_services_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'service_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                ),
                'description' => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Service Item", "careerfy-frame"),
        "base" => "careerfy_image_services_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_image_services'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "careerfy_browse_img",
                "heading" => __("Image", "careerfy-frame"),
                "param_name" => "service_img",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => 'service_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textarea",
                "heading" => __("Description", "careerfy-frame"),
                "param_name" => "service_desc",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Link", "careerfy-frame"),
                "param_name" => 'service_link',
                'value' => '',
                "description" => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Image_Services extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Image_Services_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Our Partners shortcode
 * @return markup
 */
function careerfy_vc_our_partners_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Our Partners", "careerfy-frame"),
        "base" => "careerfy_our_partners",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_our_partners_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'partner_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                ),
                'description' => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => 'partner_title',
                'value' => '',
                "description" => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Partner Item", "careerfy-frame"),
        "base" => "careerfy_our_partners_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_our_partners'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "careerfy_browse_img",
                "heading" => __("Image", "careerfy-frame"),
                "param_name" => "partner_img",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("URL", "careerfy-frame"),
                "param_name" => 'partner_url',
                'value' => '',
                "description" => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Our_Partners extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Our_Partners_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Recent Questions shortcode
 * @return markup
 */
function careerfy_vc_recent_questions()
{

    $attributes = array(
        "name" => esc_html__("Recent Questions", "careerfy-frame"),
        "base" => "careerfy_recent_questions",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_recent_questions_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => "ques_title",
                'value' => '',
                "description" => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Question Item", "careerfy-frame"),
        "base" => "careerfy_recent_questions_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_recent_questions'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "textfield",
                "heading" => __("Question", "careerfy-frame"),
                "param_name" => 'q_question',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Link", "careerfy-frame"),
                "param_name" => "q_url",
                'value' => '',
                "description" => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Recent_Questions extends WPBakeryShortCodesContainer
        {

        }
    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Recent_Questions_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * FAQs shortcode
 * @return markup
 */
function careerfy_vc_faqs_shortcode()
{
    $categories = get_terms(array(
        'taxonomy' => 'faq-category',
        'hide_empty' => false,
    ));

    $cate_array = array(esc_html__("Select Category", "careerfy-frame") => '');
    if (is_array($categories) && sizeof($categories) > 0) {
        foreach ($categories as $category) {
            $cate_array[$category->name] = $category->slug;
        }
    }

    $attributes = array(
        "name" => esc_html__("FAQs", "careerfy-frame"),
        "base" => "careerfy_faqs",
        "class" => "",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "params" => apply_filters('careerfy_faqs_vcsh_params_arr', array(
            // add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'view',
                'default' => 'style1',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'style1',
                    esc_html__("Style 2", "careerfy-frame") => 'style2',
                ),
                'description' => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => "ques_title",
                'value' => '',
                "description" => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Open First Question", "careerfy-frame"),
                'param_name' => 'op_first_q',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Category", "careerfy-frame"),
                'param_name' => 'faq_cat',
                'value' => $cate_array,
                'description' => esc_html__("Select Category.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Excerpt Length", "careerfy-frame"),
                'param_name' => 'faq_excerpt',
                'value' => '20',
                'description' => esc_html__("Set the number of words you want to show for faq answer.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Order", "careerfy-frame"),
                'param_name' => 'faq_order',
                'value' => array(
                    esc_html__("Descending", "careerfy-frame") => 'DESC',
                    esc_html__("Ascending", "careerfy-frame") => 'ASC',
                ),
                'description' => esc_html__("Choose the faq list items order.", "careerfy-frame")
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Orderby", "careerfy-frame"),
                'param_name' => 'faq_orderby',
                'value' => array(
                    esc_html__("Date", "careerfy-frame") => 'date',
                    esc_html__("Title", "careerfy-frame") => 'title',
                ),
                'description' => esc_html__("Choose faq list items orderby.", "careerfy-frame")
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__("No of Questions", "careerfy-frame"),
                'param_name' => 'num_of_faqs',
                'value' => '10',
                'description' => ''
            ),
        )),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Our Team shortcode
 * @return markup
 */
function careerfy_vc_our_team_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Our Team", "careerfy-frame"),
        "base" => "careerfy_our_team",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_our_team_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Syle", "careerfy-frame"),
                'param_name' => 'team_style',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'style1',
                    esc_html__("Style 2", "careerfy-frame") => 'style2',
                ),
                'description' => esc_html__("Choose a team style.", "careerfy-frame")
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Team Item", "careerfy-frame"),
        "base" => "careerfy_our_team_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_our_team'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "careerfy_browse_img",
                "heading" => __("Image", "careerfy-frame"),
                "param_name" => "team_img",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => 'team_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Position", "careerfy-frame"),
                "param_name" => "team_pos",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Experience", "careerfy-frame"),
                "param_name" => "team_experience",
                'value' => '',
                "description" => __("Add experience in years without any year/month string. (Like:5.) Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textarea",
                "heading" => __("Biography", "careerfy-frame"),
                "param_name" => "team_biography",
                'value' => '',
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Facebook", "careerfy-frame"),
                "param_name" => "team_fb",
                'value' => '',
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Google +", "careerfy-frame"),
                "param_name" => "team_google",
                'value' => '',
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Twitter", "careerfy-frame"),
                "param_name" => "team_twitter",
                'value' => '',
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("LinkedIn", "careerfy-frame"),
                "param_name" => "team_linkedin",
                'value' => '',
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Our_Team extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Our_Team_Item extends WPBakeryShortCode
        {

        }
    }
}

/**
 * Help Links shortcode
 * @return markup
 */
function careerfy_vc_help_links_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Help Links", "careerfy-frame"),
        "base" => "careerfy_help_links",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_help_links_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(// add params same as with any other content element
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Help Item", "careerfy-frame"),
        "base" => "careerfy_help_links_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_help_links'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "iconpicker",
                "heading" => __("Icon", "careerfy-frame"),
                "param_name" => "help_icon",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => 'help_title',
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Button Text", "careerfy-frame"),
                "param_name" => "btn_txt",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Button URL", "careerfy-frame"),
                "param_name" => "btn_url",
                'value' => '',
                "description" => ''
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Help_Links extends WPBakeryShortCodesContainer
        {

        }

    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Help_Links_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * Testimonials with image shortcode
 * @return markup
 */
function careerfy_vc_testimonials_with_image()
{
    $attributes = array(
        "name" => esc_html__("Testimonials", "careerfy-frame"),
        "base" => "careerfy_testimonials",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "as_parent" => array('only' => 'careerfy_testimonial_item'),
        "content_element" => true,
        "show_settings_on_create" => false,
        "is_container" => true,
        "params" => array(
            //add params same as with any other content element
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Style", "careerfy-frame"),
                'param_name' => 'testi_view',
                'value' => array(
                    esc_html__("Style 1", "careerfy-frame") => 'view1',
                    esc_html__("Style 2", "careerfy-frame") => 'view2',
                    esc_html__("Style 3", "careerfy-frame") => 'view3',
                    esc_html__("Style 4", "careerfy-frame") => 'view4',
                    esc_html__("Style 5", "careerfy-frame") => 'view5',
                    esc_html__("Style 6", "careerfy-frame") => 'view6',
                    esc_html__("Style 7", "careerfy-frame") => 'view7',
                    esc_html__("Style 8", "careerfy-frame") => 'view8',
                    esc_html__("Style 9", "careerfy-frame") => 'view9',
                    esc_html__("Style 10", "careerfy-frame") => 'view10',
                    esc_html__("Style 11", "careerfy-frame") => 'view11',
                    esc_html__("Style 12", "careerfy-frame") => 'view12',
                    esc_html__("Style 13", "careerfy-frame") => 'view13',
                    esc_html__("Style 14", "careerfy-frame") => 'view14',
                    esc_html__("Style 15", "careerfy-frame") => 'view15',
                ),
                'description' => ''
            ),
            array(
                "type" => "careerfy_browse_img",
                "heading" => __("Image", "careerfy-frame"),
                "param_name" => "img",
                'value' => '',
                "description" => ''
            ),
        ),
        "js_view" => 'VcColumnView'
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    $attributes = array(
        "name" => esc_html__("Testimonial Item", "careerfy-frame"),
        "base" => "careerfy_testimonial_item",
        "category" => esc_html__("Careerfy Theme", "careerfy-frame"),
        "content_element" => true,
        "as_child" => array('only' => 'careerfy_testimonials'),
        "show_settings_on_create" => true,
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "careerfy_browse_img",
                "heading" => __("Image", "careerfy-frame"),
                "param_name" => "img",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textarea",
                "heading" => __("Text", "careerfy-frame"),
                "param_name" => "desc",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Title", "careerfy-frame"),
                "param_name" => "title",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Position", "careerfy-frame"),
                "param_name" => "position",
                'value' => '',
                "description" => ''
            ),
            array(
                "type" => "textfield",
                "heading" => __("Date Text", "careerfy-frame"),
                "param_name" => "date_txt",
                'value' => '',
                "description" => __('Date Text will be added when style 11 will be selected.', 'careerfy-frame')
            ),
            array(
                "type" => "textfield",
                "heading" => __("URL", "careerfy-frame"),
                "param_name" => "testimonial_url",
                'value' => '',
                "description" => __('URL will be added when style 11 will be selected.', 'careerfy-frame')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Location", "careerfy-frame"),
                "param_name" => "location",
                'value' => '',
                "description" => __('Location will be added when style 7 will be selected.', 'careerfy-frame')
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__("Choose Background Color", "careerfy-frame"),
                'param_name' => 'bg_color',
                'value' => '',
                'description' => esc_html__("This Color will apply at 'Testimonial background'. and on style 7", "careerfy-frame"),
            ),
            array(
                "type" => "textfield",
                "heading" => __("Position", "careerfy-frame"),
                "param_name" => "position",
                'value' => '',
                "description" => __('Position field is for style 10', "careerfy-frame")
            ),
            array(
                "type" => "textfield",
                "heading" => __("Facebook URL", "careerfy-frame"),
                "param_name" => "fb_url",
                'value' => '',
                "description" => __('Social link will be added when style 9 will be selected.')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Twitter URL", "careerfy-frame"),
                "param_name" => "twitter_url",
                'value' => '',
                "description" => __('Social link will be added when style 9 will be selected.')
            ),
            array(
                "type" => "textfield",
                "heading" => __("linkedIn URL", "careerfy-frame"),
                "param_name" => "linkedin_url",
                'value' => '',
                "description" => __('Social link will be added when style 9 will be selected.')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Link Button Text", "careerfy-frame"),
                "param_name" => "link_btn_txt",
                'value' => '',
                "description" => __('Link Button Text field is for style 10')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Link Button URL", "careerfy-frame"),
                "param_name" => "link_btn_url",
                'value' => '',
                "description" => __('Link Button URL field is for style 10')
            ),
        ),
    );
    if (function_exists('vc_map')) {
        vc_map($attributes);
    }

    if (class_exists('WPBakeryShortCodesContainer')) {

        class WPBakeryShortCode_Careerfy_Testimonials extends WPBakeryShortCodesContainer
        {

        }
    }

    if (class_exists('WPBakeryShortCode')) {

        class WPBakeryShortCode_Careerfy_Testimonial_Item extends WPBakeryShortCode
        {

        }

    }
}

/**
 * adding embeddable jobs shortcode
 * @return markup
 */
function careerfy_vc_embeddable_jobs()
{

    $sh_atts = array();
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Site Title", "careerfy-frame"),
        'param_name' => 'site_title',
        'value' => array(
            esc_html__('On', 'wp-jobsearch') => 'on',
            esc_html__('Off', 'wp-jobsearch') => 'off',
        ),
        'description' => esc_html__("Site title in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Employer base jobs", "careerfy-frame"),
        'param_name' => 'employer_base_jobs',
        'value' => array(
            esc_html__('No', 'wp-jobsearch') => 'no',
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
        ),
        'description' => esc_html__("Employer base jobs in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Keyword search", "careerfy-frame"),
        'param_name' => 'keyword_search',
        'value' => array(
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
            esc_html__('No', 'wp-jobsearch') => 'no',
        ),
        'description' => esc_html__("Keyword search in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Location search", "careerfy-frame"),
        'param_name' => 'location_search',
        'value' => array(
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
            esc_html__('No', 'wp-jobsearch') => 'no',
        ),
        'description' => esc_html__("Location search in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Sector search", "careerfy-frame"),
        'param_name' => 'job_sector',
        'value' => array(
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
            esc_html__('No', 'wp-jobsearch') => 'no',
        ),
        'description' => esc_html__("Sector search in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Job Type search", "careerfy-frame"),
        'param_name' => 'job_type',
        'value' => array(
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
            esc_html__('No', 'wp-jobsearch') => 'no',
        ),
        'description' => esc_html__("Job Type search in embeddable jobs on/off.", "careerfy-frame")
    );
    $sh_atts[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Custom Fields", "careerfy-frame"),
        'param_name' => 'custom_fields',
        'value' => array(
            esc_html__('No', 'wp-jobsearch') => 'no',
            esc_html__('Yes', 'wp-jobsearch') => 'yes',
        ),
        'description' => esc_html__("Custom Fields in embeddable jobs on/off.", "careerfy-frame")
    );

    $attributes = array(
        "name" => esc_html__("Embeddable Jobs", "careerfy-frame"),
        "base" => "jobsearch_embeddable_jobs_generator",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => $sh_atts,
    );

    if (function_exists('vc_map') && class_exists('JobSearch_plugin')) {
        vc_map($attributes);
    }
}

/**
 * adding user job shortcode
 * @return markup
 */
function careerfy_vc_user_job_shortcode()
{
    $attributes = array(
        "name" => esc_html__("Post New Job", "careerfy-frame"),
        "base" => "jobsearch_user_job",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'title',
                'value' => '',
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * adding user job shortcode
 * @return markup
 */
function careerfy_vc_banner_advertisement()
{
    global $jobsearch_plugin_options;
    $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';
    $sinle_value = isset($jobsearch_plugin_options['ad_banners_list']) ? $jobsearch_plugin_options['ad_banners_list'] : '';

    $group_add_arr = array(esc_html__('Select banner', 'careerfy-frame') => '');
    if (isset($groups_value) && !empty($groups_value) && is_array($groups_value)) {
        for ($ad = 0; $ad < count($groups_value['group_title']); $ad++) {
            $ad_title = $groups_value['group_title'][$ad];
            $ad_code = $groups_value['group_code'][$ad];
            $group_add_arr[$ad_title] = $ad_code;
        }
    }
    $single_add_arr = array(esc_html__('Select banner', 'careerfy-frame') => '');
    if (isset($sinle_value) && !empty($sinle_value) && is_array($sinle_value)) {
        for ($ad = 0; $ad < count($sinle_value['banner_title']); $ad++) {
            $ad_title = $sinle_value['banner_title'][$ad];
            $ad_code = $sinle_value['banner_code'][$ad];
            $single_add_arr[$ad_title] = $ad_code;
        }
    }
    $attributes = array(
        "name" => esc_html__("Banner Advertisement", "careerfy-frame"),
        "base" => "jobsearch_banner_advertisement",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Banner Style", "careerfy-frame"),
                'param_name' => 'banner_style',
                'value' => array(
                    esc_html__("Single Banner", "careerfy-frame") => 'single_banner',
                    esc_html__("Group Banner ", "careerfy-frame") => 'group_banner',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Single Style", "careerfy-frame"),
                'param_name' => 'banner_sinle_style',
                'value' => $single_add_arr,
                'description' => '',
                'dependency' => array('element' => 'banner_style', 'value' => array('single_banner'))
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Group Style", "careerfy-frame"),
                'param_name' => 'banner_group_style',
                'value' => $group_add_arr,
                'description' => '',
                'dependency' => array('element' => 'banner_style', 'value' => array('group_banner'))
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}

/**
 * Login Registration Form shortcode
 * @return markup
 */
function careerfy_vc_login_registration_shortcode()
{

    $attributes = array(
        "name" => esc_html__("Login Registration Form", "careerfy-frame"),
        "base" => "jobsearch_login_registration",
        "class" => "",
        "category" => esc_html__("Wp JobSearch", "careerfy-frame"),
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__("Title", "careerfy-frame"),
                'param_name' => 'login_registration_title',
                'value' => '',
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Enable Register", "careerfy-frame"),
                'param_name' => 'login_register_form',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'on',
                    esc_html__("No", "careerfy-frame") => 'off',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Form Type", "careerfy-frame"),
                'param_name' => 'logreg_form_type',
                'value' => array(
                    esc_html__("Both Forms", "careerfy-frame") => 'on',
                    esc_html__("Register Form Only", "careerfy-frame") => 'reg_only',
                    esc_html__("Login Form Only", "careerfy-frame") => 'login_only',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Enable Candidate Registration", "careerfy-frame"),
                'param_name' => 'login_candidate_register',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__("Enable Employer Registration", "careerfy-frame"),
                'param_name' => 'login_employer_register',
                'value' => array(
                    esc_html__("Yes", "careerfy-frame") => 'yes',
                    esc_html__("No", "careerfy-frame") => 'no',
                ),
                'description' => ''
            ),
        )
    );

    if (function_exists('vc_map')) {
        vc_map($attributes);
    }
}
