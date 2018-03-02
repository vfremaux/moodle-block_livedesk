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

// disabler JQuery in theme layouts if ever inserted
global $NOJQUERY;
$NOJQUERY = false;

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

$PAGE->requires->js('/blocks/livedesk/js/jquery-1.8.2.min.js', true); 

if ($bid) {

    $livedesk_reference = $DB->get_record('block_livedesk_blocks', array('blockid' => $bid));
    if (empty($livedesk_reference)) {
        print_error('instance_notbounded_to_livedesk', 'block_livedesk');
    }

    $livedeskid = $livedesk_reference->livedeskid;
    $livedesk = $DB->get_record('block_livedesk_instance', array('id' => $livedeskid));

    $context = context_block::instance($bid);
    require_capability('block/livedesk:runlivedesk', $context);
} else {
    $livedesk = $DB->get_record('block_livedesk_instance', array('id' => $livedeskid));
    require_once $CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php';
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

//Toolbar
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxcommon.js', true);   
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxmenu.js', true);   
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js', true); 

//Menu 
// require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxcommon.js' );   
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxtoolbar.js', true); 
    //Layout
// require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxcommon.js' );   
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxcontainer.js', true); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxlayout.js', true); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase//patterns/dhtmlxlayout_pattern4c.js', true); 
//Windows
//  require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxcommon.js' );   
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxcontainer.js', true);

$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxwindows.js', true); 

//Calendar
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/dhtmlxcalendar.js' ); 
$PAGE->requires->js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js', true);
//TabBar
//JQuery
$PAGE->requires->js('/blocks/livedesk/js/jquery-1.8.2.min.js' );
//noty jquery plugin 
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/jquery.noty.js', true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottom.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomCenter.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/center.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/centerLeft.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/centerRight.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/inline.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/top.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topCenter.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topLeft.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topRight.js',true);
$PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/themes/default.js',true);

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
// Check how to add <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />');// for IE8

$url = new moodle_url('/blocks/livedesk/run.php');
$PAGE->set_url($url);

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
echo '<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />';// for IE8
echo $OUTPUT->header();

if (empty($livedesk)) {
    print_error('invalid_livedesk','block_livedesk');
}

// unfortunately not capable to use require_js here due to the CGI parameter
//some variables neededby the js 
echo '<script type="text/javascript">var bid = '.$bid.'</script>'; 
echo '<script type="text/javascript">var courseid = '.$courseid.'</script>'; 
echo '<script type="text/javascript">var livedeskid = '.$livedeskid.'</script>'; 
echo '<script type="text/javascript">var wwwroot = \''.$CFG->wwwroot.'\'</script>'; 
$params = array('id' => $courseid, 'keepalive' => $livedesk->keepalivedelay, 'refresh' => $livedesk->refresh);
$scripturl = new moodle_url('/blocks/livedesk/js/init.php', $params);
echo '<script src="'.$scripturl.'"></script>';
// echo '<div class="header">';
echo '<div class="livedesk-headerlogo" >'.$OUTPUT->pix_icon('logo', get_string('pluginname', 'block_livedesk'), 'block_livedesk').'</div>';
echo '<div id="livedesk-toolbar-wrap">';
echo '<div id="livedesk-maintoolbar"></div>'; 
echo '<div id="livedesk-toolbar"></div>';

echo '<div id="masterlayout"  style="position: relative; top: 20px; left: 20px; width: 1200px; height: 800px; aborder: #B5CDE4 1px solid;">';
echo '<div id="livedesk-masterlayout">';
//  echo '</div>';
echo '<div id="livedesk-postsgrid" class="livedesk-panel" width="100%" height="100%" style = "background-color:white;"></div>';
// echo '</div>';
echo '<div id="livedesk-onlineuserscont" class="livedesk-panel"  width="100%" height="100%" style = "background-color:white;"></div>';
echo '<div id="livedesk-plugins" class="livedesk-panel"></div>  ';

$cmd = '';

if (has_capability('block/livedesk:managelivedesks', context_system::instance())) {
    $ldurl = new moodle_url('/blocks/livedesk/edit_instance.php', array('bid' => $bid, 'livedeskid' => $livedeskid));
    $cmd = '<a href="'.$ldurl.'">'.$OUTPUT->pix_icon('t/edit', get_string('edit')).'</a>';
}

echo '<div id="livedesk-info" width="100%" height="100%">';
echo '<b>'.format_string($livedesk->name).'</b> '.$cmd.'<br/>'.format_string($livedesk->description);
echo "</div>\n";
echo '</div>';

// echo '<style>th, td { padding: 0px; } </style>';
add_to_log($courseid,'livedesk', 'run', 'run.php', $livedeskid, $bid, $USER->id);

$getasexcelstr = get_string('getasexcel', 'block_livedesk');
echo "<p align=\"center\"><input type=\"button\" value=\"$getasexcelstr\" onclick=\"mygrid.toExcel('{$CFG->wwwroot}/blocks/livedesk/js/dhtmlx/3.0/grid_exporter/generate.php');\"></p>";

echo $OUTPUT->footer(); 
