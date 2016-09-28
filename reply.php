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
 * This file allows replying to posts from livedesk context.
 * Most part of code is hacked from forum/post.php
 *
 * TODO : probably cleanup all what is clearly useless here (guest roles, unused use cases)
 *
 * @usecase reply
 */
require('../../config.php');
require_once($CFG->dirroot.'/blocks/livedesk/lib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/blocks/livedesk/reply_form.php');
require_once($CFG->libdir.'/completionlib.php');

$reply        = optional_param('reply', 0, PARAM_INT);
$cmid        = optional_param('cmid', 0, PARAM_INT);
$forum        = optional_param('forum', 0, PARAM_INT);
$edit        = optional_param('edit', 0, PARAM_INT);
$delete        = optional_param('delete', 0, PARAM_INT);
$prune        = optional_param('prune', 0, PARAM_INT);
$name        = optional_param('name', '', PARAM_CLEANHTML);
$confirm    = optional_param('confirm', 0, PARAM_INT);
$groupid    = optional_param('groupid', null, PARAM_INT);

$PAGE->set_url('/blocks/livedesk/reply.php', array(
        'reply' => $reply,
        'forum' => $forum,
        'edit'  => $edit,
        'delete'=> $delete,
        'prune' => $prune,
        'name'  => $name,
        'confirm' => $confirm,
        'groupid' => $groupid,
        ));

// These page_params will be passed as hidden variables later in the form.

$page_params = array('reply' => $reply, 'forum' => $forum, 'edit' => $edit);
$sitecontext = context_system::instance();

// Security.

require_login();   // Script is useless unless they're logged in

// NEW LIVEDESK   
// lock the message temporarily;

$queue_rec = $DB->get_record('block_livedesk_queue', array('itemid' => $reply, 'cmid' => $cmid));

// check record already locked.

if ($queue_rec->locked == 1 && ($queue_rec->lockedby != $USER->id)) {
    echo get_string('messagealreadylocked','block_livedesk');
    exit;
}

$queue_rec->locked = 1 ; 
$queue_rec->locktime = time() ; 
$queue_rec->lockedby = $USER->id; 
$DB->update_record('block_livedesk_queue', ($queue_rec));

// /NEW

