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
require_once ($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');

class livedesk_form extends moodleform {

    function definition() {
        global $CFG;
        
        $mform    =& $this->_form;
        
        $mform->addElement('text', 'name', get_string('livedeskname', 'block_livedesk'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('htmleditor', 'description', get_string('livedeskdescription', 'block_livedesk'));

        $mform->addElement('text', 'resolvereleasedelay', get_string('resolvereleasedelay', 'block_livedesk'));
        $mform->setDefault('resolvereleasedelay', @$CFG->block_livedesk_resolving_post_release);
        $mform->addElement('text', 'attenderreleasetime', get_string('attenderreleasetime', 'block_livedesk'));
        $mform->setDefault('attenderreleasetime', @$CFG->block_livedesk_attendee_release_time);
        $mform->addElement('text', 'stackovertime', get_string('stackovertime', 'block_livedesk'));
        $mform->setDefault('stackovertime', @$CFG->block_livedesk_stack_over_time);
        $mform->addElement('text', 'maxstacksize', get_string('maxstacksize', 'block_livedesk'));
        $mform->setDefault('maxstacksize', @$CFG->block_livedesk_max_stack_size);
        $mform->addElement('text', 'keepalivedelay', get_string('keepalivedelay', 'block_livedesk'));
        $mform->setDefault('keepalivedelay', @$CFG->block_livedesk_keepalive_delay);
        $mform->addElement('text', 'refresh', get_string('refresh', 'block_livedesk'));
        $mform->setDefault('refresh', @$CFG->block_livedesk_refresh);
        
        $hours = array();
        for($i = 0; $i < 24 ; $i++){
			$hours[$i] = $i;
        }

        $minrange = array();
        for($i = 0; $i < 60 ; $i = $i + 5){
			$mins[$i] = $i;
        }
        
        $group1[] = & $mform->createElement('select', 'servicestarttime_h', get_string('servicestarttime', 'block_livedesk'), $hours);
        $mform->setDefault('servicestarttime_h', @$CFG->block_livedesk_service_timerange_start_h);
        $group1[] = & $mform->createElement('select', 'servicestarttime_m', get_string('servicestarttime', 'block_livedesk'), $mins);
        $mform->setDefault('servicestarttime_m', @$CFG->block_livedesk_service_timerange_start_m);
        $mform->addGroup($group1, 'servicestarttime', get_string('servicestarttime', 'block_livedesk'), array(''), false);

        $group2[] = & $mform->createElement('select', 'serviceendtime_h', get_string('serviceendtime', 'block_livedesk'), $hours);
        $mform->setDefault('serviceendtime_h', @$CFG->block_livedesk_service_timerange_end_h);
        $group2[] = & $mform->createElement('select', 'serviceendtime_m', get_string('serviceendtime', 'block_livedesk'), $mins);
        $mform->setDefault('serviceendtime_m', @$CFG->block_livedesk_service_timerange_end_m);
        $mform->addGroup($group2, 'serviceendtime', get_string('serviceendtime', 'block_livedesk'), array(''), false);

        $mform->addElement('html', $this->get_monitoredplugins_list());
        $mform->addElement('hidden', 'livedeskid');
        $mform->addElement('hidden', 'bid');
        
        $this->add_action_buttons();        
    }
    
    function set_data($data){
    	
    	if (!isset($data->servicestarttime)){
    		$data->servicestarttime_h = @$CFG->block_livedesk_service_timerange_start_h;
    		$data->servicestarttime_m = @$CFG->block_livedesk_service_timerange_start_m;
    	} else {
    		$data->servicestarttime_h = floor($data->servicestarttime / 3600);
    		$data->servicestarttime_m = floor($data->servicestarttime % 3600 / 60);
    	}
    	if (!isset($data->serviceendtime)) {
    		$data->serviceendtime_h = @$CFG->block_livedesk_service_timerange_end_h;
    		$data->serviceendtime_m = @$CFG->block_livedesk_service_timerange_end_m;
    	} else {
    		$data->serviceendtime_h = floor($data->serviceendtime / 3600);
    		$data->serviceendtime_m = floor($data->serviceendtime % 3600 / 60);
    	}

		parent::set_data($data);    	
	}
	
	function validation($data, $files){
		
		$errors = array();

		if (empty($data['name'])){
			$errors['name'] = get_string('errornoname', 'block_livedesk');
		}

		return $errors;
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
     
      		if ($monitoredplugins){
            	foreach($monitoredplugins as $plugin){
                	$selected_plugins_arr[] = $plugin->cmid;
            	}
        	}
      	}
        // load courses user has access to .
        $courses = get_records('course');
        $cap = "moodle/course:view";
		
        foreach ($courses as $id => $course) {
        	$context = get_context_instance(CONTEXT_COURSE,$id);
            
            if (!has_capability($cap, $context, $USER->id)) {
                unset($courses[$id]);
            } else {
                $table .= '<div class="course-div">';
                $table .= "<div style='font-size:14px;margin-bottom:3px;'><b>".$course->fullname."</b></div>";

				// load the plugins
				
				$plugins = livedesk::get_monitorable_plugins();
				$modulenames_cs = implode("','", $plugins);
				 
                $sql = "
                	SELECT 
                		cm.id,
                		cm.instance,
                		m.name as modulename
                	FROM 
                		{$CFG->prefix}course_modules cm,
                		{$CFG->prefix}modules m 
                	WHERE 
                 		cm.course = {$course->id} AND
                 		cm.module = m.id AND
                 		m.name IN ('$modulenames_cs')
                " ;
               
                if (!$plugins = get_records_sql($sql)){
                    $table .= get_string('nomonitorableplugins', 'block_livedesk');
                    continue;
                }
                
                // add name of the activity for all matched plugins.
                foreach($plugins as $pid => $p){
                	$plugins[$pid]->name = get_string('modulename', $p->modulename). ' : '.get_field($p->modulename, 'name', 'id', $p->instance);
                }
                                
                foreach ($plugins as $plugin){
                    if(in_array($plugin->id, $selected_plugins_arr)){
                    	$checked = " checked='checked' ";    
                    } else {
                    	$checked = "";    
                    }
                    
                    $table .= "<div style='padding-left:5px;'>
                    <input type='checkbox' name='pluginids[]' ".$checked." value='".$plugin->id."' />
                    ".format_string($plugin->name).'</div>';
                }
                 $table .= '</div>'; //course div
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