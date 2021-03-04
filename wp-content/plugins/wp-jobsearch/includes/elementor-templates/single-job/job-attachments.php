<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobAttachments extends Widget_Base {

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
        return 'single-job-attachments';
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
        return __('Single Job Attachments', 'wp-jobsearch');
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
        return ['jobsearch-job-single'];
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
        $this->start_controls_section(
                'content_section', [
            'label' => __('Job Attachments Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;

        ob_start();
        $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
        if ($job_attachments_switch == 'on') {
            $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
            if (!empty($all_attach_files)) {?>
                <div class="jobsearch-content-title">
                    <h2><?php esc_html_e('Attached Files', 'wp-jobsearch') ?></h2>
                </div>
                <div class="jobsearch-file-attach-sec">
                    <ul class="jobsearch-row">
                        <?php
                        foreach ($all_attach_files as $_attach_file) {
                            $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                            $_attach_post = get_post($_attach_id);
                            $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                            $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                            $attach_name = basename($_attach_guide);

                            $file_icon = 'fa fa-file-text-o';
                            if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                $file_icon = 'fa fa-file-image-o';
                            } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                $file_icon = 'fa fa-file-word-o';
                            } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                $file_icon = 'fa fa-file-excel-o';
                            } else if ($_attach_mime == 'application/pdf') {
                                $file_icon = 'fa fa-file-pdf-o';
                            }
                            ?>
                            <li class="jobsearch-column-4">
                                <div class="file-container">
                                    <a href="<?php echo($_attach_file) ?>"
                                       oncontextmenu="javascript: return false;"
                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {
                                               return false
                                           }
                                           ;"
                                       download="<?php echo($attach_name) ?>"
                                       class="file-download-icon"><i
                                                class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                    </a>
                                    <a href="<?php echo($_attach_file) ?>"
                                       oncontextmenu="javascript: return false;"
                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) { return false };"
                                       download="<?php echo($attach_name) ?>"
                                       class="file-download-btn"><?php esc_html_e('Download', 'wp-jobsearch') ?>
                                        <i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
