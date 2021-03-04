<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<script type='text/javascript' src='<?php echo jobsearch_plugin_get_url('js/jobsearch-embed.js') ?>'></script>
<div id="embeddable-job-embarg">
    <div id="embeddable-embarg-headinmain-con"></div>
    <div id="embeddable-job-embarg-content"></div>
</div>

<script>
var the_site_title = jobsearch_embeddable_job_options.site_title;
if (the_site_title != 'off') {
    var heading_div_con = document.getElementById('embeddable-embarg-headinmain-con');
    heading_div_con.innerHTML = '<div id="embeddable-job-embarg-heading"><?php esc_html_e('Jobs From', 'wp-jobsearch') ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a></div>';
}
</script>