<?php  //$Id: upgrade.php,v 1.3 2010/05/19 15:58:23 vf Exp $

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
    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2013010500) { //New version in version.php
        
   /// Define field attenderreleasetime to be added to block_livedesk_instance
        $table = new XMLDBTable('block_livedesk_instance');
        $field = new XMLDBField('attenderreleasetime');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'description');

    /// Launch add field attenderreleasetime
        $result = $result && add_field($table, $field);

   /// Define field stackovertime to be added to block_livedesk_instance
        $table = new XMLDBTable('block_livedesk_instance');
        $field = new XMLDBField('stackovertime');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'attenderreleasetime');

    /// Launch add field stackovertime
        $result = $result && add_field($table, $field);

    /// Define field resolvereleasedelay to be added to block_livedesk_instance
        $table = new XMLDBTable('block_livedesk_instance');
        $field = new XMLDBField('resolvereleasedelay');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'stackovertime');

    /// Launch add field resolvereleasedelay
        $result = $result && add_field($table, $field);

    /// Define field servicestarttime to be added to block_livedesk_instance
        $table = new XMLDBTable('block_livedesk_instance');
        $field = new XMLDBField('servicestarttime');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'resolvereleasedelay');

    /// Launch add field servicestarttime
        $result = $result && add_field($table, $field);

    /// Define field serviceendtime to be added to block_livedesk_instance
        $table = new XMLDBTable('block_livedesk_instance');
        $field = new XMLDBField('serviceendtime');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'servicestarttime');

    /// Launch add field serviceendtime
        $result = $result && add_field($table, $field);
	}
	
    return $result;
}

?>
