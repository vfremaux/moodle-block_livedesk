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
 * Edit and save a new post to a discussion
 *
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/blocks/livedesk/reply_form.php');
require_once($CFG->libdir.'/completionlib.php');

$reply   = optional_param('reply', 0, PARAM_INT);
$forum   = optional_param('forum', 0, PARAM_INT);
$edit    = optional_param('edit', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$prune   = optional_param('prune', 0, PARAM_INT);
$name    = optional_param('name', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$groupid = optional_param('groupid', null, PARAM_INT);

$PAGE->set_url('/mod/forum/post.php', array(
        'reply' => $reply,
        'forum' => $forum,
        'edit'  => $edit,
        'delete'=> $delete,
        'prune' => $prune,
        'name'  => $name,
        'confirm'=>$confirm,
        'groupid'=>$groupid,
        ));
//these page_params will be passed as hidden variables later in the form.
$page_params = array('reply'=>$reply, 'forum'=>$forum, 'edit'=>$edit);

$sitecontext = context_system::instance();

if (!isloggedin() or isguestuser()) {

    if (!isloggedin() and !get_local_referer()) {
        // No referer+not logged in - probably coming in via email  See MDL-9052
        require_login();
    }

    if (!empty($forum)) {
        // User is starting a new discussion in a forum
        if (! $forum = $DB->get_record('forum', array('id' => $forum))) {
            print_error('invalidforumid', 'forum');
        }
    } else if (!empty($reply)) {
        // User is writing a new reply
        if (! $parent = forum_get_post_full($reply)) {
            print_error('invalidparentpostid', 'forum');
        }
        if (! $discussion = $DB->get_record('forum_discussions', array('id' => $parent->discussion))) {
            print_error('notpartofdiscussion', 'forum');
        }
        if (! $forum = $DB->get_record('forum', array('id' => $discussion->forum))) {
            print_error('invalidforumid');
        }
    }
    if (! $course = $DB->get_record('course', array('id' => $forum->course))) {
        print_error('invalidcourseid');
    }

    if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) { // For the logs
        print_error('invalidcoursemodule');
    } else {
        $modcontext = context_module::instance($cm->id);
    }

    $PAGE->set_cm($cm, $course, $forum);
    $PAGE->set_context($modcontext);
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    $referer = get_local_referer(false);

    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('noguestpost', 'forum').'<br /><br />'.get_string('liketologin'), get_login_url(), $referer);
    echo $OUTPUT->footer();
    exit;
}

require_login(0, false);   // Script is useless unless they're logged in

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

    // Retrieve the contexts.
    $modcontext    = context_module::instance($cm->id);
    $coursecontext = context_course::instance($course->id);

    if (! forum_user_can_post_discussion($forum, $groupid, -1, $cm)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {
                if (enrol_selfenrol_available($course->id)) {
                    $SESSION->wantsurl = qualified_me();
                    $SESSION->enrolcancel = get_local_referer(false);
                    redirect(new moodle_url('/enrol/index.php', array('id' => $course->id,
                        'returnurl' => '/mod/forum/view.php?f=' . $forum->id)),
                        get_string('youneedtoenrol'));
                }
            }
        }
        print_error('nopostforum', 'forum');
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
        print_error("activityiscurrentlyhidden");
    }

    $SESSION->fromurl = get_local_referer(false);

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

    // Unsetting this will allow the correct return URL to be calculated later.
    unset($SESSION->fromdiscussion);

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

    // Retrieve the contexts.
    $modcontext    = context_module::instance($cm->id);
    $coursecontext = context_course::instance($course->id);

    if (! forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {  // User is a guest here!
                $SESSION->wantsurl = qualified_me();
                $SESSION->enrolcancel = get_local_referer(false);
                redirect(new moodle_url('/enrol/index.php', array('id' => $course->id,
                    'returnurl' => '/mod/forum/view.php?f=' . $forum->id)),
                    get_string('youneedtoenrol'));
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

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
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

    // Unsetting this will allow the correct return URL to be calculated later.
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

    // Unsetting this will allow the correct return URL to be calculated later.
    unset($SESSION->fromdiscussion);

} else if (!empty($delete)) {  // User is deleting a post
    // Useless for livedesk. Case deleted.
} else if (!empty($prune)) {  // Pruning
    // Useless for livedesk. Case deleted.
} else {
    print_error('unknowaction');
}

if (!isset($coursecontext)) {
    // Has not yet been set by post.php.
    $coursecontext = context_course::instance($forum->course);
}


// from now on user must be logged on properly

if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) { // For the logs
    print_error('invalidcoursemodule');
}
$modcontext = context_module::instance($cm->id);
require_login($course, false, $cm);

