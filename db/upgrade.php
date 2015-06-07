<?php  //$Id: upgrade.php,v 1.1 2013-01-19 09:16:51 vf Exp $
// This file keeps track of upgrades to 
// the vmoodle block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php
function xmldb_block_livedesk_upgrade($oldversion = 0) {
    global $CFG, $THEME, $DB;
    
    $dbman = $DB->get_manager();

    $result = true;

    if ($result && $oldversion < 2013010500) { //New version in version.php
        $table = new xmldb_table('block_livedesk_instance');

        // Define field attenderreleasetime to be added to block_livedesk_instance
        $field = new xmldb_field('attenderreleasetime');

        if (!$dbman->field_exists($table, $field)) {

            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'description');
            // Launch add field attenderreleasetime
            $dbman->add_field($table, $field);
            // Define field stackovertime to be added to block_livedesk_instance
            $table = new xmldb_table('block_livedesk_instance');
            $field = new xmldb_field('stackovertime');
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'attenderreleasetime');
            // Launch add field stackovertime
            $dbman->add_field($table, $field);
            // Define field resolvereleasedelay to be added to block_livedesk_instance
            $table = new xmldb_table('block_livedesk_instance');
            $field = new xmldb_field('resolvereleasedelay');
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'stackovertime');
            // Launch add field resolvereleasedelay
            $dbman->add_field($table, $field);
        }

        // Define field servicestarttime to be added to block_livedesk_instance
        $table = new xmldb_table('block_livedesk_instance');
        $field = new xmldb_field('servicestarttime');
        if (!field_exists($table, $field)){
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'resolvereleasedelay');
            // Launch add field servicestarttime
            $dbman->add_field($table, $field);
            // Define field serviceendtime to be added to block_livedesk_instance
            $table = new xmldb_table('block_livedesk_instance');
            $field = new xmldb_field('serviceendtime');
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'servicestarttime');
            // Launch add field serviceendtime
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2013010500, 'livedesk');
    }

    if ($result && $oldversion < 2013010600) {
        // Define field maxstacksize to be added to block_livedesk_instance
        $table = new xmldb_table('block_livedesk_instance');
        $field = new xmldb_field('maxstacksize');
        if (!$dbman->field_exists($table, $field)){
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, 'resolvereleasedelay');
            // Launch add field maxstacksize
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2013010600, 'livedesk');
    }

    if ($result && $oldversion < 2013011400) {
        // Define field keepalivedelay and refresh to be added to block_livedesk_instance
        $table = new xmldb_table('block_livedesk_instance');

        $field = new xmldb_field('keepalivedelay');

        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, 300, 'serviceendtime');
            // Launch add field keepalivedelay
            $dbman->add_field($table, $field);

            $field = new xmldb_field('refresh');
            $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, 10, 'keepalivedelay');
            // Launch add field refresh
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2013011400, 'livedesk');
    }

    if ($result && $oldversion < 2014120300) {
        $table = new xmldb_table('block_livedesk_queue');

        // Define field attenderreleasetime to be added to block_livedesk_instance
        $field = new xmldb_field('mailsent');

        if (!$dbman->field_exists($table, $field)) {

            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, 0, 'answeredbydeskid');
            // Launch add field attenderreleasetime
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('block_livedesk_instance');

        $field = new xmldb_field('sendnotification');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, 0, 'refresh');

        if (!$dbman->field_exists($table, $field)) {
            // Launch add field keepalivedelay
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('notificationmail');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'sendnotification');
        if (!$dbman->field_exists($table, $field)) {
            // Launch add field refresh
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2014120300, 'livedesk');
    }

    if ($result && $oldversion < 2015010701) {

        $table = new xmldb_table('block_livedesk_instance');

        $field = new xmldb_field('notificationtitle');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, 'notificationmail');
        if (!$dbman->field_exists($table, $field)) {
            // Launch add field refresh
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2015010701, 'livedesk');
    }

    return $result;
}

