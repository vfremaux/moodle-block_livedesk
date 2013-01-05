<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for editing HTML block instances.
 *
 * @package   block_livedesk
 * @author 2012 Wafa adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_livedesk extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_livedesk');
        $this->version = 2013010500;
        $this->cron = 1;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('livedesk', 'block_livedesk'));
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG, $COURSE;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        
        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        if ($this->content_is_trusted()) {
            // fancy html allowed only on course, category and system blocks.
            $filteropt->noclean = true;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = "";
        
        if(has_capability('block/livedesk:runlivedesk', $context)){
        	$courseparam = '';
        	if ($COURSE->id > SITEID){
        		$courseparam = "&course=".$COURSE->id;
        	}
	        $this->content->text = '<p style="text-align:center;padding-top:10px;">';
	        $this->content->text .= "<a target=\"_blank\" href=\"{$CFG->wwwroot}/blocks/livedesk/run.php?bid={$this->instance->id}{$courseparam}\">";
	        $this->content->text .= "<img src=\"{$CFG->wwwroot}/blocks/livedesk/pix/logo.png\"/>";
	        $this->content->text .= '</a></p>';
        }
           
        if(has_capability('block/livedesk:managelivedesks',$system_context)){
        	$manageinstancesstr = get_string('manageinstances', 'block_livedesk');
	        $this->content->text .= '<div>';
	        $this->content->text .= "<ul class='livedeskmenu'>";
	        $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/manage.php\" ><b>{$manageinstancesstr}</b></a></li>";
        }
        
        if(has_capability('block/livedesk:createlivedesk',$system_context)){
        	$addlivedeskstr = get_string('adddeskinstances', 'block_livedesk');
	        $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/edit.php?livedeskid=0\" >$addlivedeskstr</a></li>";
	        $this->content->text .= '</ul>';
	        $this->content->text .= '</div>';
        }

        unset($filteropt); // memory footprint

        return $this->content;
    }


    function content_is_trusted() {
        global $SCRIPT;

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }
    
    public function instance_config_save($data, $pinned = false) {      
      
         
         $blockreference =  get_record('block_livedesk_blocks', 'blockid', $this->instance->id);
          
		if(empty($blockreference)) {
			$blockreference = new stdClass();
			$blockreference->blockid = $this->instance->id;
			$blockreference->livedeskid = $data->livedesk_instnace;
			insert_record('block_livedesk_blocks', $blockreference);
        } else {
            //just update 
        	$blockreference->livedeskid = $data->livedesk_instance;
			update_record('block_livedesk_blocks', $blockreference);
        }
              
        $data = stripslashes_recursive($data);
        $this->config = $data;
        $table = 'block_instance';
        if (!empty($pinned)) {
            $table = 'block_pinned';
        }
        return set_field($table, 'configdata', base64_encode(serialize($data)), 'id', $this->instance->id);
    }
    
    public function cron() {
        global $CFG;

    	mtrace( 'Cron block_livedesk' );
    
    	mtrace( 'Unlocking livedesk locked messages' );   

    	// do something    	
    	$current_time = time();

    	//load all queue records
    	$locked_records = get_records_sql('select * from '.$CFG->prefix.'block_livedesk_queue where locked =1');
    	$unlock_count = 0 ;
    
    	if($locked_records){
        	foreach ($locked_records as $rec){
          		$lock_period_mins = (($current_time - $rec->locktime)/60);
          
          		if($lock_period_mins > $CFG->block_livedesk_attendee_release_time){
					//unlock
					$rec->locked= 0;
					$rec->lockedby=null;
					$rec->locktime=null;
					$unlock_count++;
					update_record('block_livedesk_queue',$rec);
          		}
        	}
    	}
    
    	mtrace($unlock_count." messages were unlocked.");
    
    	// handle queue prioritization
    	mtrace('Fixing queue priorities.');   
    	$all_records = get_records('block_livedesk_queue', '', '');
  
    	foreach ($all_records as $rec){
        	$post_period_min = (($current_time - $rec->timecreated) / 60);
       
       		//new messages that have not been answered for long time should be pushed back in the stack.  
       		if($rec->mstatus == 'new' && $post_period_min >= $CFG->block_livedesk_resolving_post_release){
           		$rec->priority = 5; //lower the priority
           		update_record('block_livedesk_queue', addslashes_object($rec));
       		} 
    	}
  
    	return true;
    }

	/**
	* setup special triggers
	*/    
    function after_install(){
    	global $CFG;
    	
    	$sql = "
    		CREATE TRIGGER LiveDesk_Trigger
			AFTER INSERT ON {$CFG->prefix}forum_posts   
			FOR EACH ROW
			INSERT INTO 
				{$CFG->prefix}block_livedesk_queue (
					itemid, 
					message, 
					callerid, 
					moduleid,
					cmid, 
					mstatus, 
					timecreated, 
					timeanswered, 
					notified, 
					locked, 
					lockedby, 
					locktime, 
					priority, 
					answerby, 
					ans_session, 
					answeredbydeskid)
			VALUES (
				new.id, 
				new.message, 
				new.userid,
				(SELECT 
					id
				 FROM
				 	{$CFG->prefix}modules
				 WHERE
				    name = 'forum'),
				(SELECT 
					cm.id 
				 FROM 
				 	{$CFG->prefix}course_modules cm, 
				 	{$CFG->prefix}modules m, 
				 	{$CFG->prefix}forum_discussions fd 
				 WHERE 
				 	cm.module = m.id AND 
				 	m.name = 'forum' AND 
				 	fd.forum = cm.instance AND 
				 	fd.id = new.discussion ),
				 'new',
				 UNIX_TIMESTAMP(now()),
				 0,0,0,0,0,10,0,0,0)
		";
		
		execute_sql($sql);

		$sql = "
			CREATE TRIGGER LiveDesk_Trigger_Update_Discussion
			AFTER INSERT ON {$CFG->prefix}forum_discussions   
			FOR EACH ROW
			UPDATE 
				{$CFG->prefix}block_livedesk_queue 
			SET 
				cmid = (SELECT 
					cm.id 
				 FROM 
				 	{$CFG->prefix}course_modules cm, 
				 	{$CFG->prefix}modules m
				 WHERE 
				 	cm.module = m.id AND 
				 	m.name = 'forum' AND 
				 	cm.instance = new.forum )
			WHERE 
				itemid = new.firstpost AND
				moduleid = (SELECT id FROM {$CFG->prefix}modules WHERE name = 'forum')
		";

		execute_sql($sql);
    	
    }
    
    /**
    * Removes additional stuff.
    */
    function before_delete(){

    	$sql = "DROP TRIGGER LiveDesk_Trigger";
		execute_sql($sql);
    	
    	$sql = "DROP TRIGGER LiveDesk_Trigger_Update_Discussion";
		execute_sql($sql);
    }
}
