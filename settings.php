<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    
	$settings->add(new admin_setting_configtext('block_livedesk_resolving_post_release', get_string('block_livedesk_resolving_post_release', 'block_livedesk'),
                   get_string('block_livedesk_resolving_post_release_comment', 'block_livedesk'), 5, PARAM_INT));
    
    $settings->add(new admin_setting_configtext('block_livedesk_attender_release_time', get_string('block_livedesk_attender_release_time', 'block_livedesk'),
                   get_string('block_livedesk_attender_release_time_comment', 'block_livedesk'), 10, PARAM_INT));
                   
    $settings->add(new admin_setting_configtext('block_livedesk_stack_over_time', get_string('block_livedesk_stack_over_time', 'block_livedesk'),
                   get_string('block_livedesk_stack_over_time_comment', 'block_livedesk'), 120, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_max_stack_size', get_string('block_livedesk_max_stack_size', 'block_livedesk'),
                   get_string('block_livedesk_max_stack_size_comment', 'block_livedesk'), 100, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_keepalive_delay', get_string('block_livedesk_keepalive_delay', 'block_livedesk'),
                   get_string('block_livedesk_keepalive_delay_comment', 'block_livedesk'), 300, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk_refresh', get_string('block_livedesk_refresh', 'block_livedesk'),
                   get_string('block_livedesk_refresh_comment', 'block_livedesk'), 10, PARAM_INT));
        
    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_start_h', 'block_livedesk_service_timerange_start_m', get_string('block_livedesk_service_timerange_start', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_start_comment', 'block_livedesk'),null));
    
    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_end_h', 'block_livedesk_service_timerange_end_m', get_string('block_livedesk_service_timerange_end', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_end_comment', 'block_livedesk'),null));               

    $settings->add(new admin_setting_configtext('block_livedesk_notification_refresh_time', get_string('block_livedesk_notification_refresh_time', 'block_livedesk'),
                   get_string('block_livedesk_notification_refresh_time_comment', 'block_livedesk'), 10000));               

    $settings->add(new admin_setting_configtext('block_livedesk_notification_onscreen_time', get_string('block_livedesk_notification_onscreen_time', 'block_livedesk'),
                   get_string('block_livedesk_notification_onscreen_time_comment', 'block_livedesk'), 5000));               

	$options = array();
	$options['0'] = get_string('allusers', 'block_livedesk');
	$options['capability'] = get_string('capabilitycontrol', 'block_livedesk');
	$options['profilefield'] = get_string('profilefieldcontrol', 'block_livedesk');
    $settings->add(new admin_setting_configselect('block_livedesk_live_notification_control', get_string('block_livedesk_live_notification_control', 'block_livedesk'),
                   get_string('block_livedesk_live_notification_control_comment', 'block_livedesk'), 'capability', $options));

    $settings->add(new admin_setting_configtext('block_livedesk_live_notification_control_value', get_string('block_livedesk_live_notification_control_value', 'block_livedesk'),
                   get_string('block_livedesk_live_notification_control_value_comment', 'block_livedesk'), 'block/livedesk:managelivedesks', PARAM_TEXT));
}

