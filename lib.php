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

defined('MOODLE_INTERNAL') || die();

/**
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This function is given for theme setup where js requirement can be done on all moodle pages.
 */
function block_livedesk_before_http_headers() {
    block_livedesk_setup_theme_requires();
}

/**
 * This function is given for theme setup where js requirement can be done on all moodle pages.
 */
function block_livedesk_setup_theme_requires() {
    global $CFG, $DB;

    $block = $DB->get_record('block', array('name' => 'livedesk'));
    if (!$block->visible) {
        return;
    }

    require_once($CFG->dirroot.'/blocks/livedesk/block_livedesk.php');

    block_livedesk::get_livedesk_javascript();
}

/**
 * this function adds the code for the noty div. the livedesk notification will not appear if
 * - the current user has no livedesk dashnoard screen open (refreshing the $SESSION->livedesk->session value
 * - we are over timed (day time work)
 * - there is a capability value control set, and the user has not the capability
 * - there is a profile field control value set, and the user has not the profile field condition set
 */
function block_livedesk_setup_theme_notification() {
    global $USER, $COURSE, $DB, $PAGE, $SESSION;

    $config = get_config('block_livedesk');

    // Session is initiated by the current user launching a livedesk board.
    if (empty($SESSION->livedesk->session)) {
        return;
    }
    $lasttickguard = $SESSION->livedesk->session + $config->keepalive_delay + 60;
    if (!empty($SESSION->livedesk->session) && (time() > $lasttickguard)) {
        unset($SESSION->livedesk->session);
        return;
    }

    // Out of working hours.
    $startmintime = $config->service_timerange_start_h * 60 + $config->service_timerange_start_m;
    if ((strftime('%H') * 60 + strftime('%M')) < $startmintime) {
        return;
    }

    $endmintime = $config->service_timerange_end_h * 60 + $config->service_timerange_end_m;
    if ((strftime('%H') * 60 + strftime('%M')) < $endmintime) {
        return;
    }

    // Control for adding the code to the footer. This saves performance with non concerned users.
    if (@$config->live_notification_control == 'capability') {
        if (!has_capability($config->live_notification_control_value, context_system::instance())) {
            return;
        }
    } else if (@$config->live_notification_control == 'profilefield') {
        $profilefield = $DB->get_record('user_info_field', array('shortname' => @$config->live_notification_control_value));
        $profilevalue = $DB->get_record('user_info_data', array('userid' => $USER->id, 'fieldid' => @$profilefield->id));
        if (!$profilevalue || empty($profilevalue->data)) {
            return;
        }
    }

    if (empty($config->notification_layout)) {
        $config->notification_layout = 'topRight';
        set_config('notification_layout', 'topRight', 'block_livedesk');
    }

    $params = array('courseid' => $COURSE->id,
                    'notification_refresh_time' => $config->notification_refresh_time,
                    'theme' => $config->notification_theme,
                    'layout' => $config->notification_layout);
    $PAGE->requires->js_call_amd('block_livedesk/notyfication', 'init', array($params));
    $PAGE->requires->js_call_amd('block_livedesk/notyfication', 'start');
}

/**
 * Collects and build all param values to pass to JS
 *
 */
function block_livedesk_build_params($courseid, $livedesk, $bid) {
    global $SESSION;

    $systemcontext = context_system::instance();
    $coursecontext = context_course::instance($courseid);

    $params = array();

    // Other params.

    $keepalive = optional_param('keepalive', $livedesk->keepalivedelay, PARAM_INT);
    $keepalive = $keepalive * 1000; // Javascript uses milliseconds
    $params['keepalive'] = $keepalive;
    $refresh = optional_param('refresh', $livedesk->refresh, PARAM_INT);
    $refresh = $refresh * 1000; // Javascript uses milliseconds
    $params['refresh'] = $refresh;

    $params['livedeskid'] = $livedesk->id;
    $params['bid'] = $bid;
    $params['courseid'] = $courseid;

    $params['canviewsettings'] = 0;
    if (has_capability('moodle/course:manageactivities', $coursecontext)) {
        $params['canviewsettings'] = 1;
    }

    $params['canviewstats'] = 0;
    if (has_any_capability(array('block/livedesk:viewuserstatistics', 
                                 'block/livedesk:viewinstancestatistics', 
                                 'block/livedesk:viewlivedeskstatistics'), $systemcontext)){
        $params['canviewstats'] = 1;
    }

    $params['canmanage'] = 0;
    if (has_capability('block/livedesk:managelivedesks', $systemcontext)){
        $params['canmanage'] = 1;
    }

    if (!isset($SESSION->livedesk)) {
        $SESSION->livedesk = new StdClass;
    }
    if (!isset($SESSION->livedesk->show_answered)) {
        $SESSION->livedesk->show_answered = true;
    }
    if (!isset($SESSION->livedesk->show_discarded)) {
        $SESSION->livedesk->show_discarded = true;
    }
    if (!isset($SESSION->livedesk->show_locked)) {
        $SESSION->livedesk->show_locked = true;
    }

    $params['view_answered'] = 0;
    if ($SESSION->livedesk->show_answered) {
        $params['view_answered'] = 1;
    }

    $params['view_discarded'] = 0;
    if ($SESSION->livedesk->show_discarded) {
        $params['view_discarded'] = 1;
    }

    $params['view_locked'] = 0;
    if ($SESSION->livedesk->show_locked) {
        $params['view_locked'] = 1;
    }

    return $params;
}