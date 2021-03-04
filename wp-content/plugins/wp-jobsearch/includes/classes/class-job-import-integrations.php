<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Job_Import_Integrations
{

    // hook things up
    public function __construct()
    {
        add_filter('admin_menu', array($this, 'job_integrations_admin_menu'));
        add_action('wp_ajax_jobsearch_job_integrations_settin_save', array($this, 'integrations_settin_save'));
        add_action('wp_ajax_jobsearch_add_job_import_schedule_call', array($this, 'add_job_import_schedule'));
        add_action('wp_ajax_jobsearch_update_job_import_schedule_call', array($this, 'update_job_import_schedule'));
        add_action('wp_loaded', array($this, 'del_schedlue'));
        add_action('wp_loaded', array($this, 'bulk_schedlue_actions'));
        add_action('wp', array($this, 'indeed_conversion_code'));
    }

    public function job_integrations_admin_menu($param)
    {
        add_submenu_page('edit.php?post_type=job', esc_html__('Import Job Integrations', 'wp-jobsearch'), esc_html__('Import Job Integrations', 'wp-jobsearch'), 'manage_options', 'import-job-integrations', array($this, 'import_job_integrations'));
    }

    public function import_job_integrations()
    {

        $rand_id = rand(10000000, 99999999);
        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        $all_schedules = get_option('jobsearch_job_integration_schedules');
        ?>
        <div class="importjobs-onesection-con">
            <div class="importjobs-tabs-con">
                <a href="<?php echo add_query_arg(array('page' => 'import-job-integrations', 'tab' => 'settings'), admin_url('edit.php?post_type=job')) ?>"<?php echo($tab == 'settings' ? ' class="active-tab"' : '') ?>><?php esc_html_e('Settings', 'wp-jobsearch') ?></a>
                <a href="<?php echo add_query_arg(array('page' => 'import-job-integrations', 'tab' => 'schedule-imports'), admin_url('edit.php?post_type=job')) ?>"<?php echo($tab == 'schedule-imports' ? ' class="active-tab"' : '') ?>><?php esc_html_e('Schedule Imports', 'wp-jobsearch') ?></a>
            </div>
            <?php
            if ($tab == 'schedule-imports') {

                if (isset($_GET['add-schedule']) && $_GET['add-schedule'] == 'true') {
                    ?>
                    <div class="import-add-schedule">
                        <form method="post">
                            <div class="integrations-schedule-con">
                                <div class="integrations-setins-hding">
                                    <h2><?php esc_html_e('Add new Schedule', 'wp-jobsearch') ?></h2></div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Import From', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <select name="schedule_import_from">
                                            <option value="indeed"><?php esc_html_e('Indeed', 'wp-jobsearch') ?></option>
                                            <option value="ziprecruiter"><?php esc_html_e('Ziprecruiter', 'wp-jobsearch') ?></option>
                                            <option value="careerjet"><?php esc_html_e('CareerJet', 'wp-jobsearch') ?></option>
                                            <option value="careerbuilder"><?php esc_html_e('CareerBuilder', 'wp-jobsearch') ?></option>
                                            <?php do_action('jobsearch_schedule_jobs_form_type_opts_after') ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Keyword', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="schedule_import_keyword">
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Location', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="schedule_import_location">
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Posted on', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <select name="schedule_import_on">
                                            <option value="3"><?php esc_html_e('3 Days', 'wp-jobsearch') ?></option>
                                            <option value="7"><?php esc_html_e('7 Days', 'wp-jobsearch') ?></option>
                                            <option value="30"><?php esc_html_e('30 Days', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Expired on', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="schedule_import_expire_on">
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Posted By', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <?php
                                        jobsearch_get_custom_post_field('', 'employer', esc_html__('employer', 'wp-jobsearch'), 'job_username', 'job_username');
                                        ?>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Status', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <select name="schedule_status">
                                            <option value="active"><?php esc_html_e('Active', 'wp-jobsearch') ?></option>
                                            <option value="inactive"><?php esc_html_e('Inactive', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="fields-save-buttoncon">
                                    <input type="hidden" name="action" value="jobsearch_add_job_import_schedule_call">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-addjobimport-schedule button-primary"><?php esc_html_e('Add Schedule', 'wp-jobsearch') ?></a>
                                    <strong class="savesettins-loder"></strong>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php
                } else if (isset($_GET['update-schedule']) && $_GET['update-schedule'] == 'true') {
                    $upschedule_id = isset($_GET['id']) ? $_GET['id'] : '';
                    if ($upschedule_id != '' && isset($all_schedules[$upschedule_id])) {
                        $schedule_itm = $all_schedules[$upschedule_id];

                        $import_from = isset($schedule_itm['schedule_import_from']) ? $schedule_itm['schedule_import_from'] : '';
                        $import_on_days = isset($schedule_itm['schedule_import_on']) ? $schedule_itm['schedule_import_on'] : '';
                        $import_location = isset($schedule_itm['schedule_import_location']) ? $schedule_itm['schedule_import_location'] : '';
                        $import_keyword = isset($schedule_itm['schedule_import_keyword']) ? $schedule_itm['schedule_import_keyword'] : '';
                        $import_expired_days = isset($schedule_itm['schedule_import_expire_on']) ? $schedule_itm['schedule_import_expire_on'] : '';
                        $import_company_id = isset($schedule_itm['job_username']) ? $schedule_itm['job_username'] : '';
                        $import_status = isset($schedule_itm['schedule_status']) ? $schedule_itm['schedule_status'] : '';
                        ?>
                        <div class="import-add-schedule">
                            <form method="post">
                                <div class="integrations-schedule-con">
                                    <div class="integrations-setins-hding">
                                        <h2><?php esc_html_e('Update Schedule', 'wp-jobsearch') ?></h2></div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Import From', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <select name="schedule_import_from">
                                                <option value="indeed"<?php echo($import_from == 'indeed' ? ' selected' : '') ?>><?php esc_html_e('Indeed', 'wp-jobsearch') ?></option>
                                                <option value="ziprecruiter"<?php echo($import_from == 'ziprecruiter' ? ' selected' : '') ?>><?php esc_html_e('Ziprecruiter', 'wp-jobsearch') ?></option>
                                                <option value="careerjet"<?php echo($import_from == 'careerjet' ? ' selected' : '') ?>><?php esc_html_e('CareerJet', 'wp-jobsearch') ?></option>
                                                <option value="careerbuilder"<?php echo($import_from == 'careerbuilder' ? ' selected' : '') ?>><?php esc_html_e('CareerBuilder', 'wp-jobsearch') ?></option>
                                                <?php do_action('jobsearch_schedule_jobs_form_type_opts_after', $import_from) ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Keyword', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <input type="text" name="schedule_import_keyword"
                                                   value="<?php echo($import_keyword) ?>">
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Location', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <input type="text" name="schedule_import_location"
                                                   value="<?php echo($import_location) ?>">
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Posted on', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <select name="schedule_import_on">
                                                <option value="3"<?php echo($import_on_days == '3' ? ' selected' : '') ?>><?php esc_html_e('3 Days', 'wp-jobsearch') ?></option>
                                                <option value="7"<?php echo($import_on_days == '7' ? ' selected' : '') ?>><?php esc_html_e('7 Days', 'wp-jobsearch') ?></option>
                                                <option value="30"<?php echo($import_on_days == '30' ? ' selected' : '') ?>><?php esc_html_e('30 Days', 'wp-jobsearch') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Expired on', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <input type="text" name="schedule_import_expire_on"
                                                   value="<?php echo($import_expired_days) ?>">
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Posted By', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            jobsearch_get_custom_post_field($import_company_id, 'employer', esc_html__('employer', 'wp-jobsearch'), 'job_username', 'job_username');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Status', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <select name="schedule_status">
                                                <option value="active"<?php echo($import_status == 'active' ? ' selected' : '') ?>><?php esc_html_e('Active', 'wp-jobsearch') ?></option>
                                                <option value="inactive"<?php echo($import_status == 'inactive' ? ' selected' : '') ?>><?php esc_html_e('Inactive', 'wp-jobsearch') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="fields-save-buttoncon">
                                        <input type="hidden" name="id" value="<?php echo($upschedule_id) ?>">
                                        <input type="hidden" name="action"
                                               value="jobsearch_update_job_import_schedule_call">
                                        <a href="javascript:void(0);"
                                           class="jobsearch-updatejobimport-schedule button-primary"><?php esc_html_e('Update Schedule', 'wp-jobsearch') ?></a>
                                        <strong class="savesettins-loder"></strong>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        <p><?php esc_html_e('No Schedule found.', 'wp-jobsearch') ?></p>
                        <?php
                    }
                } else {
                    ?>
                    <div class="wrap">
                        <h1>
                            <?php esc_html_e('Import', 'wp-jobsearch') ?>
                            <a class="add-new-h2"
                               href="<?php echo add_query_arg(array('add-schedule' => 'true')) ?>"><?php esc_html_e('Schedule New Import', 'wp-jobsearch') ?></a>
                        </h1>
                        <form method="post" id="posts-filter">
                            <div class="tablenav top">
                                <div class="alignleft actions">
                                    <select id="jobsearch-action1" name="action">
                                        <option selected="selected"
                                                value=""><?php esc_html_e('Bulk Actions', 'wp-jobsearch') ?></option>
                                        <option value="delete"><?php esc_html_e('Delete', 'wp-jobsearch') ?></option>
                                    </select>
                                    <?php wp_nonce_field('jobsearch-bulk-schedule-acts', '_bulk_schedule_actions_nonce') ?>
                                    <input type="submit" class="button-secondary action" id="jobsearch-doaction1"
                                           value="<?php esc_html_e('Apply', 'wp-jobsearch') ?>">

                                </div>

                            </div>

                            <div class="clear">&nbsp;</div>

                            <table cellspacing="0" class="widefat post fixed wp-list-table">
                                <thead>
                                <tr>
                                    <th class="manage-column column-cb check-column" scope="col"><input type="checkbox">
                                    </th>
                                    <th class="column-primary"
                                        scope="col"><?php esc_html_e('Schedule ID', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Keyword', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Location', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Next Run', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Status', 'wp-jobsearch') ?></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th class="manage-column column-cb check-column" scope="col"><input type="checkbox">
                                    </th>
                                    <th class="column-primary"
                                        scope="col"><?php esc_html_e('Schedule ID', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Keyword', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Location', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Next Run', 'wp-jobsearch') ?></th>
                                    <th class="" scope="col"><?php esc_html_e('Status', 'wp-jobsearch') ?></th>
                                </tr>
                                </tfoot>

                                <tbody id="the-list">
                                <?php
                                if (!empty($all_schedules)) {
                                    $current_time = current_time('timestamp');

                                    foreach ($all_schedules as $schedule_id => $schedule_itm) {

                                        $import_from = isset($schedule_itm['schedule_import_from']) ? $schedule_itm['schedule_import_from'] : '';
                                        $import_on_time = isset($schedule_itm['next_run']) ? $schedule_itm['next_run'] : '';
                                        ?>
                                        <tr valign="top" class="alternate  author-self status-publish iedit">
                                            <th class="check-column" scope="row">
                                                <input type="checkbox" value="<?php echo($schedule_id) ?>"
                                                       name="item[]">
                                            </th>
                                            <td class="column-title column-primary">
                                                <strong><a title="<?php esc_html_e('Edit', 'wp-jobsearch') ?>"
                                                           href="<?php echo add_query_arg(array('update-schedule' => 'true', 'id' => $schedule_id)) ?>"
                                                           class="jobsearch-row-title"><?php echo ucfirst($import_from) ?><?php printf(esc_html__('ID %s', 'wp-jobsearch'), $schedule_id) ?></a></strong>
                                                <div class="row-actions">
                                                    <span class="edit"><a
                                                                href="<?php echo add_query_arg(array('update-schedule' => 'true', 'id' => $schedule_id)) ?>"><?php esc_html_e('Edit', 'wp-jobsearch') ?></a> | </span>
                                                    <span class=""><a
                                                                href="<?php echo esc_url(wp_nonce_url(add_query_arg('schedule-id', $schedule_id), 'jobsearch_del_schedule_nonce', '_del_schedule_nonce')); ?>"
                                                                title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"
                                                                class="jobsearch-delete"><?php esc_html_e('Delete', 'wp-jobsearch') ?></a></span>
                                                </div>
                                                <button type="button" class="toggle-row">
                                                    <span class="screen-reader-text"><?php esc_html_e('Show more details', 'wp-jobsearch') ?></span>
                                                </button>
                                            </td>
                                            <td data-colname="Keyword">
                                                <?php
                                                if ($schedule_itm['schedule_import_keyword']) {
                                                    echo($schedule_itm['schedule_import_keyword']);
                                                }
                                                ?>
                                            </td>
                                            <td data-colname="Location">
                                                <?php
                                                if ($schedule_itm['schedule_import_location']) {
                                                    echo($schedule_itm['schedule_import_location']);
                                                }
                                                ?>
                                            </td>
                                            <td data-colname="Next Run">
                                                <?php echo date_i18n(get_option('date_format'), $import_on_time) ?> <?php echo date_i18n(get_option('time_format'), $import_on_time) ?>
                                                <br>
                                                <?php
                                                if ($import_on_time > $current_time) {
                                                    echo human_time_diff($current_time, $import_on_time);
                                                }
                                                ?>
                                            </td>
                                            <td data-colname="Status">
                                                <?php
                                                if (isset($schedule_itm['schedule_status']) && $schedule_itm['schedule_status'] == 'inactive') {
                                                    esc_html_e('Inactive', 'wp-jobsearch');
                                                } else {
                                                    esc_html_e('Active', 'wp-jobsearch');
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>

                            </table>

                            <div class="tablenav">
                                <div class="tablenav-pages">
                                </div>


                                <div class="alignleft actions">
                                    <select id="jobsearch-action2" name="action2">
                                        <option selected="selected"
                                                value=""><?php esc_html_e('Bulk Actions', 'wp-jobsearch') ?></option>
                                        <option value="delete"><?php esc_html_e('Delete', 'wp-jobsearch') ?></option>
                                    </select>
                                    <input type="submit" class="button action" id="jobsearch-doaction2"
                                           value="<?php esc_html_e('Apply', 'wp-jobsearch') ?>">

                                    <br class="clear">
                                </div>

                                <br class="clear">
                            </div>
                            <input type="hidden" name="jobsearch_schedule_bulk_acts" value="1">

                        </form>
                    </div>
                    <?php
                }
            } else {
                $indeed_import_jobs = get_option('jobsearch_integration_indeed_jobs');
                $indeed_publisher_id = get_option('jobsearch_integration_indeed_publisherid');
                $indeed_conversion = get_option('jobsearch_integration_indeed_conversion');
                $indeed_conversion_id = get_option('jobsearch_integration_indeed_conversionid');
                $indeed_conversion_label = get_option('jobsearch_integration_indeed_conversion_labl');

                $ziprecruiter_import_jobs = get_option('jobsearch_integration_ziprecruiter_jobs');
                $ziprecruiter_api_key = get_option('jobsearch_integration_ziprecruiter_api');
                $careerjet_import_jobs = get_option('jobsearch_integration_careerjet_jobs');
                $careerjet_api_key = get_option('jobsearch_integration_careerjet_affid');
                $careerbuild_import_jobs = get_option('jobsearch_integration_careerbuild_jobs');
                $careerbuild_api_key = get_option('jobsearch_integration_careerbuild_api');
                ?>
                <div class="job-integrations-setins">
                    <form method="post">
                        <div class="integrations-setins-con">
                            <div class="integrations-setins-section">
                                <div class="integrations-setins-hding">
                                    <h2><?php esc_html_e('Indeed Jobs import Settings', 'wp-jobsearch') ?></h2></div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Indeed Jobs import', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <div class="onoff-button"><input id="onoff-indeed-<?php echo($rand_id) ?>"
                                                                         type="checkbox"<?php echo($indeed_import_jobs == 'on' ? ' checked' : '') ?>><label
                                                    for="onoff-indeed-<?php echo($rand_id) ?>"></label><input
                                                    type="hidden" name="jobsearch_integration_indeed_jobs"
                                                    value="<?php echo($indeed_import_jobs) ?>"></div>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Indeed Publisher ID', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_indeed_publisherid"
                                               value="<?php echo($indeed_publisher_id) ?>">
                                        <p><?php _e('You can get publisher ID from <a href="https://www.indeed.com/publisher" target="_blank">https://www.indeed.com/publisher</a>', 'wp-jobsearch') ?></p>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Enable conversion tracking', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <div class="onoff-button"><input id="onoff-indeed-conv-<?php echo($rand_id) ?>"
                                                                         type="checkbox"<?php echo($indeed_conversion == 'on' ? ' checked' : '') ?>><label
                                                    for="onoff-indeed-conv-<?php echo($rand_id) ?>"></label><input
                                                    type="hidden" name="jobsearch_integration_indeed_conversion"
                                                    value="<?php echo($indeed_conversion) ?>"></div>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Indeed Conversion ID', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_indeed_conversionid"
                                               value="<?php echo($indeed_conversion_id) ?>">
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Indeed Conversion Label', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_indeed_conversion_labl"
                                               value="<?php echo($indeed_conversion_label) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="integrations-setins-section">
                                <div class="integrations-setins-hding">
                                    <h2><?php esc_html_e('Ziprecruiter Jobs import Settings', 'wp-jobsearch') ?></h2>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Ziprecruiter Jobs import', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <div class="onoff-button"><input id="onoff-zip-<?php echo($rand_id) ?>"
                                                                         type="checkbox"<?php echo($ziprecruiter_import_jobs == 'on' ? ' checked' : '') ?>><label
                                                    for="onoff-zip-<?php echo($rand_id) ?>"></label><input type="hidden"
                                                                                                           name="jobsearch_integration_ziprecruiter_jobs"
                                                                                                           value="<?php echo($ziprecruiter_import_jobs) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('Ziprecruiter API', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_ziprecruiter_api"
                                               value="<?php echo($ziprecruiter_api_key) ?>">
                                        <p><?php _e('You can get API key from <a href="https://www.ziprecruiter.com/zipsearch" target="_blank">https://www.ziprecruiter.com/zipsearch</a>', 'wp-jobsearch') ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="integrations-setins-section">
                                <div class="integrations-setins-hding">
                                    <h2><?php esc_html_e('CareerJet Jobs import Settings', 'wp-jobsearch') ?></h2></div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('CareerJet Jobs import', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <div class="onoff-button"><input id="onoff-career-<?php echo($rand_id) ?>"
                                                                         type="checkbox"<?php echo($careerjet_import_jobs == 'on' ? ' checked' : '') ?>><label
                                                    for="onoff-career-<?php echo($rand_id) ?>"></label><input
                                                    type="hidden" name="jobsearch_integration_careerjet_jobs"
                                                    value="<?php echo($careerjet_import_jobs) ?>"></div>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('CareerJet AFFID', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_careerjet_affid"
                                               value="<?php echo($careerjet_api_key) ?>">
                                        <p><?php _e('You can get AFFID from <a href="https://www.careerjet.com/contact-us" target="_blank">https://www.careerjet.com/contact-us</a>', 'wp-jobsearch') ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="integrations-setins-section">
                                <div class="integrations-setins-hding">
                                    <h2><?php esc_html_e('CareerBuilder Jobs import Settings', 'wp-jobsearch') ?></h2>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('CareerBuilder Jobs import', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <div class="onoff-button"><input id="onoff-careerbuild-<?php echo($rand_id) ?>"
                                                                         type="checkbox"<?php echo($careerbuild_import_jobs == 'on' ? ' checked' : '') ?>><label
                                                    for="onoff-careerbuild-<?php echo($rand_id) ?>"></label><input
                                                    type="hidden" name="jobsearch_integration_careerbuild_jobs"
                                                    value="<?php echo($careerbuild_import_jobs) ?>"></div>
                                    </div>
                                </div>
                                <div class="jobsearch-element-field">
                                    <div class="elem-label">
                                        <label><?php esc_html_e('CareerBuilder API Key', 'wp-jobsearch') ?></label>
                                    </div>
                                    <div class="elem-field">
                                        <input type="text" name="jobsearch_integration_careerbuild_api"
                                               value="<?php echo($careerbuild_api_key) ?>">
                                        <p><?php _e('You can get API key from <a href="https://developer.careerbuilder.com/" target="_blank">https://developer.careerbuilder.com/</a>', 'wp-jobsearch') ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                            do_action('jobsearch_jobimport_api_integrations_bklist_after');
                            ?>
                            <div class="fields-save-buttoncon">
                                <input type="hidden" name="action" value="jobsearch_job_integrations_settin_save">
                                <a href="javascript:void(0);"
                                   class="jobsearch-save-integrationsetins button-primary"><?php esc_html_e('Save Settings', 'wp-jobsearch') ?></a>
                                <strong class="savesettins-loder"></strong>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public function integrations_settin_save()
    {

        if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_job_integrations_settin_save') {
            foreach ($_POST as $post_key => $post_val) {
                if ($post_key != 'action') {
                    update_option($post_key, $post_val);
                }
            }

            echo json_encode(array('msg' => esc_html__('Settings Saved', 'wp-jobsearch')));
            die;
        }
    }

    public function add_job_import_schedule()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_add_job_import_schedule_call') {
            unset($_POST['action']);

            $import_data = $_POST;
            $rand_id = rand(10000000, 99999999);

            if (isset($_POST['schedule_import_on']) && $_POST['schedule_import_on'] > 0) {
                $to_import_days = absint($_POST['schedule_import_on']);
                $to_schedule_time = strtotime("+" . $to_import_days . " days", current_time('timestamp'));
                $import_data['next_run'] = $to_schedule_time;
            }

            $all_schedules = get_option('jobsearch_job_integration_schedules');
            $all_schedules = empty($all_schedules) ? array() : $all_schedules;

            $all_schedules[$rand_id] = $import_data;
            update_option('jobsearch_job_integration_schedules', $all_schedules);

            echo json_encode(array('msg' => esc_html__('Schedule added', 'wp-jobsearch'), 'redirect' => add_query_arg(array('page' => 'import-job-integrations', 'tab' => 'schedule-imports'), admin_url('edit.php?post_type=job'))));
            die;
        }
    }

    public function update_job_import_schedule()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_update_job_import_schedule_call') {
            unset($_POST['action']);

            $import_data = $_POST;
            $rand_id = absint($_POST['id']);

            $all_schedules = get_option('jobsearch_job_integration_schedules');
            if (isset($all_schedules[$rand_id]) && !empty($all_schedules[$rand_id])) {
                foreach ($all_schedules[$rand_id] as $schedule_key => $sched_val) {
                    if (isset($import_data[$schedule_key])) {
                        $all_schedules[$rand_id][$schedule_key] = $import_data[$schedule_key];
                    }
                }
                update_option('jobsearch_job_integration_schedules', $all_schedules);
            }

            echo json_encode(array('msg' => esc_html__('Schedule updated', 'wp-jobsearch'), 'redirect' => add_query_arg(array('page' => 'import-job-integrations', 'tab' => 'schedule-imports'), admin_url('edit.php?post_type=job'))));
            die;
        }
    }

    public function del_schedlue()
    {
        if (isset($_GET['schedule-id']) && isset($_GET['_del_schedule_nonce'])) { // WPCS: input var ok, CSRF ok.
            if (!wp_verify_nonce(sanitize_key(wp_unslash($_GET['_del_schedule_nonce'])), 'jobsearch_del_schedule_nonce')) { // WPCS: input var ok, CSRF ok.
                wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'wp-jobsearch'));
            }

            $schedule_id = sanitize_text_field(wp_unslash($_GET['schedule-id'])); // WPCS: input var ok, CSRF ok.
            $all_schedules = get_option('jobsearch_job_integration_schedules');
            if (isset($all_schedules[$schedule_id])) {
                unset($all_schedules[$schedule_id]);
                update_option('jobsearch_job_integration_schedules', $all_schedules);
            }
        }
    }

    public function bulk_schedlue_actions()
    {

        if (isset($_POST['jobsearch_schedule_bulk_acts']) && isset($_POST['_bulk_schedule_actions_nonce'])) {
            if (!wp_verify_nonce(sanitize_key(wp_unslash($_POST['_bulk_schedule_actions_nonce'])), 'jobsearch-bulk-schedule-acts')) { // WPCS: input var ok, CSRF ok.
                wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'wp-jobsearch'));
            }

            $all_items = isset($_POST['item']) ? $_POST['item'] : '';

            if (!empty($all_items) && isset($_POST['action']) && $_POST['action'] == 'delete') {
                $all_schedules = get_option('jobsearch_job_integration_schedules');
                foreach ($all_items as $schedule_id) {
                    if (isset($all_schedules[$schedule_id])) {
                        unset($all_schedules[$schedule_id]);
                    }
                }
                update_option('jobsearch_job_integration_schedules', $all_schedules);
            }
        }
    }

    public function indeed_conversion_code()
    {
        $indeed_conversion = get_option('jobsearch_integration_indeed_conversion');
        if ($indeed_conversion == 'on') {
            add_action('wp_footer', function () {
                $indeed_conversion_id = get_option('jobsearch_integration_indeed_conversionid');
                $indeed_conversion_label = get_option('jobsearch_integration_indeed_conversion_labl');
                ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    var indeed_conversion_id = '<?php echo($indeed_conversion_id) ?>';
                    var indeed_conversion_label = '<?php echo($indeed_conversion_label) ?>';
                    /* ]]> */
                </script>
                <script type="text/javascript" src="//conv.indeed.com/pagead/conversion.js">
                </script>
                <noscript>
                    <img height=1 width=1 border=0
                         src="//conv.indeed.com/pagead/conv/<?php echo($indeed_conversion_id) ?>/?script=0">
                </noscript>
                <?php
            }, 20, 1);
        }
    }

}

return new Jobsearch_Job_Import_Integrations();
