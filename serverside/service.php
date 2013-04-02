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
     
    if (!check_session()){
    	$json_data = json_encode(array('error' => 'Session Lost Error'));
    	echo($json_data);
		die;
    }
    
    mysql_query('SET NAMES UTF8');

    $action = optional_param('action', null, PARAM_TEXT);
    $block_id = optional_param('bid', 0, PARAM_INT);
    $courseid = optional_param('course', 0, PARAM_INT);
      
    switch($action){
 
	    case 'load_liveentries':
	    	require_login();
	        $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 
		    livedesk::load_liveentries($block_id);
		    exit;
	    
	    case 'set_message_status':
            $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 
	    
		    $plugin_id  = optional_param('plugin_id', null, PARAM_INT);
		    $message_id  = optional_param('message_id', null, PARAM_INT);
		    $status  = optional_param('status', null, PARAM_TEXT);
		    livedesk::set_message_status($message_id, $status);
		    
		    break;
	    
	    case 'get_online_users_count':
	        $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 

		    $bid  = optional_param('bid',null,PARAM_INT);
		    $user_count_arrs = livedesk::get_online_users_count($bid);
		    $output = $user_count_arrs;
		    
		    break;
	    
	    case 'keep_me_live':
	//       debugbreak();
		    $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 

            $bid = optional_param('bid',null,PARAM_INT);
		    $courseid = optional_param('courseid', null, PARAM_INT);
		    $livedeskid = optional_param('livedeskid', null, PARAM_INT);
		    livedesk::keep_me_alive($courseid, $bid, $livedeskid);
		    exit;
		    break  ;
	    
	    case 'get_monitored_plugins':
	        $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 

		    $livedeskid  = required_param('livedeskid', PARAM_INT);
		    $output = livedesk::get_monitored_plugins($livedeskid);
		    
		    break  ;

	    case 'unlock_item':
            $context = get_context_instance(CONTEXT_BLOCK, $block_id);
            require_capability('block/livedesk:runlivedesk', $context); 
	     
		    $livedeskid  = optional_param('livedeskid', null, PARAM_INT);
		    $itemid  = optional_param('messageid', null, PARAM_INT);
		    livedesk::unlock_message($itemid);
		    exit;
         
        case 'discard_post':
          	$context = get_context_instance(CONTEXT_BLOCK, $block_id);
          	require_capability('block/livedesk:runlivedesk', $context);  
          	$itemid  = optional_param('messageid', null, PARAM_INT);
          	$ddate  = optional_param('date', null, PARAM_TEXT);
          	livedesk::livedesk_discard_post($itemid,$ddate);
          	break;
          
		case 'get_unnotified_messages':    
          	$course = get_record('course', 'id', "$courseid");
            require_login($course); // security. people could extract private discussions
          	$messages = livedesk::get_unnotified_messages();
         	$output = $messages ;
          	break;

		case 'change_state':    
            require_login(); // security. we need to get current user session loaded
            $item = required_param('item', PARAM_TEXT);
            $state = required_param('state', PARAM_TEXT);
            $SESSION->livedesk->$item = ($state == 'true');
            break;
    }
   
    if(!empty($output)){
	    $json_data = json_encode($output);
	    echo($json_data);
    }

/// Extra functions
    
    function check_session(){
    	global $USER;

    	return !empty($USER);
    }
?>