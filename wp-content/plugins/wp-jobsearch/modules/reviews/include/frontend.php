<?php
/*
  Class : Reviews Frontend
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Reviews_Frontend
{

    public function __construct()
    {
        add_action('jobsearch_add_review_btn', array($this, 'add_review_btn'), 10, 1);
        add_action('jobsearch_add_review_form', array($this, 'add_review_form_html'), 10, 1);
        add_action('jobsearch_post_avg_rating', array($this, 'post_avg_rating'), 10, 1);
        add_action('jobsearch_cand_detail_post_avg_rating', array($this, 'post_avg_rating'), 10, 1);
        add_action('jobsearch_post_reviews_list', array($this, 'post_reviews_list'), 10, 1);
        add_action('wp_ajax_jobsearch_user_review_post', array($this, 'user_review_submit'));
        add_action('wp_ajax_nopriv_jobsearch_user_review_post', array($this, 'user_review_submit'));

        //
        add_action('wp_ajax_jobsearch_user_review_updatin', array($this, 'user_review_update'));
    }

    public function add_review_btn($args = array())
    {

        global $jobsearch_review_popup_args, $jobsearch_plugin_options;

        $public_reviews = isset($jobsearch_plugin_options['public_reviews_switch']) ? $jobsearch_plugin_options['public_reviews_switch'] : '';

        $jobsearch_review_popup_args = $args;

        $do_rev_form = true;
        if ($public_reviews != 'on' && !is_user_logged_in()) {
            $do_rev_form = false;
        }

        $post_id = isset($args['post_id']) ? $args['post_id'] : 0;
        $classes = isset($args['classes']) && !empty($args['classes']) ? ' ' . $args['classes'] . '' : ' ' . 'jobsearch-employerdetail-btn' . '';

        if ($do_rev_form) {
            wp_enqueue_script('jobsearch-barrating');
            wp_enqueue_script('jobsearch-add-review');
            ?>
            <a href="javascript:void(0);" data-target="add_review_form_sec"
               class="jobsearch-go-to-review-form<?php echo($classes); ?>" data-post_id="<?php echo($post_id) ?>"><i
                        class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Add a review', 'wp-jobsearch') ?>
            </a>
            <?php
        } else {
            ?>
            <a href="javascript:void(0);" class="jobsearch-open-signin-tab<?php echo($classes); ?>"><i
                        class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Add a review', 'wp-jobsearch') ?>
            </a>
            <?php
        }
    }

    public function add_review_form_html($args = array())
    {
        global $jobsearch_plugin_options;
        $public_reviews = isset($jobsearch_plugin_options['public_reviews_switch']) ? $jobsearch_plugin_options['public_reviews_switch'] : '';
        $view_form = true;
        if ($public_reviews != 'on' && !is_user_logged_in()) {
            $view_form = false;
        }

        if (isset($args['must_login']) && $args['must_login'] == 'no') {
            $view_form = true;
        }
        if ($view_form === true) {
            global $jobsearch_review_popup_args, $jobsearch_plugin_options, $sitepress;

            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }

            $post_id = isset($args['post_id']) ? $args['post_id'] : 0;
            $rev_post_type = get_post_type($post_id);
            if ($rev_post_type == 'candidate') {
                $main_class = 'jobsearch-candidate-wrap-section';
                $review_titles = isset($jobsearch_plugin_options['cand_reviews_titles']) ? $jobsearch_plugin_options['cand_reviews_titles'] : '';
            } else {
                $main_class = 'jobsearch-employer-wrap-section';
                $review_titles = isset($jobsearch_plugin_options['reviews_titles']) ? $jobsearch_plugin_options['reviews_titles'] : '';
            }

            wp_enqueue_script('jobsearch-barrating');
            wp_enqueue_script('jobsearch-add-review');
            ?>
            <div id="add_review_form_sec" class="<?php echo($main_class) ?> jobsearch-margin-bottom">
                <div class="jobsearch-content-title jobsearch-addmore-space">
                    <h2><?php esc_html_e('Leave Your Review', 'wp-jobsearch'); ?></h2></div>
                <div class="jobsearch-add-review-con">

                    <form id="jobsearch-review-form" class="jobsearch-addreview-form" method="post">
                        <ul>
                            <li>
                                <div class="review-stars-sec">
                                    <?php
                                    if (!empty($review_titles)) {
                                        $review_title_count = 1;
                                        foreach ($review_titles as $review_title) {

                                            $review_title = apply_filters('wpml_translate_single_string', $review_title, 'JobSearch Options', 'Review Title - ' . $review_title, $lang_code);
                                            ?>

                                            <div class="review-stars-holder">
                                                <label><?php echo($review_title) ?></label>
                                                <select id="review-stars-selector-<?php echo($review_title_count) ?>"
                                                        name="user_rating_<?php echo($review_title_count) ?>"
                                                        autocomplete="off">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                            </div>
                                            <?php
                                            $review_title_count++;
                                        }
                                    } else { ?>
                                        <div class="review-stars-holder">
                                            <label><?php esc_html_e("Rating", "wp-jobsearch") ?></label>
                                            <select id="review-stars-selector" name="user_rating_1" autocomplete="off">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="review-overall-stars-sec">
                                    <span class="rating-text"><?php esc_html_e("Overall Rating", "wp-jobsearch") ?></span>
                                    <span class="rating-num">0</span>
                                    <div class="jobsearch-company-rating"><span class="jobsearch-company-rating-box"
                                                                                style="width: 20%;"></span></div>
                                </div>
                            </li>
                            <?php
                            if (!is_user_logged_in()) { ?>
                                <li>
                                    <input type="text" name="user_name"
                                           placeholder="<?php esc_html_e("Your Name", "wp-jobsearch") ?>">
                                </li>
                                <li>
                                    <input type="text" name="user_email"
                                           placeholder="<?php esc_html_e("Email Address", "wp-jobsearch") ?>">
                                </li>
                            <?php } ?>
                            <li>
                                <textarea name="user_comment"
                                          placeholder="<?php esc_html_e("Your Review", "wp-jobsearch") ?>"></textarea>
                            </li>
                            <li>
                                <input type="hidden" name="review_post" value="<?php echo absint($post_id) ?>">
                                <input type="hidden" name="action" value="jobsearch_user_review_post">
                                <input type="submit" id="jobsearch-review-submit-btn"
                                       value="<?php esc_html_e("Submit now", "wp-jobsearch") ?>">
                                <span class="jobsearch-review-loader"></span>
                                <span class="jobsearch-review-msg"></span>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
            <?php
        }
    }

    public function post_reviews_list($args = array())
    {

        global $jobsearch_plugin_options, $sitepress;
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $current_user_id = 0;
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
        }

        $post_id = isset($args['post_id']) ? $args['post_id'] : '';
        $prefix = isset($args['prefix']) && $args['prefix'] != '' ? $args['prefix'] : 'jobsearch';
        $rev_post_type = get_post_type($post_id);
        $post_user_id = get_post_meta($post_id, 'jobsearch_user_id', true);
        $rev_list_label = isset($args['list_label']) ? $args['list_label'] : '';

        $com_args = array(
            'post_id' => $post_id,
            'parent' => 0,
            'status' => 'approve',
        );
        $all_comments = get_comments($com_args);
        
        // updating Post review overall
        $all_avg_ratings = $all_overall_ratings = array(
            'rating_1' => 0,
            'rating_2' => 0,
            'rating_3' => 0,
            'rating_4' => 0,
            'rating_5' => 0,
        );

        $all_avg_ratingee = 0;
        $count_reviews = 0;
        $overall_user_rate = '';
        
        if (!empty($all_comments)) {
            foreach ($all_comments as $r_comment) {
                $com_id = $r_comment->comment_ID;
                $rev_avg_rating = get_comment_meta($com_id, 'review_avg_rating', true);
                $all_avg_ratings = $this->user_avg_review_asign_to_arr($all_avg_ratings, $rev_avg_rating);

                $all_avg_ratingee += $rev_avg_rating;

                $rev_overall_rating = get_comment_meta($com_id, 'review_overall_rating', true);
                if (!empty($rev_overall_rating)) {
                    $overall_user_rate = array();
                    $review_title_count = 1;
                    foreach ($rev_overall_rating as $review_title_rate) {
                        $overall_user_rate['user_rating_' . $review_title_count] = $this->user_review_asign_to_arr($all_overall_ratings, $review_title_rate);
                        $review_title_count++;
                    }
                }
                $count_reviews++;
            }

            $all_avg_ratingee = $all_avg_ratingee / $count_reviews;
        }

        update_post_meta($post_id, 'oveall_review_avg_rating', $all_avg_ratingee);
        update_post_meta($post_id, 'oveall_review_avg_ratings', $all_avg_ratings);
        update_post_meta($post_id, 'oveall_review_overall_ratings', $overall_user_rate);
        update_post_meta($post_id, 'oveall_review_count', $count_reviews);
        
        //
        /// End here ///
        //

        wp_enqueue_script('jobsearch-barrating');
        wp_enqueue_script('jobsearch-add-review');
        
        $curr_user_comments = '';
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
            $ccom_args = array(
                'user_id' => $current_user_id,
                'post_id' => $post_id,
                'parent' => 0,
                'status' => 'hold',
            );
            $curr_user_comments = get_comments($ccom_args);
        }
        
        if (!empty($all_comments) || !empty($curr_user_comments)) {

            //
            $revuser_def__url = get_avatar_url($post_user_id, array('size' => 60));
            $revuser_user_avatid = get_post_thumbnail_id($post_id);
            if ($revuser_user_avatid > 0) {
                $revuser_thumb_image = wp_get_attachment_image_src($revuser_user_avatid, 'thumbnail');
                $revuser_def__url = isset($revuser_thumb_image[0]) && esc_url($revuser_thumb_image[0]) != '' ? $revuser_thumb_image[0] : '';
            }
            //

            if ($rev_post_type == 'candidate') {
                $main_class = $prefix . '-candidate-wrap-section';
                $review_titles = isset($jobsearch_plugin_options['cand_reviews_titles']) ? $jobsearch_plugin_options['cand_reviews_titles'] : '';
            } else {
                $main_class = $prefix . '-employer-wrap-section';
                $review_titles = isset($jobsearch_plugin_options['reviews_titles']) ? $jobsearch_plugin_options['reviews_titles'] : '';
            }
            $main_class = isset($args['main_class']) && $args['main_class'] != '' ? $args['main_class'] : $main_class;
            ?>
            <div <?php echo(isset($args['div_id']) && $args['div_id'] != '' ? 'id="' . $args['div_id'] . '"' : '') ?>
                    class="dash-reviews-list <?php echo($main_class) ?>">
                <div class="<?php echo($prefix) ?>-content-title <?php echo($prefix) ?>-addmore-space">
                    <h2><?php echo($rev_list_label) ?></h2></div>

                <div class="<?php echo($prefix) ?>-company-review">
                    <?php
                    if (!empty($curr_user_comments)) {
                        foreach ($curr_user_comments as $r_comment) {

                            $current_user_com = false;

                            $com_id = $r_comment->comment_ID;
                            $comment_date = $r_comment->comment_date;

                            $rev_avg_rating = get_comment_meta($com_id, 'review_avg_rating', true);
                            $rev_overall_rating = get_comment_meta($com_id, 'review_overall_rating', true);
                            $rev_guest_user = get_comment_meta($com_id, 'review_guest_user', true);

                            $_avg_rting_perc = 0;
                            if ($rev_avg_rating > 0) {
                                $_avg_rting_perc = ($rev_avg_rating / 5) * 100;
                            }

                            $rev_avg_rating = $rev_avg_rating > 0 ? $rev_avg_rating : 0;

                            if ($rev_guest_user == '1') {
                                $user_def_avatar_url = get_avatar_url($r_comment->comment_author_email, array('size' => 60));
                            } else {
                                $comment_user_id = $r_comment->user_id;
                                $comment_user_obj = get_user_by('ID', $comment_user_id);

                                if ($current_user_id > 0 && $comment_user_id == $current_user_id) {
                                    $current_user_com = true;
                                }

                                if ($rev_post_type == 'candidate') {
                                    $comment_candidate_id = jobsearch_get_user_employer_id($comment_user_id);
                                } else {
                                    $comment_candidate_id = jobsearch_get_user_candidate_id($comment_user_id);
                                }

                                $user_def_avatar_url = get_avatar_url($comment_user_id, array('size' => 60));
                                $user_avatar_id = get_post_thumbnail_id($comment_candidate_id);
                                if ($user_avatar_id > 0) {
                                    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                }
                            }

                            // check reply
                            $com_replied = false;
                            $rep_com_args = array(
                                'post_id' => $post_id,
                                'user_id' => $post_user_id,
                                'parent' => $com_id,
                                'status' => 'approve',
                            );
                            $rep_all_coms = get_comments($rep_com_args);
                            if (isset($rep_all_coms[0]) && isset($rep_all_coms[0]->comment_content)) {
                                $com_replied = true;
                                $com_replied_content = $rep_all_coms[0]->comment_content;
                            }
                            ?>
                            <div class="reviw-mainitem-con <?php echo($com_replied === true ? 'replied-coment' : '') ?>">
                                <?php
                                if (!empty($rev_overall_rating) && sizeof($rev_overall_rating) > 1) {
                                    ?>
                                    <div class="review-detail-popover">
                                        <?php
                                        $overall_ratee_count = 0;
                                        foreach ($rev_overall_rating as $rev_overall_ratee) {
                                            $o_avg_rting_perc = 0;
                                            if ($rev_overall_ratee > 0) {
                                                $o_avg_rting_perc = ($rev_overall_ratee / 5) * 100;
                                            }

                                            $review_title = isset($review_titles[$overall_ratee_count]) ? $review_titles[$overall_ratee_count] : '';
                                            $review_title = apply_filters('wpml_translate_single_string', $review_title, 'JobSearch Options', 'Review Title - ' . $review_title, $lang_code);
                                            ?>
                                            <div class="rating-detail-item">
                                                <span class="rating-title"><?php echo($review_title) ?></span>
                                                <div class="<?php echo($prefix) ?>-company-rating"><span
                                                            class="<?php echo($prefix) ?>-company-rating-box"
                                                            style="width: <?php echo($o_avg_rting_perc) ?>%;"></span></div>
                                            </div>
                                            <?php
                                            $overall_ratee_count++;
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }

                                $under_rev_msg = '<span class="undereview-msg">' . esc_html__("Your review is under review for approval by admin.", "wp-jobsearch") . '</span>';
                                
                                if ($rev_guest_user == '1') {
                                    $com_user_dname = $r_comment->comment_author;
                                    $reviewr_url = '<strong class="company-review-thumb"><img src="' . $user_def_avatar_url . '" alt=""></strong>';
                                    $reviewr_name_url = '<strong class="reviewr-user-name">' . $com_user_dname . '</strong>';
                                } else {
                                    $com_user_dname = isset($comment_user_obj->display_name) ? $comment_user_obj->display_name : '';
                                    $com_user_dname = apply_filters('jobsearch_user_display_name', $com_user_dname, $comment_user_obj);
                                    $reviewr_url = '<a href="' . get_permalink($comment_candidate_id) . '" class="company-review-thumb"><img src="' . $user_def_avatar_url . '" alt=""></a>';
                                    $reviewr_name_url = '<a href="' . get_permalink($comment_candidate_id) . '" class="reviewr-user-name">' . $com_user_dname . ' ' . $under_rev_msg . '</a>';
                                }
                                ?>
                                <figure>
                                    <?php echo($reviewr_url) ?>
                                    <figcaption>
                                        <div class="<?php echo($prefix) ?>-company-review-left">
                                            <?php echo($reviewr_name_url); ?>
                                            <div class="<?php echo($prefix) ?>-company-rating"><span
                                                        class="<?php echo($prefix) ?>-company-rating-box"
                                                        style="width: <?php echo($_avg_rting_perc) ?>%;"></span></div>
                                            <small><?php echo number_format($rev_avg_rating, 1) ?></small>
                                        </div>
                                        <?php
                                        if ($comment_date != '') {
                                            ?>
                                            <time datetime="<?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($comment_date)) ?>"><?php echo date_i18n(get_option('date_format'), strtotime($comment_date)) ?></time>
                                            <?php
                                        }
                                        ?>
                                    </figcaption>
                                </figure>
                                <div class="reviw-contntholdr-con">
                                    <div class="<?php echo($prefix) ?>-company-review-text">
                                        <p><?php echo($r_comment->comment_content) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    foreach ($all_comments as $r_comment) {

                        $current_user_com = false;

                        $com_id = $r_comment->comment_ID;
                        $comment_date = $r_comment->comment_date;

                        $rev_avg_rating = get_comment_meta($com_id, 'review_avg_rating', true);
                        $rev_overall_rating = get_comment_meta($com_id, 'review_overall_rating', true);
                        $rev_guest_user = get_comment_meta($com_id, 'review_guest_user', true);

                        $_avg_rting_perc = 0;
                        if ($rev_avg_rating > 0) {
                            $_avg_rting_perc = ($rev_avg_rating / 5) * 100;
                        }

                        $rev_avg_rating = $rev_avg_rating > 0 ? $rev_avg_rating : 0;

                        if ($rev_guest_user == '1') {
                            $user_def_avatar_url = get_avatar_url($r_comment->comment_author_email, array('size' => 60));
                        } else {
                            $comment_user_id = $r_comment->user_id;
                            $comment_user_obj = get_user_by('ID', $comment_user_id);

                            if ($current_user_id > 0 && $comment_user_id == $current_user_id) {
                                $current_user_com = true;
                            }

                            if ($rev_post_type == 'candidate') {
                                $comment_candidate_id = jobsearch_get_user_employer_id($comment_user_id);
                            } else {
                                $comment_candidate_id = jobsearch_get_user_candidate_id($comment_user_id);
                            }

                            $user_def_avatar_url = get_avatar_url($comment_user_id, array('size' => 60));
                            $user_avatar_id = get_post_thumbnail_id($comment_candidate_id);
                            if ($user_avatar_id > 0) {
                                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                            }
                        }

                        // check reply
                        $com_replied = false;
                        $rep_com_args = array(
                            'post_id' => $post_id,
                            'user_id' => $post_user_id,
                            'parent' => $com_id,
                            'status' => 'approve',
                        );
                        $rep_all_coms = get_comments($rep_com_args);
                        if (isset($rep_all_coms[0]) && isset($rep_all_coms[0]->comment_content)) {
                            $com_replied = true;
                            $com_replied_content = $rep_all_coms[0]->comment_content;
                        }
                        ?>
                        <div class="reviw-mainitem-con <?php echo($com_replied === true ? 'replied-coment' : '') ?>">
                            <?php
                            if (!empty($rev_overall_rating) && sizeof($rev_overall_rating) > 1) {
                                ?>
                                <div class="review-detail-popover">
                                    <?php
                                    $overall_ratee_count = 0;
                                    foreach ($rev_overall_rating as $rev_overall_ratee) {
                                        $o_avg_rting_perc = 0;
                                        if ($rev_overall_ratee > 0) {
                                            $o_avg_rting_perc = ($rev_overall_ratee / 5) * 100;
                                        }

                                        $review_title = isset($review_titles[$overall_ratee_count]) ? $review_titles[$overall_ratee_count] : '';
                                        $review_title = apply_filters('wpml_translate_single_string', $review_title, 'JobSearch Options', 'Review Title - ' . $review_title, $lang_code);
                                        ?>
                                        <div class="rating-detail-item">
                                            <span class="rating-title"><?php echo($review_title) ?></span>
                                            <div class="<?php echo($prefix) ?>-company-rating"><span
                                                        class="<?php echo($prefix) ?>-company-rating-box"
                                                        style="width: <?php echo($o_avg_rting_perc) ?>%;"></span></div>
                                        </div>
                                        <?php
                                        $overall_ratee_count++;
                                    }
                                    ?>
                                </div>
                                <?php
                            }

                            if ($rev_guest_user == '1') {
                                $com_user_dname = $r_comment->comment_author;
                                $reviewr_url = '<strong class="company-review-thumb"><img src="' . $user_def_avatar_url . '" alt=""></strong>';
                                $reviewr_name_url = '<strong class="reviewr-user-name">' . $com_user_dname . '</strong>';
                            } else {
                                $com_user_dname = isset($comment_user_obj->display_name) ? $comment_user_obj->display_name : '';
                                $com_user_dname = apply_filters('jobsearch_user_display_name', $com_user_dname, $comment_user_obj);
                                $reviewr_url = '<a href="' . get_permalink($comment_candidate_id) . '" class="company-review-thumb"><img src="' . $user_def_avatar_url . '" alt=""></a>';
                                $reviewr_name_url = '<a href="' . get_permalink($comment_candidate_id) . '" class="reviewr-user-name">' . $com_user_dname . '</a>';
                            }
                            ?>
                            <figure>
                                <?php echo($reviewr_url) ?>
                                <figcaption>
                                    <div class="<?php echo($prefix) ?>-company-review-left">
                                        <?php echo($reviewr_name_url) ?>
                                        <div class="<?php echo($prefix) ?>-company-rating"><span
                                                    class="<?php echo($prefix) ?>-company-rating-box"
                                                    style="width: <?php echo($_avg_rting_perc) ?>%;"></span></div>
                                        <small><?php echo number_format($rev_avg_rating, 1) ?></small>
                                    </div>
                                    <?php
                                    if ($comment_date != '') {
                                        ?>
                                        <time datetime="<?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($comment_date)) ?>"><?php echo date_i18n(get_option('date_format'), strtotime($comment_date)) ?></time>
                                        <?php
                                    }
                                    if ($current_user_com) {
                                        ?>
                                        <a href="javascript:void(0);" class="update-cuser-review"
                                           data-id="<?php echo($com_id) ?>"><i
                                                    class="fa fa-pencil"></i> <?php esc_html_e('Update', 'wp-jobsearch') ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </figcaption>
                            </figure>
                            <div class="reviw-contntholdr-con">
                                <div class="<?php echo($prefix) ?>-company-review-text">
                                    <p><?php echo($r_comment->comment_content) ?></p>
                                </div>
                            </div>
                            <?php
                            if ($current_user_com) {
                                ?>
                                <div id="coment-updatrev-holdr<?php echo($com_id) ?>" class="jobsearch-updaterev-holdr"
                                     style="display: none;">
                                    <a href="javascript:void(0);" class="update-review-close"><i
                                                class="fa fa-times"></i></a>
                                    <div class="jobsearch-content-title jobsearch-addmore-space">
                                        <h2><?php esc_html_e('Update Review', 'wp-jobsearch'); ?></h2></div>
                                    <div class="jobsearch-add-review-con">
                                        <form id="jobsearch-review-form<?php echo($com_id) ?>"
                                              class="jobsearch-addreview-form" method="post">
                                            <ul>
                                                <li>
                                                    <div class="review-stars-sec">
                                                        <?php
                                                        if (!empty($review_titles)) {
                                                            $review_title_count = 1;
                                                            foreach ($review_titles as $review_title) {

                                                                $selected_ratin = '';
                                                                if (isset($rev_overall_rating['user_rating_' . $review_title_count])) {
                                                                    $selected_ratin = ($rev_overall_rating['user_rating_' . $review_title_count]);
                                                                }

                                                                $review_title = apply_filters('wpml_translate_single_string', $review_title, 'JobSearch Options', 'Review Title - ' . $review_title, $lang_code);
                                                                ?>

                                                                <div class="review-stars-holder">
                                                                    <label><?php echo($review_title) ?></label>
                                                                    <select id="review-stars-selector-<?php echo($review_title_count) ?>-<?php echo($com_id) ?>"
                                                                            name="user_rating_<?php echo($review_title_count) ?>"
                                                                            autocomplete="off">
                                                                        <option value="1"<?php echo($selected_ratin == '1' ? ' selected="selected"' : '') ?>>
                                                                            1
                                                                        </option>
                                                                        <option value="2"<?php echo($selected_ratin == '2' ? ' selected="selected"' : '') ?>>
                                                                            2
                                                                        </option>
                                                                        <option value="3"<?php echo($selected_ratin == '3' ? ' selected="selected"' : '') ?>>
                                                                            3
                                                                        </option>
                                                                        <option value="4"<?php echo($selected_ratin == '4' ? ' selected="selected"' : '') ?>>
                                                                            4
                                                                        </option>
                                                                        <option value="5"<?php echo($selected_ratin == '5' ? ' selected="selected"' : '') ?>>
                                                                            5
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <?php
                                                                $review_title_count++;
                                                            }
                                                        } else {
                                                            $selected_ratin = '';
                                                            if (isset($rev_overall_rating['user_rating_1'])) {
                                                                $selected_ratin = ($rev_overall_rating['user_rating_1']);
                                                            }
                                                            ?>
                                                            <div class="review-stars-holder">
                                                                <label><?php esc_html_e("Rating", "wp-jobsearch") ?></label>
                                                                <select id="review-stars-selector-<?php echo($com_id) ?>"
                                                                        name="user_rating_1" autocomplete="off">
                                                                    <option value="1"<?php echo($selected_ratin == '1' ? ' selected="selected"' : '') ?>>
                                                                        1
                                                                    </option>
                                                                    <option value="2"<?php echo($selected_ratin == '2' ? ' selected="selected"' : '') ?>>
                                                                        2
                                                                    </option>
                                                                    <option value="3"<?php echo($selected_ratin == '3' ? ' selected="selected"' : '') ?>>
                                                                        3
                                                                    </option>
                                                                    <option value="4"<?php echo($selected_ratin == '4' ? ' selected="selected"' : '') ?>>
                                                                        4
                                                                    </option>
                                                                    <option value="5"<?php echo($selected_ratin == '5' ? ' selected="selected"' : '') ?>>
                                                                        5
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="review-overall-stars-sec">
                                                        <span class="rating-text"><?php esc_html_e("Overall Rating", "wp-jobsearch") ?></span>
                                                        <span class="rating-num"><?php echo number_format($rev_avg_rating, 1) ?></span>
                                                        <div class="jobsearch-company-rating"><span
                                                                    class="jobsearch-company-rating-box"
                                                                    style="width: <?php echo($_avg_rting_perc) ?>%;"></span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <textarea name="user_comment"
                                                              placeholder="<?php esc_html_e("Your Review", "wp-jobsearch") ?>"><?php echo($r_comment->comment_content) ?></textarea>
                                                </li>
                                                <li>
                                                    <input type="hidden" name="review_post"
                                                           value="<?php echo absint($post_id) ?>">
                                                    <input type="hidden" name="review_cid"
                                                           value="<?php echo absint($com_id) ?>">
                                                    <input type="hidden" name="action"
                                                           value="jobsearch_user_review_updatin">
                                                    <input type="submit" class="jobsearch-revupdte-submit-btn"
                                                           value="<?php esc_html_e("Update", "wp-jobsearch") ?>">
                                                    <span class="jobsearch-review-loader"></span>
                                                    <span class="jobsearch-review-msg"></span>
                                                </li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                            if ($com_replied) {
                                ?>
                                <div id="coment-reply-holdr<?php echo($com_id) ?>" class="comrnt-replyholdr-con">
                                    <div class="replied-review-box">
                                        <div class="revuser-img"><img src="<?php echo($revuser_def__url) ?>" alt="">
                                        </div>
                                        <div class="revuser-conent">
                                            <span><?php esc_html_e('Response', 'wp-jobsearch') ?></span>
                                            <p><?php echo($com_replied_content) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }

    public function post_avg_rating($args = array())
    {

        global $jobsearch_plugin_options;

        $post_id = isset($args['post_id']) ? $args['post_id'] : '';
        $rating_view = isset($args['view']) ? $args['view'] : '';
        $rating_numbrs = isset($args['total_revs']) ? $args['total_revs'] : '';
        $rating_prefix = isset($args['prefix']) ? $args['prefix'] : '';
        $classes_ext = isset($args['classes']) && !empty($args['classes']) ? ' ' . $args['classes'] . '' : '';
        $class_prefix = $rating_prefix == '' ? 'jobsearch' : $rating_prefix;
        $over_all_avg_rting = 0;
        $over_all_avg_rting_perc = 0;
        $oveall_review_avg_rating = get_post_meta($post_id, 'oveall_review_avg_rating', true);
        if ($oveall_review_avg_rating > 0) {
            $over_all_avg_rting = $oveall_review_avg_rating;
            $over_all_avg_rting_perc = ($over_all_avg_rting / 5) * 100;
        }

        ob_start();
        if ($over_all_avg_rting > 0) {
            if ($rating_view == 'number') {
                echo number_format($over_all_avg_rting, 1);
            } else {
                if ($rating_view == 'job3') {
                    $com_args = array(
                        'post_id' => $post_id,
                        'status' => 'approve',
                    );
                    $all_comments = get_comments($com_args);
                    $tot_reviews = !empty($all_comments) ? sizeof($all_comments) : 0;
                    ?>
                    <div class="careerfy-company-rating-style5">
                        <span class="careerfy-company-rating-box-style5"
                              style="width:<?php echo($over_all_avg_rting_perc); ?>%">

                        </span>
                    </div>
                    <small><?php echo($tot_reviews) ?><?php echo esc_html__('Reviews', 'wp-jobsearch'); ?></small>
                <?php } else if ($rating_view == 'cand5') { ?>
                    <?php
                    if (isset($rating_view) && $rating_view != 'listing') { ?>
                        <div class="<?php echo($class_prefix) ?>-rating-info-style5"><?php echo number_format($over_all_avg_rting, 1) ?></div>
                    <?php } ?>
                    <div class="<?php echo($class_prefix) ?>-company-rating"><span
                                class="<?php echo($class_prefix) ?>-company-rating-box"
                                style="width: <?php echo($over_all_avg_rting_perc) ?>%;"></span></div>

                <?php } else { ?>
                    <div class="<?php echo($class_prefix) ?>-detrating-con">
                        <?php
                        if (isset($rating_view) && $rating_view != 'listing') { ?>
                            <div class="<?php echo($class_prefix) ?>-rating-info"><?php echo number_format($over_all_avg_rting, 1) ?></div>
                        <?php } ?>
                        <div class="<?php echo($class_prefix) ?>-rating">
                            <small class="<?php echo($class_prefix) ?>-rating-box<?php echo($classes_ext) ?>"
                                   style="width:<?php echo($over_all_avg_rting_perc) ?>%"></small>
                        </div>
                        <?php
                        if ($rating_numbrs == 'yes') {
                            $com_args = array(
                                'post_id' => $post_id,
                                'status' => 'approve',
                            );
                            $all_comments = get_comments($com_args);
                            $tot_reviews = !empty($all_comments) ? sizeof($all_comments) : 0;
                            ?>
                            <span class="careerfy-employer-detail2-toparea-reviews"><?php printf(esc_html__('%s reviews', 'wp-jobsearch'), $tot_reviews) ?></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php }
            }
        }
        $html = ob_get_clean();
        echo apply_filters('jobsearch_avg_review_frontend_html', $html);
    }

    private function user_avg_review_asign_to_arr($arr = array(), $rate = 1)
    {
        if ($rate > 0 && $rate < 2) {
            $arr['rating_1'] += 1;
        } else if ($rate >= 2 && $rate < 3) {
            $arr['rating_2'] += 1;
        } else if ($rate >= 3 && $rate < 4) {
            $arr['rating_3'] += 1;
        } else if ($rate >= 4 && $rate < 5) {
            $arr['rating_4'] += 1;
        } else {
            $arr['rating_5'] += 1;
        }
        return $arr;
    }

    private function user_review_asign_to_arr($arr = array(), $rate = '1')
    {
        switch ($rate) {
            case '1':
                $arr['rating_1'] += 1;
                break;
            case '2':
                $arr['rating_2'] += 1;
                break;
            case '3':
                $arr['rating_3'] += 1;
                break;
            case '4':
                $arr['rating_4'] += 1;
                break;
            case '5':
                $arr['rating_5'] += 1;
                break;
        }
        return $arr;
    }

    public function user_review_submit()
    {
        global $jobsearch_plugin_options;

        $review_text_length = isset($jobsearch_plugin_options['review_text_length']) ? $jobsearch_plugin_options['review_text_length'] : '';
        $review_text_length = absint($review_text_length) > 0 ? absint($review_text_length) : 5000;

        $public_reviews = isset($jobsearch_plugin_options['public_reviews_switch']) ? $jobsearch_plugin_options['public_reviews_switch'] : '';

        $user_can_review = true;
        if ($public_reviews != 'on' && !is_user_logged_in()) {
            $user_can_review = false;
        }
        
        $manual_approve = get_option('comment_moderation');

        $time = current_time('mysql');

        $review_post_id = isset($_POST['review_post']) ? $_POST['review_post'] : '';
        $user_comment = isset($_POST['user_comment']) ? $_POST['user_comment'] : '';

        $rev_post_type = get_post_type($review_post_id);

        if ($user_can_review) {
            $user_id = 0;
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_obj = get_user_by('ID', $user_id);

                if ($rev_post_type == 'candidate') {
                    $user_can_post = jobsearch_user_is_employer($user_id);
                } else {
                    $user_can_post = jobsearch_user_is_candidate($user_id);
                }
            } else {
                $user_can_post = true;
            }

            if ($user_can_post) {
                if (is_user_logged_in()) {
                    $user_name = $user_obj->display_name;
                    $user_email = $user_obj->user_email;
                } else {
                    $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
                    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
                    if ($user_name == '') {
                        $msg = esc_html__("Please enter your name.", "wp-jobsearch");
                        echo json_encode(array('msg' => $msg, 'error' => '1'));
                        die;
                    }
                    if ($user_email == '') {
                        $msg = esc_html__("Please enter your email address.", "wp-jobsearch");
                        echo json_encode(array('msg' => $msg, 'error' => '1'));
                        die;
                    }
                    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                        $msg = esc_html__("Please enter a valid email address.", "wp-jobsearch");
                        echo json_encode(array('msg' => $msg, 'error' => '1'));
                        die;
                    }
                }

                if ($user_comment == '') {
                    $msg = esc_html__("Review text cannot be blank.", "wp-jobsearch");
                    echo json_encode(array('msg' => $msg, 'error' => '1'));
                    die;
                }
                if (strlen($user_comment) > $review_text_length) {
                    $msg = sprintf(esc_html__("The maximum Review text allowed is %s characters.", "wp-jobsearch"), $review_text_length);
                    echo json_encode(array('msg' => $msg, 'error' => '1'));
                    die;
                }

                if ($rev_post_type == 'candidate') {
                    $review_titles = isset($jobsearch_plugin_options['cand_reviews_titles']) ? $jobsearch_plugin_options['cand_reviews_titles'] : '';
                } else {
                    $review_titles = isset($jobsearch_plugin_options['reviews_titles']) ? $jobsearch_plugin_options['reviews_titles'] : '';
                }

                // already reviewd check
                $args = array(
                    'post_id' => $review_post_id,
                );
                $post_all_comments = get_comments($args);

                $user_has_reviewd = false;
                if (!empty($post_all_comments)) {
                    if (is_user_logged_in()) {
                        foreach ($post_all_comments as $postr_comment) {
                            $comment_user_id = $postr_comment->user_id;

                            if ($user_id == $comment_user_id) {
                                $user_has_reviewd = true;
                                break;
                            }
                        }
                    } else {
                        foreach ($post_all_comments as $postr_comment) {
                            $comment_user_email = $postr_comment->comment_author_email;

                            if ($user_email == $comment_user_email) {
                                $user_has_reviewd = true;
                                break;
                            }
                        }
                    }
                }
                if ($user_has_reviewd === true) {
                    $msg = esc_html__("You have already posted a review here.", "wp-jobsearch");
                    echo json_encode(array('msg' => $msg, 'error' => '1'));
                    die;
                }
                //

                $review_user_rate = array();
                
                if (!empty($review_titles)) {
                    $review_title_count = 1;
                    foreach ($review_titles as $review_title) {
                        $review_user_rate['user_rating_' . $review_title_count] = isset($_POST['user_rating_' . $review_title_count]) && absint($_POST['user_rating_' . $review_title_count]) > 0 ? $_POST['user_rating_' . $review_title_count] : 1;
                        $review_title_count++;
                    }
                } else {
                    $review_user_rate['user_rating_1'] = isset($_POST['user_rating_1']) && absint($_POST['user_rating_1']) > 0 ? $_POST['user_rating_1'] : 1;
                }
                //
                $total_rating = 0;
                $num_ratings = 0;
                foreach ($review_user_rate as $review_user_rat_key => $review_user_rat_val) {
                    $review_user_rat_val = absint($review_user_rat_val) > 5 ? 5 : absint($review_user_rat_val);
                    $total_rating += $review_user_rat_val;
                    $num_ratings++;
                }
                $avg_rating = 1;
                if ($total_rating > 0 && $num_ratings > 0) {
                    $avg_rating = $total_rating / $num_ratings;
                }

                $review_data = array(
                    'comment_post_ID' => $review_post_id,
                    'comment_author' => $user_name,
                    'comment_author_email' => $user_email,
                    'comment_author_url' => '',
                    'comment_content' => $user_comment,
                    'comment_type' => '',
                    'comment_parent' => 0,
                    'user_id' => $user_id,
                    'comment_author_IP' => '',
                    'comment_agent' => '',
                    'comment_date' => $time,
                    'comment_approved' => ($manual_approve ? 0 : 1),
                );

                $comment_id = wp_insert_comment($review_data);

                add_comment_meta($comment_id, 'review_avg_rating', $avg_rating);
                add_comment_meta($comment_id, 'review_overall_rating', $review_user_rate);
                if (!is_user_logged_in()) {
                    add_comment_meta($comment_id, 'review_guest_user', '1');
                }

                if ($manual_approve) {
                    //
                    $msg = esc_html__("Review is submitted for admin approval.", "wp-jobsearch");
                } else {
                    // updating review Post
                    $all_avg_ratings = $all_overall_ratings = array(
                        'rating_1' => 0,
                        'rating_2' => 0,
                        'rating_3' => 0,
                        'rating_4' => 0,
                        'rating_5' => 0,
                    );

                    $args = array(
                        'post_id' => $review_post_id,
                        'parent' => 0,
                        'status' => 'approve',
                    );
                    $all_comments = get_comments($args);

                    $all_avg_ratingee = 0;
                    $count_reviews = 0;
                    if (!empty($all_comments)) {
                        foreach ($all_comments as $r_comment) {
                            $com_id = $r_comment->comment_ID;
                            $rev_avg_rating = get_comment_meta($com_id, 'review_avg_rating', true);
                            $all_avg_ratings = $this->user_avg_review_asign_to_arr($all_avg_ratings, $rev_avg_rating);

                            $all_avg_ratingee += $rev_avg_rating;

                            $rev_overall_rating = get_comment_meta($com_id, 'review_overall_rating', true);
                            if (!empty($rev_overall_rating)) {
                                $overall_user_rate = array();
                                $review_title_count = 1;
                                foreach ($rev_overall_rating as $review_title_rate) {
                                    $overall_user_rate['user_rating_' . $review_title_count] = $this->user_review_asign_to_arr($all_overall_ratings, $review_title_rate);
                                    $review_title_count++;
                                }
                            }
                            $count_reviews++;
                        }

                        $all_avg_ratingee = $all_avg_ratingee / $count_reviews;
                    }

                    update_post_meta($review_post_id, 'oveall_review_avg_rating', $all_avg_ratingee);
                    update_post_meta($review_post_id, 'oveall_review_avg_ratings', $all_avg_ratings);
                    update_post_meta($review_post_id, 'oveall_review_overall_ratings', $overall_user_rate);
                    update_post_meta($review_post_id, 'oveall_review_count', $count_reviews);

                    $msg = esc_html__("Review submit successfully.", "wp-jobsearch");
                }
                echo json_encode(array('msg' => $msg, 'acton' => 'added'));
                die;
                //
            } else {
                $msg = esc_html__("You cannot add a review.", "wp-jobsearch");
                echo json_encode(array('msg' => $msg, 'error' => '1'));
                die;
            }
            //
        } else {
            $msg = esc_html__("You are not logged in.", "wp-jobsearch");
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            die;
        }
    }

    public function user_review_update()
    {
        global $jobsearch_plugin_options;

        $review_text_length = isset($jobsearch_plugin_options['review_text_length']) ? $jobsearch_plugin_options['review_text_length'] : '';
        $review_text_length = absint($review_text_length) > 0 ? absint($review_text_length) : 5000;

        $review_post_id = isset($_POST['review_post']) ? $_POST['review_post'] : '';
        $review_com_id = isset($_POST['review_cid']) ? $_POST['review_cid'] : '';
        $user_comment = isset($_POST['user_comment']) ? $_POST['user_comment'] : '';

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_obj = get_user_by('ID', $user_id);

            $revpost_user_id = get_post_meta($review_post_id, 'jobsearch_user_id', true);

            $rep_com_args = array(
                'comment__in' => array($review_com_id),
                'post_id' => $review_post_id,
                'user_id' => $user_id,
            );
            $rep_all_coms = get_comments($rep_com_args);
            if (isset($rep_all_coms[0])) {
                // do nothing
            } else {
                echo json_encode(array('msg' => esc_html__('You can update only your own reviews.', 'wp-jobsearch'), 'error' => '1'));
                die;
            }

            $rev_post_type = get_post_type($review_post_id);

            if ($rev_post_type == 'candidate') {
                $user_can_post = jobsearch_user_is_employer($user_id);
                $review_titles = isset($jobsearch_plugin_options['cand_reviews_titles']) ? $jobsearch_plugin_options['cand_reviews_titles'] : '';
            } else {
                $user_can_post = jobsearch_user_is_candidate($user_id);
                $review_titles = isset($jobsearch_plugin_options['reviews_titles']) ? $jobsearch_plugin_options['reviews_titles'] : '';
            }

            if ($user_can_post) {

                if ($user_comment == '') {
                    $msg = esc_html__("Review text cannot be blank.", "wp-jobsearch");
                    echo json_encode(array('msg' => $msg, 'error' => '1'));
                    die;
                }
                if (strlen($user_comment) > $review_text_length) {
                    $msg = sprintf(esc_html__("The maximum Review text allowed is %s characters.", "wp-jobsearch"), $review_text_length);
                    echo json_encode(array('msg' => $msg, 'error' => '1'));
                    die;
                }
                //

                $review_user_rate = array();
                if (!empty($review_titles)) {
                    $review_title_count = 1;
                    foreach ($review_titles as $review_title) {
                        $review_user_rate['user_rating_' . $review_title_count] = isset($_POST['user_rating_' . $review_title_count]) && absint($_POST['user_rating_' . $review_title_count]) > 0 ? $_POST['user_rating_' . $review_title_count] : 1;
                        $review_title_count++;
                    }
                } else {
                    $review_user_rate['user_rating_1'] = isset($_POST['user_rating_1']) && absint($_POST['user_rating_1']) > 0 ? $_POST['user_rating_1'] : 1;
                }
                //
                $total_rating = 0;
                $num_ratings = 0;
                foreach ($review_user_rate as $review_user_rat_key => $review_user_rat_val) {
                    $review_user_rat_val = absint($review_user_rat_val) > 5 ? 5 : absint($review_user_rat_val);
                    $total_rating += $review_user_rat_val;
                    $num_ratings++;
                }
                $avg_rating = 1;
                if ($total_rating > 0 && $num_ratings > 0) {
                    $avg_rating = $total_rating / $num_ratings;
                }

                $review_data = array(
                    'comment_ID' => $review_com_id,
                    'comment_content' => $user_comment,
                );

                wp_update_comment($review_data);

                update_comment_meta($review_com_id, 'review_avg_rating', $avg_rating);
                update_comment_meta($review_com_id, 'review_overall_rating', $review_user_rate);

                // delete reply
                $del_com_args = array(
                    'post_id' => $review_post_id,
                    'user_id' => $revpost_user_id,
                    'parent' => $review_com_id,
                );
                $del_all_coms = get_comments($del_com_args);
                if (!empty($del_all_coms)) {
                    foreach ($del_all_coms as $delin_com) {
                        $del_comid = $delin_com->comment_ID;
                        wp_delete_comment($del_comid, true);
                    }
                }

                // updating review Post
                $all_avg_ratings = $all_overall_ratings = array(
                    'rating_1' => 0,
                    'rating_2' => 0,
                    'rating_3' => 0,
                    'rating_4' => 0,
                    'rating_5' => 0,
                );

                $args = array(
                    'post_id' => $review_post_id,
                    'parent' => 0,
                    'status' => 'approve',
                );
                $all_comments = get_comments($args);

                $all_avg_ratingee = 0;
                $count_reviews = 0;
                if (!empty($all_comments)) {
                    foreach ($all_comments as $r_comment) {
                        $com_id = $r_comment->comment_ID;
                        $rev_avg_rating = get_comment_meta($com_id, 'review_avg_rating', true);
                        $all_avg_ratings = $this->user_avg_review_asign_to_arr($all_avg_ratings, $rev_avg_rating);

                        $all_avg_ratingee += $rev_avg_rating;

                        $rev_overall_rating = get_comment_meta($com_id, 'review_overall_rating', true);
                        if (!empty($rev_overall_rating)) {
                            $overall_user_rate = array();
                            $review_title_count = 1;
                            foreach ($rev_overall_rating as $review_title_rate) {
                                $dynover_rate_var = 'overall_dyn_ratings' . $review_title_count;
                                $$dynover_rate_var = $this->user_review_asign_to_arr($all_overall_ratings, $review_title_rate);
                                $overall_user_rate['user_rating_' . $review_title_count] = $$dynover_rate_var;
                                $review_title_count++;
                            }
                        }
                        $count_reviews++;
                    }

                    $all_avg_ratingee = $all_avg_ratingee / $count_reviews;
                }

                update_post_meta($review_post_id, 'oveall_review_avg_rating', $all_avg_ratingee);
                update_post_meta($review_post_id, 'oveall_review_avg_ratings', $all_avg_ratings);
                update_post_meta($review_post_id, 'oveall_review_overall_ratings', $overall_user_rate);
                update_post_meta($review_post_id, 'oveall_review_count', $count_reviews);

                $msg = esc_html__("Review updated.", "wp-jobsearch");
                echo json_encode(array('msg' => $msg, 'acton' => 'update'));
                die;
                //
            } else {
                $msg = esc_html__("You cannot add a review.", "wp-jobsearch");
                echo json_encode(array('msg' => $msg, 'error' => '1'));
                die;
            }
            //
        } else {
            $msg = esc_html__("You are not logged in.", "wp-jobsearch");
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            die;
        }
    }

}

// class Jobsearch_Reviews 
$Jobsearch_Reviews_Frontend_obj = new Jobsearch_Reviews_Frontend();
global $Jobsearch_Reviews_Frontend_obj;
