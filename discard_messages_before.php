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
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$courseid = required_param('course', PARAM_INT);
$livedeskid = required_param('livedeskid', PARAM_INT);
$bid = required_param('bid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('coursemisconfig');
}

$livedesk = $DB->get_record('livedesk_instance', array('id' => $livedeskid));

$params('courseid' => $courseid, 'livedeskid' => $livedeskid, 'bid' => $bid);
$url = new moodle_url('/blocks/livedesk/discard_messages_before.php', $params);

// fast access to style sheet.
$PAGE->requires->css('/blocks/livedesk/style.css');
$livedeskparams = block_livedesk_build_params($courseid, $livedesk, $bid);
$livedeskparams['creationdate'] = date("d.m.Y h:i", $timecreated); // TODO Find where timecrreated comes from !!
$PAGE->requires->js_call_amd('block_livedesk/livedeskinit', 'init', array($livedeskparams));

require_login();

$timecreated = required_param('date', PARAM_TEXT);

$template = new StdClass;
$template->discardbeforestr = get_string('discard_before_txt','block_livedesk');
$template->discarddatestr = get_string('discard_date','block_livedesk');
$template->timecreated = date("d.m.Y h:i", $timecreated);
$template->confirmstr = get_string('confirmdiscard', 'block_livedesk');

$OUTPUT->render_from_template('block_livedesk/discardpopup', $template);
