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
 * manage.php
 * 
 * This file provides direct use cases for manage.php.
 *
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @usecase show livedesks
 */
require('../../config.php');
require_once($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');

$courseid = optional_param('course', SITEID, PARAM_INT);

$url = new moodle_url('/blocks/livedesk/manage.php', array('course' => $courseid));
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id' => $courseid))){
    print_error('errorinvalidcourse');
}

$context = null;

if ($courseid != SITEID) {
    $context = context_course::instance($courseid);
}

// Security.

$systemcontext = context_system::instance();
require_login();
require_capability('block/livedesk:managelivedesks', $systemcontext);

// Execute controller.
$action = optional_param('what', '', PARAM_TEXT);

if ($action) {
    require($CFG->dirroot.'/blocks/livedesk/manage.controller.php');
}

$PAGE->set_pagelayout('standard');
$PAGE->set_context($systemcontext);
if ($context) {
    $PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', array('id' => $courseid)));
}
$PAGE->navbar->add(get_string('livedesks', 'block_livedesk'));

echo $OUTPUT->header();

$livedesks = $DB->get_records('block_livedesk_instance');

// Build the table.
$namestr = get_string('name');
$commandsstr = get_string('commands', 'block_livedesk');
$blocksattachedstr = get_string('blocksattached', 'block_livedesk');
$pluginsattachedstr = get_string('pluginssattached', 'block_livedesk');

$table = new html_table();
$table->width = '100%';
$table->head = array("<b>$namestr</b>", "<b>$blocksattachedstr</b>", "<b>$pluginsattachedstr</b>", "<b>$commandsstr</b>");
$table->size = array('40%', '20%', '20%', '20%');
$table->align = array('left', 'center', 'center', 'right');

if ($livedesks) {
    foreach ($livedesks as $livedesk) {
        $ldblocks = livedesk::get_blocks_attached($livedesk);

        if (!empty($ldblocks)) {
            $blocksattached = count($ldblocks);
        } else {
            $blocksattached = 0;
        }

        $pluginsattached = $DB->count_records('block_livedesk_modules', array('livedeskid' => $livedesk->id));
        $row = array();

        $livedeskwindow = livedesk::get_livedesk_window_name($livedesk);

        if ($blocksattached) {
            $lvdurl = new moodle_url('/blocks/livedesk/run.php', array('ldid' => $livedesk->id, 'course' => $course->id));
            $row[] = '<a href="'.$lvdurl.'" target="'.$livedeskwindow.'">'.format_string($livedesk->name).'</a>';
        } else {
            $row[] = format_string($livedesk->name);
        }

        $row[] = $blocksattached;
        $row[] = $pluginsattached;
        $params = array('course' => $course->id, 'livedeskid' => $livedesk->id);
        $editurl = new moodle_url('/blocks/livedesk/edit_instance.php', $params);
        $cmd = '<a href="'.$editurl.'">'.$OUTPUT->pix_icon('t/edit', get_string('edit')).'</a>';
        if (!$blocksattached) {
            $params = array('course' => $course->id, 'what' => 'delete', 'livedeskid' => $livedesk->id);
            $deleteurl = new moodle_url('/blocks/livedesk/manage.php', $params);
            $cmd .= '&nbsp;<a href="'.$deleteurl.'">'.$OUTPUT->pix_icon('t/delete', get_string('delete')).'</a>';
        }
        $row[] = $cmd;
        $table->data[] = $row;
    }
}

$template = new StdClass;
$template->heading = $OUTPUT->heading(get_string('livedeskmanagement', 'block_livedesk'));
$template->managetable = html_writer::table($table);
$params = array('course' => $course->id, 'livedeskid' => 0);
$template->location = new moodle_url('/blocks/livedesk/edit_instance.php', $params);
$template->createnewinstancestr = get_string('createnewinstance', 'block_livedesk');

echo $OUTPUT->render_from_template('block_livedesk/manage', $template);

echo $OUTPUT->footer();
