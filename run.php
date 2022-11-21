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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * This file provides code for livedesk main screen.
 */
require('../../config.php');
require_once($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');
require_once($CFG->dirroot.'/blocks/livedesk/lib.php');

$livedeskid = optional_param('ldid', null, PARAM_INT);
$bid = optional_param('bid', 0, PARAM_INT);
$courseid = optional_param('course', 1, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => "$courseid"))) {
    print_error('invalidcourseid');
}

if ($course->id == SITEID) {
    require_login();
} else {
    require_login($course);
}

if ($bid) {
    $livedeskref = $DB->get_record('block_livedesk_blocks', array('blockid' => $bid));
    if (empty($livedeskref)) {
        print_error('instance_notbounded_to_livedesk', 'block_livedesk');
    }

    $livedeskid = $livedeskref->livedeskid;
    $livedesk = $DB->get_record('block_livedesk_instance', array('id' => $livedeskid));

    $context = context_block::instance($bid);
    require_capability('block/livedesk:runlivedesk', $context);
} else {
    $livedesk = $DB->get_record('block_livedesk_instance', array('id' => $livedeskid));
    $bid = 0 + livedesk::find_block_by_instance_course($livedesk->id, $course->id);
}

$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgrid.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxlayout.css');

$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_web.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/skins/dhtmlxtoolbar_dhx_web.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_web.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxwindows.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/skins/dhtmlxwindows_dhx_web.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/dhtmlxcalendar.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_dhx_skyblue.css');
$PAGE->requires->css('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/skins/dhtmlxlayout_dhx_web.css');
$PAGE->requires->css('/blocks/livedesk/styles.css');
//Grid
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxcommon.js', true); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxcommon.js', true); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgrid.js', true); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgridcell.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/ext/dhtmlxgrid_group.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/ext/dhtmlxgrid_export.js', true);

//Vault
//require_js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxVault/codebase/dhtmlxvault.js');

// Toolbar.
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxcommon.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxmenu.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js', true);

// Menu
// require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxcommon.js' );
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxtoolbar.js', true);

// Layout.
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxcontainer.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxlayout.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase//patterns/dhtmlxlayout_pattern4c.js', true);

// Windows.
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxcontainer.js', true);
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxwindows.js', true);

// Calendar.
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/dhtmlxcalendar.js' );
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js', true);

$strtitle = get_string('livedesk', 'block_livedesk');

require_login();

$system_context = context_system::instance();

$PAGE->set_pagelayout('embedded');
$PAGE->set_context($system_context);
$PAGE->set_title($strtitle);
$PAGE->set_focuscontrol('');
$PAGE->set_cacheable(false);
$PAGE->set_button('');
$PAGE->set_headingmenu('');

// TODO
// Check how to add <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />'); // for IE8

$params = array('course' => $courseid, 'ldid' => $livedeskid, 'bid' => $bid);
$url = new moodle_url('/blocks/livedesk/run.php', $params);
$PAGE->set_url($url);

if (empty($livedesk)) {
    print_error('invalidlivedesk', 'block_livedesk');
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
echo '<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />'; // for IE8.
echo $OUTPUT->header();

// echo '<div class="header">';
$template = new Stdclass;
$template->logoiconurl = $OUTPUT->image_url('logo', get_string('pluginname', 'block_livedesk'), 'block_livedesk');

if (has_capability('block/livedesk:managelivedesks', context_system::instance())) {
    $ldurl = new moodle_url('/blocks/livedesk/edit_instance.php', array('bid' => $bid, 'livedeskid' => $livedeskid));
    $template->managecmd = '<a href="'.$ldurl.'">'.$OUTPUT->pix_icon('t/edit', get_string('edit')).'</a>';
}

$template->name = format_string($livedesk->name);
$template->description = format_string($livedesk->description);

echo $OUTPUT->render_from_template('block_livedesk/run', $template);

$livedeskparams = block_livedesk_build_params($courseid, $livedesk, $bid);
$PAGE->requires->js_call_amd('block_livedesk/livedeskinit', 'init', array($livedeskparams));

// Trigger run event.
livedesk::keep_me_alive($livedeskid);

$template = new StdClass;
$template->getasexcelstr = get_string('getasexcel', 'block_livedesk');
$template->generateurl = new moodle_url('/blocks/livedesk/js/dhtmlx/3.0/grid_exporter/generate.php');
echo $OUTPUT->render_from_template('block_livedesk/exportbutton', $template);

echo $OUTPUT->footer();