if (isguestuser()) {
    // just in case
    print_error('noguest');
}

if (!isset($forum->maxattachments)) {  // TODO - delete this once we add a field to the forum table
    $forum->maxattachments = 3;
}

$thresholdwarning = forum_check_throttling($forum, $cm);
$mform_post = new block_livedesk_reply_form('reply.php', array('course' => $course,
                                                        'cm' => $cm,
                                                        'coursecontext' => $coursecontext,
                                                        'modcontext' => $modcontext,
                                                        'forum' => $forum,
                                                        'post' => $post,
                                                        'subscribe' => \mod_forum\subscriptions::is_subscribed($USER->id, $forum,
                                                                null, $cm),
                                                        'thresholdwarning' => $thresholdwarning,
                                                        'edit' => $edit), 'post', '', array('id' => 'mformforum'));

$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $modcontext->id, 'mod_forum', 'attachment', empty($post->id)?null:$post->id, mod_forum_post_form::attachment_options($forum));

// Load data into form NOW!

if ($USER->id != $post->userid) {
    // Not the original author, so add a message to the end
    $data = new stdClass();
    $data->date = userdate($post->modified);
    if ($post->messageformat == FORMAT_HTML) {
        $data->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&course='.$post->course.'">'.
                       fullname($USER).'</a>';
        $post->message .= '<p><span class="edited">('.get_string('editedby', 'forum', $data).')</span></p>';
    } else {
        $data->name = fullname($USER);
        $post->message .= "\n\n(".get_string('editedby', 'forum', $data).')';
    }
    unset($data);
}

$formheading = '';
if (!empty($parent)) {
    $heading = get_string("yourreply", "forum");
    $formheading = get_string('reply', 'forum');
} else {
    if ($forum->type == 'qanda') {
        $heading = get_string('yournewquestion', 'forum');
    } else {
        $heading = get_string('yournewtopic', 'forum');
    }
}

$postid = empty($post->id) ? null : $post->id;
$draftid_editor = file_get_submitted_draft_itemid('message');
$currenttext = file_prepare_draft_area($draftid_editor, $modcontext->id, 'mod_forum', 'post', $postid, mod_forum_post_form::editor_options($modcontext, $postid), $post->message);

$manageactivities = has_capability('moodle/course:manageactivities', $coursecontext);
if (\mod_forum\subscriptions::subscription_disabled($forum) && !$manageactivities) {
    // User does not have permission to subscribe to this discussion at all.
    $discussionsubscribe = false;
} else if (\mod_forum\subscriptions::is_forcesubscribed($forum)) {
    // User does not have permission to unsubscribe from this discussion at all.
    $discussionsubscribe = true;
} else {
    if (isset($discussion) && \mod_forum\subscriptions::is_subscribed($USER->id, $forum, $discussion->id, $cm)) {
        // User is subscribed to the discussion - continue the subscription.
        $discussionsubscribe = true;
    } else if (!isset($discussion) && \mod_forum\subscriptions::is_subscribed($USER->id, $forum, null, $cm)) {
        // Starting a new discussion, and the user is subscribed to the forum - subscribe to the discussion.
        $discussionsubscribe = true;
    } else {
        // User is not subscribed to either forum or discussion. Follow user preference.
        $discussionsubscribe = $USER->autosubscribe;
    }
}

$mform_post->set_data(array(        'attachments'=>$draftitemid,
                                    'general'=>$heading,
                                    'subject'=>$post->subject,
                                    'message'=>array(
                                        'text'=>$currenttext,
                                        'format'=>empty($post->messageformat) ? editors_get_preferred_format() : $post->messageformat,
                                        'itemid'=>$draftid_editor
                                    ),
                                    'discussionsubscribe' => $discussionsubscribe,
                                    'mailnow'=>!empty($post->mailnow),
                                    'userid'=>$post->userid,
                                    'parent'=>$post->parent,
                                    'discussion'=>$post->discussion,
                                    'course'=>$course->id) +
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

                            (isset($discussion->pinned) ? array(
                                     'pinned' => $discussion->pinned) :
                                array()) +

                            (isset($post->groupid)?array(
                                    'groupid'=>$post->groupid):
                                array()) +

                            (isset($discussion->id)?
                                    array('discussion'=>$discussion->id):
                                    array()));

