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
 * Library for theme notifications.
 *
 * @package   block_livedesk
 * @version   moodle 2.2
 * @copyright 2012 Wafa Adham,, Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die ('You cannot use this script this way');
}

function block_livedesk_setup_theme_requires() {
    global $PAGE, $CFG, $JQUERYNOTY;

    if (!empty($JQUERYNOTY)) return;

    $JQUERYNOTY = true;

    // require_once $CFG->dirroot.'/blocks/livedesk/block_livedesk.php';
    block_livedesk::check_jquery();

    // Noty jquery plugin.
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/jquery.noty.js', true);
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottom.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomCenter.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/center.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/centerLeft.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/centerRight.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/inline.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/top.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topCenter.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topLeft.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/layouts/topRight.js', true );
    $PAGE->requires->js('/blocks/livedesk/js/jquery_plugins/noty/themes/default.js', true ); 
}

function block_livedesk_setup_theme_notification() {
    global $CFG, $USER, $COURSE, $DB;

    // Control for adding the code to the footer. This saves performance with non concerned users.
    if (@$CFG->block_livedesk_live_notification_control == 'capability') {
        if (!has_capability($CFG->block_livedesk_live_notification_control_value, context_system::instance())) {
            return;
        }
    } elseif (@$CFG->block_livedesk_live_notification_control == 'profilefield') {
        $profilefield = $DB->get_record('user_info_field', array('shortname' => @$CFG->block_livedesk_live_notification_control_value));
        $profilevalue = $DB->get_record('user_info_data', array('userid' => $USER->id, 'fieldid' => @$profilefield->id));
        if (!$profilevalue || empty($profilevalue->data)) {
            return;
        }
    }

    echo '<input type="hidden" id="wwwroot" value="'.$CFG->wwwroot.'" />';
    echo "<script src=\"{$CFG->wwwroot}/blocks/livedesk/js/notif_init.php?id={$COURSE->id}\"></script>"; 
}
