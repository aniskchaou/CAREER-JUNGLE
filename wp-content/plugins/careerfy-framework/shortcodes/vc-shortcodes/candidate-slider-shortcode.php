<?php

/**
 * Candidate Slider Shortcode
 * @return html
 */

use WP_Jobsearch\Candidate_Profile_Restriction;
add_shortcode('careerfy_candidate_slider', 'careerfy_candidate_slider_shortcode');

function careerfy_candidate_slider_shortcode($atts, $content = '')
{
    global $jobsearch_plugin_options;
    extract(shortcode_atts(array(
        'candidate_cat' => '',
        'candidate_nums' => '',
        'first_btn_color' => '',
        'second_btn_color' => '',
        'slider_style' => 'style1',
    ), $atts));

    $rand_num = rand(10000000, 99909999);
    $cand_profile_restrict = new Candidate_Profile_Restriction;
    $candidate_nums = isset($candidate_nums) && $candidate_nums != "" ? $candidate_nums : '-1';
    $candidates_reviews = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';
    
    $candidate_listing_percent = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
    $candmin_listing_percent = isset($jobsearch_plugin_options['cand_min_listpecent']) ? $jobsearch_plugin_options['cand_min_listpecent'] : '';
    $candmin_listing_percent = absint($candmin_listing_percent);

    $element_filter_arr = array();
    $element_filter_arr[] = array(
        'key' => 'jobsearch_field_candidate_approved',
        'value' => 'on',
        'compare' => '=',
    );
    $element_filter_arr[] = array(
        'key' => 'cuscand_feature_fbckend',
        'value' => 'on',
        'compare' => '=',
    );
    if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
        $element_filter_arr[] = array(
            'key' => 'overall_skills_percentage',
            'value' => $candmin_listing_percent,
            'compare' => '>=',
            'type' => 'NUMERIC',
        );
    }

    $args_count = array(
        'posts_per_page' => $candidate_nums,
        'post_type' => 'candidate',
        'post_status' => 'publish',
        'meta_query' => array(
            $element_filter_arr,
        ),
        'fields' => 'ids'
    );
    if ($candidate_cat != "") {
        $args_count['tax_query'][] = array(
            'taxonomy' => 'sector',
            'field' => 'slug',
            'terms' => $candidate_cat
        );
    }
    $query = new WP_Query($args_count);

    $html = '';
    if ($query->found_posts != 0) {

        $html .= '
        <div id="careerfy-slidmaintop-' . ($rand_num) . '" style="position: relative; float: left; width: 100%;">
        <div id="careerfy-slidloder-' . ($rand_num) . '" class="careerfy-slidloder-section"><div class="ball-scale-multiple"><div></div><div></div><div></div></div></div>';

        if ($slider_style == 'style3') {
            $html .= '<div id="careerfy-popcands-' . ($rand_num) . '" class="careerfy-popular-candidates-style14-slider">';
        } else if ($slider_style == 'style1') {
            $html .= '<div id="careerfy-popcands-' . ($rand_num) . '" class="careerfy-popular-candidates">';
        } else if ($slider_style == 'style4') {
            $html .= '<div id="careerfy-popcands-' . ($rand_num) . '" class="careerfy-sixteen-candidate-slider">
            <div class="careerfy-sixteen-candidate-layer">
                                    <div class="careerfy-sixteen-candidate-grid">
                                        <ul class="row">';
        } else {
            $html .= '<div id="careerfy-popcands-' . ($rand_num) . '" class="careerfy-candidates-style11-slider">';
        }
        $first_btn_color = $first_btn_color != "" ? 'style="background-color: ' . $first_btn_color . '"' : '';
        $second_btn_color = $second_btn_color != "" ? 'style="background-color: ' . $second_btn_color . '"' : '';
        $count = 0;
        ///////////Counter is for slider only which is started from 1 ////////////
        $slider_counter = 1;
        foreach ($query->posts as $post_id) {

            $candidate_expertise_skills = "";
            $inopt_resm_skills = isset($jobsearch_plugin_options['cand_resm_skills']) ? $jobsearch_plugin_options['cand_resm_skills'] : '';
            $careerfy_theme_color = isset($careerfy__options['careerfy-main-color']) && $careerfy__options['careerfy-main-color'] != '' ? $careerfy__options['careerfy-main-color'] : '#13b5ea';

            if (!$cand_profile_restrict::cand_field_is_locked('expertise_defields', 'detail_page')) {
                if ($inopt_resm_skills != 'off') {

                    $exfield_list = get_post_meta($post_id, 'jobsearch_field_skill_title', true);
                    $skill_percentagefield_list = get_post_meta($post_id, 'jobsearch_field_skill_percentage', true);
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                        $exfield_counter = 0;
                        $total_skills_percent = 0;
                        foreach ($exfield_list as $exfield) {

                            $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? absint($skill_percentagefield_list[$exfield_counter]) : '';
                            $skill_percentagefield_val = $skill_percentagefield_val > 100 ? 100 : $skill_percentagefield_val;
                            $total_skills_percent += $skill_percentagefield_val;

                            $exfield_counter++;
                        }
                        $candidate_expertise_skills = ($total_skills_percent / $exfield_counter);
                    }

                }
            }
            //$do_shortlist = do_action('jobsearch_add_employer_resume_to_list_btn', array('id' => $post_id, 'style' => 'style1'));
            $candidate_rank = get_post_meta($post_id, 'jobsearch_field_candidate_jobtitle', true);
            $candidate_rank = jobsearch_esc_html($candidate_rank);
            $_job_salary_type = get_post_meta($post_id, 'jobsearch_field_candidate_salary_type', true);
            $salary_type = '';
            if ($_job_salary_type == 'type_1') {
                $salary_type = 'Monthly';
            } else if ($_job_salary_type == 'type_2') {
                $salary_type = 'Weekly';
            } else if ($_job_salary_type == 'type_3') {
                $salary_type = 'Hr';
            }
            $currency_symbol = '$';
            if(function_exists('jobsearch_get_currency_symbol')){
                $currency_symbol = jobsearch_get_currency_symbol();
            }
            $candidate_salary = get_post_meta($post_id, 'jobsearch_field_candidate_salary', true);
            $candidate_salary = isset($candidate_salary) && $candidate_salary != "" ? '<span>' . esc_html__($currency_symbol, 'careerfy-frame') . ' ' . $candidate_salary . ' ' . esc_html__('/'.$salary_type, 'careerfy-frame') . ' </span>' : esc_html__('No Salary exist', 'careerfy-frame');
            $no_rating_class = isset($candidates_reviews) && $candidates_reviews == 'off' ? 'no-candidate-rating' : '';
            $oveall_review_avg_rating = '';
            if ($candidates_reviews == 'on') {
                $oveall_review_avg_rating = get_post_meta($post_id, 'oveall_review_avg_rating', true);
                $over_all_avg_rting_perc = 0;
                if ($oveall_review_avg_rating > 0) {
                    $over_all_avg_rting_perc = ($oveall_review_avg_rating / 5) * 100;
                }
            } else {
                $over_all_avg_rting_perc = '';
            }


            $get_item_city = get_post_meta($post_id, 'jobsearch_field_location_location3', true);
            if ($get_item_city != '') {
                $get_item_city . ',';
            }
            $get_item_state = get_post_meta($post_id, 'jobsearch_field_location_location2', true);

            if ($get_item_state != '') {
                $get_item_state . ',';
            }

            $get_item_country = get_post_meta($post_id, 'jobsearch_field_location_location1', true);
            $user_facebook_url = get_post_meta($post_id, 'jobsearch_field_user_facebook_url', true);
            $user_twitter_url = get_post_meta($post_id, 'jobsearch_field_user_twitter_url', true);
            $user_google_plus_url = get_post_meta($post_id, 'jobsearch_field_user_google_plus_url', true);
            $user_linkedin_url = get_post_meta($post_id, 'jobsearch_field_user_linkedin_url', true);

            $post_thumbnail_src = jobsearch_candidate_img_url_comn($post_id);
            $final_color = '';
            $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
            if ($candidate_skills == 'on') {

                $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '';
                $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '';
                $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '';
                $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '';
                $jobsearch_sectors = wp_get_post_terms($post_id, 'sector', array("fields" => "all"));

                $cand_user_id = jobsearch_get_candidate_user_id($post_id);
                $all_skill_msgs = jobsearch_candidate_skill_percent_count($cand_user_id, 'msgs');
                preg_match_all('!\d+!', @$all_skill_msgs[$count], $matches);
                $overall_candidate_skills = get_post_meta($post_id, 'overall_skills_percentage', true);
                if ($overall_candidate_skills <= 25 && $low_skills_clr != '') {
                    $final_color = 'style="background-color: ' . $low_skills_clr . ';"';
                } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50 && $med_skills_clr != '') {
                    $final_color = 'style="background-color: ' . $med_skills_clr . ';"';
                } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75 && $high_skills_clr != '') {
                    $final_color = 'style="background-color: ' . $high_skills_clr . ';"';
                } else if ($overall_candidate_skills > 75 && $comp_skills_clr != '') {
                    $final_color = 'style="background-color: ' . $comp_skills_clr . ';"';
                }
            }

            if ($slider_style == 'style4') {
                $html .= '<li class="col-md-4">
                                        <div class="careerfy-sixteen-candidate-grid-inner">
                                        <figure>
                                        <a href="' . get_permalink($post_id) . '"><img src="' . $post_thumbnail_src . '" alt=""></a>
                                        <figcaption>';


                ob_start();
                $html .= $candidate_salary;
                do_action('jobsearch_add_employer_resume_to_list_btn', array('id' => $post_id, 'style' => 'style4'));
                $html .= ob_get_clean();

                $html .= '</figcaption>
                                        </figure>
                                        <div class="careerfy-sixteen-candidate-grid-text">
                                        <h2><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h2>
                                        <span>' . $candidate_rank . '</span>
                                        <div class="careerfy-sixteen-candidate-grid-bottom '.$no_rating_class.'  ">';
                if ($candidates_reviews == 'on') {
                    if ($oveall_review_avg_rating != "") {
                        $html .= '<div class="careerfy-featured-rating" ><span class="careerfy-featured-rating-box" style = "width:' . ($over_all_avg_rting_perc) . '%;" ></span ></div >';
                    } else {
                        $html .= '<div class="no-rating-text"><p>No Rating Yet</p></div>';
                    }
                }
                    if ($get_item_country != "") {
                        $html .= '<span class="careerfy-featured-candidates-loc">
                        <i class="fa fa-map-marker"></i> ' . $get_item_country . '
                        </span>';
                    }

                $html .= '</div>
                                </div>
                               </div>
                               <a ' . $first_btn_color . ' href="' . esc_url(get_permalink($post_id)) . '" class="careerfy-sixteen-candidate-grid-btn active">' . esc_html__('View Profile', 'careerfy-frame') . '</a>
                               <a ' . $second_btn_color . ' href="' . esc_url(get_permalink($post_id)) . '" class="careerfy-sixteen-candidate-grid-btn">' . esc_html__('Hire Me', 'careerfy-frame') . '</a>
                             </li>';
            } else if ($slider_style == 'style3') {
                $html .= '<div class="careerfy-popular-candidates-style14">
                                    <div class="careerfy-popular-candidates-style14-inner">
                                        <figure><a href="' . get_permalink($post_id) . '"><img
                                                src="' . $post_thumbnail_src . '" alt=""></a>
                                        </figure>
                                        <h2><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h2>
                                        <span>' . $candidate_rank . '</span>
                                        <small>' . esc_html__('Location:', 'careerfy-frame') . ' ' . $get_item_state . '' . $get_item_country . '</small>';
                if (!empty($jobsearch_sectors)) {
                    $html .= '<small>' . esc_html__('Sector:', 'careerfy-frame') . ' ' . $jobsearch_sectors[0]->name . '</small>';
                }
                $html .= '<ul class="careerfy-popular-candidates-style14-social">';
                if (!empty($user_facebook_url)) {
                    $html .= '<li><a href="' . $user_facebook_url . '" class="careerfy-icon careerfy-facebook"></a></li>';
                }
                if (!empty($user_twitter_url)) {
                    $html .= '<li><a href="' . $user_twitter_url . '" class="careerfy-icon careerfy-twitter"></a></li>';
                }
                if (!empty($user_google_plus_url)) {
                    $html .= '<li><a href="' . $user_google_plus_url . '" class="careerfy-icon careerfy-google-plus"></a></li>';
                }
                if (!empty($user_linkedin_url)) {
                    $html .= '<li><a href="' . $user_linkedin_url . '" class="careerfy-icon careerfy-linkedin"></a></li>';
                }

                $html .= '</ul>
                                    </div>
                                    <strong>' . esc_html__('Salary', 'careerfy-frame') . ' ' . $candidate_salary . '</strong>
                                </div>';

            } else if ($slider_style == 'style1') {
                $html .= '
            <div class="careerfy-popular-candidates-layer">
                <figure><a href="' . get_permalink($post_id) . '"><img src="' . $post_thumbnail_src . '" alt=""><span ' . $final_color . '></span> </a></figure>
                 <div class="careerfy-popular-candidates-text">
                    <h2><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h2>
                    <span>' . $candidate_rank . '</span>
                    ' . $candidate_salary . '
                </div>
            </div>' . "\n";
            } else {
                wp_enqueue_script('careerfy-progressbar-two');
                $html .= '
            <div class="careerfy-candidates-style11-slider-layer">
             <div class="careerfy-candidates-style11">
              <ul class="row">
                <li class="col-md-12">
                 <div class="careerfy-candidates-style11-inner">
                 <div class="careerfy-candidates-style11-top">';
                if ($candidate_skills == 'on') {
                    $html .= '<span>' . esc_html__('Score ', 'careerfy-frame') . ' ' . $overall_candidate_skills . '%</span>';
                }
                if (!empty($candidate_expertise_skills)) {
                    $html .= '<span>' . esc_html__('Skills', 'careerfy-frame') . ' ' . floor($candidate_expertise_skills) . '%</span>';
                }
                $html .= '</div>
                  <figure>
                  <a href="' . get_permalink($post_id) . '"><img src="' . $post_thumbnail_src . '" alt=""></a>
                  <figcaption>
                  <h2><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h2>
                  <span>' . $candidate_rank . '</span>
                  <small>' . $get_item_city . '' . $get_item_state . ' ' . $get_item_country . '</small>
                    <div class="careerfy-candidates-style11-progress">
                  <ul>
                  <li>' . esc_html__('25', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('50', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('75', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('100', 'careerfy-frame') . '</li>
                  </ul>
                  <div id="single_candidate_progress"  data-width="' . floor($candidate_expertise_skills) . '"></div>';

                $html .= '
                  <script>
                       jQuery(document).ready(function ($) {
                          jQuery(\'#single_candidate_progress\').progressBar({
                           percentage: true,
                           animation: true,
                           height: "24",
                           });
                          });
                 </script>
                 
                  <ul>
                  <li>' . esc_html__('Poor', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('Fair', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('Good', 'careerfy-frame') . '</li>
                  <li>' . esc_html__('Excellent', 'careerfy-frame') . '</li>
                  </ul>
                  </div>
                  </figcaption>
                  </figure>
                  </div>
                </li>
                </ul>
               </div>
             </div>' . "\n";
            }

            if ($slider_counter % 3 === 0 && $slider_style == 'style4' && $slider_counter != $candidate_nums) {

                $html .= '</ul>
                    </div>
                </div>
                <div class="careerfy-sixteen-candidate-layer">
                    <div class="careerfy-sixteen-candidate-grid">
                        <ul class="row">';
            }
            $count++;
            ///////////Counter is for slider only which is started from 1 ////////////
            $slider_counter++;
        }

        if ($slider_style == 'style4') {
            $html .= '</ul></div></div>';
        }

        $html .= "</div></div></div>";
        ob_start();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {

                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-popular-candidates-layer').css({'display': 'inline-block'});

                <?php if($slider_style == 'style3'){ ?>
                jQuery('#careerfy-popcands-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-next-long'></i><?php echo esc_html__('BACK', 'careerfy-frame') ?> </span>",
                    nextArrow: "<span class='slick-arrow-right'><?php echo esc_html__('NEXT', 'careerfy-frame') ?><i class='careerfy-icon careerfy-next-long'></i></span>",
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
                <?php } else if($slider_style == 'style1'){ ?>
                jQuery('#careerfy-popcands-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-arrow-right-light'></i></span>",
                    nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-arrow-right-light'></i></span>",
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
                <?php } else if($slider_style == 'style4'){ ?>
                jQuery('.careerfy-sixteen-candidate-slider').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: true,
                    arrows: false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
                <?php } else { ?>
                jQuery('#careerfy-popcands-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-next'></i></span>",
                    nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-next'></i></span>",
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });

                <?php }?>
                var remSlidrLodrInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidloder-<?php echo($rand_num) ?>').remove();
                    clearInterval(remSlidrLodrInt<?php echo($rand_num) ?>);
                }, 1500);
                //

                var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-popular-candidates-layer').css({'display': 'inline-block'});

                    var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                    var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_popcands_slidr_lheight';
                    var c_date_<?php echo($rand_num) ?> = new Date();
                    c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                    var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                    document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";

                    clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                }, 2500);
            });
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-popular-candidates-layer').css({'display': 'none'});

            var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_popcands_slidr_lheight']) && $_COOKIE['careerfy_popcands_slidr_lheight'] != '' ? $_COOKIE['careerfy_popcands_slidr_lheight'] . 'px' : '300px') ?>';
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': slider_height_<?php echo($rand_num) ?>});
        </script>
        <?php

        $html .= ob_get_clean();
    } else {
        $html = esc_html__('No Record exist', 'careerfy-frame');
    }
    return $html;
}