if (!empty($forum)) {      // User is starting a new discussion in a forum
    if (! $forum = $DB->get_record("forum", array("id" => $forum))) {
        print_error('invalidforumid', 'forum');
    }
    if (! $course = $DB->get_record("course", array("id" => $forum->course))) {
        print_error('invalidcourseid');
    }
    if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        print_error("invalidcoursemodule");
    }
    $coursecontext = context_course::instance($course->id);

    if (! forum_user_can_post_discussion($forum, $groupid, -1, $cm)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {
                if (enrol_selfenrol_available($course->id)) {
                    $SESSION->wantsurl = $FULLME;
                    $SESSION->enrolcancel = $_SERVER['HTTP_REFERER'];
                    redirect($CFG->wwwroot.'/enrol/index.php?id='.$course->id, get_string('youneedtoenrol'));
                }
            }
        }
        print_error('nopostforum', 'forum');
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $coursecontext)) {
        print_error("activityiscurrentlyhidden");
    }

    if (isset($_SERVER["HTTP_REFERER"])) {
        $SESSION->fromurl = $_SERVER["HTTP_REFERER"];
    } else {
        $SESSION->fromurl = '';
    }

    // Load up the $post variable.

    $post = new stdClass();
    $post->course        = $course->id;
    $post->forum         = $forum->id;
    $post->discussion    = 0;           // ie discussion # not defined yet
    $post->parent        = 0;
    $post->subject       = '';
    $post->userid        = $USER->id;
    $post->message       = '';
    $post->messageformat = editors_get_preferred_format();
    $post->messagetrust  = 0;

    if (isset($groupid)) {
        $post->groupid = $groupid;
    } else {
        $post->groupid = groups_get_activity_group($cm);
    }

    forum_set_return();

} else if (!empty($reply)) {      // User is writing a new reply
            
    if (! $parent = forum_get_post_full($reply)) {
        print_error('invalidparentpostid', 'forum');
    }
    if (! $discussion = $DB->get_record("forum_discussions", array("id" => $parent->discussion))) {
        print_error('notpartofdiscussion', 'forum');
    }
    if (! $forum = $DB->get_record("forum", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forum');
    }
    if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
        print_error('invalidcourseid');
    }
    if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        print_error('invalidcoursemodule');
    }

    // Ensure lang, theme, etc. is set up properly. MDL-6926
    $PAGE->set_cm($cm, $course, $forum);

    $coursecontext = context_course::instance($course->id);
    $modcontext    = context_module::instance($cm->id);

    if (! forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {  // User is a guest here!
                $SESSION->wantsurl = $FULLME;
                $SESSION->enrolcancel = $_SERVER['HTTP_REFERER'];
                redirect($CFG->wwwroot.'/enrol/index.php?id='.$course->id, get_string('youneedtoenrol'));
            }
        }
        print_error('nopostforum', 'forum');
    }

    // Make sure user can post here
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
        if ($discussion->groupid == -1) {
            print_error('nopostforum', 'forum');
        } else {
            if (!groups_is_member($discussion->groupid)) {
                print_error('nopostforum', 'forum');
            }
        }
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $coursecontext)) {
        print_error("activityiscurrentlyhidden");
    }

    // Load up the $post variable.

    $post = new stdClass();
    $post->course      = $course->id;
    $post->forum       = $forum->id;
    $post->discussion  = $parent->discussion;
    $post->parent      = $parent->id;
    $post->subject     = $parent->subject;
    $post->userid      = $USER->id;
    $post->message     = '';

    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

    $strre = get_string('re', 'forum');
    if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
        $post->subject = $strre.' '.$post->subject;
    }

    unset($SESSION->fromdiscussion);

} else if (!empty($edit)) {  // User is editing their own post

    if (! $post = forum_get_post_full($edit)) {
        print_error('invalidpostid', 'forum');
    }
    if ($post->parent) {
        if (! $parent = forum_get_post_full($post->parent)) {
            print_error('invalidparentpostid', 'forum');
        }
    }

    if (! $discussion = $DB->get_record("forum_discussions", array("id" => $post->discussion))) {
        print_error('notpartofdiscussion', 'forum');
    }
    if (! $forum = $DB->get_record("forum", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forum');
    }
    if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        print_error('invalidcoursemodule');
    } else {
        $modcontext = context_module::instance($cm->id);
    }

    $PAGE->set_cm($cm, $course, $forum);

    if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
        if (((time() - $post->created) > $CFG->maxeditingtime) and
                    !has_capability('mod/forum:editanypost', $modcontext)) {
            print_error('maxtimehaspassed', 'forum', '', format_time($CFG->maxeditingtime));
        }
    }
    if (($post->userid <> $USER->id) and
                !has_capability('mod/forum:editanypost', $modcontext)) {
        print_error('cannoteditposts', 'forum');
    }


    // Load up the $post variable.
    $post->edit   = $edit;
    $post->course = $course->id;
    $post->forum  = $forum->id;
    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

    $post = trusttext_pre_edit($post, 'message', $modcontext);

    unset($SESSION->fromdiscussion);

/* LIVEDESK : This is useless
} elseif (!empty($delete)) {  // User is deleting a post
    // Removed useless usecase
} elseif (!empty($prune)) {  // Pruning
    // Removed useless usecase
*/
} else {
    print_error('unknowaction');
}

if (!isset($coursecontext)) {
    // Has not yet been set by reply.php.
    $coursecontext = context_course::instance($forum->course);
}

// From now on user must be logged on properly.

if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
    // For the logs
    print_error('invalidcoursemodule');
}
$modcontext = context_module::instance($cm->id);
require_login($course, false, $cm);

if (isguestuser()) {
    // just in case
    print_error('noguest');
}

if (!isset($forum->maxattachments)) {
    // TODO - delete this once we add a field to the forum table
    $forum->maxattachments = 3;
}

require_once('reply_form.php');

// LIVEDESK change
$mform_post = new block_livedesk_reply_form('reply.php', array('course'=>$course, 'cm'=>$cm, 'coursecontext'=>$coursecontext, 'modcontext'=>$modcontext, 'forum'=>$forum, 'post'=>$post));
$mform_post->set_data(array('cmid'=>$cmid));
$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $modcontext->id, 'mod_forum', 'attachment', empty($post->id)?null:$post->id);

// Load data into form NOW!

