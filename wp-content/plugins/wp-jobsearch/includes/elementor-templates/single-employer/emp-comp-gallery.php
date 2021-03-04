<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpCompGallery extends Widget_Base
{

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'single-emp-com-gallery';
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
    public function get_title()
    {
        return __('Single Employer Company Gallery', 'wp-jobsearch');
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
    public function get_icon()
    {
        return 'fa fa-image';
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
    public function get_categories()
    {
        return ['jobsearch-emp-single'];
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
    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section', [
                'label' => __('Employer Company Gallery Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render_overlay_header()
    { ?>
        <div class="jobsearch-emp">
        <?php
    }

    protected function render_overlay_footer()
    { ?>
        </div>
        <?php
    }

    private function render_post()
    {
        global $post;

        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;
        $company_gal_imgs = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', true);
        $company_gal_videos = get_post_meta($employer_id, 'jobsearch_field_company_gallery_videos', true);
        $company_gal_descs = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs_description', true);
        $company_gal_titles = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs_title', true);

        if (!empty($company_gal_imgs)) {
            $_gal_img_counter = 0;

            foreach ($company_gal_imgs as $company_gal_img) {

                if ($company_gal_img != '' && !is_numeric($company_gal_img)) {
                    $company_gal_img = jobsearch_get_attachment_id_from_url($company_gal_img);
                }


                if ($company_gal_img > 0) {
                    $_gal_img_counter++;
                }
            }
            ob_start();
            if ($_gal_img_counter > 0) { ?>
                <div class="jobsearch-employer-wrap-section">
                    <div class="jobsearch-gallery jobsearch-simple-gallery">
                        <ul class="jobsearch-row grid">
                            <?php
                            $profile_gal_counter = 1;
                            $_gal_img_counter = 0;
                            foreach ($company_gal_imgs as $company_gal_img) {
                                if ($company_gal_img != '' && absint($company_gal_img) <= 0) {
                                    $company_gal_img = jobsearch_get_attachment_id_from_url($company_gal_img);
                                }
                                $gal_thumbnail_image = wp_get_attachment_image_src($company_gal_img, 'large');
                                $gal_thumb_image_src = isset($gal_thumbnail_image[0]) && esc_url($gal_thumbnail_image[0]) != '' ? $gal_thumbnail_image[0] : '';

                                $gal_img_title = isset($company_gal_titles[$_gal_img_counter]) && ($company_gal_titles[$_gal_img_counter]) != '' ? $company_gal_titles[$_gal_img_counter] : '';
                                $gal_img_desc = isset($company_gal_descs[$_gal_img_counter]) && ($company_gal_descs[$_gal_img_counter]) != '' ? $company_gal_descs[$_gal_img_counter] : '';

                                $gal_video_url = isset($company_gal_videos[$_gal_img_counter]) && ($company_gal_videos[$_gal_img_counter]) != '' ? $company_gal_videos[$_gal_img_counter] : '';
                                if ($gal_video_url != '') {

                                    if (strpos($gal_video_url, 'watch?v=') !== false) {
                                        $gal_video_url = str_replace('watch?v=', 'embed/', $gal_video_url);
                                    }

                                    if (strpos($gal_video_url, '?') !== false) {
                                        $gal_video_url .= '&autoplay=1';
                                    } else {
                                        $gal_video_url .= '?autoplay=1';
                                    }
                                }

                                $gal_full_image = wp_get_attachment_image_src($company_gal_img, 'full');
                                $gal_full_image_src = isset($gal_full_image[0]) && esc_url($gal_full_image[0]) != '' ? $gal_full_image[0] : '';

                                if ($company_gal_img > 0) { ?>
                                    <li class="grid-item <?php echo($profile_gal_counter == 2 ? 'jobsearch-column-6' : 'jobsearch-column-3') ?>">
                                        <figure>
                                         <span class="grid-item-thumb">
                                             <small style="background-image: url('<?php echo($gal_thumb_image_src) ?>');"></small>
                                         </span>
                                            <figcaption>
                                                <div class="img-icons">
                                                    <a href="<?php echo($gal_video_url != '' ? $gal_video_url : $gal_full_image_src) ?>"
                                                       title="<?php echo($gal_img_title) ?>"
                                                       data-caption="<?php echo($gal_img_desc) ?>"
                                                       class="<?php echo($gal_video_url != '' ? 'fancybox-video' : 'fancybox-galimg') ?>" <?php echo($gal_video_url != '' ? 'data-fancybox-type="iframe"' : '') ?>
                                                       data-fancybox-group="group">
                                                        <i class="<?php echo($gal_video_url != '' ? 'fa fa-play' : 'fa fa-image') ?>"></i>
                                                    </a>
                                                </div>
                                            </figcaption>
                                        </figure>
                                    </li>
                                    <?php
                                }
                                $profile_gal_counter++;
                                $_gal_img_counter++;
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function render()
    {
        $this->render_overlay_header();
        $this->render_post();
        $this->render_overlay_footer();
    }

    protected function _content_template()
    {

    }

}
