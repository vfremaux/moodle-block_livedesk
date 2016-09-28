<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die;

/**
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if ($ADMIN->fulltree) {

    $managerurl = new moodle_url('/blocks/livedesk/manage.php');
    $settings->add(new admin_setting_heading('instancemanager', get_string('instances', 'block_livedesk'),
                   '<a href="'.$managerurl.'" >'.get_string('manageinstances', 'block_livedesk').'</a>'));

    $settings->add(new admin_setting_heading('globalparams', get_string('globalparams', 'block_livedesk'), ''));

    $settings->add(new admin_setting_configtext('block_livedesk/resolving_post_release', get_string('configresolvingpostrelease', 'block_livedesk'),
                   get_string('configresolvingpostrelease_desc', 'block_livedesk'), 5, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk/attender_release_time', get_string('configattenderreleasetime', 'block_livedesk'),
                   get_string('configattenderreleasetime_desc', 'block_livedesk'), 10, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk/stack_over_time', get_string('configstackovertime', 'block_livedesk'),
                   get_string('configstackovertime_desc', 'block_livedesk'), 120, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk/max_stack_size', get_string('configmaxstacksize', 'block_livedesk'),
                   get_string('configmaxstacksize_desc', 'block_livedesk'), 500, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk/keepalive_delay', get_string('configkeepalivedelay', 'block_livedesk'),
                   get_string('configkeepalivedelay_desc', 'block_livedesk'), 300, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_livedesk/refresh', get_string('configrefresh', 'block_livedesk'),
                   get_string('configrefresh_desc', 'block_livedesk'), 10, PARAM_INT));

    $settings->add(new admin_setting_configtime('block_livedesk/service_timerange_start_h', 'block_livedesk_service_timerange_start_m', get_string('configservicetimerangestart', 'block_livedesk'),
                   get_string('configservicetimerangestart_desc', 'block_livedesk'), null));

    $settings->add(new admin_setting_configtime('block_livedesk/service_timerange_end_h', 'block_livedesk_service_timerange_end_m', get_string('configservicetimerangeend', 'block_livedesk'),
                   get_string('configservicetimerangeend_desc', 'block_livedesk'),null));

    $settings->add(new admin_setting_configtext('block_livedesk/notification_refresh_time', get_string('confignotificationrefreshtime', 'block_livedesk'),
                   get_string('confignotificationrefreshtime_desc', 'block_livedesk'), 10000));

    $settings->add(new admin_setting_configtext('block_livedesk/notification_onscreen_time', get_string('confignotificationonscreentime', 'block_livedesk'),
                   get_string('confignotificationonscreentime_desc', 'block_livedesk'), 5000));

    $settings->add(new admin_setting_configtext('block_livedesk/notification_backtrack_range', get_string('confignotificationbacktrackrange', 'block_livedesk'),
                   get_string('confignotificationbacktrackrange_desc', 'block_livedesk'), 300));

    $options = array();
    $options['0'] = get_string('allusers', 'block_livedesk');
    $options['capability'] = get_string('capabilitycontrol', 'block_livedesk');
    $options['profilefield'] = get_string('profilefieldcontrol', 'block_livedesk');
    $settings->add(new admin_setting_configselect('block_livedesk/live_notification_control', get_string('configlivenotificationcontrol', 'block_livedesk'),
                   get_string('configlivenotificationcontrol_desc', 'block_livedesk'), 'capability', $options));

    $settings->add(new admin_setting_configtext('block_livedesk/live_notification_control_value', get_string('configlivenotificationcontrolvalue', 'block_livedesk'),
                   get_string('configlivenotificationcontrolvalue_desc', 'block_livedesk'), 'block/livedesk:managelivedesks', PARAM_TEXT));
}

