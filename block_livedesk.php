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

require_once 'classes/livedesk.class.php';

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
        $this->version = 2013010601;
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
        global $CFG, $COURSE, $SITE;

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
        	
        	if (!empty($this->config->livedesk_instance)){
	        	$livedeskwindow = livedesk::get_livedesk_window_name($this->config->livedesk_instance);
		        $this->content->text = '<p style="text-align:center;padding-top:10px;">';
		        $this->content->text .= "<a target=\"$livedeskwindow\" href=\"{$CFG->wwwroot}/blocks/livedesk/run.php?course={$COURSE->id}&bid={$this->instance->id}{$courseparam}\">";
		        $this->content->text .= "<img src=\"{$CFG->wwwroot}/blocks/livedesk/pix/logo.png\"/>";
		        $this->content->text .= '</a></p>';
		    } else {
		    	$this->content->text = notify(get_string('instance_notbounded_to_livedesk', 'block_livedesk'), 'notifyproblem', 'center', true);
		    }
        }
           
        if(has_capability('block/livedesk:managelivedesks',$system_context)){
        	$manageinstancesstr = get_string('manageinstances', 'block_livedesk');
	        $this->content->text .= '<div>';
	        $this->content->text .= "<ul class='livedeskmenu'>";
	        $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/manage.php?course={$COURSE->id}\" ><b>{$manageinstancesstr}</b></a></li>";
        }
        
        if(has_capability('block/livedesk:createlivedesk',$system_context)){
        	$addlivedeskstr = get_string('adddeskinstances', 'block_livedesk');
	        $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/edit.php?livedeskid=0&course={$COURSE->id}\" >$addlivedeskstr</a></li>";
	        $this->content->text .= '</ul>';
	        $this->content->text .= '</div>';
        }

        unset($filteropt); // memory footprint

        return $this->content;
    }

	/**
	*
	*
	*/
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
    
    /**
    *
    *
    */
    public function instance_config_save($data, $pinned = false) {      
               
         $blockreference =  get_record('block_livedesk_blocks', 'blockid', $this->instance->id);
          
		if(empty($blockreference)) {
			$blockreference = new stdClass();
			$blockreference->blockid = $this->instance->id;
			$blockreference->livedeskid = $data->livedesk_instance;
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
    
    /**
    * processes some cron cleanup :
    * locked messages for too long must be released
    * too old messages (stack over time) must be discarded
    * do a per livedsk processing with licedesk instance characteristics
    */
    public function cron() {
        global $CFG;

    	mtrace( 'Cron block_livedesk' );
    
    	
    	$livedesks = get_records('block_livedesk_instance');
    	
    	if (empty($livedesks)) return;
    	
    	foreach($livedesks as $livedesk){

		/// check too old messages to discard them

    		mtrace( 'Removing messages older than '.$livedesk->stackovertime.' mins');   
	        // get liveinstance monitored plugins 

			$monitored_plugins_cs = '';
			if ($monitored_plugins = get_records('block_livedesk_modules', 'livedeskid', $livedesk->id)){
	        	$monitored_plugins_arr = array();
	            foreach($monitored_plugins as $p){
	                $monitored_plugins_arr[] = $p->cmid;  
				}
				$monitored_plugins_cs = implode("','", $monitored_plugins_arr);
			} else {
				continue;
			}
			
			$stacktimeover = time() - $livedesk->stackovertime * 60; // stack over time in minutes

			$discard_count = 0;    		
			$select = " 
				cmid IN ('$monitored_plugins_cs') AND 
				timecreated < $stacktimeover AND 
				mstatus = 'new' 
			";
    		if ($old_messages = get_records_select('block_livedesk_queue', $select, 'id, message, mstatus')){
	    		foreach($old_messages as $m){
	    			$m->mstatus = 'discarded';
	    			update_record('block_livedesk_queue', addslashes_object($m));
	    			$discard_count++;
	    		}
	    	}

    		mtrace('... '.$discard_count." messages were discarded.");

		/// release locked messages from too long time

    		mtrace( 'Unlocking livedesk locked messages' );   
	    	// load all locked queue records
	    	$timelockover = time() - ($livedesk->resolvereleasedelay * 60);
	    	$oldlockedrecords = get_records_select('block_livedesk_queue', " cmid IN ('$monitored_plugins_cs') AND locked = 1 AND locktime < $timelockover ");
	    	$unlock_count = 0 ;

	    	if($oldlockedrecords){
	        	foreach ($oldlockedrecords as $m){	          
					//unlock
					$rec->locked = 0;
					$rec->lockedby = null;
					$rec->timelock = null;
					$unlock_count++;
					update_record('block_livedesk_queue', $m);
	        	}
	    	}

    		mtrace('... '.$unlock_count." messages were unlocked.");

    		if ($livedesk->maxstacksize){
    			mtrace("truncating queue to $livedesk->maxstacksize entries.");
    			$sql = "
    				SELECT 
    					id,
    					timecreated,
    					timeanswered
    				FROM
						{$CFG->prefix}block_livedesk_queue
					WHERE
						cmid IN ('$monitored_plugins_cs')
					ORDER BY
						timecreated DESC, 
						timeanswered DESC,
						id DESC
    			";
    			if ($endstackentry = get_records_sql($sql, $livedesk->maxstacksize, 1)){
	    			$entry = array_pop($endstackentry);
					
					// do never delete answered messages
	    			$select = " cmid IN ('$monitored_plugins_cs') AND id <= {$entry->id} AND timeanswered = 0";
					if (!empty($CFG->livedesk_delete_oversize)){
		    			$ret = delete_records_select('block_livedesk_queue', $select);
	    				mtrace("... full truncated $ret->_numOfRows entries.");
	    			} else {
	    				$sql = "
	    					UPDATE
	    					   {$CFG->prefix}block_livedesk_queue
	    					SET
	    						mstatus = 'deleted'
	    					WHERE
	    						$select
	    				";
		    			execute_sql($sql, false);
	    				mtrace("... truncated entries.");
	    			}
	    		}
    		}

    	}
    
    	// handle queue prioritization
    	/*
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
    	*/
  
    	return true;
    }

	/**
	* setup special triggers
	*/    
    function after_install(){
    	global $CFG;
    	
    	// install core triggers
    	
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

		$this->call_plugins_function('livedesk_on_install');
    	
    }
    
    /**
    * Removes additional stuff.
    */
    function before_delete(){
    	
    	$sql = "DROP TRIGGER LiveDesk_Trigger";
		execute_sql($sql);
    	
    	$sql = "DROP TRIGGER LiveDesk_Trigger_Update_Discussion";
		execute_sql($sql);

		$this->call_plugins_function('livedesk_on_delete');
    }

	/**
	*
	*/
	private function call_plugins_function($fname, $args = null, $plugin = null){
    	global $CFG;
		
    	$otherplugins = livedesk::get_monitorable_plugins();
    	
    	if (!is_null($plugin)){
			if (is_file($CFG->dirroot.'/mod/'.$plugin.'/livedesklib.php')){
				include_once $CFG->dirroot.'/mod/'.$op.'/livedesklib.php';
				$plugindeletefunc = $plugin.'_'.$fname;
				return $plugindeletefunc($args);
			}
    	}

		foreach ($otherplugins as $op){
			if ($op == 'forum') continue;
			
			if (is_file($CFG->dirroot.'/mod/'.$op.'/livedesklib.php')){
				include_once $CFG->dirroot.'/mod/'.$op.'/livedesklib.php';
				$plugindeletefunc = $op.'_'.$fname;
				$plugindeletefunc($args);
			}
		}
	}
}
