<?php
/*
  Class : Packages
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Packages
{

// hook things up
    public function __construct()
    {

        $this->load_files();
        add_action('admin_enqueue_scripts', array($this, 'admin_style_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_style_scripts'));
        add_action('add_meta_boxes', array($this, 'packages_meta_box'));
        //
        add_action('save_post', array($this, 'update_package_product_meta'), 10, 1);
    }

    private function load_files()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'packages/include/package-post-type.php';
        include plugin_dir_path(dirname(__FILE__)) . 'packages/include/custom-fields.php';
        include plugin_dir_path(dirname(__FILE__)) . 'packages/include/vc-shortcodes.php';
        include plugin_dir_path(dirname(__FILE__)) . 'packages/shortcodes/packages-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'packages/include/package-functions.php';
    }

    public function admin_style_scripts()
    {
        wp_enqueue_script('jobsearch-packages-scripts', plugin_dir_url(dirname(__FILE__)) . 'packages/js/packages-admin.js', array(), '', true);
    }

    public function front_style_scripts()
    {
        wp_register_script('jobsearch-packages-scripts', plugin_dir_url(dirname(__FILE__)) . 'packages/js/packages.js', array(), '', true);
        $jobsearch_plugin_arr = array(
            'plugin_url' => jobsearch_plugin_get_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
        );
        wp_localize_script('jobsearch-packages-scripts', 'jobsearch_packages_vars', $jobsearch_plugin_arr);
    }

    public function packages_meta_box()
    {
        add_meta_box('jobsearch-package-options', esc_html__('Package Options', 'wp-jobsearch'), array($this, 'package_options_box'), 'package', 'normal');
    }

    public function package_options_box()
    {
        global $post, $wpdb, $jobsearch_form_fields, $jobsearch_plugin_options, $Jobsearch_Package_Custom_Fields;

        $_post_id = $post->ID;

        wp_enqueue_script('jobsearch-selectize');

        $cand_pkg_base_profile = isset($jobsearch_plugin_options['cand_pkg_base_profile']) ? $jobsearch_plugin_options['cand_pkg_base_profile'] : '';
        $emp_pkg_base_profile = isset($jobsearch_plugin_options['emp_pkg_base_profile']) ? $jobsearch_plugin_options['emp_pkg_base_profile'] : '';

        $package_type = get_post_meta($post->ID, 'jobsearch_field_package_type', true);
        $charges_type = get_post_meta($post->ID, 'jobsearch_field_charges_type', true);

        $unlimitd_exp = get_post_meta($post->ID, 'jobsearch_field_unlimited_pkg', true);
        ?>
        <div class="jobsearch-post-settings" style="min-height: 500px;">
            <div class="pckges-typeinfo-con">
                <h2><?php esc_html_e('Package Types Information', 'wp-jobsearch') ?></h2>
                <ul>
                    <li><strong><?php esc_html_e('Jobs Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for employers to post jobs.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Jobs Package with featured credits', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for employers to post jobs with featured credits.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Employer download CV\'s Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for employers to save a specific number of resumes.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('All in one Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for employers to post jobs, featured jobs and download CVs.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Single Featured Job credit', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for employers to post single featured job only.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Candidate Job Apply Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for candidates to apply a specific number of jobs.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Promote Profile', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for both employers/candidates to promote profile in top of the listings.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Urgent Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is for both employers/candidates to get urgent tag with his/her profile.', 'wp-jobsearch') ?>
                    </li>
                    <li><strong><?php esc_html_e('Candidate Profile Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is usefull for candidates profile limitations according to subscribed package.', 'wp-jobsearch') ?>
                    </li>
                    <li>
                        <strong>
                            <?php esc_html_e('Employer Profile Package', 'wp-jobsearch') ?>
                            :</strong> <?php esc_html_e('This package is usefull for employers profile limitations according to subscribed package.', 'wp-jobsearch') ?>
                    </li>
                    <?php echo do_action('jobsearch_pkg_admin_descriptions_after'); ?>
                </ul>
            </div>

            <div class="jobsearch-element-field to_unlimit_pexp"
                 style="display: <?php echo($package_type == 'cand_resume' ? 'none' : 'block') ?>">
                <div class="elem-label">
                    <label><?php esc_html_e('Recommended Package', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch-is-feature-pkg',
                        'name' => 'feature_pkg',
                        'options' => array(
                            'no' => esc_html__('No', 'wp-jobsearch'),
                            'yes' => esc_html__('Yes', 'wp-jobsearch'),
                        ),
                    );
                    $jobsearch_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Charges Type', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch-package-charges-type',
                        'name' => 'charges_type',
                        'ext_attr' => 'onchange="jobsearch_onchange_package_price_type(this.value)"',
                        'options' => array(
                            'paid' => esc_html__('Paid', 'wp-jobsearch'),
                            'free' => esc_html__('Free', 'wp-jobsearch'),
                        ),
                    );
                    $jobsearch_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-package-price-fields"
                 style="display: <?php echo($charges_type == 'free' ? 'none' : 'block') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Package Price', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch-package-price',
                            'name' => 'package_price',
                            'std' => '50',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Package Type', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $pckg_types_options = array(
                        'job' => esc_html__('Jobs Package', 'wp-jobsearch'),
                        'featured_jobs' => esc_html__('Jobs Package with featured credits', 'wp-jobsearch'),
                        'cv' => esc_html__('Employer download CV\'s Package', 'wp-jobsearch'),
                        'emp_allin_one' => esc_html__('All in one', 'wp-jobsearch'),
                        'feature_job' => esc_html__('Single Featured Job credit', 'wp-jobsearch'),
                        'candidate' => esc_html__('Candidate Job Apply Package', 'wp-jobsearch'),
                        'promote_profile' => esc_html__('Promote Profile', 'wp-jobsearch'),
                        'urgent_pkg' => esc_html__('Urgent Package', 'wp-jobsearch'),
                    );
                    $pckg_types_options['candidate_profile'] = esc_html__('Candidate Profile Package', 'wp-jobsearch');
                    $pckg_types_options['employer_profile'] = esc_html__('Employer Profile Package', 'wp-jobsearch');
                    $pckg_types_options = apply_filters('jobsearch_admin_change_package_types', $pckg_types_options);

                    $field_params = array(
                        'name' => 'package_type',
                        'ext_attr' => 'onchange="jobsearch_onchange_package_type(this.value)"',
                        'options' => $pckg_types_options,
                    );
                    $jobsearch_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field to_unlimit_pexp"
                 style="display: <?php echo($package_type == 'cand_resume' ? 'none' : 'block') ?>">
                <div class="elem-label">
                    <label><?php esc_html_e('Package Expiry Time', 'wp-jobsearch') ?></label>
                </div>
                <?php
                $unl_pkg_rand = rand(10000000, 99999999);
                $unlimtd_pkg_expiryval = get_post_meta($_post_id, 'jobsearch_field_unlimited_pkg', true);
                ?>
                <div class="elem-field">
                    <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                         class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_expiryval == 'on' ? 'limted-disabled' : '') ?>"
                         style="float: left; width: 70%;">
                        <div class="input-select-field input-f">
                            <?php
                            $field_params = array(
                                'name' => 'package_expiry_time',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div class="input-select-field select-f">
                            <?php
                            $field_params = array(
                                'name' => 'package_expiry_time_unit',
                                'options' => array(
                                    'days' => esc_html__('Days', 'wp-jobsearch'),
                                    'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                    'months' => esc_html__('Months', 'wp-jobsearch'),
                                    'years' => esc_html__('Years', 'wp-jobsearch'),
                                ),
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div style="float: right; width: 27%;">
                        <div class="unlimitd-chekbox">
                            <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                   data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_expiryval == 'on' ? 'checked' : '') ?>>
                            <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                            <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                   name="jobsearch_field_unlimited_pkg" value="<?php echo($unlimtd_pkg_expiryval) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php do_action('jobsearch_admin_package_meta_fields', $post->ID); ?>
            <div id="candidate_package_fields" class="candidate-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'candidate' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Applications', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numcappsval = get_post_meta($_post_id, 'jobsearch_field_unlimited_numcapps', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numcappsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'num_of_apps',
                                'std' => '50',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numcappsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_numcapps"
                                       value="<?php echo($unlimtd_pkg_numcappsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            ob_start();
            ?>
            <div id="job_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo(($package_type == '' || $package_type == 'job') ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numjobsval = get_post_meta($_post_id, 'jobsearch_field_unlimited_numjobs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numjobsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'num_of_jobs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numjobsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_numjobs"
                                       value="<?php echo($unlimtd_pkg_numjobsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                ob_start();
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Job Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_jobexpval = get_post_meta($_post_id, 'jobsearch_field_unlimited_jobsexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_jobexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'job_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'job_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_jobexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_jobsexp"
                                       value="<?php echo($unlimtd_pkg_jobexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $pkg_job_exp_field = ob_get_clean();
                echo apply_filters('jobsearch_pkgs_job_exp_meta_field', $pkg_job_exp_field);
                ?>
                <?php echo($Jobsearch_Package_Custom_Fields->init_fields('job_package')); ?>
            </div>
            <?php
            $job_meta_fields = ob_get_clean();
            echo apply_filters('jobsearch_pkg_admin_job_meta_fields', $job_meta_fields);
            ?>

            <?php echo apply_filters('jobsearch_pkg_admin_resume_meta_fields', $package_type, $_post_id) ?>

            <div id="featured_jobs_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'featured_jobs' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numfjobsval = get_post_meta($_post_id, 'jobsearch_field_unlimited_numfjobs', true);
                    ?>
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'num_of_fjobs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_numfjobs"
                                       value="<?php echo($unlimtd_pkg_numfjobsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Job Credits', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobscrval = get_post_meta($_post_id, 'jobsearch_field_unlimited_fjobscr', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'feat_job_credits',
                                'std' => '5',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_fjobscr"
                                       value="<?php echo($unlimtd_pkg_fjobscrval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                ob_start();
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Job Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobexpval = get_post_meta($_post_id, 'jobsearch_field_unlimited_fjobexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fjob_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fjob_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_fjobexp"
                                       value="<?php echo($unlimtd_pkg_fjobexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $pkg_job_exp_field = ob_get_clean();
                echo apply_filters('jobsearch_pkgs_fjobs_exp_meta_field', $pkg_job_exp_field);
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Credit Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fcredexpval = get_post_meta($_post_id, 'jobsearch_field_unlimited_fcredexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fcred_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fcred_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_fcredexp"
                                       value="<?php echo($unlimtd_pkg_fcredexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo($Jobsearch_Package_Custom_Fields->init_fields('featured_jobs_package')); ?>
            </div>
            <div id="emp_allin_one_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'emp_allin_one' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numfjobsval = get_post_meta($_post_id, 'jobsearch_field_unlim_allinjobs', true);
                    ?>
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'allin_num_jobs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_allinjobs"
                                       value="<?php echo($unlimtd_pkg_numfjobsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Job Credits', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobscrval = get_post_meta($_post_id, 'jobsearch_field_unlim_allinfjobs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'allin_num_fjobs',
                                'std' => '5',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_allinfjobs"
                                       value="<?php echo($unlimtd_pkg_fjobscrval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Job Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobexpval = get_post_meta($_post_id, 'jobsearch_field_unlim_allinjobexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'allinjob_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'allinjob_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_allinjobexp"
                                       value="<?php echo($unlimtd_pkg_fjobexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Credit Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fcredexpval = get_post_meta($_post_id, 'jobsearch_field_unlimited_fall_credexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fall_cred_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'fall_cred_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_fall_credexp"
                                       value="<?php echo($unlimtd_pkg_fcredexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of CV\'s', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numcvsval = get_post_meta($_post_id, 'jobsearch_field_unlim_allinnumcvs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'allin_num_cvs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_allinnumcvs"
                                       value="<?php echo($unlimtd_pkg_numcvsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Credit Consume on Resume View', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'allinview_consume_cvs',
                            'std' => '',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <?php echo($Jobsearch_Package_Custom_Fields->init_fields('emp_allin_one_package')); ?>
            </div>
            <div id="cv_package_fields" class="cv-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'cv' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of CV\'s', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numcvsval = get_post_meta($_post_id, 'jobsearch_field_unlimited_numcvs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'num_of_cvs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_numcvs"
                                       value="<?php echo($unlimtd_pkg_numcvsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Credit Consume on Resume View', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'onview_consume_cvs',
                            'std' => '',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $emp_cvpbase_restrictions = isset($jobsearch_plugin_options['emp_cv_pkgbase_restrictions']) ? $jobsearch_plugin_options['emp_cv_pkgbase_restrictions'] : '';
                if ($emp_cvpbase_restrictions == 'on') {
                    ?>
                    <div class="jobsearch-elem-heading">
                        <h2><?php esc_html_e('Candidate Profile Restriction Settings', 'wp-jobsearch') ?></h2>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Profile Fields', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'db_name' => 'empcv_pbase_profile',
                                'cus_name' => 'jobsearch_field_empcv_pbase_profile[]',
                                'options' => array(
                                    'display_name' => __('Display Name', 'wp-jobsearch'),
                                    'profile_img' => __('Profile Picture', 'wp-jobsearch'),
                                    'cover_img' => __('Cover Photo', 'wp-jobsearch'),
                                    'date_of_birth' => __('Date of Birth', 'wp-jobsearch'),
                                    'email' => __('Email', 'wp-jobsearch'),
                                    'phone' => __('Phone', 'wp-jobsearch'),
                                    'sector' => __('Sector', 'wp-jobsearch'),
                                    'job_title' => __('Job Title', 'wp-jobsearch'),
                                    'salary' => __('Salary', 'wp-jobsearch'),
                                    'about_desc' => __('Description', 'wp-jobsearch'),
                                ),
                                'classes' => 'packge-selectize',
                                'std' => array('display_name', 'profile_img', 'cover_img', 'sector')
                            );
                            $jobsearch_form_fields->multi_select_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Social Links', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_socialicons',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Custom Fields', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_customfields',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Location/Address', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_address',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Contact Form', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_contactfrm',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Skills', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_skills',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Education', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_edu',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Experience', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_exp',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Portfolio', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_port',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Expertise', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_expertise',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Honors & Awards', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'name' => 'empcv_pbase_awards',
                            );
                            $jobsearch_form_fields->checkbox_field($field_params);
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <?php echo($Jobsearch_Package_Custom_Fields->init_fields('cv_package')); ?>
            </div>

            <div id="candidate_profile_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'candidate_profile' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Profile Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'cand_pbase_profile',
                            'cus_name' => 'jobsearch_field_cand_pbase_profile[]',
                            'options' => array(
                                'cover_img' => __('Cover Photo', 'wp-jobsearch'),
                                'profile_url' => __('Profile URL', 'wp-jobsearch'),
                                'public_view' => __('Profile for Public View', 'wp-jobsearch'),
                                'date_of_birth' => __('Date of Birth', 'wp-jobsearch'),
                                'phone' => __('Phone', 'wp-jobsearch'),
                                'sector' => __('Sector', 'wp-jobsearch'),
                                'job_title' => __('Job Title', 'wp-jobsearch'),
                                'salary' => __('Salary', 'wp-jobsearch'),
                                'about_desc' => __('Description', 'wp-jobsearch'),
                            ),
                            'classes' => 'packge-selectize',
                            'std' => array('cover_img', 'public_view', 'sector')
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';
                $cand_pkgbase_social_arr = array(
                    'facebook' => __('Facebook', 'wp-jobsearch'),
                    'twitter' => __('Twitter', 'wp-jobsearch'),
                    'google_plus' => __('Google Plus', 'wp-jobsearch'),
                    'linkedin' => __('Linkedin', 'wp-jobsearch'),
                    'dribbble' => __('Dribbble', 'wp-jobsearch'),
                );
                if (!empty($candidate_social_mlinks)) {
                    if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                        $field_counter = 0;
                        foreach ($candidate_social_mlinks['title'] as $cand_social_mlink) {
                            $cand_pkgbase_social_arr['dynm_social' . $field_counter] = $cand_social_mlink;
                            $field_counter++;
                        }
                    }
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Social Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'cand_pbase_social',
                            'cus_name' => 'jobsearch_field_cand_pbase_social[]',
                            'options' => $cand_pkgbase_social_arr,
                            'classes' => 'packge-selectize',
                            'std' => ''
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $cand_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
                if (is_array($cand_custom_fields_saved_data) && sizeof($cand_custom_fields_saved_data) > 0) {
                    $cand_pkgbase_cusfileds_arr = array();
                    foreach ($cand_custom_fields_saved_data as $cand_cus_field_key => $cand_cus_field_kdata) {
                        $cusfield_label = isset($cand_cus_field_kdata['label']) ? $cand_cus_field_kdata['label'] : '';
                        $cusfield_name = isset($cand_cus_field_kdata['name']) ? $cand_cus_field_kdata['name'] : '';
                        if ($cusfield_label != '' && $cusfield_name != '') {
                            $cand_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                        }
                    }
                    //
                    ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Custom Fields', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'db_name' => 'cand_pbase_cusfields',
                                'cus_name' => 'jobsearch_field_cand_pbase_cusfields[]',
                                'options' => $cand_pkgbase_cusfileds_arr,
                                'classes' => 'packge-selectize',
                                'std' => ''
                            );
                            $jobsearch_form_fields->multi_select_field($field_params);
                            ?>
                        </div>
                    </div>
                    <?php
                }

                //
                $cand_pkgbase_dashsecs_arr = apply_filters('jobsearch_cand_dash_menu_in_opts', array(
                    'my_profile' => __('My Profile', 'wp-jobsearch'),
                    'my_resume' => __('My Resume', 'wp-jobsearch'),
                    'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
                    'cv_manager' => __('CV Manager', 'wp-jobsearch'),
                    'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
                    'packages' => __('Packages', 'wp-jobsearch'),
                    'transactions' => __('Transactions', 'wp-jobsearch'),
                    'my_emails' => __('My Emails', 'wp-jobsearch'),
                    'following' => __('Following', 'wp-jobsearch'),
                    'change_password' => __('Change Password', 'wp-jobsearch'),
                ));
                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
                $post_ids_query .= " ON postmeta.post_id = posts.ID";
                $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
                $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='cand') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

                $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

                if (!empty($cusmenu_post_ids)) {
                    foreach ($cusmenu_post_ids as $cust_dashpage) {
                        $the_page = get_post($cust_dashpage);
                        if (isset($the_page->ID)) {
                            $cand_pkgbase_dashsecs_arr[$the_page->post_name] = $the_page->post_title;
                        }
                    }
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Dashboard Sections', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'cand_pbase_dashtabs',
                            'cus_name' => 'jobsearch_field_cand_pbase_dashtabs[]',
                            'options' => $cand_pkgbase_dashsecs_arr,
                            'classes' => 'packge-selectize',
                            'std' => ''
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Candidate Statistics', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_stats',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Location Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_location',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_coverltr',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Education', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_resmedu',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Experience', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_resmexp',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Portfolio', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_resmport',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Expertise', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_resmskills',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Honors & Awards', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'cand_pbase_resmawards',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Applications', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numappsval = get_post_meta($_post_id, 'jobsearch_field_unlim_candprofnumapps', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numappsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'candprof_num_apps',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numappsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_candprofnumapps"
                                       value="<?php echo($unlimtd_pkg_numappsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Promote Profile', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candprof_promote_profile',
                            'std' => '',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Promote Profile Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_promote_exp = get_post_meta($_post_id, 'jobsearch_field_unlimited_candprof_promote_exp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_promote_exp == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'candprof_promote_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'candprof_promote_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_promote_exp == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_candprof_promote_exp"
                                       value="<?php echo($unlimtd_promote_exp) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="employer_profile_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'employer_profile' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Profile Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'emp_pbase_profile',
                            'cus_name' => 'jobsearch_field_emp_pbase_profile[]',
                            'options' => array(
                                'jobs_cover_img' => __('Jobs Cover Photo', 'wp-jobsearch'),
                                'profile_url' => __('Profile URL', 'wp-jobsearch'),
                                'public_view' => __('Profile Public View', 'wp-jobsearch'),
                                'phone' => __('Phone', 'wp-jobsearch'),
                                'website' => __('Website', 'wp-jobsearch'),
                                'sector' => __('Sector', 'wp-jobsearch'),
                                'founded_date' => __('Founded Date', 'wp-jobsearch'),
                                'about_company' => __('About the Company', 'wp-jobsearch'),
                            ),
                            'classes' => 'packge-selectize',
                            'std' => array('jobs_cover_img', 'public_view', 'sector')
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $employer_social_mlinks = isset($jobsearch_plugin_options['employer_social_mlinks']) ? $jobsearch_plugin_options['employer_social_mlinks'] : '';
                $emp_pkgbase_social_arr = array(
                    'facebook' => __('Facebook', 'wp-jobsearch'),
                    'twitter' => __('Twitter', 'wp-jobsearch'),
                    'google_plus' => __('Google Plus', 'wp-jobsearch'),
                    'linkedin' => __('Linkedin', 'wp-jobsearch'),
                    'dribbble' => __('Dribbble', 'wp-jobsearch'),
                );
                if (!empty($employer_social_mlinks)) {
                    if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                        $field_counter = 0;
                        foreach ($employer_social_mlinks['title'] as $emp_social_mlink) {
                            $emp_pkgbase_social_arr['dynm_social' . $field_counter] = $emp_social_mlink;
                            $field_counter++;
                        }
                    }
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Social Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'emp_pbase_social',
                            'cus_name' => 'jobsearch_field_emp_pbase_social[]',
                            'options' => $emp_pkgbase_social_arr,
                            'classes' => 'packge-selectize',
                            'std' => ''
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $emp_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
                if (is_array($emp_custom_fields_saved_data) && sizeof($emp_custom_fields_saved_data) > 0) {
                    $emp_pkgbase_cusfileds_arr = array();
                    foreach ($emp_custom_fields_saved_data as $emp_cus_field_key => $emp_cus_field_kdata) {
                        $cusfield_label = isset($emp_cus_field_kdata['label']) ? $emp_cus_field_kdata['label'] : '';
                        $cusfield_name = isset($emp_cus_field_kdata['name']) ? $emp_cus_field_kdata['name'] : '';
                        if ($cusfield_label != '' && $cusfield_name != '') {
                            $emp_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                        }
                    }
                    //
                    ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Custom Fields', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'db_name' => 'emp_pbase_cusfields',
                                'cus_name' => 'jobsearch_field_emp_pbase_cusfields[]',
                                'options' => $emp_pkgbase_cusfileds_arr,
                                'classes' => 'packge-selectize',
                                'std' => ''
                            );
                            $jobsearch_form_fields->multi_select_field($field_params);
                            ?>
                        </div>
                    </div>
                    <?php
                }

                //
                $emp_pkgbase_dashsecs_arr = apply_filters('jobsearch_emp_dash_menu_in_opts', array(
                    'company_profile' => __('Company Profile', 'wp-jobsearch'),
                    'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
                    'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
                    'all_applicants' => __('All Applicants', 'wp-jobsearch'),
                    'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
                    'packages' => __('Packages', 'wp-jobsearch'),
                    'transactions' => __('Transactions', 'wp-jobsearch'),
                    'my_emails' => __('My Emails', 'wp-jobsearch'),
                    'followers' => __('Followers', 'wp-jobsearch'),
                    'change_password' => __('Change Password', 'wp-jobsearch'),
                ));
                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
                $post_ids_query .= " ON postmeta.post_id = posts.ID";
                $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
                $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='emp') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

                $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

                if (!empty($cusmenu_post_ids)) {
                    foreach ($cusmenu_post_ids as $cust_dashpage) {
                        $the_page = get_post($cust_dashpage);
                        if (isset($the_page->ID)) {
                            $emp_pkgbase_dashsecs_arr[$the_page->post_name] = $the_page->post_title;
                        }
                    }
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Dashboard Sections', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'db_name' => 'emp_pbase_dashtabs',
                            'cus_name' => 'jobsearch_field_emp_pbase_dashtabs[]',
                            'options' => $emp_pkgbase_dashsecs_arr,
                            'classes' => 'packge-selectize',
                            'std' => ''
                        );
                        $jobsearch_form_fields->multi_select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Employer Statistics', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_stats',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Location Fields', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_location',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Account Members', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_accmembs',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Employer Team', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_team',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Employer Awards', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_award',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Affiliations', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_affiliation',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Company Photos/Videos', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emp_pbase_gphotos',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numfjobsval = get_post_meta($_post_id, 'jobsearch_field_unlim_emprofjobs', true);
                    ?>
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of Jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'emprof_num_jobs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numfjobsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_emprofjobs"
                                       value="<?php echo($unlimtd_pkg_numfjobsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Job Credits', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobscrval = get_post_meta($_post_id, 'jobsearch_field_unlim_emproffjobs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'emprof_num_fjobs',
                                'std' => '5',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobscrval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_emproffjobs"
                                       value="<?php echo($unlimtd_pkg_fjobscrval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Job Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fjobexpval = get_post_meta($_post_id, 'jobsearch_field_unlim_emprofjobexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprofjob_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprofjob_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fjobexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_emprofjobexp"
                                       value="<?php echo($unlimtd_pkg_fjobexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Featured Credit Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_fcredexpval = get_post_meta($_post_id, 'jobsearch_field_unlimited_emprof_fcredexp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprof_fcred_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprof_fcred_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_fcredexpval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_emprof_fcredexp"
                                       value="<?php echo($unlimtd_pkg_fcredexpval) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of CV\'s', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_pkg_numcvsval = get_post_meta($_post_id, 'jobsearch_field_unlim_emprofnumcvs', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <?php
                            $field_params = array(
                                'name' => 'emprof_num_cvs',
                                'std' => '10',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_pkg_numcvsval == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlim_emprofnumcvs"
                                       value="<?php echo($unlimtd_pkg_numcvsval) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Credit Consume on Resume View', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emprofview_consume_cvs',
                            'std' => '',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Promote Profile', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'emprof_promote_profile',
                            'std' => '',
                        );
                        $jobsearch_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Promote Profile Expiry Time', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    $unl_pkg_rand = rand(10000000, 99999999);
                    $unlimtd_promote_exp = get_post_meta($_post_id, 'jobsearch_field_unlimited_emprof_promote_exp', true);
                    ?>
                    <div class="elem-field">
                        <div id="limted-pkgexp-con-<?php echo($unl_pkg_rand) ?>"
                             class="limited-expiry-pkkgcon <?php echo($unlimtd_promote_exp == 'on' ? 'limted-disabled' : '') ?>"
                             style="float: left; width: 70%;">
                            <div class="input-select-field input-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprof_promote_expiry_time',
                                    'std' => '7',
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                            <div class="input-select-field select-f">
                                <?php
                                $field_params = array(
                                    'name' => 'emprof_promote_expiry_time_unit',
                                    'options' => array(
                                        'days' => esc_html__('Days', 'wp-jobsearch'),
                                        'weeks' => esc_html__('Weeks', 'wp-jobsearch'),
                                        'months' => esc_html__('Months', 'wp-jobsearch'),
                                        'years' => esc_html__('Years', 'wp-jobsearch'),
                                    ),
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </div>
                        <div style="float: right; width: 27%;">
                            <div class="unlimitd-chekbox">
                                <input type="checkbox" id="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"
                                       data-id="<?php echo($unl_pkg_rand) ?>" <?php echo($unlimtd_promote_exp == 'on' ? 'checked' : '') ?>>
                                <label for="unli-pkgexp-<?php echo($unl_pkg_rand) ?>"><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                                <input type="hidden" id="unli_pkgexp_<?php echo($unl_pkg_rand) ?>"
                                       name="jobsearch_field_unlimited_emprof_promote_exp"
                                       value="<?php echo($unlimtd_promote_exp) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="feature_job_package_fields" class="feature-job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'feature_job' ? 'block' : 'none') ?>;">
                <?php echo force_balance_tags($Jobsearch_Package_Custom_Fields->init_fields('feature_job_package')); ?>
            </div>
            <div class="pckg-extra-fields-con">
                <?php
                $pkg_exfield_title = get_post_meta($post->ID, 'jobsearch_field_package_exfield_title', true);
                $pkg_exfield_val = get_post_meta($post->ID, 'jobsearch_field_package_exfield_val', true);
                $pkg_exfield_status = get_post_meta($post->ID, 'jobsearch_field_package_exfield_status', true);
                if (!empty($pkg_exfield_title)) {
                    $_exf_counter = 0;
                    foreach ($pkg_exfield_title as $_exfield_title) {
                        $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                        $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                        ?>
                        <div class="pckg-extra-field-item">
                            <div class="field-heder">
                                <a class="drag-point"><i class="dashicons dashicons-image-flip-vertical"></i></a>
                                <h2><?php esc_html_e('Extra Field', 'wp-jobsearch') ?></h2>
                            </div>
                            <div class="field-remove-con">
                                <a href="javascript:void(0);" class="field-remove-btn"><i
                                            class="dashicons dashicons-no-alt"></i></a>
                            </div>
                            <div class="jobsearch-element-field">
                                <div class="elem-label">
                                    <label><?php esc_html_e('Field Text', 'wp-jobsearch') ?></label>
                                </div>
                                <div class="elem-field">
                                    <input type="text" name="jobsearch_field_package_exfield_title[]"
                                           value="<?php echo($_exfield_title) ?>">
                                </div>
                            </div>
                            <div class="jobsearch-element-field">
                                <div class="elem-label">
                                    <label><?php esc_html_e('Field Status', 'wp-jobsearch') ?></label>
                                </div>
                                <div class="elem-field">
                                    <select name="jobsearch_field_package_exfield_status[]">
                                        <option value="active"<?php echo($_exfield_status == 'active' ? ' selected="selected"' : '') ?>><?php esc_html_e('Active', 'wp-jobsearch') ?></option>
                                        <option value="inactive"<?php echo($_exfield_status == 'inactive' ? ' selected="selected"' : '') ?>><?php esc_html_e('Inactive', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                        $_exf_counter++;
                    }
                }
                ?>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">&nbsp;</div>
                <div class="elem-field">
                    <a href="javascript:void(0);"
                       class="button button-primary button-large add-pkg-more-fields"><?php esc_html_e('Add Extra Info', 'wp-jobsearch') ?></a>
                </div>
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery('.packge-selectize').selectize({
                        //allowEmptyOption: true,
                        plugins: ['remove_button'],
                    });
                });
                jQuery(document).on('click', '.unlimitd-chekbox input[type="checkbox"]', function () {
                    var _this = jQuery(this);
                    var _this_id = _this.attr('data-id');
                    if (_this.is(":checked")) {
                        jQuery('#limted-pkgexp-con-' + _this_id).addClass('limted-disabled');
                        jQuery('#unli_pkgexp_' + _this_id).val('on');
                    } else {
                        jQuery('#limted-pkgexp-con-' + _this_id).removeClass('limted-disabled');
                        jQuery('#unli_pkgexp_' + _this_id).val('');
                    }
                });
            </script>
        </div>
        <?php
    }

    public function update_package_product_meta($post_id = '')
    {
        global $post;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        if (!class_exists('WooCommerce')) {
            return false;
        }

        if (!isset($_POST['post_title'])) {
            return false;
        }

        //
        if (!isset($_POST['jobsearch_field_package_exfield_title'])) {
            update_post_meta($post_id, 'jobsearch_field_package_exfield_title', '');
            update_post_meta($post_id, 'jobsearch_field_package_exfield_val', '');
        }

        //
        if (isset($_POST['jobsearch_field_charges_type']) && $_POST['jobsearch_field_charges_type'] == 'free' && get_post_type($post_id) == 'package') {
            $package_product = get_post_meta($post_id, 'jobsearch_package_product', true);

            $package_product_obj = $package_product != '' ? get_page_by_path($package_product, 'OBJECT', 'product') : '';

            if ($package_product != '' && is_object($package_product_obj)) {
                $product_id = $package_product_obj->ID;
                wp_delete_post($product_id, true);

                // update package product
                update_post_meta($post_id, 'jobsearch_package_product', '');
            }
        }

        if (get_post_type($post_id) == 'package') {
            $package_price = isset($_POST['jobsearch_field_package_price']) ? $_POST['jobsearch_field_package_price'] : '';
            $package_title = isset($_POST['post_title']) ? $_POST['post_title'] : '';

            $package_charges_type = isset($_POST['jobsearch_field_charges_type']) ? $_POST['jobsearch_field_charges_type'] : '';
            //
            $package_obj = get_post($post_id);
            $package_name = $package_obj->post_name;

            $package_product = get_post_meta($post_id, 'jobsearch_package_product', true);

            $package_product_obj = $package_product != '' ? get_page_by_path($package_product, 'OBJECT', 'product') : '';

            if ($package_product != '' && is_object($package_product_obj)) {
                $product_id = $package_product_obj->ID;

                // Product Title
                $prod_post = array(
                    'ID' => $product_id,
                    'post_title' => wp_strip_all_tags($package_title),
                    'post_content' => '',
                );
                wp_update_post($prod_post);

                $_product = wc_get_product($product_id);
                if ($_product) {
                    $_product->set_catalog_visibility('hidden');
                    $_product->save();
                }
            } else {
                $post_args = array(
                    'post_title' => wp_strip_all_tags($package_title),
                    'post_content' => '',
                    'post_status' => "publish",
                    'post_type' => "product",
                );

                $product_id = wp_insert_post($post_args);

                $_product = wc_get_product($product_id);
                if ($_product) {
                    $_product->set_catalog_visibility('hidden');
                    $_product->save();
                }

                wp_set_object_terms($product_id, 'simple', 'product_type');

                update_post_meta($product_id, '_visibility', 'visible');
                update_post_meta($product_id, '_stock_status', 'instock');
                update_post_meta($product_id, 'total_sales', '0');
            }

            $prod_obj = get_post($product_id);

            // update package product
            update_post_meta($post_id, 'jobsearch_package_product', $prod_obj->post_name);

            // update product attach type -> package
            update_post_meta($product_id, 'jobsearch_attach_with', 'package');
            update_post_meta($product_id, 'jobsearch_attach_package', $package_name);

            // Price
            if ($package_charges_type != 'paid') {
                $package_price = 0;
            }
            if ($package_price > 0) {
                $price_amount = $package_price;
            } else {
                $price_amount = '0';
            }
            $price_amount = (float)$price_amount;
            update_post_meta($product_id, '_regular_price', $price_amount);
            update_post_meta($product_id, '_price', $price_amount);
        }
    }

}

// class Jobsearch_Packages 
global $Jobsearch_Packages_obj;
$Jobsearch_Packages_obj = new Jobsearch_Packages();
