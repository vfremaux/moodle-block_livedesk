<?php

function xmldb_block_livedesk_install(){
	global $DB;
	
	$sql = "DROP TRIGGER LiveDesk_Trigger";
	$DB->execute($sql);
	$sql = "DROP TRIGGER LiveDesk_Trigger_Update_Discussion";
	$DB->execute($sql);

	block_livedesk::call_plugins_function('livedesk_on_delete');

}
