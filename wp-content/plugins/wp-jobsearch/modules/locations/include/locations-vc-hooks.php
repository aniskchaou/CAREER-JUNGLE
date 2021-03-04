<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_allocations_vc_hooks;

if (!class_exists('jobsearch_allocations_vc_hooks')) {

    class jobsearch_allocations_vc_hooks
    {

        // hook things up
        public function __construct()
        {
            $this->vc_add_shortcode_param();
        }

        public function vc_add_shortcode_param()
        {
            if (function_exists('vc_add_shortcode_param')) {
                vc_add_shortcode_param('jobsearch_gapi_locs', array($this, 'apiloc_dropdowns_field'));
            }
        }

        public function apiloc_dropdowns_field($settings, $value)
        {
            global $wpdb;
            //ini_set('display_errors', 1);
            //ini_set('display_startup_errors', 1);
            //error_reporting(E_ALL);
            $dropdown_class = 'wpb_vc_param_value wpb-textinput ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field';
            $api_contries_list = $settings['api_contry_list'];

            $rand_num = rand(1000000, 9999999);

            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

            $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';

            $nameof_singl_contry = '';
            $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';

            if ($contry_singl_contry != '' && ($loc_optionstype == '2' || $loc_optionstype == '3')) {
                $nameof_singl_contry = isset($api_contries_list[$contry_singl_contry]) ? $api_contries_list[$contry_singl_contry] : '';
            }

            if ($value != '' && !is_array($value)) {
                $value = explode('|', $value);
            }
            $saved_country = isset($value[0]) ? $value[0] : '';
            $saved_state = isset($value[1]) ? $value[1] : '';
            $saved_city = isset($value[2]) ? $value[2] : '';

            ob_start();
            ?>
            <script>
                var jobsearch_vc_custm_getJSON = function (url, callback) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);
                    xhr.responseType = 'json';
                    xhr.onload = function () {
                        var status = xhr.status;
                        if (status === 200) {
                            callback(null, xhr.response);
                        } else {
                            callback(status, xhr.response);
                        }
                    };
                    xhr.send();
                };

                function all_loc_str_snd_<?php echo($rand_num) ?>() {
                    var loc_contry = '';
                    if (jQuery('#countryId').length > 0) {
                        loc_contry = jQuery('#countryId').val();
                    }

                    var loc_state = jQuery('#stateId').val();

                    var loc_city = '';
                    if (jQuery('#cityId').val() != "pls_wait") {
                        loc_city = jQuery('#cityId').val();
                    }

                    var loc_str = '';
                    loc_str = loc_contry + '|' + loc_state + '|' + loc_city;
                    jQuery('#api_all_locs_<?php echo($rand_num) ?>').val(loc_str);
                }

                $('#countryId').on('change', function () {
                    all_loc_str_snd_<?php echo($rand_num) ?>();
                });
                $(document).on('change', '#stateId', function () {
                    all_loc_str_snd_<?php echo($rand_num) ?>();
                });
                $(document).on('change', '#cityId', function () {
                    all_loc_str_snd_<?php echo($rand_num) ?>();
                });
            </script>
            <?php
            if ($loc_optionstype == '0' || $loc_optionstype == '1') { ?>
                <div class="jobsearch-vcloc-dropdwn-con">
                    <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                    <select id="countryId" class="countries">
                        <?php
                        foreach ($api_contries_list as $dr_opt_key => $dr_opt_val) { ?>
                            <option value="<?php echo esc_html($dr_opt_val) ?>"
                                    code="<?php echo esc_html($dr_opt_key) ?>" <?php echo($dr_opt_val == $saved_country ? 'selected="selected"' : '') ?>
                                    data-countryid="<?php echo esc_html($dr_opt_key) ?>"><?php echo esc_html($dr_opt_val) ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <?php if ($loc_optionstype != '4') { ?>
            <div class="jobsearch-vcloc-dropdwn-con">
                <label><?php esc_html_e('State', 'wp-jobsearch') ?></label>
                <?php
                $single_country_code = '';
                //$total_countries = read_location_file('countries.json');
                $total_countries = $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` order by name ");
                
                $myArray = $total_countries;
                $valueToCheckAgainst = $nameof_singl_contry != "" ? trim($nameof_singl_contry) : $saved_country;

                $myNewArray = array_filter($myArray, function ($value) use ($valueToCheckAgainst) {
                    if ($value->name == $valueToCheckAgainst) {
                        return $value->code;
                    }
                });
                if (count($myNewArray) > 0) {
                    $arrayKey = array_keys($myNewArray);
                    $single_country_code = $myNewArray[$arrayKey[0]]->name;
                }
                $api_states_list = jobsearch_allocation_settings_handle::get_states($single_country_code);

                if ($loc_optionstype == '2' || $loc_optionstype == '3') { ?>
                    <input type="hidden" id="countryId" value="<?php echo($single_country_code) ?>">
                <?php } ?>
                <select id="stateId">
                    <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                    <?php
                    if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                        $states_cntry = $nameof_singl_contry;
                    } else {
                        $states_cntry = $saved_country;
                    }

                    if ($states_cntry != '') {
                        if (count($api_states_list) > 0) {
                            foreach ($api_states_list as $api_state_key => $api_state_val) { ?>
                                <option value="<?php echo($api_state_val->state_name) ?>" <?php echo($api_state_val->state_name == $saved_state ? 'selected="selected"' : '') ?>><?php echo($api_state_val->state_name) ?></option>
                                <?php
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        <?php } ?>

            <?php
            if ($loc_optionstype == '4') {
                $api_cities_list = jobsearch_allocation_settings_handle::get_cities_by_state_ids();
            } else {
                $api_cities_list = jobsearch_allocation_settings_handle::get_cities($single_country_code, $saved_state);
            }
            if ($loc_optionstype == '1' || $loc_optionstype == '2' || $loc_optionstype == '4') { ?>
                <div class="jobsearch-vcloc-dropdwn-con">
                    <label><?php esc_html_e('City', 'wp-jobsearch') ?></label>
                    <select id="cityId">
                        <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                        <?php
                        if ($loc_optionstype == '4') {
                            foreach ($api_cities_list as $api_city_key => $api_city_val) { ?>
                                <option value="<?php echo($api_city_val) ?>" <?php echo($api_city_val == $saved_city ? 'selected="selected"' : '') ?>
                                        data-cityid="<?php echo($api_city_key) ?>"><?php echo($api_city_val) ?></option>
                                <?php
                            }
                        } else if (isset($api_states_list) && !empty($api_states_list) && $saved_state != '') {
                            foreach ($api_cities_list as $api_city_key => $api_city_val) { ?>
                                <option value="<?php echo($api_city_val->city_name) ?>" <?php echo($api_city_val->city_name == $saved_city ? 'selected="selected"' : '') ?>
                                        data-cityid="<?php echo($api_city_key->city_name) ?>"><?php echo($api_city_val->city_name) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
            }

            $saved_value = '';
            if (!empty($value)) {
                $saved_value = implode('|', $value);
            }
            ?>
            <input id="api_all_locs_<?php echo($rand_num) ?>" type="hidden"
                   name="<?php echo esc_html($settings['param_name']) ?>"
                   class="<?php echo esc_html($dropdown_class) ?>" value="<?php echo($saved_value) ?>">
            <?php
            $dropdown_html = ob_get_clean();
            return $dropdown_html;
        }
    }

    $jobsearch_allocations_vc_hooks = new jobsearch_allocations_vc_hooks();
}
