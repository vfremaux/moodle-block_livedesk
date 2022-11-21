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
 * Draws some statistic outputs
 *
 * TODO : draw some other usefull indicators : 
 * "average attended posts per attender" (on livedesk and instance, nonsense on self stats)
 *
 * "attended/all posts ratio" (on livedesk and instance, aggregated for all attenders)
 * calculates ratio of all posts and part of them that have an answer from livedesk.
 *
 * "attended/all posts ratio per forum" (on livedesk and instance, aggregated for all attenders)
 * calculates ratio of all posts and part of them that have an answer from livedesk.
 */
require('../../config.php');
require_once($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');

require_login();

$instanceid = optional_param('bid', 0, PARAM_INT);

$systemcontext = context_system::instance();

// Livedesk statics

$livedeskstatsstr = get_string('livedeskstats', 'block_livedesk');
$attendedpostscountstr = get_string('attendedpostscount', 'block_livedesk');
$maxattendedpostsbysessionstr = get_string('maxattendedpostsbysession', 'block_livedesk');
$averageanswerdelaystr = get_string('averageanswerdelay', 'block_livedesk');
$instancestatsstr = get_string('instancestats', 'block_livedesk');
$userstatsstr = get_string('userstats', 'block_livedesk');

$url = new moodle_url('/blocks/livedesk/statistics.php');
$PAGE->set_pagelayout('embedded');
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);

// Do NOT try to use any header/footer call as we are embeded.

echo $OUTPUT->heading(get_string('stats', 'block_livedesk'));

if (has_capability('block/livedesk:viewlivedeskstatistics', $systemcontext)) {
    $table = new html_table();
    $table->head = array("<b>$livedeskstatsstr</b>", '');
    $table->width = '80%';
    $table->size = array('90%', '10%');
    $table->align = array('left', 'right');
    $table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('SYSTEM'));
    $table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_maxpostsession('SYSTEM'));
    $table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('SYSTEM'));
    echo html_writer::table($table);
}

//aggregated instance statics

if (has_capability('block/livedesk:viewinstancestatistics', $systemcontext)) {
    $table = new html_table();
    $table->head = array("<b>$instancestatsstr</b>", '');
    $table->width = '80%';
    $table->size = array('90%', '10%');
    $table->align = array('left', 'right');
    $table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('INSTANCE', $instanceid));
    $table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_maxpostsession('INSTANCE', $instanceid));
    $table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('INSTANCE', $instanceid));
    echo html_writer::table($table);
}

// my user statics

if (has_capability('block/livedesk:viewuserstatistics', $systemcontext)) {
    $table = new html_table();
    $table->head = array("<b>$userstatsstr</b>", '');
    $table->width = '80%';
    $table->size = array('90%', '10%');
    $table->align = array('left', 'right');
    $table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('USER', null, $USER->id));
    $table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_maxpostsession('USER', null, $USER->id));
    $table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('USER', null, $USER->id));
    echo html_writer::table($table);
}
