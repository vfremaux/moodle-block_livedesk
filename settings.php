<?php

defined('MOODLE_INTERNAL') || die;

if (!isset($CFG->block_livedesk_resolving_post_release)) {
    set_config('block_livedesk_resolving_post_release', 5);
    set_config('block_livedesk_attender_release_time', 10);
    set_config('block_livedesk_stack_over_time', 120);
    set_config('block_livedesk_max_stack_size', 500);
    set_config('block_livedesk_keepalive_delay', 300);
    set_config('block_livedesk_refresh', 10);
    set_config('block_livedesk_service_timerange_start_h', 8);
    set_config('block_livedesk_service_timerange_start_m', 0);
    set_config('block_livedesk_service_timerange_end_h', 17);
    set_config('block_livedesk_service_timerange_end_m', 0);
    set_config('block_livedesk_notification_refresh_time', 10000);
    set_config('block_livedesk_notification_backtrack_range', 300);
    set_config('block_livedesk_notification_onscreen_time', 5000);
    set_config('block_livedesk_live_notification_control', 'capability');
    set_config('block_livedesk_live_notification_control_value', 'block/livedesk:managelivedesks');
}

if ($ADMIN->fulltree) {

    $managerurl = new moodle_url('/blocks/livedesk/manage.php');
    $settings->add(new admin_setting_heading('instancemanager', get_string('instances', 'block_livedesk'),
                   '<a href="'.$managerurl.'" >'.get_string('manageinstances', 'block_livedesk').'</a>'));

    $settings->add(new admin_setting_heading('globalparams', get_string('globalparams', 'block_livedesk'), ''));

    $settings->add(new admin_setting_configtext('block_livedesk_resolving_post_release', get_string('block_livedesk_resolving_post_release', 'block_livedesk'),
                   get_string('block_livedesk_resolving_post_release_comment', 'block_livedesk'), $CFG->block_livedesk_resolving_post_release, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_attender_release_time', get_string('block_livedesk_attender_release_time', 'block_livedesk'),
                   get_string('block_livedesk_attender_release_time_comment', 'block_livedesk'), $CFG->block_livedesk_attender_release_time, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_stack_over_time', get_string('block_livedesk_stack_over_time', 'block_livedesk'),
                   get_string('block_livedesk_stack_over_time_comment', 'block_livedesk'), $CFG->block_livedesk_stack_over_time, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_max_stack_size', get_string('block_livedesk_max_stack_size', 'block_livedesk'),
                   get_string('block_livedesk_max_stack_size_comment', 'block_livedesk'), $CFG->block_livedesk_max_stack_size, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_keepalive_delay', get_string('block_livedesk_keepalive_delay', 'block_livedesk'),
                   get_string('block_livedesk_keepalive_delay_comment', 'block_livedesk'), $CFG->block_livedesk_keepalive_delay, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_refresh', get_string('block_livedesk_refresh', 'block_livedesk'),
                   get_string('block_livedesk_refresh_comment', 'block_livedesk'), $CFG->block_livedesk_refresh, PARAM_INT));

    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_start_h', 'block_livedesk_service_timerange_start_m', get_string('block_livedesk_service_timerange_start', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_start_comment', 'block_livedesk'),null));

    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_end_h', 'block_livedesk_service_timerange_end_m', get_string('block_livedesk_service_timerange_end', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_end_comment', 'block_livedesk'),null));               

    $settings->add(new admin_setting_configtext('block_livedesk_notification_refresh_time', get_string('block_livedesk_notification_refresh_time', 'block_livedesk'),
                   get_string('block_livedesk_notification_refresh_time_comment', 'block_livedesk'), $CFG->block_livedesk_notification_refresh_time));               

    $settings->add(new admin_setting_configtext('block_livedesk_notification_onscreen_time', get_string('block_livedesk_notification_onscreen_time', 'block_livedesk'),
                   get_string('block_livedesk_notification_onscreen_time_comment', 'block_livedesk'), $CFG->block_livedesk_notification_onscreen_time));               

    $settings->add(new admin_setting_configtext('block_livedesk_notification_backtrack_range', get_string('block_livedesk_notification_backtrack_range', 'block_livedesk'),
                   get_string('block_livedesk_notification_backtrack_range_comment', 'block_livedesk'), $CFG->block_livedesk_notification_backtrack_range));               

    $options = array();
    $options['0'] = get_string('allusers', 'block_livedesk');
    $options['capability'] = get_string('capabilitycontrol', 'block_livedesk');
    $options['profilefield'] = get_string('profilefieldcontrol', 'block_livedesk');
    $settings->add(new admin_setting_configselect('block_livedesk_live_notification_control', get_string('block_livedesk_live_notification_control', 'block_livedesk'),
                   get_string('block_livedesk_live_notification_control_comment', 'block_livedesk'), $CFG->block_livedesk_live_notification_control, $options));

    $settings->add(new admin_setting_configtext('block_livedesk_live_notification_control_value', get_string('block_livedesk_live_notification_control_value', 'block_livedesk'),
                   get_string('block_livedesk_live_notification_control_value_comment', 'block_livedesk'), $CFG->block_livedesk_live_notification_control_value, PARAM_TEXT));
}

