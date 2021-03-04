<?php
/*
  Class : Reviews Dashboard
 */

use WP_Jobsearch\Package_Limits;

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Reviews_Dashboard {

    public function __construct() {
        add_filter('jobsearch_emp_dash_menu_in_opts', array($this, 'empdash_menu_items_inopts_arr'), 10, 1);
        add_filter('jobsearch_emp_dash_menu_in_opts_swch', array($this, 'empdash_menu_items_inopts_swch_arr'), 10, 1);
        add_filter('jobsearch_emp_menudash_link_reviews_item', array($this, 'empdash_menu_items_in_fmenu'), 10, 5);

        //
        add_filter('jobsearch_cand_dash_menu_in_opts', array($this, 'canddash_menu_items_inopts_arr'), 10, 1);
        add_filter('jobsearch_cand_dash_menu_in_opts_swch', array($this, 'canddash_menu_items_inopts_swch_arr'), 10, 1);
        add_filter('jobsearch_cand_menudash_link_reviews_item', array($this, 'canddash_menu_items_in_fmenu'), 10, 5);

        //
        add_filter('jobsearch_dashboard_tab_content_ext', array($this, 'dash_tab_reviews_add'), 10, 2);

        //
        add_filter('wp_ajax_jobsearch_user_replying_to_review', array($this, 'user_replying_to_review'));
    }

    public function empdash_menu_items_inopts_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['reviews_switch']) ? $jobsearch__options['reviews_switch'] : '';

        if ($reviews_switch == 'on') {
            $opts_arr['reviews'] = __('Reviews', 'wp-jobsearch');
        }

        return $opts_arr;
    }

    public function empdash_menu_items_inopts_swch_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['reviews_switch']) ? $jobsearch__options['reviews_switch'] : '';

        if ($reviews_switch == 'on') {
            $opts_arr['reviews'] = true;
        }

        return $opts_arr;
    }

    public function empdash_menu_items_in_fmenu($opts_item = '', $emp_menu_item, $get_tab, $page_url, $employer_id) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['reviews_switch']) ? $jobsearch__options['reviews_switch'] : '';

        $user_pkg_limits = new Package_Limits;
        
        if ($reviews_switch == 'on') {
            $dashmenu_links_emp = isset($jobsearch__options['emp_dashbord_menu']) ? $jobsearch__options['emp_dashbord_menu'] : '';
            ob_start();
            $link_item_switch = isset($dashmenu_links_emp['reviews']) ? $dashmenu_links_emp['reviews'] : '';
            if ($emp_menu_item == 'reviews' && $link_item_switch == '1') {
                ?>
                <li<?php echo ($get_tab == 'reviews' ? ' class="active"' : '') ?>>
                    <?php
                    if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|reviews')) {
                        echo ($user_pkg_limits::dashtab_locked_html('reviews', 'fa fa-star-o', esc_html__('Reviews', 'wp-jobsearch')));
                    } else {
                        ?>
                        <a href="<?php echo add_query_arg(array('tab' => 'reviews'), $page_url) ?>">
                            <i class="fa fa-star-o"></i>
                            <?php esc_html_e('Reviews', 'wp-jobsearch') ?>
                        </a>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
        }

        return $opts_item;
    }

    public function canddash_menu_items_inopts_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['candidate_reviews_switch']) ? $jobsearch__options['candidate_reviews_switch'] : '';

        if ($reviews_switch == 'on') {
            $opts_arr['reviews'] = __('Reviews', 'wp-jobsearch');
        }

        return $opts_arr;
    }

    public function canddash_menu_items_inopts_swch_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['candidate_reviews_switch']) ? $jobsearch__options['candidate_reviews_switch'] : '';

        if ($reviews_switch == 'on') {
            $opts_arr['reviews'] = true;
        }

        return $opts_arr;
    }

    public function canddash_menu_items_in_fmenu($opts_item = '', $cand_menu_item, $get_tab, $page_url, $candidate_id) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $reviews_switch = isset($jobsearch__options['candidate_reviews_switch']) ? $jobsearch__options['candidate_reviews_switch'] : '';

        $user_pkg_limits = new Package_Limits;
        
        if ($reviews_switch == 'on') {
            $dashmenu_links_cand = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
            $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
            ob_start();
            $link_item_switch = isset($dashmenu_links_cand['reviews']) ? $dashmenu_links_cand['reviews'] : '';
            if ($cand_menu_item == 'reviews' && $link_item_switch == '1') {
                ?>
                <li<?php echo ($get_tab == 'reviews' ? ' class="active"' : '') ?>>
                    <?php
                    if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|reviews')) {
                        echo ($user_pkg_limits::dashtab_locked_html('reviews', 'fa fa-star-o', esc_html__('Reviews', 'wp-jobsearch')));
                    } else {
                        ?>
                        <a href="<?php echo add_query_arg(array('tab' => 'reviews'), $page_url) ?>">
                            <i class="fa fa-star-o"></i>
                            <?php esc_html_e('Reviews', 'wp-jobsearch') ?>
                        </a>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
        }

        return $opts_item;
    }

    public function dash_tab_reviews_add($html = '', $get_tab = '') {
        global $sitepress, $Jobsearch_User_Dashboard_Settings;
        $jobsearch__options = get_option('jobsearch_plugin_options');
        
        $page_id = $user_dashboard_page = isset($jobsearch__options['user-dashboard-template-page']) ? $jobsearch__options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
        $reults_per_page = isset($jobsearch__options['user-dashboard-per-page']) && $jobsearch__options['user-dashboard-per-page'] > 0 ? $jobsearch__options['user-dashboard-per-page'] : 10;
        $page_num = isset($_GET['page_num']) && $_GET['page_num'] > 1 ? $_GET['page_num'] : 1;

        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        $is_candidate = jobsearch_user_is_candidate($user_id);

        if ($is_employer) {
            $reviews_switch = isset($jobsearch__options['reviews_switch']) ? $jobsearch__options['reviews_switch'] : '';
        } else {
            $reviews_switch = isset($jobsearch__options['candidate_reviews_switch']) ? $jobsearch__options['candidate_reviews_switch'] : '';
        }
        
        $dashmenu_links_cand = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
        $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
        
        $dashmenu_links_emp = isset($jobsearch__options['emp_dashbord_menu']) ? $jobsearch__options['emp_dashbord_menu'] : '';
        
        $dash_menuactive = false;
        if ($is_candidate && isset($dashmenu_links_cand['reviews']) && $dashmenu_links_cand['reviews'] == '1') {
            $dash_menuactive = true;
        } else if ($is_employer && isset($dashmenu_links_emp['reviews']) && $dashmenu_links_emp['reviews'] == '1') {
            $dash_menuactive = true;
        }

        if ($reviews_switch == 'on' && $get_tab == 'reviews' && ($is_employer || $is_candidate) && $dash_menuactive === true) {
            ob_start();

            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }

            if ($is_employer) {
                $post_id = jobsearch_get_user_employer_id($user_id);
            } else {
                $post_id = jobsearch_get_user_candidate_id($user_id);
            }

            $oveall_review_avg_rating = get_post_meta($post_id, 'oveall_review_avg_rating', true);
            $oveall_review_avg_ratings = get_post_meta($post_id, 'oveall_review_avg_ratings', true);
            $oveall_review_overall_ratings = get_post_meta($post_id, 'oveall_review_overall_ratings', true);
            $oveall_review_count = get_post_meta($post_id, 'oveall_review_count', true);

            $over_all_avg_rting_perc = 0;
            if ($oveall_review_avg_rating > 0) {
                $over_all_avg_rting_perc = ($oveall_review_avg_rating / 5) * 100;
            }
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title jobsearch-revmain-tholdr">
                    <div class="jobsearch-revmtitle-box">
                        <h2><?php printf(esc_html__('%s Reviews', 'wp-jobsearch'), absint($oveall_review_count)) ?></h2>
                        <div class="jobsearch-company-rating"><span class="jobsearch-company-rating-box" style="width:<?php echo ($over_all_avg_rting_perc); ?>%"></span></div>
                    </div>
                    <?php
                    if ($oveall_review_count > 0) {
                        $revm_gsortin = isset($_GET['review_sort']) ? $_GET['review_sort'] : '';
                        ?>
                        <div class="jobsearch-revmsort-box">
                            <span><?php esc_html_e('Sort by', 'wp-jobsearch') ?></span>
                            <form id="review-sortin-form" method="get">
                                <input type="hidden" name="tab" value="reviews">
                                <select class="selectize-select" name="review_sort" onchange="this.form.submit()">
                                    <option value="recent"><?php esc_html_e('Recent', 'wp-jobsearch') ?></option>
                                    <option value="high"<?php echo ($revm_gsortin == 'high' ? ' selected="selected"' : '') ?>><?php esc_html_e('High Rated', 'wp-jobsearch') ?></option>
                                    <option value="low"<?php echo ($revm_gsortin == 'low' ? ' selected="selected"' : '') ?>><?php esc_html_e('Low Rated', 'wp-jobsearch') ?></option>
                                </select>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                $revuser_def__url = get_avatar_url($user_id, array('size' => 60));
                $revuser_user_avatid = get_post_thumbnail_id($post_id);
                if ($revuser_user_avatid > 0) {
                    $revuser_thumb_image = wp_get_attachment_image_src($revuser_user_avatid, 'thumbnail');
                    $revuser_def__url = isset($revuser_thumb_image[0]) && esc_url($revuser_thumb_image[0]) != '' ? $revuser_thumb_image[0] : '';
                }

                $prefix = 'jobsearch';

                $rev_post_type = get_post_type($post_id);

                $com_args = array(
                    'post_id' => $post_id,
                    'parent' => 0,
                    'status' => 'approve',
                );
                $totl_reviews_get = get_comments($com_args);
                $total_reviews = !empty($totl_reviews_get) ? count($totl_reviews_get) : 0;

                $com_args['number'] = $reults_per_page;
                $com_args['offset'] = $page_num > 1 ? (($page_num - 1) * $reults_per_page) : 0;

                if (isset($_GET['review_sort'])) {
                    $revg_sort = $_GET['review_sort'];
                    if ($revg_sort == 'low') {
                        $com_args['meta_key'] = 'review_avg_rating';
                        $com_args['order_by'] = 'meta_value_num';
                        $com_args['order'] = 'DESC';
                    } else if ($revg_sort == 'high') {
                        $com_args['meta_key'] = 'review_avg_rating';
                        $com_args['order_by'] = 'meta_value_num';
                        $com_args['order'] = 'ASC';
                    }
                }
                $all_comments = get_comments($com_args);

                wp_enqueue_script('jobsearch-barrating');
                wp_enqueue_script('jobsearch-add-review');
                if (!empty($all_comments)) {

                    $main_class = $prefix . '-employer-wrap-section';

                    $review_titles = isset($jobsearch__options['reviews_titles']) ? $jobsearch__options['reviews_titles'] : '';
                    ?>
                    <div class="dash-reviews-list <?php echo ($main_class) ?>">

                        <div class="<?php echo ($prefix) ?>-company-review">
                            <?php
                            foreach ($all_comments as $r_comment) {
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

                                    if ($is_employer) {
                                        $comment_member_id = jobsearch_get_user_candidate_id($comment_user_id);
                                    } else {
                                        $comment_member_id = jobsearch_get_user_employer_id($comment_user_id);
                                    }
                                    $user_def_avatar_url = get_avatar_url($comment_user_id, array('size' => 60));
                                    $user_avatar_id = get_post_thumbnail_id($comment_member_id);
                                    if ($user_avatar_id > 0) {
                                        $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                        $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                    }
                                }
                                ?>
                                <div class="reviw-mainitem-con">
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
                                                    <span class="rating-title"><?php echo ($review_title) ?></span>
                                                    <div class="<?php echo ($prefix) ?>-company-rating"><span class="<?php echo ($prefix) ?>-company-rating-box" style="width: <?php echo ($o_avg_rting_perc) ?>%;"></span></div>
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
                                        $reviewr_url = '<a href="' . get_permalink($comment_member_id) . '" class="company-review-thumb"><img src="' . $user_def_avatar_url . '" alt=""></a>';
                                        $reviewr_name_url = '<a href="' . get_permalink($comment_member_id) . '" class="reviewr-user-name">' . $com_user_dname . '</a>';
                                    }

                                    $com_replied = false;
                                    $rep_com_args = array(
                                        'post_id' => $post_id,
                                        'user_id' => $user_id,
                                        'parent' => $com_id,
                                        'status' => 'approve',
                                    );
                                    $rep_all_coms = get_comments($rep_com_args);
                                    if (isset($rep_all_coms[0]) && isset($rep_all_coms[0]->comment_content)) {
                                        $com_replied = true;
                                        $com_replied_content = $rep_all_coms[0]->comment_content;
                                    }
                                    ?>
                                    <figure>
                                        <?php echo ($reviewr_url) ?>
                                        <figcaption>
                                            <div class="<?php echo ($prefix) ?>-company-review-left">
                                                <?php echo ($reviewr_name_url) ?>
                                                <div class="<?php echo ($prefix) ?>-company-rating"><span class="<?php echo ($prefix) ?>-company-rating-box" style="width: <?php echo ($_avg_rting_perc) ?>%;"></span></div>
                                                <small><?php echo number_format($rev_avg_rating, 1) ?></small>
                                            </div>
                                            <?php
                                            if ($comment_date != '') {
                                                ?>
                                                <time datetime="<?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($comment_date)) ?>"><?php echo date_i18n(get_option('date_format'), strtotime($comment_date)) ?></time>
                                                <?php
                                            }
                                            if ($com_replied === false) {
                                                ?>
                                                <a href="javascript:void(0);" class="reply-review" data-id="<?php echo ($com_id) ?>"><i class="fa fa-reply"></i><?php esc_html_e('Reply', 'wp-jobsearch') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </figcaption>
                                    </figure>
                                    <div class="reviw-contntholdr-con">
                                        <div class="<?php echo ($prefix) ?>-company-review-text">
                                            <p><?php echo ($r_comment->comment_content) ?></p>
                                        </div>
                                    </div>
                                    <div id="coment-reply-holdr<?php echo ($com_id) ?>" class="comrnt-replyholdr-con">
                                        <?php
                                        if ($com_replied) {
                                            ?>
                                            <div class="replied-review-box">
                                                <div class="revuser-img"><img src="<?php echo ($revuser_def__url) ?>" alt=""></div>
                                                <div class="revuser-conent">
                                                    <span><?php esc_html_e('Your Response', 'wp-jobsearch') ?></span>
                                                    <p><?php echo ($com_replied_content) ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="comrnt-reply-con" style="display: none;">
                                                <span class="writ-respons-hding"><?php esc_html_e('Your Response', 'wp-jobsearch') ?></span>
                                                <a href="javascript:void(0);" class="reply-review-close"><i class="fa fa-times"></i></a>
                                                <textarea name="comernt_reply" placeholder="<?php esc_html_e('Type your response here...', 'wp-jobsearch') ?>"></textarea>
                                                <p><?php esc_html_e('Your response will be publicly visible. you will not able to edit your reply again if user change his review than you can add reply to review old reply will be deleted automatically.', 'wp-jobsearch') ?></p>
                                                <div class="submt-replybtn-con">
                                                    <a href="javascript:void(0);" class="reply-review-submit" data-id="<?php echo ($com_id) ?>"><?php esc_html_e('Submit Reply', 'wp-jobsearch') ?></a>
                                                    <span class="revreply-loder"></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $total_pages = 1;
                    if ($total_reviews > 0 && $reults_per_page > 0 && $total_reviews > $reults_per_page) {
                        $total_pages = ceil($total_reviews / $reults_per_page);
                        ?>
                        <div class="jobsearch-pagination-blog">
                            <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>' . esc_html__('No Review Found.', 'wp-jobsearch') . '</p>';
                }
                ?>
            </div>
            <?php
            $html .= ob_get_clean();

            return $html;
        }
    }

    public function user_replying_to_review() {
        $com_id = isset($_POST['com_id']) ? $_POST['com_id'] : '';
        $reply_txt = isset($_POST['reply_txt']) ? $_POST['reply_txt'] : '';

        $time = current_time('mysql');
        $user_id = get_current_user_id();
        $user_obj = get_user_by('ID', $user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        $user_is_candidate = jobsearch_user_is_candidate($user_id);

        $post_id = 0;
        if ($user_is_employer) {
            $post_id = jobsearch_get_user_employer_id($user_id);
        }
        if ($user_is_candidate) {
            $post_id = jobsearch_get_user_candidate_id($user_id);
        }

        $comment = get_comment($com_id);

        if (isset($comment->comment_post_ID) && $comment->comment_post_ID == $post_id && isset($comment->comment_parent) && $comment->comment_parent == 0) {

            $rep_com_args = array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'parent' => $com_id,
                'status' => 'approve',
            );
            $rep_all_coms = get_comments($rep_com_args);
            if (isset($rep_all_coms[0])) {
                echo json_encode(array('msg' => esc_html__('You have already replied to this review.', 'wp-jobsearch')));
                die;
            }

            //
            $revuser_def__url = get_avatar_url($user_id, array('size' => 60));
            $revuser_user_avatid = get_post_thumbnail_id($post_id);
            if ($revuser_user_avatid > 0) {
                $revuser_thumb_image = wp_get_attachment_image_src($revuser_user_avatid, 'thumbnail');
                $revuser_def__url = isset($revuser_thumb_image[0]) && esc_url($revuser_thumb_image[0]) != '' ? $revuser_thumb_image[0] : '';
            }
            //

            $user_name = $user_obj->display_name;
            $user_name = apply_filters('jobsearch_user_display_name', $user_name, $user_obj);
            $user_email = $user_obj->user_email;
            $review_data = array(
                'comment_post_ID' => $post_id,
                'comment_author' => $user_name,
                'comment_author_email' => $user_email,
                'comment_author_url' => '',
                'comment_content' => $reply_txt,
                'comment_type' => '',
                'comment_parent' => $com_id,
                'user_id' => $user_id,
                'comment_author_IP' => '',
                'comment_agent' => '',
                'comment_date' => $time,
                'comment_approved' => 1,
            );

            $comment_id = wp_insert_comment($review_data);

            ob_start();
            ?>
            <div class="replied-review-box" style="display: none;">
                <div class="revuser-img"><img src="<?php echo ($revuser_def__url) ?>" alt=""></div>
                <div class="revuser-conent">
                    <span><?php esc_html_e('Your Response', 'wp-jobsearch') ?></span>
                    <p><?php echo ($reply_txt) ?></p>
                </div>
            </div>
            <?php
            $html = ob_get_clean();

            echo json_encode(array('reply' => $html));
            die;
        } else {
            echo json_encode(array('msg' => esc_html__('You cannot reply to this review.', 'wp-jobsearch')));
            die;
        }
    }

}

// class Jobsearch_Reviews 
return new Jobsearch_Reviews_Dashboard();
