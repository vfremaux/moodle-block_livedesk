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

/**
 * service.php
 * 
 * Answers to dynamic ajax calls for refreshing data.
 *
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../../config.php');
require_once($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');

$sql = 'SET NAMES UTF8';

$DB->execute($sql, array(), false);

$debug = optional_param('debug', 0, PARAM_BOOL);
if ($debug) {
    $CFG->debug = DEBUG_DEVELOPER;
}

// Security : Distributed into controller's use cases.

$action = optional_param('action', null, PARAM_TEXT);
$block_id = optional_param('bid', 0, PARAM_INT);
$courseid = optional_param('course', 0, PARAM_INT);

$sitecontext = context_system::instance();
$PAGE->set_context($sitecontext);

switch ($action) {
    case 'load_liveentries':
        require_login();
        $context = context_block::instance($block_id);
        require_capability('block/livedesk:runlivedesk', $context);
        livedesk::load_liveentries($block_id);
        exit;
    case 'set_message_status':
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $plugin_id  = optional_param('plugin_id', null, PARAM_INT);
        $message_id  = optional_param('message_id', null, PARAM_INT);
        $status  = optional_param('status', null, PARAM_TEXT);
        livedesk::set_message_status($message_id, $status);
        break;
    case 'get_online_users_count':
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $bid  = optional_param('bid',null,PARAM_INT);
        $user_count_arrs = livedesk::get_online_users_count($bid);
        $output = $user_count_arrs;
        break;
    case 'keep_me_live':
//       debugbreak();
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $bid = optional_param('bid',null,PARAM_INT);
        $courseid = optional_param('courseid', null, PARAM_INT);
        $livedeskid = optional_param('livedeskid', null, PARAM_INT);
        livedesk::keep_me_alive($courseid, $bid, $livedeskid);
        exit;
        break;
    case 'get_monitored_plugins':
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $livedeskid  = required_param('livedeskid', PARAM_INT);
        $output = livedesk::get_monitored_plugins($livedeskid);
        break;
    case 'unlock_item':
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $livedeskid  = optional_param('livedeskid', null, PARAM_INT);
        $itemid  = optional_param('messageid', null, PARAM_INT);
        livedesk::unlock_message($itemid);
        exit;
    case 'discard_post':
        $context = context_block::instance($block_id);
        require_login();
        require_capability('block/livedesk:runlivedesk', $context);
        $itemid  = optional_param('messageid', null, PARAM_INT);
        $ddate  = optional_param('date', null, PARAM_TEXT);
        livedesk::livedesk_discard_post($itemid,$ddate);
        break;
    case 'get_unnotified_messages':
        $course = $DB->get_record('course', array('id' => "$courseid"));
        require_login($course); // Security. people could extract private discussions.
        $messages = livedesk::get_unnotified_messages();
        $output = $messages ;
        break;
    case 'change_state':
        require_login(); // Security. we need to get current user session loaded.
        $item = required_param('item', PARAM_TEXT);
        $state = required_param('state', PARAM_TEXT);
        $SESSION->livedesk->$item = ($state == 'true');
        break;
}

if (!empty($output)) {
    $json_data = json_encode($output);
    echo($json_data);
}
