<?php

/**
 * manage.php
 * 
 * This file provides direct use cases for manage.php.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 * @usecase show livedesks
 */
  
  	require_once('../../config.php');
  	require_once($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');
    
  	$courseid = optional_param('course',1,PARAM_INT);
  	$action = optional_param('what', '', PARAM_TEXT);
  	
  	if ($action){
  		include 'manage.controller.php';
  	}

  	$course = get_record('course', 'id', $courseid);
  	if (!$course){
    	error("invalid course");  
  	}

/// security

  	require_login();
  
  	$system_context = get_context_instance(CONTEXT_SYSTEM);
  	require_capability('block/livedesk:managelivedesks', $system_context);

/// header and page start
  
  	$navlinks = array(
  		array('name' => $course->shortname, 'link' => $CFG->wwwroot.'/course/view.php?id='.$course->id, 'type' => 'link'),
  		array('name' => "Livedesks", 'link' => '', 'type' => 'title')
  	);
  	$navigation = build_navigation($navlinks);
  	print_header("$course->shortname", 
                 "$course->fullname", 
                 $navigation, 
                 '', 
                 '', 
                 true);
  
  	$livedesks = get_records('block_livedesk_instance');
  
// build the table 
  	
  	$namestr = get_string('name');
  	$commandsstr = get_string('commands', 'block_livedesk');
  	$blocksattachedstr = get_string('blocksattached', 'block_livedesk');
  	$pluginsattachedstr = get_string('pluginssattached', 'block_livedesk');

	$table = new StdClass; 
  	$table->width = '100%';
  	$table->head = array("<b>$namestr</b>", "<b>$blocksattachedstr</b>", "<b>$pluginsattachedstr</b>", "<b>$commandsstr</b>");
  	$table->size = array('40%', '20%', '20%', '20%');
  	$table->align = array('left', 'center', 'center', 'right');
      
  	if($livedesks){
      
      	foreach($livedesks as $livedesk){
      		
      		$ldblocks = livedesk::get_blocks_attached($livedesk);
      		$blocksattached = count($ldblocks);
      		$pluginsattached = count_records('block_livedesk_modules', 'livedeskid', $livedesk->id);
          
          	$row = array();
			$livedeskwindow = livedesk::get_livedesk_window_name($livedesk);
            if ($blocksattached){
	            $row[] = "<a href=\"{$CFG->wwwroot}/blocks/livedesk/run.php?ldid={$livedesk->id}&course={$course->id}\" target=\"$livedeskwindow\">".format_string($livedesk->name).'</a>';
	        } else {
	            $row[] = format_string($livedesk->name);
	        }
            $row[] = $blocksattached;
            $row[] = $pluginsattached;
            
            $cmd = "<a href=\"{$CFG->wwwroot}/blocks/livedesk/edit.php?course={$course->id}&livedeskid={$livedesk->id}\"><img src=\"{$CFG->pixpath}/t/edit.gif\"></a>";            
            if (!$blocksattached){
	            $cmd .= "&nbsp;<a href=\"{$CFG->wwwroot}/blocks/livedesk/manage.php?course={$course->id}&what=delete&livedeskid={$livedesk->id}\"><img src=\"{$CFG->pixpath}/t/delete.gif\"></a>";
	        }
            $row[] = $cmd;
            $table->data[] = $row;
      	}      
  	}
    
	echo "<div id=\"livedesk-instances-table\">";
  	print_heading(get_string('livedeskmanagement', 'block_livedesk'));
 	print_table($table);

	$location = $CFG->wwwroot."/blocks/livedesk/edit.php?course={$course->id}&livedeskid=0";	
	echo ('<p align="right"><input type="button" name="create_new_instance" value="'.get_string('createnewinstance', 'block_livedesk').'" onClick="window.location=\''.$location.'\'"  />').'</p>';

  	echo '</div>';
  
 	print_footer();
?>
