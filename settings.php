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

    $key = 'block_livedesk/resolving_post_release';
    $label = get_string('configresolvingpostrelease', 'block_livedesk');
    $desc = get_string('configresolvingpostrelease', 'block_livedesk');
    $default = 5;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $key = 'block_livedesk/attender_release_time';
    $label = get_string('configattenderreleasetime', 'block_livedesk');
    $desc = get_string('configattenderreleasetime_desc', 'block_livedesk');
    $default = 10;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $key = 'block_livedesk/stack_over_time';
    $label = get_string('configstackovertime', 'block_livedesk');
    $desc = get_string('configstackovertime_desc', 'block_livedesk');
    $default = 150;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $key = 'block_livedesk/max_stack_size';
    $label = get_string('configmaxstacksize', 'block_livedesk');
    $desc = get_string('configmaxstacksize_desc', 'block_livedesk');
    $default = 500;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $key = 'block_livedesk/keepalive_delay';
    $label = get_string('configkeepalivedelay', 'block_livedesk');
    $desc = get_string('configkeepalivedelay_desc', 'block_livedesk');
    $default = 300;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $key = 'block_livedesk/refresh';
    $label = get_string('configrefresh', 'block_livedesk');
    $desc = get_string('configrefresh_desc', 'block_livedesk');
    $default = 10;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_INT));

    $keyh = 'block_livedesk/service_timerange_start_h';
    $keym = 'service_timerange_start_m';
    $label = get_string('configservicetimerangestart', 'block_livedesk');
    $desc = get_string('configservicetimerangestart_desc', 'block_livedesk');
    $settings->add(new admin_setting_configtime($keyh, $keym, $label, $desc, null));

    $keyh = 'block_livedesk/service_timerange_end_h';
    $keym = 'service_timerange_end_m';
    $label = get_string('configservicetimerangeend', 'block_livedesk');
    $desc = get_string('configservicetimerangeend_desc', 'block_livedesk');
    $settings->add(new admin_setting_configtime($keyh, $keym, $label, $desc, null));

    $key = 'block_livedesk/notification_refresh_time';
    $label = get_string('confignotificationrefreshtime', 'block_livedesk');
    $desc = get_string('confignotificationrefreshtime_desc', 'block_livedesk');
    $default = 10000;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_livedesk/notification_onscreen_time';
    $label = get_string('confignotificationonscreentime', 'block_livedesk');
    $desc = get_string('confignotificationonscreentime_desc', 'block_livedesk');
    $default = 5000;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_livedesk/notification_backtrack_range';
    $label = get_string('confignotificationbacktrackrange', 'block_livedesk');
    $desc = get_string('confignotificationbacktrackrange_desc', 'block_livedesk');
    $default = 300;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $options = array(
        'bootstrap-v3' => "Boostrap 3",
        'bootstrap-v4' => "Boostrap 4",
        'light' => "Light",
        'metroui' => "Metro",
        'mint' => "Mint",
        'nest' => "Nest",
        'relax' => "Relax",
        'semanticui' => "Semantic",
        'sunset' => "Sunset"
    );
    $key = 'block_livedesk/notification_theme';
    $label = get_string('confignotificationtheme', 'block_livedesk');
    $desc = get_string('confignotificationtheme_desc', 'block_livedesk');
    $default = 'bootstrap-v4';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

    $options = array(
        'top' => get_string('top', 'block_livedesk'),
        'topLeft' => get_string('topleft', 'block_livedesk'),
        'topCenter' => get_string('topcenter', 'block_livedesk'),
        'topRight' => get_string('topright', 'block_livedesk'),
        'center' => get_string('center', 'block_livedesk'),
        'centerLeft' => get_string('centerleft', 'block_livedesk'),
        'centerRight' => get_string('centerright', 'block_livedesk'),
        'bottom' => get_string('bottom', 'block_livedesk'),
        'bottomLeft' => get_string('bottomleft', 'block_livedesk'),
        'bottomCenter' => get_string('bottomcenter', 'block_livedesk'),
        'bottomRight' => get_string('bottomright', 'block_livedesk')
    );
    $key = 'block_livedesk/notification_layout';
    $label = get_string('confignotificationlayout', 'block_livedesk');
    $desc = get_string('confignotificationlayout_desc', 'block_livedesk');
    $default = 'topRight';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

    $options = array();
    $options['0'] = get_string('allusers', 'block_livedesk');
    $options['capability'] = get_string('capabilitycontrol', 'block_livedesk');
    $options['profilefield'] = get_string('profilefieldcontrol', 'block_livedesk');

    $key = 'block_livedesk/live_notification_control';
    $label = get_string('configlivenotificationcontrol', 'block_livedesk');
    $desc = get_string('configlivenotificationcontrol_desc', 'block_livedesk');
    $default = 'capability';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

    $key = 'block_livedesk/live_notification_control_value';
    $label = get_string('configlivenotificationcontrolvalue', 'block_livedesk');
    $desc = get_string('configlivenotificationcontrolvalue_desc', 'block_livedesk');
    $default = 'block/livedesk:managelivedesks';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_TEXT));
}