if ($USER->id != $post->userid) {   // Not the original author, so add a message to the end
    $data->date = userdate($post->modified);
    if ($post->messageformat == FORMAT_HTML) {
        $data->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&course='.$post->course.'">'.
                       fullname($USER).'</a>';
        $post->message .= '<p><span class="edited">('.get_string('editedby', 'forum', $data).')</span></p>';
    } else {
        $data->name = fullname($USER);
        $post->message .= "\n\n(".get_string('editedby', 'forum', $data).')';
    }
}

if (!empty($parent)) {
    $heading = get_string("yourreply", "forum");
} else {
    if ($forum->type == 'qanda') {
        $heading = get_string('yournewquestion', 'forum');
    } else {
        $heading = get_string('yournewtopic', 'forum');
    }
}

if (forum_is_subscribed($USER->id, $forum->id)) {
    $subscribe = true;

} else if (forum_user_has_posted($forum->id, 0, $USER->id)) {
    $subscribe = false;

} else {
    // User not posted yet - use subscription default specified in profile.
    $subscribe = !empty($USER->autosubscribe);
}

$draftid_editor = file_get_submitted_draft_itemid('message');
$currenttext = file_prepare_draft_area($draftid_editor, $modcontext->id, 'mod_forum', 'post', empty($post->id) ? null : $post->id, array('subdirs'=>true), $post->message);
$mform_post->set_data(array('attachments' => $draftitemid,
                            'general' => $heading,
                            'subject' => $post->subject,
                            'message' => array(
                                'text' => $currenttext,
                                    'format' => empty($post->messageformat) ? editors_get_preferred_format() : $post->messageformat,
                                    'itemid' => $draftid_editor
                                ),
                            'subscribe' => $subscribe?1:0,
                            'mailnow' => !empty($post->mailnow),
                            'userid' => $post->userid,
                            'parent' => $post->parent,
                            'discussion' => $post->discussion,
                            'course' => $course->id) +
                        $page_params +
                        (isset($post->format)?array(
                                'format'=>$post->format):
                            array())+

                        (isset($discussion->timestart)?array(
                                'timestart'=>$discussion->timestart):
                            array())+

                        (isset($discussion->timeend)?array(
                                'timeend'=>$discussion->timeend):
                            array())+

                        (isset($post->groupid)?array(
                                'groupid'=>$post->groupid):
                            array())+

                        (isset($discussion->id)?
                                array('discussion' => $discussion->id):
                                array()));

