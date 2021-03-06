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

// fast access to style sheet
echo '<link src="'.$CFG->wwwroot.'/blocks/livedesk/style.css" rel="styilesheet" type="text/css" />';

require_login();

$timecreated = required_param('date', PARAM_TEXT);

echo '<div class="livedesk-popup">'.get_string('discard_before_txt','block_livedesk').'</div>';
echo '<div class="livedesk-popup">'.get_string('discard_date','block_livedesk').' <input id="discard_date" type="text" value="'.date("d.m.Y h:i", $timecreated).'" /></div>';
echo '<div class="livedesk-popup-button"><input id="discard_btn" type="button" value="'.get_string('confirmdiscard', 'block_livedesk').'" /></div>';
echo '<script>';
echo 'var wwwroot = \''.$CFG->wwwroot.'\'';
echo 'var calendar = new dhtmlXCalendarObject("discard_date");';
echo 'calendar.setDate(\''.date("d.m.Y h:i", $timecreated).'\');';
echo 'calendar.setDateFormat("%d.%m.%Y %h:%i");';
echo '
$(\'#discard_btn\').click(function(){
	discard_post_before_date();
});

// wwwroot is set globally in javascript environment
function discard_post_before_date(){
   var ddate = $(\'#discard_date\').val();
   url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=discard_post\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid+\'&date=\'+ddate;
   $.post(url, function(data) {
   window.discard_messages_window.close();
    }); 
}

';
echo '</script>';

