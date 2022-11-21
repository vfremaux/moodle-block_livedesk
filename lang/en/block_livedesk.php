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

// Capabilities.
$string['livedesk:addinstance'] = 'Can add an instance';
$string['livedesk:myaddinstance'] = 'Can add an instance to My Page';
$string['livedesk:runlivedesk'] = 'Can coach monitored activities';
$string['livedesk:createlivedesk'] = 'Can create livedesks';
$string['livedesk:managelivedesks'] = 'Can update livedesks';
$string['livedesk:deletelivedesk'] = 'Can destroy livedesks';
$string['livedesk:viewinstancestatistics'] = 'Can view stats of instance';
$string['livedesk:viewuserstatistics'] = 'Can view stats of users';
$string['livedesk:viewlivedeskstatistics'] = 'Can view overall service statistics';

// Privacy.
$string['privacy:metadata'] = 'The Livedesk block does not directly store any personal data about any user.';

// Admin settings.
$string['adddeskinstances'] = '+ Create new instance';
$string['allusers'] = 'All users check notifications (discouraged)';
$string['answered'] = 'Message has been answered';
$string['attenderreleasetime'] = 'Attender Release Time' ;
$string['blockname'] = 'LiveDesk';
$string['blocksattached'] = 'Blocks using';
$string['capabilitycontrol'] = 'Control by capability check';
$string['commands'] = 'Commands';
$string['configattenderreleasetime'] = 'Attender Release Time (default)';
$string['configattenderreleasetime_desc'] = 'An amount of time an attender will NOT be solicited after having attended a post.';
$string['configcontent'] = 'Content';
$string['configkeepalivedelay'] = 'Keepalive delay';
$string['configkeepalivedelay_desc'] = 'The Livedesk fires a keepalive request every keepalive_delay seconds.';
$string['configlivenotificationcontrol'] = 'Live notification access control';
$string['configlivenotificationcontrol_desc'] = 'Chooses the way the code for live notification control is added to all Moodle pages.';
$string['configlivenotificationcontrolvalue'] = 'Live notification control value';
$string['configlivenotificationcontrolvalue_desc'] = 'A name of a capability to control the presence of the live notification code (System level) or the name of the profile field used for check (non empty).';
$string['configmaxstacksize'] = 'Max stack size (default)';
$string['configmaxstacksize_desc'] = 'Default value for max size of a new instance queue';
$string['confignotificationtheme'] = 'Notification theme';
$string['confignotificationtheme_desc'] = 'A choice for CSS styling sheet for notifications.';
$string['confignotificationlayout'] = 'Layout';
$string['confignotificationlayout_desc'] = 'Where the notifications will appear on screen.';
$string['confignotificationbacktrackrange'] = 'Notification backtrack range';
$string['confignotificationbacktrackrange_desc'] = 'The time range for back guessing wich livedesk has recent activity to notify.';
$string['confignotificationonscreentime'] = 'Notification on screen time';
$string['confignotificationonscreentime_desc'] = 'The delay in milliseconds the notification popup is visible. Too short times may not allow users to click on links. Should not be set higher to Refresh Period.';
$string['confignotificationrefreshtime'] = 'Notification refresh period';
$string['confignotificationrefreshtime_desc'] = 'The time in milliseconds the notification are polled from server. Shorter times will increase server load.';
$string['configrefresh'] = 'Livedesk refresh period';
$string['configrefresh_desc'] = 'The period livedesk information is refreshed. Tune it up or down to optimize performance/responsivness ratio.';
$string['configresolvingpostrelease'] = 'Resolving Post release (default)';
$string['configresolvingpostrelease_desc'] = 'If a post is considered being “in resolution” during more than the release time, it probably might not be never answered . So it should be pushed back in stack.';
$string['configservicetimerangeend'] = 'Desk service end time (default)';
$string['configservicetimerangeend_desc'] = 'Closing time of the desk service.';
$string['configservicetimerangestart'] = 'Desk service start time (default) ';
$string['configservicetimerangestart_desc'] = 'Opening time of the desk service.';
$string['configstackovertime'] = 'Stack over time (default)';
$string['configstackovertime_desc'] = 'Time gap (default) before which no unattended messages are considered. This is to save attenders from persistant popup raise on highly used forum tracks.';
$string['configtitle'] = 'Visible title of the block';
$string['configurations'] = 'Configurations';
$string['confirmdiscard'] = 'Confirm discard';
$string['createnewinstance'] = 'Create Livedek Instance';
$string['deleted'] = 'Deleted';
$string['discard'] = 'Discard';
$string['discarded'] = 'Message is unrelevant';
$string['discardbefore'] = 'Discard messages before...';
$string['discardbefore_txt'] = 'Discard messages before the following date';
$string['discarddate'] = 'Discard date';
$string['editdeskinstance'] = 'Edit Desk instance';
$string['emailuser'] = 'Send a message to user';
$string['eventlivedeskrun'] = 'Run a livedesk board';
$string['errornoname'] = 'Name is missing';
$string['globalparams'] = 'Global settings';
$string['instancenotboundedtolivedesk'] = 'This block is currently unbounded to any livedesk, please check block settings.';
$string['instances'] = 'Instances';
$string['invalidlivedesk'] = 'Invalid livedesk instance';
$string['inprogress'] = 'Message is being processed';
$string['keepalivedelay'] = 'Keepalive delay' ;
$string['leaveblanktohide'] = 'leave blank to hide the title';
$string['livequeue'] = 'Live Queue';
$string['livedesk'] = 'Livedesk';
$string['livedeskinfo'] = 'Livedesk Info.';
$string['livedeskdescription'] = 'Livedesk instance description';
$string['livedeskmanagement'] = 'Livedesk Instances Management';
$string['livedeskname'] = 'Livedesk instance name';
$string['livedeskoperator'] = 'Livedesk operator';
$string['livedeskoperator_desc'] = 'People having this role at system level can control livedesks';
$string['livedeskref'] = 'Livedesk Reference:';
$string['livedesks'] = 'Livedesks';
$string['locked'] = 'Message is locked';
$string['lockedby'] = 'Locked By';
$string['managelivedesks'] = 'Manage Livedesks';
$string['manageinstances'] = 'Manage Livedesk Instances';
$string['maxstacksize'] = 'Max queue size (in entries)';
$string['message'] = 'Message';
$string['messagesent'] = 'Your message was sent successfully.';
$string['messagetime'] = 'Message Time';
$string['messagealreadylocked'] = 'Opps!, looks like the current message is being answered by other agent,please wait until this message is answered then you will be able to view/re-answer it.';
$string['messagesinqueue'] = 'Messages pending for queue: {$a} ';
$string['monitorableplugins'] = 'Monitorable plugins';
$string['monitoredplugins'] = 'Monitored plugins';
$string['morethanmessagesinqueue'] = 'More than {$a->count} messages are awaiting attention queue {$a->queue}';
$string['newlivedesk'] = 'New livedesk instance';
$string['newlivedeskblock'] = '(new LiveDesk block)';
$string['newmessage'] = 'New message';
$string['newmessages'] = 'New incoming messages... ';
$string['nomonitorableplugins'] = 'There is no monitorable plugin in this course.';
$string['noreference'] = 'There is no LiveDesk instance define yet to use this block with. You should create one if you have capability for.';
$string['notificationmail'] = 'Notification mail text';
$string['notificationtitle'] = 'Notification mail title (leave blank for default title)';
$string['onlineattenderscount'] = 'Online attenders:&snsp;';
$string['onlineusers'] = 'Online Users';
$string['onlineuserscount'] = 'Online users:&ensp;';
$string['opened'] = 'Message has been opened';
$string['origin'] = 'Origin';
$string['pluginname'] = 'LiveDesk';
$string['pluginssattached'] = 'Monitored plugins';
$string['profilefieldcontrol'] = 'Control by profile field';
$string['received'] = 'Livedesk Input Acknowledge';
$string['refresh'] = 'Refresh period';
$string['refreshposts'] = 'Refresh Posts';
$string['reply'] = 'Reply';
$string['resolvereleasedelay'] = 'Resolving Post release';
$string['sendnotification'] = 'Send notification when accepting message in queue';
$string['serviceendtime'] = 'Desk service end time';
$string['servicestarttime'] = 'Desk service start time';
$string['stackovertime'] = 'Stack over time';
$string['statistics'] = 'Statistics';
$string['task_livedesk'] = 'Livedesk background processing';
$string['user'] = 'User';

