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

defined('MOODLE_INTERNAL') || die();

/**
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Standard post install handler.
 *
 * Creates triggers to capture incoming forum posts and replicate
 * to the livedesk queue.
 */
function xmldb_block_livedesk_install() {
    global $DB;

    // install core triggers.
    $sql = "
        CREATE
        DEFINER CURRENT_USER
        TRIGGER IF NOT EXISTS LiveDesk_Trigger
        AFTER INSERT ON {forum_posts}
        FOR EACH ROW
        INSERT INTO
            {block_livedesk_queue} (
                itemid,
                message,
                callerid,
                moduleid,
                cmid,
                mstatus,
                timecreated,
                timeanswered,
                notified,
                locked,
                lockedby,
                locktime,
                priority,
                answerby,
                ans_session,
                answeredbydeskid)
        VALUES (
            new.id,
            new.message,
            new.userid,
            (SELECT
                id
             FROM
                 {modules}
             WHERE
                name = 'forum'),
            (SELECT
                cm.id
             FROM
                 {course_modules} cm,
                 {modules} m,
                 {forum_discussions} fd
             WHERE
                 cm.module = m.id AND
                 m.name = 'forum' AND
                 fd.forum = cm.instance AND
                 fd.id = new.discussion),
             'new',
             UNIX_TIMESTAMP(now()),
             0,0,0,0,0,10,0,0,0)
    ";
    $DB->execute($sql);

    $sql = "
        CREATE
        DEFINER CURRENT_USER
        TRIGGER IF NOT EXISTS LiveDesk_Trigger_Update_Discussion
        AFTER INSERT ON {forum_discussions}
        FOR EACH ROW
        UPDATE
            {block_livedesk_queue}
        SET
            cmid = (SELECT
                cm.id
             FROM
                 {course_modules} cm,
                 {modules} m
             WHERE
                 cm.module = m.id AND
                 m.name = 'forum' AND
                 cm.instance = new.forum )
        WHERE
            itemid = new.firstpost AND
            moduleid = (SELECT id FROM {modules} WHERE name = 'forum')
    ";

    $DB->execute($sql);

    block_livedesk::call_plugins_function('livedesk_on_install');
    set_config('block_livedesk_late_install', 1);
}

function xmldb_block_livedesk_late_install() {
    global $DB, $CFG;

    $context = context_system::instance();

    // Create the livedeskoperator role if absent.
    // Use platform install language for rôle name and description.
    if (!$DB->record_exists('role', array('shortname' => 'livedeskoperator'))) {
        $rolestr = new lang_string('livedeskoperator', 'block_livedesk', '', $CFG->lang);
        $roledesc = new lang_string('livedeskoperator_desc', 'block_livedesk', '', $CFG->lang);
        $livedeskopid = create_role($rolestr->out(), 'livedeskoperator', str_replace("'", "\\'", $roledesc->out()));
        set_role_contextlevels($livedeskopid, array(CONTEXT_SYSTEM));
    }

    role_change_permission($livedeskopid, $context, 'block/livedesk:runlivedesk', CAP_ALLOW);
    role_change_permission($livedeskopid, $context, 'block/livedesk:managelivedesk', CAP_ALLOW);
    role_change_permission($livedeskopid, $context, 'block/livedesk:viewuserstatistics', CAP_ALLOW);
    role_change_permission($livedeskopid, $context, 'block/livedesk:viewinstancestatistics', CAP_ALLOW);
    role_change_permission($livedeskopid, $context, 'block/livedesk:viewlivedeskstatistics', CAP_ALLOW);
}
