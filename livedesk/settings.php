<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    
	$settings->add(new admin_setting_configtext('block_livedesk_resolving_post_release', get_string('block_livedesk_resolving_post_release', 'block_livedesk'),
                   get_string('block_livedesk_resolving_post_release_comment', 'block_livedesk'), 60, PARAM_INT));
    
    $settings->add(new admin_setting_configtext('block_livedesk_attendee_release_time', get_string('block_livedesk_attendee_release_time', 'block_livedesk'),
                   get_string('block_livedesk_attendee_release_time_comment', 'block_livedesk'), 5, PARAM_INT));
                   
    $settings->add(new admin_setting_configtext('block_livedesk_stack_over_time', get_string('block_livedesk_stack_over_time', 'block_livedesk'),
                   get_string('block_livedesk_stack_over_time_comment', 'block_livedesk'), 5, PARAM_INT));
    
    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_start_h','block_livedesk_service_timerangestart_m', get_string('block_livedesk_service_timerange_start', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_start_comment', 'block_livedesk'),null));
    
    $settings->add(new admin_setting_configtime('block_livedesk_service_timerange_end_h','block_livedesk_desk_service_timerange_end_m', get_string('block_livedesk_service_timerange_end', 'block_livedesk'),
                   get_string('block_livedesk_service_timerange_end_comment', 'block_livedesk'),null));               
}