// Statistics.

$string['attendedpostscount'] = 'Number of Attended Posts';
$string['stats'] = 'Statistics';
$string['livedeskstats'] = 'LiveDesk Service Statistics';
$string['maxattendedpostsbysession'] = 'Maximum Attended Posts/Session';
$string['averageanswerdelay'] = 'Average Answer Delay';
$string['instancestats'] = 'LiveDesk <i>Instance</i> Statistics';
$string['userstats'] = 'LiveDesk <i>User</i> Statistics';
$string['showhideanswered'] = 'Show / Hide answered entries ';
$string['showhidelocked'] = 'Show / Hide locked entries ';
$string['showhidediscarded'] = 'Show / Hide discarded entries ';
$string['getasexcel'] = 'Get list as excel';
$string['reply_sent'] = 'Your reply has been sent.';
$string['close_window'] = 'Close Window';

// Layout

$string['top'] = "Top";
$string['topleft'] = "Top left";
$string['topcenter'] = "Top center";
$string['topright'] = "Top right";
$string['center'] = "Center";
$string['centerleft'] = "Center left";
$string['centerright'] = "Center right";
$string['bottom'] = "Bottom";
$string['bottomleft'] = "Bottom left";
$string['bottomcenter'] = "Bottom center";
$string['bottomright'] = "Bottom right";
