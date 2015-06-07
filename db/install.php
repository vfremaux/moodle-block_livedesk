<?php

function xmldb_block_livedesk_install(){
    global $DB;
    
    // install core triggers
    $sql = "
        CREATE TRIGGER LiveDesk_Trigger
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
                 fd.id = new.discussion ),
             'new',
             UNIX_TIMESTAMP(now()),
             0,0,0,0,0,10,0,0,0)
    ";
    $DB->execute($sql);

    $sql = "
        CREATE TRIGGER LiveDesk_Trigger_Update_Discussion
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
}