if ($fromform = $mform_post->get_data()) {
    if (empty($SESSION->fromurl)) {
        $errordestination = "$CFG->wwwroot/mod/forum/view.php?f=$forum->id";
    } else {
        $errordestination = $SESSION->fromurl;
    }
    $fromform->itemid        = $fromform->message['itemid'];
    $fromform->messageformat = $fromform->message['format'];
    $fromform->message       = $fromform->message['text'];
    // WARNING: the $fromform->message array has been overwritten, do not use it anymore!
    $fromform->messagetrust  = trusttext_trusted($modcontext);
    $contextcheck = isset($fromform->groupinfo) && has_capability('mod/forum:movediscussions', $modcontext);
    if ($fromform->edit) {           // Updating a post
        unset($fromform->groupid);
        $fromform->id = $fromform->edit;
        $message = '';
        //fix for bug #4314
        if (!$realpost = $DB->get_record('forum_posts', array('id' => $fromform->id))) {
            $realpost = new stdClass();
            $realpost->userid = -1;
        }
        // if user has edit any post capability
        // or has either startnewdiscussion or reply capability and is editting own post
        // then he can proceed
        // MDL-7066
        if ( !(($realpost->userid == $USER->id && (has_capability('mod/forum:replypost', $modcontext)
                            || has_capability('mod/forum:startdiscussion', $modcontext))) ||
                            has_capability('mod/forum:editanypost', $modcontext)) ) {
            print_error('cannotupdatepost', 'forum');
        }
        // If the user has access to all groups and they are changing the group, then update the post.
        if ($contextcheck) {
            $DB->set_field('forum_discussions' ,'groupid' , $fromform->groupinfo, array('firstpost' => $fromform->id));
        }
        $updatepost = $fromform; //realpost
        $updatepost->forum = $forum->id;
        if (!forum_update_post($updatepost, $mform_post, $message)) {
            print_error("couldnotupdate", "forum", $errordestination);
        }
        // MDL-11818
        if (($forum->type == 'single') && ($updatepost->parent == '0')){ // updating first post of single discussion type -> updating forum intro
            $forum->intro = $updatepost->message;
            $forum->timemodified = time();
            $DB->update_record("forum", $forum);
        }
        $timemessage = 2;
        if (!empty($message)) { // if we're printing stuff about the file upload
            $timemessage = 4;
        }
        $message .= '<br />'.get_string("postupdated", "forum");
        if ($subscribemessage = forum_post_subscription($fromform, $forum)) {
            $timemessage = 4;
        }
        if ($forum->type == 'single') {
            // Single discussion forums are an exception. We show
            // the forum itself since it only has one discussion
            // thread.
            $discussionurl = "view.php?f=$forum->id";
        } else {
            
            $discussionurl = "discuss.php?d=$discussion->id#p$fromform->id";
        }
        add_to_log($course->id, "forum", "update post",
                "$discussionurl&amp;parent=$fromform->id", "$fromform->id", $cm->id);
        redirect(forum_go_back_to("$discussionurl"), $message.$subscribemessage, $timemessage);
        exit;
    } else if ($fromform->discussion) { // Adding a new post to an existing discussion
        unset($fromform->groupid);
        $message = '';
        $addpost = $fromform;
        $addpost->forum=$forum->id;
        if ($fromform->id = forum_add_new_post($addpost, $mform_post, $message)) {
            // ********************* NEW Livedesk operations ***********************
            // Delete this from post from queue not to cause a looped post.
            $DB->delete_records('block_livedesk_queue', array('itemid' => $fromform->id));
            // Update status of replied message.
            $replyed_post = $DB->get_record('block_livedesk_queue', array('itemid' => $reply));
            $replyed_post->mstatus = 'answered';
            $replyed_post->answertime = time();
            $replyed_post->answerby = $USER->id;
            $replyed_post->ans_session = sesskey();
            $replyed_post->locked = 0;
            $replyed_post->lockedby = null;
            $replyed_post->locktime = null;
            $DB->update_record('block_livedesk_queue', $replyed_post);
                // /NEW
            $timemessage = 2;
            if (!empty($message)) { // if we're printing stuff about the file upload
                $timemessage = 4;
            }
            if ($subscribemessage = forum_post_subscription($fromform, $forum)) {
                $timemessage = 4;
            }
            if (!empty($fromform->mailnow)) {
                $message .= get_string("postmailnow", "forum");
                $timemessage = 4;
            } else {
                $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
            }
            if ($forum->type == 'single') {
                // Single discussion forums are an exception. We show
                // the forum itself since it only has one discussion
                // thread.
                $discussionurl = "view.php?f=$forum->id";
            } else {
                print(get_string('reply_sent','block_livedesk'));
                print("<input type='button' click='this.close();' value='".get_string('close_window','block_livedesk')."' />");
                exit;
            }
            add_to_log($course->id, "forum", "add post",
                      "$discussionurl&amp;parent=$fromform->id", "$fromform->id", $cm->id);
            // Update completion state
            $completion=new completion_info($course);
            if($completion->is_enabled($cm) &&
                ($forum->completionreplies || $forum->completionposts)) {
                $completion->update_state($cm,COMPLETION_COMPLETE);
            }
            redirect(forum_go_back_to("$discussionurl#p$fromform->id"), $message.$subscribemessage, $timemessage);
        } else {
            print_error("couldnotadd", "forum", $errordestination);
        }
        exit;
    } else {                     // Adding a new discussion
        if (!forum_user_can_post_discussion($forum, $fromform->groupid, -1, $cm, $modcontext)) {
            print_error('cannotcreatediscussion', 'forum');
        }
        // If the user has access all groups capability let them choose the group.
        if ($contextcheck) {
            $fromform->groupid = $fromform->groupinfo;
        }
        if (empty($fromform->groupid)) {
            $fromform->groupid = -1;
        }
        $fromform->mailnow = empty($fromform->mailnow) ? 0 : 1;
        $discussion = $fromform;
        $discussion->name    = $fromform->subject;
        $newstopic = false;
        if ($forum->type == 'news' && !$fromform->parent) {
            $newstopic = true;
        }
        $discussion->timestart = $fromform->timestart;
        $discussion->timeend = $fromform->timeend;
        $message = '';
        if ($discussion->id = forum_add_discussion($discussion, $mform_post, $message)) {
            add_to_log($course->id, "forum", "add discussion",
                    "discuss.php?d=$discussion->id", "$discussion->id", $cm->id);
            $timemessage = 2;
            if (!empty($message)) { // if we're printing stuff about the file upload
                $timemessage = 4;
            }
            if ($fromform->mailnow) {
                $message .= get_string("postmailnow", "forum");
                $timemessage = 4;
            } else {
                $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
            }
            if ($subscribemessage = forum_post_subscription($discussion, $forum)) {
                $timemessage = 4;
            }
            // Update completion status
            $completion=new completion_info($course);
            if($completion->is_enabled($cm) &&
                ($forum->completiondiscussions || $forum->completionposts)) {
                $completion->update_state($cm,COMPLETION_COMPLETE);
            }
            redirect(forum_go_back_to("view.php?f=$fromform->forum"), $message.$subscribemessage, $timemessage);
        } else {
            print_error("couldnotadd", "forum", $errordestination);
        }
        exit;
    }
}

