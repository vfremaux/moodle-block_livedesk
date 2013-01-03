<?php

/**
 * setup.php
 * 
 * This seems being OBSOLETE code. Eliminate
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 */
  
    require_once('../../config.php');
   
    $strtitle = get_string('livedesksetup', 'block_livedesk');

    require_login();
        
    $bid = required_param('bid',PARAM_INT);
    
    $system_context = get_context_instance(CONTEXT_SYSTEM);
    
    print_header();
        
    //check we are coming from a save
	if (@$_POST['pluginids']){
      	//first delete all previous connections .
      	delete_records('block_livedesk_modreference','blockinstance',$bid);
      
      	foreach(@$_POST['pluginids'] as $pluginid){
          	$livedesk_con = new stdClass;
          	$livedesk_con->blockinstance = $bid;
          	$livedesk_con->plugininstance = $pluginid;
          
          	insert_record('block_livedesk_modreference',$livedesk_con);
      	}
      
      	print("<div style='color:#009922;'>Settings saved Successfuly.</div>");
   }
     
    print("<form method='post' name='f1' >");
    print_config_pluginsselect();
    print('</div>');
    
    $closestr = get_string('close');
    $savesettingsstr = get_string('savesettings', 'block_livedesk');
    $saveandclosestr = get_string('saveandclose', 'block_livedesk');
           
    print('<div style="float:right;margin:auto;width:100%;text-align:center;margin-bottom:30px;">');
    print("<input type=\"button\" value=\"$closestr\" />";
    print("<input type=\"button\" onClick=\"this.value='{$savingsettingsstr}';document.forms['f1'].submit();\" value=\"$saveandclosestr\" />");
    print('</div>');
    
    print('</form>');
  
    /**
    * print the plugin election config.
    * 
    */
    function print_config_pluginsselect(){
        global $CFG, $USER, $bid;
        
     	print('<div id="pluginlist-content">');        
    	//load block instance connected plugins .
    	$plugins = get_records('block_livedesk_modreference','blockinstance',$bid);
    	$selected_plugins_arr=array();
    	if($plugins){
        	foreach($plugins as $plugin){
            	$selected_plugins_arr[] = $plugin->plugininstance;
        	}
    	}
 
    	//load courses user has access to .
    	$courses = get_records('course');
    	$cap = "moodle/course:view";
       
    	foreach ($courses as $id=>$course) {
    		$context = get_context_instance(CONTEXT_COURSE,$id);
        	if (!has_capability($cap, $context, $USER->id)) {
            	unset($courses[$id]);
        	} else {
            	print("<div style='font-size:14px;margin-bottom:5px;'><b>".$course->fullname)."</b></div>";
            	//load the forums 
            	$sql = "
            		SELECT 
            			cm.id,
            			cm.instance 
            		FROM 
            			{$CFG->prefix}course_modules cm,
            			{$CFG->prefix}modules m 
            		WHERE 
             			cm.course= {$course->id} AND
             			cm.module = m.id AND
             			m.name = 'forum'
            " ;
            
            $forums = get_records_sql($sql) ;
            
            if(!$forums){
                print("no forums found in this course.");
                continue;
            }
            
            foreach ($forums as $forum){
                if(in_array($forum->instance,$selected_plugins_arr)){
                	$checked = " checked='checked' ";    
                } else {
                	$checked = "";    
                }
                
                print("<div style='padding-left:5px;'>
                <input type='checkbox' name='pluginids[]' ".$checked." value='".$forum->instance."' />
                Forum:".$forum->instance.'</div>');
            }
            print("<br>");
        }
    }

    print('</div>');        
    }
    
    function print_config_general(){
       	print('<div id="pluginlist-content-general">');
       	print('<table>');
       	print('<tr>');
       	print('<td>'. get_string('block_livedesk_resolving_post_release', 'block_livedesk').'</td>');
       	print('<td> <input type="text" name="block_livedesk_resolving_post_release" id="block_livedesk_resolving_post_release" /></td>');
       	print('<td>'. get_string('block_livedesk_resolving_post_release_comment', 'block_livedesk').'</td>');
       	print('</tr>');
       
		print('<tr>');
       	print('<td>'. get_string('block_livedesk_attendee_release_time', 'block_livedesk').'</td>');
        print('<td> <input type="text" name="block_livedesk_attendee_release_time" id="block_livedesk_resolving_post_release" /></td>');
        print('<td>'. get_string('block_livedesk_attendee_release_time_comment', 'block_livedesk').'</td>');
        print('</tr>'); 
       
        print('<tr>');
        print('<td>'. get_string('block_livedesk_stack_over_time', 'block_livedesk').'</td>');
        print('<td> <input type="text" name="block_livedesk_stack_over_time" id="block_livedesk_resolving_post_release" /></td>');
        print('<td>'. get_string('block_livedesk_stack_over_time_comment', 'block_livedesk').'</td>');
        print('</tr>'); 
       
        print('<tr>');
        print('<td>'. get_string('block_livedesk_desk_service_timerange_start', 'block_livedesk').'</td>');
        print('<td> <input type="text" name="block_livedesk_desk_service_timerange_start" id="block_livedesk_desk_service_timerange_start" /></td>');
        print('<td>'. get_string('block_livedesk_desk_service_timerange_start', 'block_livedesk').'</td>');
        print('</tr>'); 
       
      	print('<tr>');
        print('<td>'. get_string('block_livedesk_desk_service_timerange_end', 'block_livedesk').'</td>');
        print('<td> <input type="text" name="block_livedesk_desk_service_timerange_end" id="block_livedesk_desk_service_timerange_end" /></td>');
        print('<td>'. get_string('block_livedesk_desk_service_timerange_end', 'block_livedesk').'</td>');
        print('</tr>');
       
        print('</table>');
        print('</div>');  
    }
   
    print('<script>') ;
    print('        
    tabbar = new dhtmlXTabBar("a_tabbar", "top");
    tabbar.setSkin(\'silver\');
    tabbar.setImagePath("js/dhtmlx/3.0/dhtmlxTabbar/codebase/imgs/");
    tabbar.addTab("a2", "General Settings", "100px");
    tabbar.addTab("a1", "Plugins Watchlist", "120px");
    tabbar.setContent("a1", "pluginlist-content");
    tabbar.setContent("a2", "pluginlist-content-general");

    tabbar.setTabActive("a1");');
    print('</script>');
    
?>
