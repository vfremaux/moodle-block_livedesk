<?php
/**
 * service.php
 * 
 * Answers to dynamic ajax calls for refreshing data.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 */

    require_once('../../../config.php');
    require_once('../classes/livedesk.class.php');
     
    
    mysql_query('SET NAMES UTF8');

    $action = optional_param('action', null, PARAM_TEXT);
    $block_id = optional_param('bid', null, PARAM_TEXT);

     
    $context = get_context_instance(CONTEXT_BLOCK, $block_id);
    require_capability('block/livedesk:runlivedesk', $context);
      
    switch($action){
 
	    case 'load_liveentries':
	  
		    livedesk::load_liveentries($block_id);
		    exit;
	    
	    case 'set_message_status':
	    
		    $plugin_id  = optional_param('plugin_id', null, PARAM_INT);
		    $message_id  = optional_param('message_id', null, PARAM_INT);
		    $status  = optional_param('status', null, PARAM_TEXT);
		    livedesk::set_message_status($message_id, $status);
		    
		    break;
	    
	    case 'get_online_users_count':
	    
		    $bid  = optional_param('bid',null,PARAM_INT);
		    $user_count_arrs = livedesk::get_online_users_count($bid);
		    $output = $user_count_arrs;
		    
		    break;
	    
	    case 'keep_me_live':
	//       debugbreak();
		    $bid = optional_param('bid',null,PARAM_INT);
		    $courseid = optional_param('courseid', null, PARAM_INT);
		    $livedeskid = optional_param('livedeskid', null, PARAM_INT);
		    livedesk::keep_me_alive($courseid, $bid, $livedeskid);
		    exit;
		    break  ;
	    
	    case 'get_monitored_plugins':
	     
		    $livedeskid  = optional_param('livedeskid', null, PARAM_INT);
		    $output = livedesk::get_monitored_plugins($livedeskid);
		    
		    break  ;

	    case 'unlock_item':
	     
		    $livedeskid  = optional_param('livedeskid', null, PARAM_INT);
		    $itemid  = optional_param('itemid', null, PARAM_INT);
		    livedesk::unlock_message($itemid);
		    exit;

    }

    if(!empty($output)){
	    $json_data = json_encode($output);
	    echo($json_data);
    }
?>