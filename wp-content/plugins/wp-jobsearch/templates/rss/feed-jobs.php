<?php
/**
 * Template Name: Custom RSS Template - Jobsearch Job Feed
 */
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
global $sitepress;
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
    $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
}
$jobs_query = new WP_Query($q_args);
$wpml_job_totnum = $jobs_query->found_posts;

if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
    $sitepress_def_lang = $sitepress->get_default_language();
    $sitepress_curr_lang = $sitepress->get_current_language();
    $sitepress->switch_lang($sitepress_def_lang, true);

    $jobs_query = new WP_Query($q_args);

    //
    $sitepress->switch_lang($sitepress_curr_lang, true);
}
$jobsearch__options = get_option('jobsearch_plugin_options');
$sectors_enable_switch = isset($jobsearch__options['sectors_onoff_switch']) ? $jobsearch__options['sectors_onoff_switch'] : '';
$all_location_allow = isset($jobsearch__options['all_location_allow']) ? $jobsearch__options['all_location_allow'] : '';
$job_types_switch = isset($jobsearch__options['job_types_switch']) ? $jobsearch__options['job_types_switch'] : '';
$job_views_publish_date = isset($jobsearch__options['job_views_publish_date']) ? $jobsearch__options['job_views_publish_date'] : '';
$salary_onoff_switch = isset($jobsearch__options['salary_onoff_switch']) ? $jobsearch__options['salary_onoff_switch'] : '';
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     <?php do_action('rss2_ns'); ?>>
    <channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php
        if ($jobs_query->have_posts()) {
            while ($jobs_query->have_posts()) : $jobs_query->the_post();
                $job_id = get_the_ID();
                $post_obj = get_post($job_id);
                
                $expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
                if ($expiry_date != '') {
                    $expiry_date = date('Y-m-d H:i:s', $expiry_date);
                    $expiry_date = mysql2date('D, d M Y H:i:s +0000', $expiry_date, false);
                }
                $post_content = isset($post_obj->post_content) ? $post_obj->post_content : '';
                $post_content = apply_filters('the_content', $post_content);
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $postby_emp_name = '';
                if ($postby_emp_id > 0) {
                    $postby_emp_name = get_the_title($postby_emp_id);
                }
                $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                
                $job_city_title = '';
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, true, false, true, true);
                }
                
                $job_salary = jobsearch_job_offered_salary($job_id);
                
                $job_sectors = wp_get_post_terms($job_id, 'sector');
                $job_sector = isset($job_sectors[0]->name) ? $job_sectors[0]->name : '';
                $job_types = wp_get_post_terms($job_id, 'jobtype');
                $job_type = isset($job_types[0]->name) ? $job_types[0]->name : '';
                ?>
                <item>
                    <title><![CDATA[<?php the_title_rss(); ?>]]></title>
                    <link><![CDATA[<?php the_permalink_rss(); ?>]]></link>
                    <?php
                    if ($job_views_publish_date == 'on') {
                        ?>
                        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                        <?php
                    }
                    ?>
                    <expiryDate><?php echo ($expiry_date); ?></expiryDate>
                    <?php
                    if ($salary_onoff_switch != 'off') {
                        ?>
                        <salary><![CDATA[<?php echo ($job_salary) ?>]]></salary>
                        <?php
                    }
                    ?>
                    <employer><![CDATA[<?php echo ($postby_emp_name) ?>]]></employer>
                    <employerImg><![CDATA[<?php echo ($post_thumbnail_src) ?>]]></employerImg>
                    <?php
                    if ($all_location_allow == 'on') {
                        ?>
                        <location><![CDATA[<?php echo ($job_city_title) ?>]]></location>
                        <?php
                    }
                    if ($sectors_enable_switch == 'on') {
                        ?>
                        <sector><![CDATA[<?php echo ($job_sector) ?>]]></sector>
                        <?php
                    }
                    if ($job_types_switch != 'off') {
                        ?>
                        <type><![CDATA[<?php echo ($job_type) ?>]]></type>
                        <?php
                    }
                    ?>
                    <excerpt><![CDATA[<?php the_excerpt_rss() ?>]]></excerpt>
                    <description><![CDATA[<?php echo ($post_content) ?>]]></description>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
                <?php
            endwhile;
        }
        ?>
    </channel>
</rss>