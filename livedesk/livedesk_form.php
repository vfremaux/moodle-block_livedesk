<?php

/**
 * livedesk_form.php
 * 
 * Provides livedesk instance edition form.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 */

require_once ($CFG->dirroot.'/lib/formslib.php');

class livedesk_form extends moodleform {

    function definition() {
        global $CFG, $COURSE;
        
        $mform    =& $this->_form;
        
        $mform->addElement('text', 'livedeskname', get_string('livedeskname', 'block_livedesk'));
        $mform->addElement('htmleditor', 'livedeskdescription', get_string('livedeskdescription', 'block_livedesk'));
        $mform->addElement('html', $this->get_monitoredplugins_list());
        $mform->addElement('hidden', 'livedeskid');
        $mform->addElement('hidden', 'bid');
        
        $this->add_action_buttons();
        
    }
    
	// TODO : revert to moodle forms create checkbox element in groups with <br/> insertions
    function get_monitoredplugins_list(){  
    	global $USER, $CFG;

     	$table = "" ;  
     	$table .= '<table width="100%">';
     	$table .= '<tr>';
     	$table .= '<td>';
     	$table .= print_heading(get_string('monitorableplugins', 'block_livedesk'), 'left', 3, 'main', true);
     	$table .= '<div id="pluginlist-content" style="">';        
        //load block instance connected plugins .
      
      	$livedeskid = required_param('livedeskid', PARAM_INT);
      	$selected_plugins_arr = array();
      	if($livedeskid != 0){
      		$monitoredplugins = get_records('block_livedesk_modules', 'livedeskid', $livedeskid);
     
      		if($monitoredplugins){
            	foreach($monitoredplugins as $plugin){
                	$selected_plugins_arr[] = $plugin->cmid;
            	}
        	}
      	}
        //load courses user has access to .
        $courses = get_records('course');
        $cap = "moodle/course:view";
		
        foreach ($courses as $id => $course) {
        	$context = get_context_instance(CONTEXT_COURSE,$id);
            
            if (!has_capability($cap, $context, $USER->id)) {
                unset($courses[$id]);
            } else {
                $table .= '<div class="course-div">';
                $table .= "<div style='font-size:14px;margin-bottom:3px;'><b>".$course->fullname."</b></div>";

				//load the forums 
                $sql = "
                	SELECT 
                		cm.id,
                		cm.instance,
                		f.name 
                	FROM 
                		{$CFG->prefix}forum f, 
                		{$CFG->prefix}course_modules cm,
                		{$CFG->prefix}modules m 
                	WHERE 
                 		cm.course = {$course->id} AND
                 		cm.module = m.id AND
                 		cm.instance = f.id AND
                 		m.name = 'forum'
                " ;
               
                $plugins = get_records_sql($sql) ;
                
                if(!$plugins){
                    $table .= get_string('nomonitorableplugins', 'block_livedesk');
                    continue;
                }
                
                foreach ($plugins as $plugin){
                    if(in_array($plugin->id, $selected_plugins_arr)){
                    	$checked = " checked='checked' ";    
                    } else {
                    	$checked = "";    
                    }
                    
                    $table .= "<div style='padding-left:5px;'>
                    <input type='checkbox' name='pluginids[]' ".$checked." value='".$plugin->id."' />
                    ".$plugin->name.'</div>';
                }
                 $table .= '</div>';//course div
            }
        }

        $table.='</div>';            
        $table.='</td>';
        $table.='</tr>';
        $table.='</table>';
        return $table;
    }       
}
?>