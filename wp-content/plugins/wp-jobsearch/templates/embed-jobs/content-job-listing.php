<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<li class="embeddable-job-embarg-listing">
    <div class="jobsearch-embeded-joblisting-wrap">
        <figure>
            <a href="<?php echo esc_url(get_permalink($job_id)) ?>" target="_blank">
                <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
            </a>
        </figure>
        <div class="jobsearch-list-txtcon">
            <div class="jobsearch-list-option">
                <h2 class="jobsearch-pst-title">
                    <a href="<?php echo esc_url(get_permalink($job_id)) ?>"
                       target="_blank"><?php echo esc_html(get_the_title($job_id)); ?></a>
                    <?php
                    if ($jobsearch_job_featured == 'on') { ?>
                        <span><?php esc_html_e('Featured', 'wp-jobsearch'); ?></span>
                    <?php } ?>
                </h2>
                <ul>
                    <?php
                    if ($company_name != '') {
                        ?>
                        <li class="job-company-name"><?php echo($company_name); ?></li>
                        <?php
                    }
                    if ($job_location != '') {
                        ?>
                        <li>
                            <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_location); ?>
                        </li>
                        <?php
                    }
                    if ($job_publish_date != '') {
                        ?>
                        <li>
                            <i class="jobsearch-icon jobsearch-calendar"></i><?php printf(esc_html__('Published %s', 'wp-jobsearch'), $job_publish_date); ?>
                        </li>
                        <?php
                    }
                    if (!empty($sector_str)) {
                        echo apply_filters('jobsearch_joblisting_sector_str_html', $sector_str, $job_id, '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                    }
                    ?>
                </ul>
            </div>
            <div class="jobsearch-job-typemain">
                <?php
                if ($job_type_str != '') {
                    echo($job_type_str);
                }
                ?>
            </div>
        </div>
    </div>
</li>
