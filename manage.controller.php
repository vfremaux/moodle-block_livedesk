<?php

/**
 * manage.controller.php
 * 
 * This file provides direct use cases for @see manage.php.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 * @usecase delete livedesk
 */

if ($action == 'delete'){
	$livedeskid = required_param('livedeskid', PARAM_INT);
	
	delete_records('block_livedesk_instance', 'id', $livedeskid);
	
	// TODO : probably more cleanup here
}

?>