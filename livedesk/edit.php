<?php

/**
 * edit.php
 * 
 * Displays and processes livedsk instance edition form.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 * @usecase add livedesk
 * @usecase update livedesk
 */
  
  	require_once('../../config.php');
  	require_once('livedesk_form.php');
  
  	$livedeskid = required_param('livedeskid', PARAM_INT); // 0 creates a new one
  	$courseid = optional_param('course', 1, PARAM_INT);

	// checking course
	  
  	$course = get_record('course', 'id', $courseid);
  
  	if(!$course){
    	error("invalid course");  
  	}

  	require_login();
  
 	$system_context = get_context_instance(CONTEXT_SYSTEM);
  	require_capability('block/livedesk:createlivedesk', $system_context);
  
  	$navlinks = array(array('name' => get_string('livedeskedit', 'block_livedesk'), 'link' => '', 'type' => 'title'));
  	$navigation = build_navigation($navlinks);
  	print_header("$course->shortname", 
                 "$course->fullname", 
                 $navigation, 
                 '', 
                 '', 
                 true);
    
  	$livedeskform = new livedesk_form();
  	
  	if ($livedeskform->is_cancelled()){
  		// keep track of origin course.
  		$url = $CFG->wwwroot.'/blocks/livedesk/manage.php?course='.$course->id;
  		redirect($url);
  	}
 
  	if($data = $livedeskform->get_data()){
  		
		if($data->livedeskid == 0){
          	//new livedesk
          	$livedesk = new stdClass();
          	$livedesk->name = $data->livedeskname ; 
          	$livedesk->description = $data->livedeskdescription ; 
          	$livedeskid = insert_record('block_livedesk_instance', $livedesk) ;
      	} else {
          	$livedesk = get_record('block_livedesk_instance', 'id', $data->livedeskid);
          
          	$livedesk->name= $data->livedeskname;
          	$livedesk->description= $data->livedeskdescription;
          
          	update_record('block_livedesk_instance', $livedesk);
          
          	$livedeskid = $livedesk->id;
      	}
      
      	delete_records('block_livedesk_modules','livedeskid', $livedeskid);
      	
      	$data->pluginids = $_POST['pluginids']; // this is quite ugly but works before the form has been cleaned up
 
      	if(!empty($data->pluginids)){
          	foreach($data->pluginids as $pluginid) {             
              	$livedesk_con = new stdClass;
              	$livedesk_con->livedeskid = $livedeskid;
              	$livedesk_con->plugininstance = $pluginid ;
              	insert_record('block_livedesk_modules', $livedesk_con);
          	}
      	}

  		$url = $CFG->wwwroot.'/blocks/livedesk/manage.php?course='.$course->id;
  		redirect($url);
      	exit;
  	}
    
	if($livedeskid > 0){               
      	$livedesk_obj = get_record('block_livedesk_instance', 'id', $livedeskid);
      	$livedesk_obj->livedeskname = $livedesk_obj->name;
      	$livedesk_obj->livedeskdescription = $livedesk_obj->description;
      	$livedeskform->set_data($livedesk_obj);
  	} 
  
   $data->livedeskid = $livedeskid;
   $livedeskform->set_data($data);
   print($livedeskform->display());  

   print_footer();  
   
?>
