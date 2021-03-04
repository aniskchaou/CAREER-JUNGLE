<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Jobsearch\Candidate_Profile_Restriction;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidatePortfolio extends Widget_Base {

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'single-candidate-portfolio';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Single Candidate Portfolio', 'wp-jobsearch');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'fa fa-link';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['jobsearch-cand-single'];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function _register_controls() {
        
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $inopt_resm_portfolio = isset($jobsearch_plugin_options['cand_resm_portfolio']) ? $jobsearch_plugin_options['cand_resm_portfolio'] : '';

        ob_start();
        if (!$cand_profile_restrict::cand_field_is_locked('port_defields', 'detail_page')) {
            ob_start();

            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
            $exfield_portfolio_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
            $exfield_portfolio_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                ?>
                <div class="jobsearch-candidate-title"><h2><i
                            class="jobsearch-icon jobsearch-briefcase"></i> <?php esc_html_e('Portfolio', 'wp-jobsearch') ?>
                    </h2></div>
                <div class="jobsearch-gallery candidate_portfolio">
                    <ul class="jobsearch-row grid">
                        <?php
                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $portfolio_img = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $portfolio_url = isset($exfield_portfolio_url[$exfield_counter]) ? $exfield_portfolio_url[$exfield_counter] : '';
                            $portfolio_vurl = isset($exfield_portfolio_vurl[$exfield_counter]) ? $exfield_portfolio_vurl[$exfield_counter] : '';

                            if ($portfolio_vurl != '') {
                                if (strpos($portfolio_vurl, 'watch?v=') !== false) {
                                    $portfolio_vurl = str_replace('watch?v=', 'embed/', $portfolio_vurl);
                                }

                                if (strpos($portfolio_vurl, '?') !== false) {
                                    $portfolio_vurl .= '&autoplay=1';
                                } else {
                                    $portfolio_vurl .= '?autoplay=1';
                                }
                            }

                            $port_thumb_img = jobsearch_get_cand_portimg_url($candidate_id, $portfolio_img, 'large');
                            ?>
                            <li class="grid-item <?php echo($exfield_counter == 1 ? 'jobsearch-column-6' : 'jobsearch-column-3') ?>">
                                <figure>
                                    <span class="grid-item-thumb"><small
                                            style="background-image: url('<?php echo($port_thumb_img) ?>');"></small></span>
                                    <figcaption>
                                        <div class="img-icons">
                                            <a href="<?php echo($portfolio_vurl != '' ? $portfolio_vurl : $port_thumb_img) ?>"
                                               class="<?php echo($portfolio_vurl != '' ? 'fancybox-video' : 'fancybox-galimg') ?>"
                                               title="<?php echo($exfield) ?>" <?php echo($portfolio_vurl != '' ? 'data-fancybox-type="iframe"' : '') ?>
                                               data-fancybox-group="group"><i
                                                    class="<?php echo($portfolio_vurl != '' ? 'fa fa-play' : 'fa fa-image') ?>"></i></a>
                                                <?php if ($portfolio_url != '') { ?>
                                                <a href="<?php echo($portfolio_url) ?>"
                                                   target="_blank"><i class="fa fa-chain"></i></a>
                                                <?php } ?>
                                        </div>
                                    </figcaption>
                                </figure>
                            </li>
                            <?php
                            $exfield_counter++;
                        }
                        ?>
                    </ul>
                </div>

                <?php
            }

            $ports_html = ob_get_clean();

            if ($inopt_resm_portfolio != 'off') {
                echo apply_filters('jobsearch_candidate_detail_portsfolio_html', $ports_html, $candidate_id);
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
