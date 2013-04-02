<?php

/**
 * statistics.php
 * 
 * Draws some statistic outputs
 *
 * TODO : draw some other usefull indicators : 
 * "average attended posts per attender" (on livedesk and instance, nonsense on self stats)
 * 
 * "attended/all posts ratio" (on livedesk and instance, aggregated for all attenders)
 * calculates ratio of all posts and part of them that have an answer from livedesk.
 *
 * "attended/all posts ratio per forum" (on livedesk and instance, aggregated for all attenders)
 * calculates ratio of all posts and part of them that have an answer from livedesk.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 */
   
   require_once('../../config.php');
   require_once('classes/livedesk.class.php');   
   
   $instance_id = optional_param('bid',PARAM_INT);

   $system_context = get_context_instance(CONTEXT_SYSTEM,1);
   
   // Livedesk statics
   
   $livedeskstatsstr = get_string('livedeskstats', 'block_livedesk');
   $attendedpostscountstr = get_string('attendedpostscount', 'block_livedesk');
   $maxattendedpostsbysessionstr = get_string('maxattendedpostsbysession', 'block_livedesk');
   $averageanswerdelaystr = get_string('averageanswerdelay', 'block_livedesk');
   $instancestatsstr = get_string('instancestats', 'block_livedesk');
   $userstatsstr = get_string('userstats', 'block_livedesk');
   print_heading(get_string('stats', 'block_livedesk'));

   if (has_capability('block/livedesk:viewlivedeskstatistics', $system_context)){
   	
   		$table = new StdClass;
   		$table->head = array("<b>$livedeskstatsstr</b>", '');
   		$table->width = "80%";
   		$table->size = array("90%", "10%");
   		$table->align = array('left', 'right');
   		
   		$table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('SYSTEM'));
   		$table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_attendedposts('SYSTEM'));
   		$table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('SYSTEM'));

		print_table($table);                            
   }
   
   //aggregated instance statics
   if(has_capability('block/livedesk:viewinstancestatistics',$system_context)) {
   	
   		$table = new StdClass;
   		$table->head = array("<b>$instancestatsstr</b>", '');
   		$table->width = "80%";
   		$table->size = array("90%", "10%");
   		$table->align = array('left', 'right');
   		
   		$table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('INSTANCE',$instance_id));
   		$table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_attendedposts('INSTANCE',$instance_id));
   		$table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('INSTANCE',$instance_id));

		print_table($table);                            
   }
   
   // my user statics
   if(has_capability('block/livedesk:viewuserstatistics',$system_context)) {

   		$table = new StdClass;
   		$table->head = array("<b>$userstatsstr</b>", '');
   		$table->width = "80%";
   		$table->size = array("90%", "10%");
   		$table->align = array('left', 'right');
   		
   		$table->data[] = array($attendedpostscountstr, livedesk::get_livedesk_stat_attendedposts('USER',NULL,$USER->id));
   		$table->data[] = array($maxattendedpostsbysessionstr, livedesk::get_livedesk_stat_attendedposts('USER',NULL,$USER->id));
   		$table->data[] = array($averageanswerdelaystr, livedesk::get_livedesk_stat_avganswertime('USER',NULL,$USER->id));

		print_table($table);                            
   }
   
?>