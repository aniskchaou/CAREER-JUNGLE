<?php
if (!defined('ABSPATH')) {
    die;
}
global $jobsearch_gdapi_allocation;
if (!class_exists('jobsearch_allocation_settings_handle')) {

    class jobsearch_allocation_settings_handle
    {
        public $auto_load_files;
        // hook things up
        public function __construct()
        {
            $this->auto_load_files = false;
            //header("Access-Control-Allow-Origin: *");
            add_action('init', array($this, 'load_locfiles_init'), 1);
            $this->save_locsettings();
            add_action('admin_menu', array($this, 'jobsearch_loc_settings_create_menu'));
            add_action('admin_footer', array($this, 'load_locations_js'));
            add_action('wp_footer', array($this, 'load_locations_js'), 100);
            add_action('wp_head', array($this, 'global_variables_init'), 1);
            add_action('admin_head', array($this, 'global_variables_init'), 1);
            add_action('admin_enqueue_scripts', array($this, 'load_locations_script'));
            add_action('wp_ajax_jobsearch_locations_download', array($this, 'jobsearch_locations_download_callback'), 1);
            add_action('wp_ajax_jobsearch_check_state_dir', array($this, 'jobsearch_check_state_dir_callback'));
            add_filter('jobsearch_form_fields_value', array($this, 'jobsearch_form_fields_value_callback'), 10, 2);
        }

        public function load_locfiles_init()
        {
            if (defined('WP_JOBSEARCH_VERSION')) {
                $jobsearch_version = WP_JOBSEARCH_VERSION;
                if ($jobsearch_version <= '1.3.5') {
                    $this->auto_load_files = true;
                }
            }
        }

        public function global_variables_init()
        { ?>
            <script type="text/javascript">
                var jobsearch_sloc_country = '', jobsearch_sloc_state = '', jobsearch_sloc_city = '',
                    jobsearch_sloc_type = '', jobsearch_is_admin = '', jobsearch_is_post_page = '',
                    jobsearch_is_loc_editor = '';
            </script>
        <?php }

        public function load_locations_script()
        {
            wp_register_script('jobsearch-location-editor', jobsearch_plugin_get_url('modules/locations/js/jobsearch-inline-editor.js'), array('jquery'), '', true);
        }

        public static function get_countries()
        {
            global $wpdb;
            $contries_list = '';
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` order by name ");
            if (count($countries) > 0) {
                $cont_list = $countries;
                if (isset($cont_list) && !empty($cont_list) && is_array($cont_list)) {
                    $contries_list = $cont_list;
                    update_option('jobsearch_apiloc_countires_list', $contries_list);
                }
            }
            return $contries_list;
        }

        public static function get_states($contry_name)
        {
            global $wpdb;
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` where name = '" . $contry_name . "' ");
            if (count($countries) > 0) {
                $states_list = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $countries[0]->cntry_id . "' ");
            }

            if (isset($states_list) && !empty($states_list) && is_array($states_list)) {
                return $states_list;
            }
        }


        public static function get_cities($contry_code = '', $state_name)
        {
            global $jobsearch_location_ajax, $wpdb;
            $state_id = $jobsearch_location_ajax->get_state_id_by_name($state_name);
            $cities_list = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` where state_id = '" . $state_id . "' ");
            if (isset($cities_list) && !empty($cities_list) && is_array($cities_list)) {
                return $cities_list;
            }
        }

        public static function get_cities_by_state_ids()
        {
            global $wpdb;
            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');
            $preselected_states = isset($jobsearch_locsetivcn_options['states_filtrs_by_cntry']) ? $jobsearch_locsetin_options['states_filtrs_by_cntry'] : '';

            $all_cities = [];
            foreach ($preselected_states as $state_id) {
                $cities = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` WHERE state_id = '" . $state_id . "' ");
                foreach ($cities as $city_info) {
                    $all_cities[] = $city_info->city_name;
                }
            }
            return $all_cities;
        }

        public function jobsearch_form_fields_value_callback($val, $key)
        {
            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');
            $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
            if ($key == 'jobsearch_field_location_location1' && ($loc_optionstype == 2 || $loc_optionstype == 3)) {
                $val = $this->getCountryNameByCode($val);
                return $val;
            }
            return $val;
        }

        public function jobsearch_loc_check_tables()
        {
            global $wpdb;
            $countries_table_name = $wpdb->base_prefix . 'jobsearch_countries';
            $countries_flag = false;
            if ($wpdb->get_var("SHOW TABLES LIKE '$countries_table_name'") == $countries_table_name) {
                $countries_flag = true;
            }
            //
            $states_table_name = $wpdb->base_prefix . 'jobsearch_states';
            $states_flag = false;
            if ($wpdb->get_var("SHOW TABLES LIKE '$states_table_name'") == $states_table_name) {
                $states_flag = true;
            }
            //
            $cities_table_name = $wpdb->base_prefix . 'jobsearch_cities';
            $cities_flag = false;
            if ($wpdb->get_var("SHOW TABLES LIKE '$cities_table_name'") == $cities_table_name) {
                $cities_flag = true;
            }
            if ($cities_flag == false && $countries_flag == false && $states_flag == false) {
                return true;
            }
        }

        public function jobsearch_loc_settings_create_menu()
        {
            // create new top-level menu
            add_menu_page(esc_html__('Location Manager', 'wp-jobsearch'), esc_html__('Location Manager', 'wp-jobsearch'), 'administrator', 'jobsearch-location-sett', function () {
                global $jobsearch_download_locations, $wpdb, $jobsearch_import_data_handle;
                $jobsearch_download_locations = true;

                add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
                $wp_upload_dir = wp_upload_dir();
                $upload_file_path = $wp_upload_dir['path'];
                //
                if (!file_exists($upload_file_path . "/countries")) {
                    $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
                }
                //
                $upload_file_url = $wp_upload_dir['url'];
                remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
                $jobsearch_download_locations = false;

                $import_flag = true;
                $countries = $jobsearch_import_data_handle->get_all_countries_detail();
                $cities_detail = $jobsearch_import_data_handle->get_all_cities_detail();

                if (!file_exists($upload_file_path . "/countries") && count($countries) == 0) {
                    self::download_files();
                    die();
                }
                if (!file_exists($upload_file_path . "/countries") && count($countries) != 0) {
                    $import_flag = false;
                }

                if (file_exists($upload_file_path . "/countries") && count($countries) == 0 && $import_flag == true) {
                    self::importFiles();
                    die();
                }

                if (empty($cities_detail)) {
                    self::UpdateCitiesFiles();
                    die();
                }

                $rand_id = rand(10000000, 99999999);
                wp_enqueue_script('jobsearch-gdlocation-api');
                wp_enqueue_script('jobsearch-selectize');

                $api_contries_list = self::get_countries();

                $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

                $loc_required_fields = isset($jobsearch_locsetin_options['loc_required_fields']) ? $jobsearch_locsetin_options['loc_required_fields'] : '';
                $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
                $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';

                $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
                $contry_order = $contry_order != '' ? $contry_order : 'alpha';
                $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
                $contry_filtring = $contry_filtring != '' ? $contry_filtring : 'none';
                $contry_filtr_limreslts = isset($jobsearch_locsetin_options['contry_filtr_limreslts']) ? $jobsearch_locsetin_options['contry_filtr_limreslts'] : '';
                $contry_filtr_limreslts = $contry_filtr_limreslts <= 0 ? 1000000 : $contry_filtr_limreslts;
                $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
                $states_filtrs_by_cntry = isset($jobsearch_locsetin_options['states_filtrs_by_cntry']) ? $jobsearch_locsetin_options['states_filtrs_by_cntry'] : '';
                $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
                $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
                $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
                $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';
                $autoload_files = $this->auto_load_files;
                $api_states_list = self::get_states_by_country($contry_singl_contry);

                ?>
                <div class="jobsearch-allocssett-holder">
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('.selectiz-locfield').selectize({
                                plugins: ['remove_button'],
                            });
                        });
                        jQuery(document).on('click', '.jobsearch-locsve-btn', function () {
                            jQuery('#allocs-settings-form').submit();
                        });
                        jQuery(document).on('click', '.panl-title > a', function () {
                            var _this = jQuery(this);
                            var main_acholder = jQuery('#panl-filter-options');
                            main_acholder.find('.panl-opened').removeClass('panl-opened').addClass('panl-closed');
                            main_acholder.find('.panel-body-opened').removeClass('panel-body-opened').addClass('panel-body-closed');
                            main_acholder.find('.panel-body-closed').hide();
                            //
                            _this.parents('.loc-panl-sec').find('.panl-closed').removeClass('panl-closed').addClass('panl-opened');
                            _this.parents('.loc-panl-sec').find('.panel-body-closed').removeClass('panel-body-closed').addClass('panel-body-opened');
                            _this.parents('.loc-panl-sec').find('.panel-body-opened').slideDown();
                        });
                        jQuery(document).on('change', 'select[name="loc_optionstype"]', function () {
                            if (jQuery(this).val() == '0' || jQuery(this).val() == '1') {
                                jQuery('#panl-filter-options').show(500);
                                jQuery('.setingsave-btncon').show(500);
                                jQuery('.allocs-contdrpdwn-selt').slideUp();
                                jQuery('#states-multiple-select').hide(500);
                            } else if (jQuery(this).val() == '4') {
                                jQuery('#states-multiple-select').show(500);
                                jQuery('#panl-filter-options').hide(500);
                            } else {
                                jQuery('.setingsave-btncon').hide(500);
                                jQuery('#panl-filter-options').hide(500);
                                jQuery('#contry-presel-none-<?php echo($rand_id) ?>').prop('checked', true);
                                jQuery('#contry-order-alpha-<?php echo($rand_id) ?>').prop('checked', true);
                                jQuery('#contry-filtr-none-<?php echo($rand_id) ?>').prop('checked', true);
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').hide();
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').hide();
                                jQuery('.allocs-contdrpdwn-selt').slideDown();
                                jQuery('#states-multiple-select').hide(500);
                            }
                        });

                        jQuery(document).on('change', 'input[name="contry_filtring"]', function () {
                            if (jQuery(this).val() == 'inc_contries') {
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideDown();
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-presel-contry-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-presel-none-<?php echo($rand_id) ?>').prop('checked', true);
                            } else if (jQuery(this).val() == 'exc_contries') {
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideDown();
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-presel-contry-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-presel-none-<?php echo($rand_id) ?>').prop('checked', true);
                            } else {
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideUp();
                            }
                        });
                        jQuery(document).on('change', 'input[name="contry_preselct"]', function () {
                            if (jQuery(this).val() == 'by_contry') {
                                jQuery('#contry-presel-contry-<?php echo($rand_id) ?>').slideDown();
                                jQuery('#contry-filtr-none-<?php echo($rand_id) ?>').prop('checked', true);
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideUp();
                            } else if (jQuery(this).val() == 'by_user_ip') {
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-presel-contry-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-filtr-none-<?php echo($rand_id) ?>').prop('checked', true);
                                //
                                jQuery('#contry-filtrexc-cont-<?php echo($rand_id) ?>').slideUp();
                                jQuery('#contry-filtrinc-cont-<?php echo($rand_id) ?>').slideUp();
                            } else {
                                jQuery('#contry-presel-contry-<?php echo($rand_id) ?>').slideUp();
                            }
                        });
                        //
                        jQuery(document).on('change', 'input[type="checkbox"][name="continent_group"]', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('.contint-group-options').slideDown();
                            } else {
                                jQuery('.contint-group-options').slideUp();
                            }
                        });
                        //
                        jQuery(document).on('change', '#contry-singl-contry-<?php echo($rand_id) ?>', function () {
                            var _country_code = jQuery(this).val(),
                                loc_type = jQuery("select[name=loc_optionstype]").val();
                            if (loc_type != 4) return;
                            jQuery("#states-multiple-select").html('');
                            var request = jQuery.ajax({
                                url: ajaxurl,
                                method: "POST",
                                data: {
                                    country_code: _country_code,
                                    action: 'jobsearch_locations_get_country_states',
                                },
                                dataType: "json"
                            });
                            request.done(function (response) {
                                if ('undefined' !== typeof response.status) {
                                    jQuery("#states-multiple-select").append(response.status);
                                }
                            });
                            request.fail(function (jqXHR, textStatus) {

                            });
                        });
                    </script>
                    <div class="allocs-sett-label">
                        <h1><?php esc_html_e('Preview Example', 'wp-jobsearch') ?></h1>
                    </div>
                    <div class="allocs-sett-view">
                        <div class="preview-loc-exmphdin">
                            <?php if ($loc_optionstype == '0' || $loc_optionstype == '1') { ?>
                                <h3><?php esc_html_e('Select Country', 'wp-jobsearch') ?></h3>
                            <?php } else if ($loc_optionstype == '2' || $loc_optionstype == '3') { ?>
                                <h3><?php esc_html_e('Select State', 'wp-jobsearch') ?></h3>
                            <?php } else { ?>
                                <h3><?php esc_html_e('Select City', 'wp-jobsearch') ?></h3>
                            <?php } ?>
                        </div>
                        <?php if ($loc_optionstype == '0' || $loc_optionstype == '1') { ?>
                            <select name="country" class="countries " id="countryId"
                                    data-placeholder="<?php echo esc_html_e('Select Country', 'wp-jobsearch') ?>">
                                <option value=""><?php echo esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                            </select>
                        <?php } else { ?>
                            <input type="hidden" name="country" id="countryId"
                                   value="<?php echo($contry_singl_contry) ?>"/>
                        <?php } ?>
                        <?php if ($loc_optionstype != '4') { ?>
                            <select name="state" class="states location2-state" id="stateId">
                                <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                            </select>
                        <?php } ?>
                        <?php
                        if ($loc_optionstype == '1' || $loc_optionstype == '2' || $loc_optionstype == '4') { ?>
                            <select name="city" class="cities" id="cityId">
                                <option value="0"><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                            </select>
                        <?php } ?>
                    </div>
                    <div class="allocs-sett-filtrs">
                        <div class="allocs-configdrpdwn-sett">
                            <span><?php esc_html_e('Configure your dropdowns', 'wp-jobsearch') ?></span>
                            <a href="javascript:void(0);"
                               class="jobsearch-locsve-btn button button-primary"><?php esc_html_e('Generate Settings', 'wp-jobsearch') ?></a>
                        </div>
                        <form id="allocs-settings-form" method="post">
                            <div class="allocs-configdrpdwn-sett">
                                <span><?php esc_html_e('Required Location Fields', 'wp-jobsearch') ?></span>
                                <select class="drpdwn-type-control" name="loc_required_fields">
                                    <option <?php echo($loc_required_fields == 'no' ? 'selected="selected"' : '') ?>
                                            value="no">
                                        <?php esc_html_e('No', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo($loc_required_fields == 'yes' ? 'selected="selected"' : '') ?>
                                            value="yes">
                                        <?php esc_html_e('Yes', 'wp-jobsearch') ?>
                                    </option>
                                </select>
                                <input type="hidden" name="jobsearch_allocs_setingsubmit" value="1">
                            </div>
                            <div class="allocs-configdrpdwn-sett">
                                <span><?php esc_html_e('Dropdown Sequence', 'wp-jobsearch') ?></span>
                                <select class="drpdwn-type-control" name="loc_optionstype">
                                    <option <?php echo($loc_optionstype == '0' ? 'selected="selected"' : '') ?>
                                            value="0">
                                        <?php esc_html_e('Country - State', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo($loc_optionstype == '1' || $loc_optionstype == '' ? 'selected="selected"' : '') ?>
                                            value="1">
                                        <?php esc_html_e('Country - State - City', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo($loc_optionstype == '2' ? 'selected="selected"' : '') ?>
                                            value="2">
                                        <?php esc_html_e('State - City (Single country)', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo($loc_optionstype == '3' ? 'selected="selected"' : '') ?>
                                            value="3">
                                        <?php esc_html_e('State (Single country)', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo($loc_optionstype == '4' ? 'selected="selected"' : '') ?>
                                            value="4">
                                        <?php esc_html_e('Single - Country (Cities)', 'wp-jobsearch') ?>
                                    </option>
                                </select>
                                <input type="hidden" name="jobsearch_allocs_setingsubmit" value="1">
                            </div>

                            <div class="allocs-contdrpdwn-selt"
                                 style="display: <?php echo($loc_optionstype == '0' || $loc_optionstype == '1' ? 'none' : 'block') ?>;">
                                <label for="contry-singl-contry-<?php echo($rand_id) ?>"><?php esc_html_e('Select Country', 'wp-jobsearch') ?></label>
                                <select id="contry-singl-contry-<?php echo($rand_id) ?>" name="contry_singl_contry">
                                    <?php
                                    if (!empty($api_contries_list)) {
                                        foreach ($api_contries_list as $contry_key => $contry_title) { ?>
                                            <option value="<?php echo($contry_title->code) ?>" <?php echo($contry_singl_contry == $contry_title->code ? 'selected="selected"' : '') ?>><?php echo($contry_title->name) ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="allocs-contdrpdwn-selt states-multiple-select"
                                 id="states-multiple-select"
                                 style="display: <?php echo($loc_optionstype == '4' ? 'block' : 'none') ?>;">
                                <label for="contry-states-<?php echo($rand_id) ?>"><?php esc_html_e('Select States', 'wp-jobsearch') ?></label>
                                <select id="contry-states-<?php echo($rand_id) ?>" multiple="multiple"
                                        name="states_filtrs_by_cntry[]">
                                    <?php
                                    if (!empty($api_states_list)) {
                                        foreach ($api_states_list as $state_key => $state_info) { ?>
                                            <option value="<?php echo($state_info->state_id) ?>" <?php echo(!empty($states_filtrs_by_cntry) && is_array($states_filtrs_by_cntry) && in_array($state_info->state_id, $states_filtrs_by_cntry) ? 'selected="selected"' : '') ?>><?php echo($state_info->state_name) ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <?php
                            $display = '';
                            if ($loc_optionstype == 2 || $loc_optionstype == 3 || $loc_optionstype == 4) {
                                $display = 'style="display: none" ';
                            } ?>
                            <div id="panl-filter-options" class="jobsearch-filtr-options" <?php echo $display ?>>
                                <div class="loc-panl-sec">
                                    <div class="panl-heading">
                                        <h4 class="panl-title">
                                            <a href="javascript:void(0);" class="panl-opened">
                                                <?php esc_html_e('Country Options', 'wp-jobsearch') ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-body-opened">
                                        <div class="panl-body">
                                            <div class="filtr-chks-box ordering">
                                                <span><?php esc_html_e('Ordering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-order-alpha-<?php echo($rand_id) ?>"
                                                               type="radio" name="contry_order"
                                                               value="alpha" <?php echo($contry_order == 'alpha' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-order-alpha-<?php echo($rand_id) ?>"><?php esc_html_e('Alphabetical', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="contry-order-randm-<?php echo($rand_id) ?>"
                                                               type="radio" name="contry_order"
                                                               value="random" <?php echo($contry_order == 'random' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-order-randm-<?php echo($rand_id) ?>"><?php esc_html_e('Random', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="filtr-chks-box filtering">
                                                <span><?php esc_html_e('Filtering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-filtr-none-<?php echo($rand_id) ?>"
                                                               type="radio" name="contry_filtring"
                                                               value="none" <?php echo($contry_filtring == 'none' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-filtr-none-<?php echo($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-filtr-inclist-<?php echo($rand_id) ?>"
                                                                   type="radio" name="contry_filtring"
                                                                   value="inc_contries" <?php echo($contry_filtring == 'inc_contries' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-filtr-inclist-<?php echo($rand_id) ?>"><?php esc_html_e('Include only countries selected', 'wp-jobsearch') ?></label>
                                                        </div>

                                                        <div id="contry-filtrinc-cont-<?php echo($rand_id) ?>"
                                                             class="filtrs-select-field multiseltc"
                                                             style="display: <?php echo($contry_filtring == 'inc_contries' ? 'block' : 'none') ?>;">
                                                            <select multiple="multiple"
                                                                    name="contry_filtrinc_contries[]">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    if ($contry_order == 'random') {
                                                                        $api_contries_list = self::shuffleArray($api_contries_list);
                                                                    }
                                                                    foreach ($api_contries_list as $contry_key => $contry_info) { ?>
                                                                        <option value="<?php echo($contry_info->name) ?>" <?php echo(!empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries) && in_array($contry_info->name, $contry_filtrinc_contries) ? 'selected="selected"' : '') ?>><?php echo($contry_info->name) ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-filtr-exclist-<?php echo($rand_id) ?>"
                                                                   type="radio" name="contry_filtring"
                                                                   value="exc_contries" <?php echo($contry_filtring == 'exc_contries' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-filtr-exclist-<?php echo($rand_id) ?>"><?php esc_html_e('Exclude countries selected', 'wp-jobsearch') ?></label>
                                                        </div>
                                                        <div id="contry-filtrexc-cont-<?php echo($rand_id) ?>"
                                                             class="filtrs-select-field multiseltc"
                                                             style="display: <?php echo($contry_filtring == 'exc_contries' ? 'block' : 'none') ?>;">
                                                            <select multiple="multiple"
                                                                    name="contry_filtrexc_contries[]">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    foreach ($api_contries_list as $contry_key => $contry_info) { ?>
                                                                        <option value="<?php echo($contry_info->name) ?>" <?php echo(!empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries) && in_array($contry_info->name, $contry_filtrexc_contries) ? 'selected="selected"' : '') ?>><?php echo($contry_info->name) ?></option>
                                                                        <?php
                                                                    }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="filtr-chks-box pre-select">
                                                <span><?php esc_html_e('Preselect Country', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-presel-none-<?php echo($rand_id) ?>"
                                                               type="radio" name="contry_preselct"
                                                               value="none" <?php echo($contry_preselct == 'none' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-presel-none-<?php echo($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-presel-bycontry-<?php echo($rand_id) ?>"
                                                                   type="radio" name="contry_preselct"
                                                                   value="by_contry" <?php echo($contry_preselct == 'by_contry' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-presel-bycontry-<?php echo($rand_id) ?>"><?php esc_html_e('Choose country', 'wp-jobsearch') ?></label>
                                                        </div>
                                                        <div id="contry-presel-contry-<?php echo($rand_id) ?>"
                                                             class="filtrs-select-field"
                                                             style="display: <?php echo($contry_preselct == 'by_contry' ? 'block' : 'none') ?>;">
                                                            <?php if (!empty($contry_filtrinc_contries) && $contry_filtring == 'inc_contries') {
                                                                $api_contries_list = $contry_filtrinc_contries;
                                                            } else if (!empty($contry_filtrexc_contries) && $contry_filtring == 'exc_contries') {
                                                                $api_contries_list = self::getExcludeCountriesList($contry_filtrexc_contries);
                                                            }
                                                            ?>
                                                            <select name="contry_presel_contry">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    foreach ($api_contries_list as $key => $contry_info) {
                                                                        if (!empty($contry_filtrinc_contries) && $contry_filtring == 'inc_contries') { ?>
                                                                            <option value="<?php echo(self::getCountryCode($contry_info)) ?>" <?php echo($contry_presel_contry == self::getCountryCode($contry_info) ? 'selected="selected"' : '') ?>><?php echo($contry_info) ?></option>
                                                                        <?php } else if (!empty($contry_filtrexc_contries) && $contry_filtring == 'exc_contries') { ?>
                                                                            <option value="<?php echo($contry_info->code) ?>" <?php echo($contry_presel_contry == $contry_info->code ? 'selected="selected"' : '') ?>><?php echo($contry_info->name) ?></option>
                                                                        <?php } else { ?>
                                                                            <option value="<?php echo($contry_info->code) ?>" <?php echo($contry_presel_contry == $contry_info->code ? 'selected="selected"' : '') ?>><?php echo($contry_info->name) ?></option>
                                                                        <?php }
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <input id="contry-presel-byip-<?php echo($rand_id) ?>"
                                                               type="radio" name="contry_preselct"
                                                               value="by_user_ip" <?php echo($contry_preselct == 'by_user_ip' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-presel-byip-<?php echo($rand_id) ?>"><?php esc_html_e('Predict by user IP', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="setingsave-btncon" <?php echo $display ?>>
                            <a href="javascript:void(0);"
                               class="jobsearch-locsve-btn button button-primary"><?php esc_html_e('Generate Settings', 'wp-jobsearch') ?></a>
                        </div>
                    </div>
                </div>
                <?php
            }, '', 30);

            add_submenu_page(
                'jobsearch-location-sett',
                esc_html__('Location Settings Editor', 'wp-jobsearch'), //page title
                esc_html__('Location Settings Editor', 'wp-jobsearch'), //menu title
                'administrator',
                "jobsearch-location-sett-editor",
                array($this, 'locations_settings_editor')
            );
        }

        public static function get_states_by_country($country_code)
        {
            global $jobsearch_location_ajax, $wpdb;
            $country_id = $jobsearch_location_ajax->get_country_id_by_code($country_code);
            $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE cntry_id = '" . $country_id . "' ");
            return $states;
        }

        public static function getExcludeCountriesList($contry_filtrexc_contries)
        {
            global $wpdb;
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` order by name ");
            $countries_list = [];
            foreach ($countries as $key => $val) {
                if (in_array($val->name, $contry_filtrexc_contries)) {
                    continue;
                }
                $countries_list[] = $val;
            }
            return $countries_list;
        }

        public static function getCountryNameByCode($code)
        {
            global $wpdb;
            $country_name = '';
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` order by name ");
            foreach ($countries as $key => $val) {
                if ($val->code == $code) {
                    $country_name = $val->name;
                }
            }
            return $country_name;
        }

        public static function getCountryCode($name)
        {
            global $wpdb;
            $country_code = '';
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` order by name ");
            foreach ($countries as $key => $val) {
                if ($val->name == $name) {
                    $country_code = $val->code;
                }
            }
            return $country_code;
        }

        public static function shuffleArray($list)
        {
            if (!is_array($list)) return $list;
            $keys = array_keys($list);
            shuffle($keys);
            $random = array();
            foreach ($keys as $key)
                $random[$key] = $list[$key];
            return $random;
        }

        public static function sortByPopulation($api_contries_list)
        {
            $arr = $api_contries_list;
            $sort = array();
            foreach ($arr as $k => $v) {
                $population = str_replace(',', '', $v['population']);
                $sort['population'][$k] = $population;
            }
            array_multisort($sort['population'], SORT_DESC, $arr);
            return $arr;
        }

        public function importFiles()
        { ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jobsearch_import_countries();
                });

                function jobsearch_import_countries() {
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'jobsearch_locations_import_countries',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'countries_added') {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("Please wait while adding states. Don't leave the page");
                            import_countries_states();
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }

                function import_countries_states() {
                    //
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'jobsearch_locations_import_states',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'states_added') {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("Please wait while adding cities. Don't leave the page");
                            import_cities()
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }

                function import_cities(cntry_id = 0) {

                    var _cntry_id = parseInt(cntry_id) + parseInt(1),
                        _jobsearch_import_progress = jQuery("#jobsearch-loc-data-import-percent");
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            cntry_id: _cntry_id,
                            action: 'jobsearch_locations_import_cities',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'cities_added') {
                            jQuery("#jobsearch-loc-data-import-percent,#jobsearch-loc-percent-text").show();
                            _jobsearch_import_progress.attr('value', response.data_percent);
                            jQuery("#jobsearch-loc-percent-text").text(response.data_percent + "% completed");
                            import_cities(response.cntry_id);
                            if (response.cntry_id == 220) {
                                location.reload();
                            }
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }
            </script>
            <div class="allocs-sett-filtrs">
                <div class="dnload-btn-wrapper">
                    <h3><?php echo esc_html('Please wait while your data is being imported into the database', 'wp-jobsearch') ?></h3>
                    <span class="location-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    <progress id="jobsearch-loc-data-import-percent" style="display: none" value="0"
                              max="100"></progress>
                    <span id="jobsearch-loc-percent-text" style="display: none">0</span>
                </div>
            </div>
        <?php }

        public function UpdateCitiesFiles()
        { ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    UpdateCitiesFiles();
                });
                function UpdateCitiesFiles(cntry_id = 0){
                    var _cntry_id = parseInt(cntry_id) + parseInt(1),
                        _jobsearch_import_progress = jQuery("#jobsearch-loc-data-import-percent");
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            cntry_id: _cntry_id,
                            action: 'update_country_id_cities',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        console.info(response.cntry_id);
                        if ('undefined' !== typeof response.status && response.status == 'data_updated') {
                            jQuery("#jobsearch-loc-data-import-percent,#jobsearch-loc-percent-text").show();
                            _jobsearch_import_progress.attr('value', response.data_percent);
                            jQuery("#jobsearch-loc-percent-text").text(response.data_percent + "% completed");
                            UpdateCitiesFiles(response.cntry_id);
                            if (response.cntry_id == 220) {
                                location.reload();
                            }
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }

            </script>
            <div class="allocs-sett-filtrs">
                <div class="dnload-btn-wrapper">
                    <h3><?php echo esc_html('Please wait while your data is being updated.', 'wp-jobsearch') ?></h3>
                    <span class="location-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    <progress id="jobsearch-loc-data-import-percent" style="display: none" value="0"
                              max="100"></progress>
                    <span id="jobsearch-loc-percent-text" style="display: none">0</span>
                </div>
            </div>
        <?php }

        public static function download_files()
        { ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'jobsearch_locations_download',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'data_downloaded') {
                            jobsearch_import_countries();
                        } else if ('undefined' !== typeof response.status && response.status == 'permission_issue') {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("Unable to create directory uploads/jobsearch-locations. Is its parent directory writable by the server? Please contact your hosting server provider.");
                            jQuery(document).find(".location-loader").hide();
                        } else {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("The destination folder for streaming files does not exist or cannot be written to.");
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                });

                function jobsearch_import_countries() {
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'jobsearch_locations_import_countries',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'countries_added') {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("Please wait while adding states. Don't leave the page");
                            import_countries_states();
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }

                function import_countries_states() {
                    //
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'jobsearch_locations_import_states',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'states_added') {
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text(" ");
                            jQuery(document).find(".dnload-btn-wrapper").find("h3").text("Please wait while adding cities. Don't leave the page");
                            import_cities();
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }

                function import_cities(cntry_id = 0) {
                    var _cntry_id = parseInt(cntry_id) + parseInt(1);

                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            cntry_id: _cntry_id,
                            action: 'jobsearch_locations_import_cities',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.status && response.status == 'cities_added') {
                            jQuery("#jobsearch-loc-data-import-percent,#jobsearch-loc-percent-text").show();
                            jQuery("#jobsearch-loc-data-import-percent").attr('value', response.data_percent);
                            jQuery("#jobsearch-loc-percent-text").text(response.data_percent + "% Completed");
                            import_cities(response.cntry_id);
                            if (response.cntry_id == 220) {
                                window.location.href = "<?php echo admin_url('admin.php?page=jobsearch-location-sett-editor') ?>";
                            }
                            //location.reload(true);
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        //alert(textStatus)
                    });
                }
            </script>
            <div class="allocs-sett-filtrs">
                <div class="dnload-btn-wrapper">
                    <h3><?php echo esc_html('Please wait while your files are being downloaded and is being added in database', 'wp-jobsearch') ?></h3>
                    <span class="location-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    <progress id="jobsearch-loc-data-import-percent" style="display: none" value="0"
                              max="100"></progress>
                    <span id="jobsearch-loc-percent-text" style="display: none">0</span>
                </div>
            </div>
            <?php
        }

        function jobsearch_locations_download_callback()
        {
            global $wp_filesystem, $jobsearch_download_locations;
            require_once ABSPATH . '/wp-admin/includes/file.php';
            $jobsearch_download_locations = true;
            $url = wp_nonce_url("post.php", "filesystem-nonce");
            $form_fields = array("file-data");
            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();

            $upload_file_path = $wp_upload_dir['path'];

            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }

            if (!empty($wp_upload_dir['error'])) {
                echo json_encode(array('status' => 'permission_issue'));
                wp_die();
            }
            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;

            if (!file_exists($upload_file_path . "/countries")) {
                if (connect_fs($url, "", $upload_file_path, $form_fields)) {

                    $dir = $wp_filesystem->find_folder($upload_file_path);
                    $file = trailingslashit($dir) . "locations.zip";
                    $wp_filesystem->put_contents($file, '', FS_CHMOD_FILE);
                } else {
                    return new WP_Error("filesystem_error", "Cannot initialize filesystem");
                }

                $zipFile = $upload_file_path . "/locations.zip"; // Local Zip File Path

                $response = wp_remote_get(
                    'https://careerfy.net/download-plugins/locations.zip',
                    array(
                        'timeout' => 300,
                        'stream' => true,
                        'filename' => $upload_file_path . '/locations.zip',
                    )
                );

                if (empty($response->error->http_request_failed)) {
                    unzip_file($zipFile, $upload_file_path);
                    unlink($zipFile);
                    echo json_encode(array('status' => 'data_downloaded'));
                    wp_die();
                } else {
                    echo json_encode(array('status' => 'data_not_downloaded'));
                    wp_die();
                }
            }
        }

        public function locations_settings_editor()
        {
            global $jobsearch_download_locations, $wpdb, $jobsearch_import_data_handle;
            $jobsearch_download_locations = true;
            $import_flag = true;
            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();

            $upload_file_path = $wp_upload_dir['path'];
            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }

            $upload_file_url = $wp_upload_dir['url'];
            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;
            $countries = $jobsearch_import_data_handle->get_all_countries_detail();
            $cities_detail = $jobsearch_import_data_handle->get_all_cities_detail();

            if (!file_exists($upload_file_path . "/countries") && count($countries) == 0) {
                self::download_files();
                die();
            }
            if (!file_exists($upload_file_path . "/countries") && count($countries) != 0) {
                $import_flag = false;
            }

            if (file_exists($upload_file_path . "/countries") && count($countries) == 0 && $import_flag == true) {
                self::importFiles();
                die();
            }


            if (empty($cities_detail)) {
                self::UpdateCitiesFiles();
                die();
            }

            wp_enqueue_script('jobsearch-location-editor');

            $jobsearch_location_common_text = array(
                'sav_contry' => esc_html__('Save Countries', 'wp-jobsearch'),
                'save_states' => esc_html__('Save States', 'wp-jobsearch'),
                'sav_city' => esc_html__('Save City', 'wp-jobsearch'),
                'pls_wait' => esc_html__('Please Wait...', 'wp-jobsearch'),
                'req_state' => esc_html__('Please Enter Any State Name', 'wp-jobsearch'),
                'req_city' => esc_html__('Please Enter Any City Name', 'wp-jobsearch'),
                'req_cntry' => esc_html__('Please Enter Country Code', 'wp-jobsearch'),
                'req_cntry_code_uppercase' => esc_html__('Country Code should be in uppercase.', 'wp-jobsearch'),
                'req_num' => esc_html__('Country Code cannot be numeric.', 'wp-jobsearch'),
                'req_chars' => esc_html__('Country code cannot be more than 3 alphabets.', 'wp-jobsearch'),
                'req_poplation' => esc_html__('Population can only be in numbers.', 'wp-jobsearch'),
                'cntry_success' => esc_html__('Country Saved Successfully.', 'wp-jobsearch'),
                'state_success' => esc_html__('State Saved successfully.', 'wp-jobsearch'),
                'city_success' => esc_html__('City Saved successfully.', 'wp-jobsearch'),
                'any_state_text' => esc_html__('Enter Any State', 'wp-jobsearch'),
                'state_text' => esc_html__('Enter States', 'wp-jobsearch'),
                'state_select' => esc_html__('Select States', 'wp-jobsearch'),
                'state_city' => esc_html__('Select any city', 'wp-jobsearch'),
                'reset_data' => esc_html__('Are you sure you want to reset locations data and download again?', 'wp-jobsearch'),
                'file_ext' => esc_html__('File of this extension is not allowed. Only XLSX is allowed.', 'wp-jobsearch'),
            );
            wp_localize_script('jobsearch-location-editor', 'jobsearch_location_common_text', $jobsearch_location_common_text);

            $query_var = '';
            if (isset($_GET['page'])) {
                $query_var = $_GET['page'];
            }

            $api_contries_list = self::get_countries();

            ?>
            <div class="jobsearch-allocssett-holder">
                <div class="allocs-sett-label">
                    <h1><?php esc_html_e('Locations Importer Settings', 'wp-jobsearch') ?></h1></div>
                <div class="allocs-sett-view locations-importer-wrapper">
                    <a href="javascript:void(0)"
                       class="import-file-popup"><?php esc_html_e('Import Files', 'wp-jobsearch') ?></a>
                    <a class="jobsearch-download-sample-file-btn"
                       href="<?php echo plugin_dir_url(__FILE__) . 'sample-files/locations-sample.xlsx' ?>"><?php echo esc_html__("Locations Sample File", 'wp-jobsearch') ?></a>
                    <div class="reset-loc-btn-wrap">
                        <a href="javascript:void(0)"
                           class="jobsearch-loc-reset-data"><?php echo esc_html__("Reset Data", 'wp-jobsearch') ?></a>
                    </div>
                </div>
            </div>
            <div class="jobsearch-allocssett-holder">
                <div class="allocs-sett-label">
                    <h1><?php esc_html_e('Location Editor', 'wp-jobsearch') ?></h1>
                </div>
                <div class="allocs-sett-view">
                    <div class="preview-loc-exmphdin">
                        <h3><?php esc_html_e('Select Country', 'wp-jobsearch') ?></h3></div>
                    <div class="locations-wrapper">
                        <select name="country" id="editor-country">
                            <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                            <?php
                            if (!empty($api_contries_list)) {
                                foreach ($api_contries_list as $contry_key => $contry) { ?>
                                    <option value="<?php echo($contry->cntry_id) ?>"
                                            data-country-name="<?php echo($contry->name) ?>"><?php echo($contry->name) ?></option>
                                    <?php
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <div class="card-wrapper country-table">
                            <div class="card-body">
                                <table class="table country-table-detail" id="makeEditableCountries" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th><?php echo esc_html_e('Country', 'wp-jobsearch'); ?></th>
                                        <th><?php echo esc_html_e('Country Code', 'wp-jobsearch'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="loc-error">
                                        <td class="editable"><?php echo esc_html_e('Enter Country Name', 'wp-jobsearch'); ?></td>
                                        <td class="editable"><?php echo esc_html_e('Enter Code', 'wp-jobsearch'); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="jobsearch-table-cta-btn-wrapper">
                                    <button id="submit_country_detail"
                                            class="cta-submit loc-disabled"
                                            disabled="disabled"><?php echo esc_html_e('Save Countries', 'wp-jobsearch'); ?></button>
                                    <button id="add_country"><?php echo esc_html_e('Add New Country', 'wp-jobsearch'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allocs-sett-view state-wrapper loc-hidden">
                    <div class="preview-loc-exmphdin jobsearch-load-state-name">
                        <h3><?php esc_html_e('Country States', 'wp-jobsearch') ?></h3>
                        <a href="javascript:void(0)"
                           class="jobsearch-loc-delete-states"><?php esc_html_e('Delete States', 'wp-jobsearch') ?></a>
                    </div>

                    <div class="col-md-12">
                        <div class="card-wrapper state-table">
                            <div class="card-body">
                                <table class="table state-table-detail" id="makeEditableStates" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th style="width: 150px"><input type="checkbox" class="select-all-states">
                                            <label for="select-all-states"><?php esc_html_e('Select All', 'wp-jobsearch') ?></label>
                                        </th>
                                        <th><?php echo esc_html_e('States', 'wp-jobsearch'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="jobsearch-table-cta-btn-wrapper">
                                    <div class="jobsearch-location-span jobsearch-loc-state-loader"><span
                                                class="spinner is-active"></span></div>
                                    <button id="submit_states_detail"
                                            class="cta-submit loc-disabled"
                                            disabled="disabled"><?php echo esc_html_e('Save States', 'wp-jobsearch'); ?></button>
                                    <button id="add_state"><?php echo esc_html_e('Add New State', 'wp-jobsearch'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allocs-sett-view cities-wrapper loc-hidden">
                    <div class="preview-loc-exmphdin jobsearch-load-states-cities-name">
                        <h3></h3>
                    </div>

                    <div class="locations-wrapper">
                        <select name="state" class="states location2-states" id="editor-state">
                            <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                        </select>
                        <a href="javascript:void(0)"
                           class="jobsearch-loc-delete-cities"><?php esc_html_e('Delete Cities', 'wp-jobsearch') ?></a>
                    </div>

                    <div class="col-md-12">
                        <div class="card-wrapper cities-table">
                            <div class="card-body">
                                <table class="table cities-table-detail" id="makeEditableCities" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th style="width: 150px"><input type="checkbox" class="select-all-cities">
                                            <label for="select-all-cities"><?php esc_html_e('Select All', 'wp-jobsearch') ?></label>
                                        </th>
                                        <th><?php echo esc_html_e('Cities', 'wp-jobsearch'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div class="jobsearch-table-cta-btn-wrapper">
                                    <div class="jobsearch-location-span jobsearch-loc-city-loader"></div>
                                    <button id="submit_cities_detail"
                                            class="cta-submit loc-disabled"
                                            disabled="disabled"><?php echo esc_html_e('Save Cities', 'wp-jobsearch'); ?></button>
                                    <button id="add_cities"><?php echo esc_html_e('Add New City', 'wp-jobsearch'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(".import-file-popup").on('click', function () {
                    jobsearch_modal_popup_open('JobSearchModalLocExclImporter');
                });

                <?php if($query_var != ''){ ?>
                var jobsearch_is_loc_editor = '<?php echo $query_var; ?>';
                <?php } ?>

            </script>

            <?php
        }

        public function jobsearch_check_state_dir_callback()
        {
            global $wp_filesystem, $jobsearch_download_locations;
            $jobsearch_download_locations = true;
            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();

            $upload_file_path = $wp_upload_dir['path'];
            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }

            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;

            if (file_exists($upload_file_path . "/countries/" . $_POST['country_code'])) {
                echo json_encode(array('country_code' => $_POST['country_code']));
                wp_die();
            }
        }


        public function stripslashes_deep($value)
        {
            $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

            return $value;
        }


        public function save_locsettings()
        {
            if (isset($_POST['jobsearch_allocs_setingsubmit']) && $_POST['jobsearch_allocs_setingsubmit'] == '1') {
                $data_arr_list = array();
                foreach ($_POST as $post_key => $post_val) {
                    $data_arr_list[$post_key] = $post_val;
                }
                update_option('jobsearch_locsetin_options', $data_arr_list);
            }
        }

        function getCountryCodebyname($countries_name)
        {
            global $wpdb;
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries`");
            $inc_countries = [];
            foreach ($countries as $key => $val) {
                if (in_array($val->name, $countries_name)) {
                    $inc_countries[] = $val->code;
                }
            }
            return json_encode($inc_countries);
        }

        public function wp_upload_dir_url()
        {
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['baseurl'];
            return preg_replace('/^https?:/', '', $upload_dir);
        }

        public function getCountriesDetail()
        {
            global $wpdb;
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries`");
            $totl_countries = [];
            foreach ($countries as $info) {
                $totl_countries[] = array('country_code' => $info->code, 'country_name' => $info->name);
            }
            return json_encode($totl_countries);
        }

        public function load_locations_js($force_flag = false, $ajax_flag = false)
        {
            global $pagenow, $sitepress, $jobsearch_plugin_options, $jobsearch_download_locations, $jobsearch_uploding_candimg, $jobsearch_uploding_resume, $jobsearch_import_data_handle;

            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }

            $jobsearch_uploding_resume = false;
            $jobsearch_uploding_candimg = false;
            $jobsearch_download_locations = true;
            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();

            $upload_file_path = $wp_upload_dir['path'];
            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }

            $upload_file_url = $wp_upload_dir['url'];
            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;
            //
            $countries = $jobsearch_import_data_handle->get_all_countries_detail();
            if (!file_exists($upload_file_path . "/countries") && count($countries) == 0) {
                return;
            }

            $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $page_id = icl_object_id($page_id, 'page', false, $lang_code);
            }

            $loc_flag = false;
            if ($force_flag == true) {
                $loc_flag = true;
            }

            if ($page_id > 0 && is_page($page_id)) {
                $loc_flag = true;
                $state_param_name = 'jobsearch_field_location_location2';
                $city_param_name = 'jobsearch_field_location_location3';
            } else {
                $state_param_name = 'location_location2';
                $city_param_name = 'location_location3';
            }

            if ((isset($_GET['page']) && $_GET['page'] == 'jobsearch-location-sett')) {
                $loc_flag = true;
            }

            if ((isset($_GET['page']) && $_GET['page'] == 'jobsearch-location-sett-editor')) {
                $loc_flag = true;
            }

            if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
                $loc_flag = true;
            }
            if ($loc_flag === false) {
                return false;
            }

            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');
            $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
            $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
            $contry_order = $contry_order != '' ? $contry_order : 'alpha';
            $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
            $states_filtrs_by_cntry = isset($jobsearch_locsetin_options['states_filtrs_by_cntry']) ? $jobsearch_locsetin_options['states_filtrs_by_cntry'] : '';

            $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
            $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
            $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
            $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
            $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';

            $query_var = '';
            if (isset($_GET['page'])) {
                $query_var = $_GET['page'];
            }
            ?>
            <?php if (is_admin()) { ?>
            <!-- Modal -->
            <div class="jobsearch-modal fade" id="JobSearchModalLocExclImporter">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <span class="modal-close"><i class="fa fa-times"></i></span>
                        <div class="model-title">
                            <h2><?php echo esc_html__('Import Excel or CSV file', 'wp-jobsearch') ?></h2>
                        </div>
                        <div class="model-contents">
                            <form method="post" action="" enctype="multipart/form-data"
                                  id="jobsearch-upload-excel-file-form">
                                <input type="hidden" name="action" value="jobsearch_location_load_excel_file">
                                <input type="file" id="excel-file" name="excel_file"/>
                                <input type="button" class="excel-import-btn"
                                       value="<?php echo esc_html__('Import', 'wp-jobsearch') ?>"
                                       id="jobsearch-location-upload-excel">
                                <div class="jobsearch-location-span"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">

                jQuery("#jobsearch-location-upload-excel").on('click', function () {
                    var excel_ser_form = jQuery('#jobsearch-upload-excel-file-form')[0], _this = jQuery(this),
                        fd = new FormData(excel_ser_form),
                        fileForm = jQuery('#excel-file')[0].files[0],
                        ext_not_allow = ['xlsx', 'csv'];
                    //
                    if (fileForm.size > 10000000) {
                        alert("cannot upload more than 10mb");
                        return;
                    }

                    var upload_file = fileForm.name,
                        last_element,
                        upload_file_ext = upload_file.split('.');
                    if (upload_file_ext != '') {
                        last_element = upload_file_ext[upload_file_ext.length - 1];
                        if (ext_not_allow.indexOf(last_element) == -1) {
                            alert(jobsearch_location_common_text.file_ext);
                            return;
                        }
                    }
                    if (!_this.hasClass('ajax-is-loading')) {
                        jQuery(".jobsearch-location-span").removeClass('hidden');
                        jQuery('.jobsearch-location-span').html('<span class="spinner is-active hidden"></span>');
                        //_this.addClass('ajax-is-loading');
                        jQuery.ajax({
                            url: jobsearch_plugin_vars.ajax_url,
                            type: 'post',
                            data: fd,
                            dataType: 'JSON',
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                console.info();
                                if ('undefined' !== typeof response.status && response.status == 'data_downloaded') {
                                    jQuery('.jobsearch-location-span').find('span').remove();
                                    _this.removeClass('ajax-is-loading');
                                    jQuery(".jobsearch-location-span").addClass('hidden');
                                    location.reload(true);
                                }
                            }
                        });
                    }
                });
            </script>
        <?php } ?>
            <script type="text/javascript">

                var jobsearch_sloc_type = '<?php echo $loc_optionstype ?>',
                    jobsearch_is_admin = '<?php echo is_admin(); ?>',
                    totl_countries_detail, totl_preselected_states_saved,
                    jobsearch_is_post_page = '<?php echo $pagenow; ?>';

                <?php if($query_var != ''){ ?>
                var jobsearch_is_loc_editor = '<?php echo $query_var; ?>';
                <?php } ?>

                var $ = jQuery, inc_countries = "", inc_countries_code = "",
                    exec_countries = "", contry_presel_contry, ajax_flag = false,
                    contry_preselct = '<?php echo $contry_preselct ?>',
                    contry_presel_contry_code = '<?php echo $contry_presel_contry ?>';

                /*
                * Scrapper Events
                * */
                var api_scrapper = {
                    getCountryCode: function (jobsearch_sloc_country) {
                        var country_code_from_country_name = '';
                        jQuery.each(totl_countries_detail, function (index, element) {
                            if (element.country_name == jobsearch_sloc_country) {
                                country_code_from_country_name = element.country_code;
                            }
                        });
                        return country_code_from_country_name;
                    },
                    readCityOnlyFiles: function (country_detail, state_name, selector) {

                        var $ = jQuery;
                        if (jobsearch_is_admin == '' || ajax_flag == true) {
                            jQuery('#jobsearch-gdapilocs-citycon').empty();
                            jQuery('#jobsearch-gdapilocs-citycon').append('<select placeholder="<?php echo esc_html__('Select City', 'wp-jobsearch'); ?>" name="<?php echo $city_param_name; ?>" class="cities" id="cityId"><option value="0"><?php echo esc_html__("Select City", "wp-jobsearch") ?></option></select>');
                            selector = jQuery(document).find("#cityId");
                        }

                        var request = jQuery.ajax({
                            url: jobsearch_plugin_vars.ajax_url,
                            method: "POST",
                            data: {
                                country_detail: country_detail,
                                state: state_name,
                                action: 'jobsearch_location_load_cities_data',
                            },
                            dataType: 'json',
                        });

                        request.done(function (response) {

                            var _result_cities = response;
                            if (jobsearch_is_admin == '' || ajax_flag == true) {
                                if (selector.length > 0) {
                                    selector.selectize()[0].selectize.destroy();
                                }
                            }

                            setTimeout(function () {
                                selector.html('');
                                //if (jobsearch_is_admin == 1 && ajax_flag == false) {
                                selector.append('<option value=""><?php echo esc_html_e('Select City', 'wp-jobsearch') ?></option>');
                                // }

                                var _option_select = '';
                                if (_result_cities.result.length != 0) {
                                    $.each(_result_cities.result, function (index, element) {

                                        if ($.trim(element.city_name) != "Enter Any City") {
                                            if (jobsearch_sloc_city != "") {
                                                _option_select = jobsearch_sloc_city == element.city_name ? 'selected' : '';
                                            }
                                            if (_option_select != "") {
                                                selector.append(jQuery("<option></option>").attr("value", element.city_name).attr("selected", "selected").text(element.city_name));
                                            } else {
                                                selector.append(jQuery("<option></option>").attr("value", element.city_name).text(element.city_name));
                                            }
                                        }
                                    });
                                } else {
                                    selector.html('');
                                    selector.append(jQuery("<option></option>").attr("value", "").text('<?php echo esc_html__('No cities exits.', 'wp-jobsearch') ?>'));
                                }
                            }, 50);

                            if (jobsearch_is_admin == '' || ajax_flag == true) {
                                if (_result_cities.result.length != 0) {
                                    setTimeout(function () {
                                        selector.selectize({
                                            maxOptions: 30000,
                                            sortField: [{
                                                field: 'text',
                                                direction: 'asc'
                                            }],
                                        });
                                    }, 100);
                                }
                            }
                            jQuery('#cityId').trigger('change');
                        });
                        request.fail(function (jqXHR, textStatus) {

                        });
                    },
                    readStateFile: function (country_code, selector) {

                        var $ = jQuery, request, _result_states;
                        if (jobsearch_is_admin == '' || ajax_flag == true) {
                            jQuery('#jobsearch-gdapilocs-statecon').empty();
                            jQuery('#jobsearch-gdapilocs-statecon').append('<select placeholder="<?php echo esc_html__("Select State", "wp-jobsearch") ?>"  class="states location2-states" id="stateId" name="<?php echo $state_param_name; ?>"><option value=""><?php echo esc_html__("Select State", "wp-jobsearch") ?></option></select>');
                            selector = jQuery(document).find("#stateId, .location2-states");
                        }
                        selector.html('');
                        request = jQuery.ajax({
                            url: jobsearch_plugin_vars.ajax_url,
                            method: "POST",
                            data: {
                                country_code: country_code,
                                action: 'jobsearch_location_load_states_data',
                            },
                            dataType: 'json',
                        });

                        request.done(function (response) {

                            if (jobsearch_is_admin == '' || ajax_flag == true) {
                                //selector.selectize()[0].selectize.destroy();
                            }

                            _result_states = response;

                            setTimeout(function () {
                                selector.html('');
                                //if (jobsearch_is_admin == 1 && ajax_flag == false) {
                                selector.append('<option value=""><?php echo esc_html_e('Select State', 'wp-jobsearch') ?></option>');
                                //}
                                var _option_select = '';
                                $.each(_result_states.result, function (index, element) {

                                    if ($.trim(element.state_name) != "Enter Any State") {
                                        if (jobsearch_sloc_state != "") {
                                            _option_select = jobsearch_sloc_state == element.state_name ? 'selected' : '';
                                        }

                                        if (_option_select != "") {
                                            selector.append(jQuery("<option></option>").attr("value", element.state_name).attr("selected", "selected").text(element.state_name));
                                            // selector.append(jQuery('<option>', {
                                            //     value: element,
                                            //     text: element,
                                            //     selected: _option_select,
                                            // }));
                                        } else {
                                            selector.append(jQuery("<option></option>").attr("value", element.state_name).text(element.state_name));
                                            // selector.append(jQuery('<option>', {
                                            //     value: element,
                                            //     text: element,
                                            // }));
                                        }
                                    }
                                });
                            }, 50);

                            /*
                            * If city is saved then this code will execute.
                            * */
                            if (jobsearch_sloc_city != "") {
                                setTimeout(function () {
                                    console.info("triggered second");
                                    jQuery('#stateId').trigger('change');
                                }, 1000)
                            }
                            /*
                            * Initialize Selectize
                            * */
                            if (jobsearch_is_admin == '' || ajax_flag == true) {
                                setTimeout(function () {
                                    selector.selectize({
                                        sortField: 'text'
                                    });
                                }, 100)
                            }
                        });
                        request.fail(function (jqXHR, textStatus) {

                        });
                    },
                    stripslashes: function (str) {
                        if (str == undefined) {
                            return;
                        }
                        return str.replace(/\\/g, '');
                    },
                    readCountryFile: function (selector, country) {
                        var $ = jQuery, _result_countries, request;

                        request = jQuery.ajax({
                            url: jobsearch_plugin_vars.ajax_url,
                            method: "POST",
                            data: {
                                action: 'jobsearch_location_load_countries_data',
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            _result_countries = response;
                            selector.html('');
                            if (jobsearch_is_admin == 1 && ajax_flag == true) {
                                selector.append('<option value=""><?php echo esc_html_e('Select Country', 'wp-jobsearch') ?></option>');
                            } else {
                                var $opt = jQuery('<option>');
                                $opt.val('').text('<?php echo esc_html_e('Select Country', 'wp-jobsearch') ?>');
                                $opt.appendTo(selector);
                            }
                            /*
                            * Alphabetic countries
                            * */
                            <?php if($contry_order == 'alpha'){ ?>
                            _result_countries.sort(function (a, b) {
                                return api_scrapper.compareStrings(a.name, b.name);
                            });
                            <?php } ?>
                            /*
                            * Code will execute if Include only countries option will be selected.
                            * */
                            <?php if($contry_filtring == 'inc_contries'){ ?>
                            inc_countries_code = <?php echo($this->getCountryCodebyname($contry_filtrinc_contries)) ?>;
                            inc_countries = <?php echo json_encode($contry_filtrinc_contries);
                            } ?>;
                            /*
                            * Code will execute if Exclude only countries option will be selected.
                            * */
                            <?php if($contry_filtring == 'exc_contries'){ ?>
                            exec_countries = <?php echo json_encode($contry_filtrexc_contries);
                            } ?>;
                            /*
                            * Code will execute if Random countries option will be selected.
                            * */
                            <?php if($contry_order == 'random'){ ?>
                            _result_countries = api_scrapper.shuffleArray(_result_countries);
                            <?php }
                            /*
                            * countries by population
                            * */
                            if($contry_order == 'by_population'){ ?>
                            _result_countries.sort(function (a, b) {
                                return parseFloat(b.population) - parseFloat(a.population);
                            });
                            <?php } ?>
                            /*
                            * Include only countries
                            * */
                            if (inc_countries != "" && jobsearch_is_loc_editor != 'jobsearch-location-sett-editor') {
                                var _inc_flag = false;
                                $.each(_result_countries, function (i, element) {
                                    if (i < inc_countries.length) {
                                        if (jobsearch_sloc_country == inc_countries[i]) {

                                            selector.append(jQuery("<option></option>")
                                                .attr("data-index", i)
                                                .attr("code", inc_countries_code[i])
                                                .attr("selected", "selected")
                                                .attr("value", inc_countries[i])
                                                .text(inc_countries[i]));
                                            _inc_flag = true;

                                        } else if (contry_preselct == 'by_contry' && contry_presel_contry_code == inc_countries_code[i] && ajax_flag == false && _inc_flag == false) {
                                            selector.append(jQuery("<option></option>")
                                                .attr("data-index", i)
                                                .attr("code", inc_countries_code[i])
                                                .attr("selected", "selected")
                                                .attr("value", inc_countries[i])
                                                .text(inc_countries[i]));

                                        } else {
                                            selector.append(jQuery("<option></option>")
                                                .attr("data-index", i)
                                                .attr("code", inc_countries_code[i])
                                                .attr("value", inc_countries[i])
                                                .text(inc_countries[i]));
                                        }
                                    }
                                })
                            } else if (exec_countries != '' && jobsearch_is_loc_editor != 'jobsearch-location-sett-editor') {
                                /*
                                * code will execute if "Exclude countries selected" filter option will be selected
                                * */
                                var _exec_flag = false;
                                $.each(_result_countries, function (index, element) {
                                    if (element != "") {
                                        if (exec_countries.indexOf(element.name) == -1) {
                                            /*
                                            * code will execute if Country Name is from save in metavalue
                                            * */

                                            if (jobsearch_sloc_country == element.name) {
                                                selector.append(jQuery("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("selected", "selected")
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                                _exec_flag = true;
                                            } else if (contry_preselct == 'by_contry' && contry_presel_contry_code == element.code && ajax_flag == false && _exec_flag == false) {
                                                selector.append(jQuery("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("selected", "selected")
                                                    .attr("value", element.name)
                                                    .text(element.name));

                                            } else {
                                                selector.append(jQuery("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                            }
                                        }
                                    }
                                })
                            } else {
                                var pres_selected_saved_contry = api_scrapper.getCountryCode(jobsearch_sloc_country);
                                $.each(_result_countries, function (index, element) {
                                    if (element != "") {
                                        /*
                                        * code will execute if Preselect Country option will be selected
                                        * */
                                        <?php if ($contry_preselct == 'by_contry' && $contry_presel_contry != "" && $query_var != 'jobsearch-location-sett-editor') { ?>

                                        contry_presel_contry = "<?php echo $contry_presel_contry ?>";

                                        var _option_select = contry_presel_contry == element.code ? 'selected' : '';
                                        /*
                                        * code will execute on the front end
                                        * */
                                        if (jobsearch_is_admin == '' || ajax_flag == true) {
                                            if (jobsearch_sloc_country != "") {

                                                if (pres_selected_saved_contry == element.code) {

                                                    selector.append(jQuery("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("selected", "selected")
                                                        .attr("value", element.name)
                                                        .text(element.name));

                                                } else {

                                                    selector.append(jQuery("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("value", element.name)
                                                        .text(element.name));
                                                }

                                            } else {

                                                if (contry_presel_contry == element.code) {
                                                    selector.append($('<option>', {
                                                        value: element.name,
                                                        text: element.name,
                                                        selected: 'selected',
                                                    }));
                                                } else {
                                                    selector.append($('<option>', {
                                                        value: element.name,
                                                        text: element.name,
                                                    }));
                                                }
                                            }
                                            ///////////////////end//////////////
                                        } else {

                                            if (jobsearch_sloc_country != "") {

                                                if (pres_selected_saved_contry == element.code) {

                                                    selector.append(jQuery("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("selected", "selected")
                                                        .attr("value", element.name)
                                                        .text(element.name));

                                                } else {

                                                    selector.append(jQuery("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("value", element.name)
                                                        .text(element.name));
                                                }

                                            } else {

                                                // if (contry_presel_contry == element.code) {
                                                //     selector.append(jQuery("<option></option>")
                                                //         .attr("data-index", index)
                                                //         .attr("code", element.code)
                                                //         .attr("selected", "selected")
                                                //         .attr("value", element.name)
                                                //         .text(element.name));
                                                // } else {
                                                    selector.append($("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("value", element.name)
                                                        .text(element.name));
                                                //}
                                            }

                                        }
                                        /*
                                        * Countries by IP
                                        * */

                                        <?php } else if($contry_preselct == 'by_user_ip') { ?>

                                        if (jobsearch_is_admin == '' || ajax_flag == true) {

                                            if (country == element.code) {

                                                selector.append(jQuery("<option></option>")
                                                    .attr("selected", "selected")
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                            } else {
                                                selector.append(jQuery("<option></option>")
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                            }

                                        } else {
                                            var _option_select = country == element.code ? 'selected' : '';
                                            if (country == element.code) {
                                                selector.append($("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("selected", "selected")
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                            } else {
                                                selector.append($("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("value", element.name)
                                                    .text(element.name));
                                            }
                                        }

                                        <?php } else { ?>

                                        if (jobsearch_sloc_type != 2 && jobsearch_sloc_type != 3) {

                                            var _option_select = '';
                                            if (jobsearch_sloc_country != '') {
                                                if (api_scrapper.stripslashes(jobsearch_sloc_country) == api_scrapper.stripslashes(element.name)) {
                                                    selector.append($("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("selected", "selected")
                                                        .attr("value", element.name)
                                                        .text(api_scrapper.stripslashes(element.name)));
                                                } else {
                                                    selector.append($("<option></option>")
                                                        .attr("data-index", index)
                                                        .attr("code", element.code)
                                                        .attr("value", element.name)
                                                        .text(api_scrapper.stripslashes(element.name)));
                                                }
                                            } else {
                                                selector.append($("<option></option>")
                                                    .attr("data-index", index)
                                                    .attr("code", element.code)
                                                    .attr("value", element.name)
                                                    .text(api_scrapper.stripslashes(element.name)));
                                            }

                                        } else if (jobsearch_is_loc_editor == 'jobsearch-location-sett-editor') {
                                            selector.append($("<option></option>")
                                                .attr("data-index", index)
                                                .attr("code", element.code)
                                                .attr("value", element.name)
                                                .text(api_scrapper.stripslashes(element.name)));
                                        }


                                        <?php } ?>
                                    }
                                });
                                <?php if($contry_preselct == 'by_user_ip'){ ?>
                                api_scrapper.readStateFile(country, jQuery("#stateId"));
                                <?php } ?>
                            }
                            /*
                            * Initialize Selectize
                            * */

                            if (jobsearch_is_admin == '' || ajax_flag == true) {
                                if (jobsearch_sloc_type == 0 || jobsearch_sloc_type == 1) {
                                    selector.selectize({
                                        placeholder: '<?php echo esc_html_e('Select Country', 'wp-jobsearch') ?>',
                                    });
                                }
                            }
                        });
                        request.fail(function (jqXHR, textStatus) {

                        });
                    },
                    predictByIP: function () {
                        var $ = jQuery;
                        var request = $.ajax({
                            url: "https://ipinfo.io/json",
                            dataType: "json"
                        });
                        request.done(function (result) {
                            if (result != '') {
                                api_scrapper.readCountryFile(jQuery('#countryId'), result.country);
                            } else {
                                /*
                                * Second Request will be sent if first request will fail.
                                * */
                                api_scrapper.apiSecondRequest();
                            }
                        });
                        request.fail(function (jqXHR, textStatus) {

                        });
                    },
                    shuffleArray: function (a) {
                        var j, x, i;
                        for (i = a.length - 1; i > 0; i--) {
                            j = Math.floor(Math.random() * (i + 1));
                            x = a[i];
                            a[i] = a[j];
                            a[j] = x;
                        }
                        return a;
                    },
                    apiSecondRequest: function () {
                        var request = $.ajax({
                            url: "http://ip-api.com/json",
                            dataType: "json"
                        });
                        request.done(function (result) {
                            api_scrapper.readCountryFile(jQuery('#countryId'), result.countryCode);
                            api_scrapper.readStateFile(result.countryCode, jQuery('#stateId'))
                        });
                        request.fail(function (jqXHR, textStatus) {

                        });
                    },
                    compareStrings: function (a, b) {
                        a = a.toLowerCase();
                        b = b.toLowerCase();
                        return (a < b) ? -1 : (a > b) ? 1 : 0;
                    },
                    getBulkCitiesByStates: function (preselected_states, jobsearch_sloc_city) {

                        jQuery('.cities').html('');
                        jQuery.ajax({
                            url: jobsearch_plugin_vars.ajax_url,
                            method: "POST",
                            data: {
                                preselected_states: JSON.parse(preselected_states),
                                action: 'jobsearch_get_selected_states_cities',
                            },
                            dataType: 'json',
                            success: function (response) {
                                jQuery.each(response.all_cities, function (index, city_name) {
                                    if (jobsearch_sloc_city == city_name) {
                                        jQuery('.cities')
                                            .append(jQuery("<option></option>")
                                                .attr("value", city_name)
                                                .attr("selected", "selected")
                                                .text(city_name));
                                    } else {
                                        jQuery('.cities')
                                            .append(jQuery("<option></option>")
                                                .attr("value", city_name)
                                                .text(city_name));

                                    }
                                });

                                if (jobsearch_is_admin == '' || ajax_flag == true) {
                                    setTimeout(function () {
                                        jQuery('.cities').selectize({
                                            sortField: 'text'
                                        });
                                    }, 100)
                                }
                            }
                        });
                    }
                };
                /*
                * Scrapper Events end
                * */

                <?php if($ajax_flag == false){ ?>
                jQuery(window).on('load', function () {
                    //
                    totl_countries_detail = JSON.parse('<?php echo $this->getCountriesDetail(); ?>');
                    totl_preselected_states_saved = '<?php echo json_encode($states_filtrs_by_cntry) ?>';

                    <?php } ?>

                    var $ = jQuery, _single_country_code = '';

                    if (jobsearch_sloc_type == '2' || jobsearch_sloc_type == '3') {
                        _single_country_code = jQuery("#countryId").val();
                    } else {
                        if (jobsearch_sloc_country != 0 && contry_preselct != 'by_contry') {
                            _single_country_code = api_scrapper.getCountryCode(jobsearch_sloc_country);
                        }
                    }

                    /*
                    * If location type is single country (cities)
                    * */

                    if (jobsearch_sloc_type == 4) {
                        api_scrapper.getBulkCitiesByStates(totl_preselected_states_saved, jobsearch_sloc_city);
                    }

                    <?php if($ajax_flag == true){ ?>
                    ajax_flag = '<?php echo $ajax_flag ?>';
                    if (contry_preselct != 'by_contry' && contry_preselct != 'by_user_ip') {
                        api_scrapper.readCountryFile(jQuery('#countryId'), jobsearch_sloc_country);
                    }
                    <?php } ?>

                    <?php if($contry_preselct != 'by_user_ip' || $query_var == 'jobsearch-location-sett-editor'){ ?>
                    api_scrapper.readCountryFile(jQuery('#countryId'), '');

                    <?php } ?>

                    if (jobsearch_sloc_state != "") {
                        api_scrapper.readStateFile(_single_country_code, jQuery("#stateId"))
                    }

                    if ((jobsearch_sloc_type == 2 || jobsearch_sloc_type == 3) && jobsearch_is_loc_editor != 'jobsearch-location-sett-editor') {
                        if (_single_country_code != "") {
                            api_scrapper.readStateFile(_single_country_code, jQuery("#stateId"))
                        }
                        if (jobsearch_sloc_city != "") {
                            setTimeout(function () {
                                console.info("done first");
                                jQuery('#stateId').trigger('change');
                            }, 4000);
                        }
                    }

                    <?php
                    /*
                     * Countries by user IP
                     * */
                    if($contry_preselct == 'by_user_ip' && $query_var != 'jobsearch-location-sett-editor'){ ?>
                    if (jobsearch_sloc_country == 0 || jobsearch_sloc_state == 0) {
                        api_scrapper.predictByIP();
                    } else {
                        api_scrapper.readCountryFile(jQuery('#countryId'), _single_country_code);
                    }
                    <?php } ?>
                    /*
                    * Pre select Country
                    * */
                    <?php
                    if ($contry_preselct == 'by_contry' && $contry_presel_contry != "" && $query_var != 'jobsearch-location-sett-editor') { ?>
                    if (jobsearch_sloc_country != "") {
                        _single_country_code = jobsearch_is_admin == 1 && jQuery("select[name=contry_presel_contry]").val() != undefined ? jQuery("select[name=contry_presel_contry]").val() : api_scrapper.getCountryCode(jobsearch_sloc_country);
                    } else {
                        _single_country_code = '<?php echo $contry_presel_contry ?>';
                    }

                    if (_single_country_code != "") {
                        api_scrapper.readStateFile(_single_country_code, jQuery("#stateId"));
                    }

                    <?php } ?>
                    <?php if($ajax_flag == false){ ?>
                });
                <?php } ?>

                /*
                 *countries change event
                 **/

                jQuery(document).on('change', '.countries', function () {
                    var _this = jQuery(this);
                    if (_this.val() != 0 && _this.val() != undefined) {
                        var _country_code = '';
                        if (inc_countries != "") {
                            _country_code = api_scrapper.getCountryCode(_this.val());
                        } else {
                            _country_code = jobsearch_is_admin == 1 && _this.find('option:selected').attr("code") != undefined ? _this.find('option:selected').attr("code") : api_scrapper.getCountryCode(_this.val());
                        }
                        api_scrapper.readStateFile(_country_code, jQuery("#stateId"));
                    }
                });

                /*
                * state change events
                * */

                jQuery(document).on('change', '#stateId', function () {
                    var _this = jQuery(this), _single_country_detail;
                    if (jobsearch_sloc_type == 1) {
                        _single_country_detail = jQuery("#countryId").find('option:selected').val();

                    } else if (jobsearch_sloc_type == '2' || jobsearch_sloc_type == '3') {
                        _single_country_detail = jQuery("#countryId").val();
                    } else {
                        if (jobsearch_sloc_country != 0 && contry_preselct != 'by_contry') {
                            _single_country_detail = api_scrapper.getCountryCode(jobsearch_sloc_country);
                        }
                    }

                    if (_this.val() != 0 && _this.val() != undefined) {
                        api_scrapper.readCityOnlyFiles(_single_country_detail, _this.val(), jQuery('#cityId'));
                    }
                });
            </script>
            <?php
        }
    }

    global $jobsearch_gdapi_allocation;
    $jobsearch_gdapi_allocation = new jobsearch_allocation_settings_handle();
}