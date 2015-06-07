<?php
/**
 * manage.php
 * 
 * This file provides direct use cases for manage.php.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
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

$context = NULL;

if ($courseid != SITEID) {
    $context = context_course::instance($courseid);
}
$systemcontext = context_system::instance();

// Security.
require_login();
require_capability('block/livedesk:managelivedesks', $systemcontext);

// Execute controller.
$action = optional_param('what', '', PARAM_TEXT);

if ($action) {
    include 'manage.controller.php';
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
        $editurl = new moodle_url('/blocks/livedesk/edit_instance.php', array('course' => $course->id, 'livedeskid' => $livedesk->id));
        $cmd = '<a href="'.$editurl.'"><img src="'.$OUTPUT->pix_url('t/edit').'"></a>';
        if (!$blocksattached) {
            $deleteurl = new moodle_url('/blocks/livedesk/manage.php', array('course' => $course->id, 'what' => 'delete', 'livedeskid' => $livedesk->id));
            $cmd .= '&nbsp;<a href="'.$deleteurl.'"><img src="'.$OUTPUT->pix_url('t/delete').'"></a>';
        }
        $row[] = $cmd;
        $table->data[] = $row;
    }
}

echo '<div id="livedesk-instances-table">';
echo $OUTPUT->heading(get_string('livedeskmanagement', 'block_livedesk'));
echo html_writer::table($table);
$location = $CFG->wwwroot."/blocks/livedesk/edit_instance.php?course={$course->id}&livedeskid=0";	
echo '<p align="right"><input type="button" name="create_new_instance" value="'.get_string('createnewinstance', 'block_livedesk').'" onClick="window.location=\''.$location.'\'"  /></p>';
echo '</div>';

echo $OUTPUT->footer();