if ($mform_post->is_cancelled()) {
    if (!isset($discussion->id) || $forum->type === 'qanda') {
        // Q and A forums don't have a discussion page, so treat them like a new thread..
        redirect(new moodle_url('/mod/forum/view.php', array('f' => $forum->id)));
    } else {
        redirect(new moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id)));
    }
} else if ($fromform = $mform_post->get_data()) {

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

    if ($fromform->edit) {           // Updating a post. Useless case for livedesk.
        /*
        unset($fromform->groupid);
        $fromform->id = $fromform->edit;
        $message = '';

        // fix for bug #4314
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
        if (isset($fromform->groupinfo) && has_capability('mod/forum:movediscussions', $modcontext)) {
            if (empty($fromform->groupinfo)) {
                $fromform->groupinfo = -1;
            }

            if (!forum_user_can_post_discussion($forum, $fromform->groupinfo, null, $cm, $modcontext)) {
                print_error('cannotupdatepost', 'forum');
            }

            $DB->set_field('forum_discussions' ,'groupid' , $fromform->groupinfo, array('firstpost' => $fromform->id));
        }
        // When editing first post/discussion.
        if (!$fromform->parent) {
            if (has_capability('mod/forum:pindiscussions', $modcontext)) {
                // Can change pinned if we have capability.
                $fromform->pinned = !empty($fromform->pinned) ? FORUM_DISCUSSION_PINNED : FORUM_DISCUSSION_UNPINNED;
            } else {
                // We don't have the capability to change so keep to previous value.
                unset($fromform->pinned);
            }
        }
        $updatepost = $fromform; //realpost
        $updatepost->forum = $forum->id;
        if (!forum_update_post($updatepost, $mform_post)) {
            print_error("couldnotupdate", "forum", $errordestination);
        }

        // MDL-11818
        if (($forum->type == 'single') && ($updatepost->parent == '0')){ // updating first post of single discussion type -> updating forum intro
            $forum->intro = $updatepost->message;
            $forum->timemodified = time();
            $DB->update_record("forum", $forum);
        }

        if ($realpost->userid == $USER->id) {
            $message .= get_string("postupdated", "forum");
        } else {
            $realuser = $DB->get_record('user', array('id' => $realpost->userid));
            $message .= get_string("editedpostupdated", "forum", fullname($realuser));
        }

        $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);
        if ($forum->type == 'single') {
            // Single discussion forums are an exception. We show
            // the forum itself since it only has one discussion
            // thread.
            $discussionurl = new moodle_url("/mod/forum/view.php", array('f' => $forum->id));
        } else {
            $discussionurl = new moodle_url("/mod/forum/discuss.php", array('d' => $discussion->id), 'p' . $fromform->id);
        }

        $params = array(
            'context' => $modcontext,
            'objectid' => $fromform->id,
            'other' => array(
                'discussionid' => $discussion->id,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            )
        );

        if ($realpost->userid !== $USER->id) {
            $params['relateduserid'] = $realpost->userid;
        }

        $event = \mod_forum\event\post_updated::create($params);
        $event->add_record_snapshot('forum_discussions', $discussion);
        $event->trigger();

        redirect(
                forum_go_back_to($discussionurl),
                $message . $subscribemessage,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        */
    } else if ($fromform->discussion) { // Adding a new post to an existing discussion
        // Before we add this we must check that the user will not exceed the blocking threshold.

        unset($fromform->groupid);
        $addpost = $fromform;
        $addpost->forum = $forum->id;
        if ($fromform->id = forum_add_new_post($addpost, $mform_post)) {

            // ********************* NEW Livedesk operations ***********************
            // Delete this from post from queue not to cause a looped post.
            $DB->delete_records('block_livedesk_queue', array('itemid' => $fromform->id));
            // Update status of replied message.
            $replyed_post = $DB->get_record('block_livedesk_queue', array('itemid' => $reply));
            $replyed_post->mstatus = 'answered';
            $replyed_post->timeanswered = time();
            $replyed_post->answerby = $USER->id;
            $replyed_post->ans_session = sesskey();
            $replyed_post->locked = 0;
            $replyed_post->lockedby = null;
            $replyed_post->locktime = null;
            $DB->update_record('block_livedesk_queue', $replyed_post);
                // /NEW

            $fromform->deleted = 0;
            $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);

            print(get_string('reply_sent','block_livedesk'));
            print("<input type='button' click='this.close();' value='".get_string('close_window','block_livedesk')."' />");
            exit;

            // No completion.
            // No event marking in forum scope.

            // Livedesk.
        } else {
            print_error("couldnotadd", "forum", $errordestination);
        }
        exit;

    } else { // Adding a new discussion. Unused by livedesk.
        /*
        // The location to redirect to after successfully posting.
        $redirectto = new moodle_url('view.php', array('f' => $fromform->forum));

        $fromform->mailnow = empty($fromform->mailnow) ? 0 : 1;

        $discussion = $fromform;
        $discussion->name = $fromform->subject;

        $newstopic = false;
        if ($forum->type == 'news' && !$fromform->parent) {
            $newstopic = true;
        }
        $discussion->timestart = $fromform->timestart;
        $discussion->timeend = $fromform->timeend;

        if (has_capability('mod/forum:pindiscussions', $modcontext) && !empty($fromform->pinned)) {
            $discussion->pinned = FORUM_DISCUSSION_PINNED;
        } else {
            $discussion->pinned = FORUM_DISCUSSION_UNPINNED;
        }

        $allowedgroups = array();
        $groupstopostto = array();

        // If we are posting a copy to all groups the user has access to.
        if (isset($fromform->posttomygroups)) {
            // Post to each of my groups.
            require_capability('mod/forum:canposttomygroups', $modcontext);

            // Fetch all of this user's groups.
            // Note: all groups are returned when in visible groups mode so we must manually filter.
            $allowedgroups = groups_get_activity_allowed_groups($cm);
            foreach ($allowedgroups as $groupid => $group) {
                if (forum_user_can_post_discussion($forum, $groupid, -1, $cm, $modcontext)) {
                    $groupstopostto[] = $groupid;
                }
            }
        } else if (isset($fromform->groupinfo)) {
            // Use the value provided in the dropdown group selection.
            $groupstopostto[] = $fromform->groupinfo;
            $redirectto->param('group', $fromform->groupinfo);
        } else if (isset($fromform->groupid) && !empty($fromform->groupid)) {
            // Use the value provided in the hidden form element instead.
            $groupstopostto[] = $fromform->groupid;
            $redirectto->param('group', $fromform->groupid);
        } else {
            // Use the value for all participants instead.
            $groupstopostto[] = -1;
        }

        // Before we post this we must check that the user will not exceed the blocking threshold.
        forum_check_blocking_threshold($thresholdwarning);

        foreach ($groupstopostto as $group) {
            if (!forum_user_can_post_discussion($forum, $group, -1, $cm, $modcontext)) {
                print_error('cannotcreatediscussion', 'forum');
            }

            $discussion->groupid = $group;
            $message = '';
            if ($discussion->id = forum_add_discussion($discussion, $mform_post)) {

                $params = array(
                    'context' => $modcontext,
                    'objectid' => $discussion->id,
                    'other' => array(
                        'forumid' => $forum->id,
                    )
                );
                $event = \mod_forum\event\discussion_created::create($params);
                $event->add_record_snapshot('forum_discussions', $discussion);
                $event->trigger();

                if ($fromform->mailnow) {
                    $message .= get_string("postmailnow", "forum");
                } else {
                    $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                    $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
                }

                $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);
            } else {
                print_error("couldnotadd", "forum", $errordestination);
            }
        }

        // Update completion status.
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) &&
                ($forum->completiondiscussions || $forum->completionposts)) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }

        // Redirect back to the discussion.
        redirect(
                forum_go_back_to($redirectto->out()),
                $message . $subscribemessage,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        */
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

$PAGE->set_title("$course->shortname: $strdiscussionname ".format_string($toppost->subject));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('embedded');
$PAGE->add_body_class('in-livedesk');

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($forum->name), 2);

// checkup
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

// If there is a warning message and we are not editing a post we need to handle the warning.
if (!empty($thresholdwarning) && !$edit) {
    // Here we want to throw an exception if they are no longer allowed to post.
    forum_check_blocking_threshold($thresholdwarning);
}

if (!empty($parent)) {
    if (!$discussion = $DB->get_record('forum_discussions', array('id' => $parent->discussion))) {
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

        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir.'/plagiarismlib.php');
            echo plagiarism_print_disclosure($cm->id);
        }
    }
}

if (!empty($formheading)) {
    echo $OUTPUT->heading($formheading, 2, array('class' => 'accesshide'));
}

$data = new StdClass();
if (isset($postid)) {
    // $data->tags = core_tag_tag::get_item_tags_array('mod_forum', 'forum_posts', $postid);
    $mform_post->set_data($data);
}

$mform_post->display();

echo $OUTPUT->footer();
