<?php
/**
 * FAQs Shortcode
 * @return html
 */

add_shortcode('careerfy_faqs', 'careerfy_faqs_shortcode');
function careerfy_faqs_shortcode($atts, $content = '')
{

    global $wpdb;
    extract(shortcode_atts(array(
        'view' => '',
        'ques_title' => '',
        'op_first_q' => 'yes',
        'faq_cat' => '',
        'faq_excerpt' => '',
        'faq_order' => 'DESC',
        'faq_orderby' => 'date',
        'num_of_faqs' => '10',
    ), $atts));

    $s_keyword = isset($_GET['keyword']) && $_GET['keyword'] != '' ? sanitize_text_field($_GET['keyword']) : '';

    $faq_shortcode_counter = 1;

    $faq_shortcode_rand_id = rand(10000000, 99999999);

    ob_start();

    if ($ques_title != '') { ?>
        <div class="careerfy-section-title"><h2><?php echo($ques_title) ?></h2></div>
        <?php
    }

    if ($s_keyword != '') {
        $s_quests_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
        $s_quests_query .= " WHERE post_type='faq' AND post_status='publish'";
        $s_quests_query .= " AND (posts.post_title LIKE '%{$s_keyword}%')";
        $srch_in_posts = $wpdb->get_col($s_quests_query);
    }

    $num_of_faqs = $num_of_faqs == '' ? -1 : absint($num_of_faqs);
    $args = array(
        'post_type' => 'faq',
        'posts_per_page' => $num_of_faqs,
        'post_status' => 'publish',
        'order' => $faq_order,
        'orderby' => $faq_orderby,
    );

    if ($faq_cat && $faq_cat != '' && $faq_cat != '0') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'faq-category',
                'field' => 'slug',
                'terms' => $faq_cat,
            ),
        );
    }
    if (isset($srch_in_posts) && !empty($srch_in_posts)) {
        $args['post__in'] = $srch_in_posts;
    }

    $faq_query = new WP_Query($args);
    $total_posts = $faq_query->found_posts;

    if ($faq_query->have_posts()) {
        if ($view == 'style2') { ?>
            <div class="panel-group careerfy-accordion-style2" id="accordion-<?php echo($faq_shortcode_rand_id) ?>">
                <?php
                while ($faq_query->have_posts()) : $faq_query->the_post();

                    $item_rand_id = rand(10000000, 99999999);

                    $open_faq_item = false;
                    if ($op_first_q == 'yes' && $faq_shortcode_counter == 1) {
                        $open_faq_item = true;
                    }
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a <?php echo($open_faq_item ? '' : 'class="collapsed"') ?> role="button"
                                                                                            data-toggle="collapse"
                                                                                            data-parent="#accordion-<?php echo($faq_shortcode_rand_id) ?>"
                                                                                            href="#collapse-<?php echo($item_rand_id) ?>"
                                                                                            aria-expanded="true"
                                                                                            aria-controls="collapse-<?php echo($item_rand_id) ?>">

                                    <?php echo get_the_title(get_the_ID()) ?>
                                    <i class="careerfy-icon careerfy-arrow-right-fill"></i>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-<?php echo($item_rand_id) ?>"
                             class="panel-collapse collapse <?php echo($open_faq_item ? 'in' : '') ?>">
                            <div class="panel-body">
                                <?php echo careerfy_excerpt($faq_excerpt) ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $faq_shortcode_counter++;

                endwhile;
                wp_reset_postdata();
                ?>
            </div>

        <?php } else { ?>

         <div class="panel-group careerfy-accordion" id="accordion-<?php echo($faq_shortcode_rand_id) ?>">
                <?php
                while ($faq_query->have_posts()) : $faq_query->the_post();

                    $item_rand_id = rand(10000000, 99999999);

                    $open_faq_item = false;
                    if ($op_first_q == 'yes' && $faq_shortcode_counter == 1) {
                        $open_faq_item = true;
                    }
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a <?php echo($open_faq_item ? '' : 'class="collapsed"') ?> role="button"
                                                                                            data-toggle="collapse"
                                                                                            data-parent="#accordion-<?php echo($faq_shortcode_rand_id) ?>"
                                                                                            href="#collapse-<?php echo($item_rand_id) ?>"
                                                                                            aria-expanded="true"
                                                                                            aria-controls="collapse-<?php echo($item_rand_id) ?>">
                                    <i class="careerfy-icon careerfy-arrows"></i>
                                    Q. <?php echo get_the_title(get_the_ID()) ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-<?php echo($item_rand_id) ?>"
                             class="panel-collapse collapse <?php echo($open_faq_item ? 'in' : '') ?>">
                            <div class="panel-body">
                                <?php echo careerfy_excerpt($faq_excerpt) ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $faq_shortcode_counter++;

                endwhile;
                wp_reset_postdata();
                ?>
            </div>

        <?php } ?>
        <?php
    } else {
        echo '<p>' . esc_html__('No question found.', 'careerfy-frame') . '</p>';
    }
    $html = ob_get_clean();
    return apply_filters('careerfy_faqs_shrtcde_html', $html, $atts);
}
