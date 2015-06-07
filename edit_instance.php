<?php

/**
 * edit.php
 * 
 * Displays and processes livedsk instance edition form.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 * @usecase add livedesk
 * @usecase update livedesk
 */

require_once('../../config.php');
require_once('livedesk_form.php');

$livedeskid = required_param('livedeskid', PARAM_INT); // 0 creates a new one
$bid = optional_param('bid', null, PARAM_INT);
$courseid = optional_param('course', 1, PARAM_INT);

$url = new moodle_url('/blocks/livedesk/edit_instance.php', array('livedeskid' => $livedeskid));
$PAGE->set_url($url);

// Checking course.

$course = $DB->get_record('course', array('id' => $courseid));
if (!$course) {
    print_error('invalidcourseid');
}

require_login();
$system_context = context_system::instance();

$PAGE->set_context($system_context);

require_capability('block/livedesk:createlivedesk', $system_context);

// Form controller
$livedeskform = new livedesk_form();
if ($livedeskform->is_cancelled()) {
    // Keep track of origin course.
    $url = new moodle_url('/blocks/livedesk/manage.php', array('course' => $course->id));
    redirect($url);
}

if ($data = $livedeskform->get_data()) {

    $data->description = format_text($data->description['text'], $data->description['format']);

    if ($data->livedeskid == 0) {
          // New livedesk.
          $data->servicestarttime = $data->servicestarttime_h * 3600 + $data->servicestarttime_m * 60;
          $data->serviceendtime = $data->serviceendtime_h * 3600 + $data->serviceendtime_m * 60;
          $data->livedeskid = $DB->insert_record('block_livedesk_instance', $data) ;
    } else {
        $livedesk = $DB->get_record('block_livedesk_instance', array('id' => $data->livedeskid));
        $data->id = $data->livedeskid;
        $data->description = $data->description;
        $data->servicestarttime = $data->servicestarttime_h * 3600 + $data->servicestarttime_m * 60;
        $data->serviceendtime = $data->serviceendtime_h * 3600 + $data->serviceendtime_m * 60;
        $DB->update_record('block_livedesk_instance', $data);
    }

    $DB->delete_records('block_livedesk_modules', array('livedeskid' => $data->livedeskid));
    $data->pluginids = @$_POST['pluginids']; // this is quite ugly but works before the form has been cleaned up
    if (!empty($data->pluginids)) {
        foreach ($data->pluginids as $pluginid) {
            $livedesk_con = new stdClass;
            $livedesk_con->livedeskid = $data->livedeskid;
            $livedesk_con->cmid = $pluginid ;
            $DB->insert_record('block_livedesk_modules', $livedesk_con);
        }
    }

    if ($bid) {
         $url = new moodle_url('/blocks/livedesk/run.php', array('bid' => $bid));
    } else {
         $url = new moodle_url('/blocks/livedesk/manage.php', array('course' => $course->id));
    }

    redirect($url);
    exit;
}

if ($livedeskid > 0) {
    $livedesk_obj = $DB->get_record('block_livedesk_instance', array('id' => $livedeskid));
    $description = $livedesk_obj->description;
    $livedesk_obj->description = array();
    $livedesk_obj->description['text'] = $description;
    $livedesk_obj->description['format'] = FORMAT_HTML;
    $livedesk_obj->livedeskid = $livedesk_obj->id;
    $livedesk_obj->course = $course->id; // maintain course context
    $livedeskform->set_data($livedesk_obj);
} else {
    // new instance
    $livedesk_obj = new StdClass;
    $livedesk_obj->livedeskid = 0;
    $livedesk_obj->bid = $bid;
    $livedesk_obj->name = get_string('newlivedesk', 'block_livedesk');
    $livedesk_obj->course = $course->id; // maintain course context
    $livedeskform->set_data($livedesk_obj);
}

$PAGE->set_title("$course->shortname");
$PAGE->set_heading("$course->fullname");
$PAGE->navbar->add($livedesk_obj->name);
$PAGE->navbar->add(get_string('editdeskinstance', 'block_livedesk'));
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

echo $livedeskform->display();

echo $OUTPUT->footer();
