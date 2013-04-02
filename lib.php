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
 * @version   moodle 1.9
 * @copyright 2012 Wafa Adham,, Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function block_livedesk_setup_theme_notification(){
	global $CFG, $USER, $COURSE;
	
	// Control for adding the code to the footer. This saves performance with non concerned users
	if (@$CFG->block_livedesk_live_notification_control == 'capability'){
		if (!has_capability($CFG->block_livedesk_live_notification_control_value, get_context_instance(CONTEXT_SYSTEM))){
			return;
		}
	} elseif (@$CFG->block_livedesk_live_notification_control == 'profilefield'){
		$profilefield = get_record('user_info_field', 'shortname', @$CFG->block_livedesk_live_notification_control_value);
		$profilevalue = get_record('user_info_data', 'userid', $USER->id, 'fieldid', @$profilefield->id);
		if (!$profilevalue || empty($profilevalue->data)){
			return;
		}
	}

    print('<input type="hidden" id="wwwroot" value="'.$CFG->wwwroot.'" />');
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery-1.8.2.min.js' );  
 
       //noty jquery plugin 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/jquery.noty.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottom.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomCenter.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/center.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/centerLeft.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/centerRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/inline.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/top.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topCenter.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topLeft.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topRight.js' );
    
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/themes/default.js' ); 
    
    echo "<script type=\"text/javascript\" src=\"{$CFG->wwwroot}/blocks/livedesk/js/notif_init.php?id={$COURSE->id}\"></script>"; 
}