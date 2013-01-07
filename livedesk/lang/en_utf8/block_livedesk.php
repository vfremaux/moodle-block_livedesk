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
 * Strings for component 'block_html', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_html
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// capabilities

$string['livedesk:runlivedesk'] = 'Can coach monitored activities';
$string['livedesk:createlivedesk'] = 'Can create livedesks';
$string['livedesk:managelivedesks'] = 'Can update livedesks';
$string['livedesk:deletelivedesk'] = 'Can destroy livedesks';
$string['livedesk:viewinstancestatistics'] = 'Can view stats of instance';
$string['livedesk:viewuserstatistics'] = 'Can view stats of users';
$string['livedesk:viewlivedeskstatistics'] = 'Can view overall service statistics';

//admin settings 

$string['resolvereleasedelay'] = 'Resolving Post release' ;
$string['block_livedesk_resolving_post_release'] = 'Resolving Post release (default)' ;
$string['block_livedesk_resolving_post_release_comment'] = 'If a post is considered being “in resolution” during more than the release time, it probably might not be never answered . So it should be pushed back in stack.' ;
$string['attenderreleasetime'] = 'Attender Release Time' ;
$string['block_livedesk_attender_release_time'] = 'Attender Release Time (default)' ;
$string['block_livedesk_attender_release_time_comment'] = 'An amount of time an attender will NOT be solicited after having attended a post.' ;
$string['stackovertime'] = 'Stack over time' ;
$string['block_livedesk_stack_over_time'] = 'Stack over time (default)' ;
$string['block_livedesk_stack_over_time_comment'] = 'Time gap (default) before which no unattended messages are considered. This is to save attenders from persistant popup raise on highly used forum tracks.' ;
$string['block_livedesk_max_stack_size'] = 'Max stack size (default)' ;
$string['block_livedesk_max_stack_size_comment'] = 'Default value for max size of a new instance queue' ;
$string['servicestarttime'] = 'Desk service start time' ;
$string['block_livedesk_service_timerange_start'] = 'Desk service start time (default) ' ;
$string['block_livedesk_service_timerange_start_comment'] = 'Opening time of the desk service.' ;
$string['serviceendtime'] = 'Desk service end time' ;
$string['block_livedesk_service_timerange_end'] = 'Desk service end time (default)' ;
$string['block_livedesk_service_timerange_end_comment'] = 'Closing time of the desk service.' ;

$string['adddeskinstances'] = '+ Create new instance';
$string['blockname'] = 'LiveDesk';
$string['blocksattached'] = 'Blocks using';
$string['commands'] = 'Commands';
$string['configcontent'] = 'Content';
$string['configtitle'] = 'Block title';
$string['configurations'] = "Configurations";
$string['confirmdiscard'] = 'Confirm discard';
$string['createnewinstance'] = 'Create Livedek Instance';
$string['discard'] = 'Discard';
$string['discard_before'] = 'Discard messages before...';
$string['discard_before_txt'] = 'Discard messages before the following date';
$string['discard_date'] = 'Discard date';
$string['email_user'] = 'Send a message to user';
$string['errornoname'] = 'Name is missing';
$string['instance_notbounded_to_livedesk'] = 'This block is currently unbounded to any livedesk, please check block settings.';
$string['invalid_livedesk'] = 'Invalid livedesk instance';
$string['leaveblanktohide'] = 'leave blank to hide the title';
$string['live_queue'] = 'Live Queue';
$string['livedesk'] = 'Livedesk';
$string['livedesk_info'] = 'Livedesk Info.';
$string['livedeskdescription'] = 'Livedesk instance description';
$string['livedeskedit'] = 'Editing Livedesk';
$string['livedeskmanagement'] = 'Livedesk Instances Management';
$string['livedeskname'] = 'Livedesk instance name';
$string['livedeskref'] = 'Livedesk Reference:';
$string['lockedby'] = 'Locked By';
$string['manage_livedesks'] = 'Manage Livedesks';
$string['manageinstances'] = 'Manage Livedesk Instances';
$string['maxstacksize'] = 'Max queue size (in entries)';
$string['message'] = 'Message';
$string['message_sent'] = 'Your message was sent successfully.';
$string['message_time'] = 'Message Time';
$string['messagealreadylocked'] = 'Opps!, looks like the current message is being answered by other agent,please wait until this message is answered then you will be able to view/re-answer it.';
$string['monitorableplugins'] = 'Monitorable plugins';
$string['monitoredplugins'] = 'Monitored plugins';
$string['newlivedeskblock'] = '(new LiveDesk block)';
$string['newmessage'] = 'New message';
$string['nomonitorableplugins'] = 'There is no monitorable plugin in this course.';
$string['noreference'] = 'There is no LiveDesk instance define yet to use this block with. You should create one if you have capability for.';
$string['online_attenderes_count'] = 'Online attenders: ';
$string['online_users'] = 'Online Users';
$string['online_users_count'] = 'Online users: ';
$string['origin'] = 'Origin';
$string['pluginname'] = 'LiveDesk';
$string['pluginssattached'] = 'Monitored plugins';
$string['refresh_posts'] = 'Refresh Posts';
$string['reply'] = 'Reply';
$string['statistics'] = 'Statistics';
$string['user'] = 'User';
$string['newmessages'] = 'New incoming messages... ';
$string['messagesinqueue'] = 'Messages pending for queue : $a ';
$string['morethanmessagesinqueue'] = 'More than {$a->count} messages are awaiting attention queue {$a->queue}';

// statistics

$string['attendedpostscount'] = 'Number of Attended Posts';
$string['stats'] = 'Statistics';
$string['livedeskstats'] = 'LiveDesk Service Statistics';
$string['maxattendedpostsbysession'] = 'Maximum Attended Posts/Session';
$string['averageanswerdelay'] = 'Average Answer Delay';
$string['instancestats'] = 'LiveDesk <i>Instance</i> Statistics';
$string['userstats'] = 'LiveDesk <i>User</i> Statistics';