// To get here they need to edit a post, and the $post
// variable will be loaded with all the particulars,
// so bring up the form.
// $course, $forum are defined.  $discussion is for edit and reply only.
if ($post->discussion) {
    if (! $toppost = $DB->get_record("forum_posts", array("discussion" => $post->discussion, "parent" => 0))) {
        print_error('cannotfindparentpost', 'forum', '', $post->id);
    }
} else {
    $toppost = new stdClass();
    $toppost->subject = ($forum->type == "news") ? get_string("addanewtopic", "forum") :
                                                   get_string("addanewdiscussion", "forum");
}

if (empty($post->edit)) {
    $post->edit = '';
}

if (empty($discussion->name)) {
    if (empty($discussion)) {
        $discussion = new stdClass();
    }
    $discussion->name = $forum->name;
}

if ($forum->type == 'single') {
    // There is only one discussion thread for this forum type. We should
    // not show the discussion name (same as forum name in this case) in
    // the breadcrumbs.
    $strdiscussionname = '';
} else {
    // Show the discussion name in the breadcrumbs.
    $strdiscussionname = format_string($discussion->name).':';
}

$forcefocus = empty($reply) ? NULL : 'message';

if (!empty($discussion->id)) {
    $PAGE->navbar->add(format_string($toppost->subject, true), "discuss.php?d=$discussion->id");
}
if ($post->parent) {
    $PAGE->navbar->add(get_string('reply', 'forum'));
}
if ($edit) {
    $PAGE->navbar->add(get_string('edit', 'forum'));
}
$PAGE->set_pagelayout('embedded');
$PAGE->set_title("$course->shortname: $strdiscussionname ".format_string($toppost->subject));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Checkup.

if (!empty($parent) && !forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
    print_error('cannotreply', 'forum');
}
if (empty($parent) && empty($edit) && !forum_user_can_post_discussion($forum, $groupid, -1, $cm, $modcontext)) {
    print_error('cannotcreatediscussion', 'forum');
}
if ($forum->type == 'qanda'
            && !has_capability('mod/forum:viewqandawithoutposting', $modcontext)
            && !empty($discussion->id)
            && !forum_user_has_posted($forum->id, $discussion->id, $USER->id)) {
    echo $OUTPUT->notification(get_string('qandanotify','forum'));
}
forum_check_throttling($forum, $cm);
if (!empty($parent)) {
    if (! $discussion = $DB->get_record('forum_discussions', array('id' => $parent->discussion))) {
        print_error('notpartofdiscussion', 'forum');
    }
    forum_print_post($parent, $discussion, $forum, $cm, $course, false, false, false);
    if (empty($post->edit)) {
        if ($forum->type != 'qanda' || forum_user_can_see_discussion($forum, $discussion, $modcontext)) {
            $forumtracked = forum_tp_is_tracked($forum);
            $posts = forum_get_all_discussion_posts($discussion->id, "created ASC", $forumtracked);
            forum_print_posts_threaded($course, $cm, $forum, $discussion, $parent, 0, false, $forumtracked, $posts);
        }
    }
} else {
    if (!empty($forum->intro)) {
        echo $OUTPUT->box(format_module_intro('forum', $forum, $cm->id), 'generalbox', 'intro');
    }
}
$mform_post->display();

echo $OUTPUT->footer();

