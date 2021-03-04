<?php
/**
 * Simple Jobs Listing Shortcode
 * @return html
 */
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Careerfy_App_Promo
{

    public function __construct()
    {
        add_shortcode('jobsearch_promo_stats', array($this, 'jobsearch_promo_stats_shortcode'));
    }

    public function jobsearch_promo_stats_shortcode($atts)
    {
        extract(shortcode_atts(array(
            'promo_title_1' => '',
            'promo_desc_1' => '',
            'promo_title_2' => '',
            'promo_desc_2' => '',
            'promo_ranking' => '1.0',
            'promo_rating_desc' => '',
            'title_color' => '',
            'desc_color' => '',
        ), $atts));

        $promo_ranking = $promo_ranking != "" ? $promo_ranking : "";
        if($promo_ranking > 5){
            $promo_ranking = 5;
        }
        $title_color = !empty($title_color) ? "style='color: {$title_color} '" : '';
        $desc_color = !empty($desc_color) ? "style='color: {$desc_color} '" : '';
        $rating = $promo_ranking * 20;
        ob_start(); ?>
        <div class="careerfy-rating-list">
            <ul class="row">
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count" <?php echo ($title_color) ?>><?php echo $promo_title_1 ?></span>
                    <small <?php echo ($desc_color) ?>><?php echo $promo_desc_1 ?></small>
                </li>
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count" <?php echo ($title_color) ?>><?php echo $promo_title_2 ?></span>
                    <small  ><?php echo $promo_desc_1 ?></small>
                </li>
                <li class="col-md-4">
                    <strong <?php echo ($title_color) ?>><?php echo $promo_ranking ?></strong>
                    <div class="careerfy-featured-rating" <?php echo ($desc_color) ?>><span class="careerfy-featured-rating-box"
                                                                style="width: <?php echo $rating ?>%"></span></div>
                    <small <?php echo ($desc_color) ?>><?php echo $promo_rating_desc ?></small>
                </li>
            </ul>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }


}

return new JobSearch_Careerfy_App_Promo();
