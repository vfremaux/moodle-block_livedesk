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

/*
 * @package   block_livedesk
 * @version   moodle 1.9
 * @copyright 2012 Wafa Adham,, Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('globals.php');

class livedesk {

	/**
	* load all related live entries for this instance.
	* 
	* @param mixed $blockinstanceid
	* @param string $status
	*/
    public static function load_liveentries($blockinstanceid, $status = 'all'){
    	global $CFG, $STATUS_ARRAY, $SESSION, $USER;
    	         
        // first get the linked livedesk instance 
        $livedesk_instance = get_record('block_livedesk_blocks', 'blockid', $blockinstanceid);
		$livedesk_instanceid = $livedesk_instance->livedeskid; 
		
        // load liveinstance monitored plugins 
		$monitored_plugins = get_records('block_livedesk_modules', 'livedeskid', $livedesk_instanceid);
		
		$monitored_plugins_cs = '';
		if ($monitored_plugins){
        	$monitored_plugins_arr = array();
            foreach($monitored_plugins as $p){
                $monitored_plugins_arr[] = $p->cmid;  
			}
			$monitored_plugins_cs = implode("','", $monitored_plugins_arr);
		}
		
		// compute mstatus filtering in session
		$stateexcludes = array();
		if (!@$SESSION->livedesk->show_answered){
			$stateexcludes[] = " mstatus != 'answered' ";
		}
		if (!@$SESSION->livedesk->show_locked){
			$stateexcludes[] = " (mstatus != 'locked' OR lockedby = {$USER->id}) "; // I need keep my own locks
		}
		if (!@$SESSION->livedesk->show_discarded){
			$stateexcludes[] = " mstatus != 'discarded' ";
		}
		
		$statesessionclause = '';
		if (!empty($stateexcludes)){
			$statesessionclause = " (".implode(" AND ", $stateexcludes).") AND ";
		}
          
		$sql = "
			SELECT 
				q.*,
				cm.module,
				cm.instance,
				u.firstname,
				u.lastname
			FROM 
				{$CFG->prefix}block_livedesk_queue q
			JOIN
				{$CFG->prefix}course_modules cm
			ON 
				cm.id = q.cmid
			LEFT JOIN
          		{$CFG->prefix}user u 
          	ON 
          		u.id = q.lockedby 
          	WHERE 
          		cmid IN ('{$monitored_plugins_cs}') AND
          		$statesessionclause
          		mstatus != 'deleted'
          	ORDER BY
          		timecreated DESC
        ";
           
        $entries = get_records_sql($sql);
        
		//we now form the xml for the grid.
		header("Content-Type:text/xml");
		print('<?xml version="1.0" encoding="utf-8" ?>');
		print("<rows>");
		print("<debug>");
	    print_object(@$SESSION->livedesk);
	    echo " ".$statesessionclause;
		print("</debug>");

         
		if(!empty($entries)){
        	foreach ($entries as $entry){
	    		$module = get_record('modules', 'id', $entry->module);
	    		if ($moduleinstance = get_record($module->name, 'id', $entry->instance)){
		    		$entry->seen = livedesk::livedesk_get_seen_information($module, $moduleinstance, $entry->itemid);
		    	} else {
	    			$entry->seen = 0;
		    		$entry->mstatus = 'discarded';
					$moduleinstance->name = get_string('deleted', 'block_livedesk');
		    	}
				$caller = get_record('user', 'id', $entry->callerid);
				$classes = $entry->mstatus;
				$bg_color = '';  
				if ($entry->mstatus == 'answered'){
					$bg_color = '#DDF0FF';  
				}
				if ($entry->locked){
					$icon = $STATUS_ARRAY['locked'];  
					$bg_color = '#ffc0c0';  
					$classes .= ' locked';
				} else {
					$icon = $STATUS_ARRAY[$entry->mstatus]; 
				}
                $seen_img = " <img src='pix/eye.png' />";         
				if (!$entry->seen){
                    $seen_img = " <img src='pix/eye-half.png' />";     
					$classes .= ' unseen';
				}
				$longmessage = htmlentities(shorten_text($entry->message, 200));                      
				$message = "<label class='".$classes."' title=\"$longmessage\" >".shorten_text($entry->message, 60)."</label>";
				$modname = get_string('modulename', $module->name);
				$modcourseshort = get_field('course', 'shortname', 'id', $moduleinstance->course);
				
				$modpix = "<img src=\"{$CFG->modpixpath}/{$module->name}/icon.gif\" title=\"[$modcourseshort] $modname\" />"; 
				
				$dimmedpre = ($entry->mstatus == 'discarded') ? '<div class="dimmed">' : '' ;
				$dimmedpost = ($entry->mstatus == 'discarded') ? '</div>' : '' ;
                      
				$row =  '<row id="liveentry_'.$entry->id.'" bgColor="'.$bg_color.'"> 
				<userdata name="status" >'.$entry->mstatus.'</userdata>
				<userdata name="notified">'.$entry->notified.'</userdata>
				<userdata name="messageid">'.$entry->id.'</userdata>
				<userdata name="itemid">'.$entry->itemid.'</userdata>
				<userdata name="cmid">'.$entry->cmid.'</userdata>
				<userdata name="timecreated">'.$entry->timecreated.'</userdata>
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.$entry->id.$dimmedpost.']]>    </cell>  
				<cell  bgColor="'.$bg_color.'"><![CDATA['.'<img src="pix/'.$icon.'"/>'.$seen_img.']]>    </cell>   
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.$message.$dimmedpost.']]>    </cell>  
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.fullname($caller).$dimmedpost.']]>    </cell> 
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.date('d.m.Y h:i',$entry->timecreated).$dimmedpost.']]>    </cell>  
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.'[ '.$modpix.' '.$moduleinstance->name.']'.$dimmedpost.' ]]>    </cell> 
				<cell  bgColor="'.$bg_color.'"><![CDATA['.$dimmedpre.$entry->firstname.' '.$entry->lastname.$dimmedpost.' ]]>    </cell> 
				
				</row>' ;
				print($row);
             }
         } else {
        //     print('<row></row>');
         }
         
         $sql = "
         	UPDATE 
         		{$CFG->prefix}block_livedesk_queue 
         	SET 
         		notified = 1
         ";
        // execute_sql($sql,false);
         
		print('</rows>');           
	}

	/**
	*
	* @param int $messageid
	*
	*/        
	static function set_message_status($messageid, $status){
          
		$message = get_record('block_livedesk_queue', 'id', $messageid);
          
		if(!$message){
		  	return false;
		}
          
		$message->mstatus = $status;
          
		return update_record('block_livedesk_queue', addslash_object($message));
   	}

	/**
	*
	* @param int $messageid
	*
	*/        
	static function unlock_message($messageid){
		$message = get_record('block_livedesk_queue', 'id', "$messageid");
		$message->locked = 0;
		if (!$message->mstatus != 'answered'){
			$message->lockedby = 0;
			$message->answeredby = 0;
			$message->locktime = 0;
		}
		update_record('block_livedesk_queue', addslashes_object($message));
   	}
      
	/**
    * get livedesk statistics
    * 
    * @param mixed $level  : [SYSTEM,INSTANCE,USER]
    * @param mixed $instance_id, livedesk instane id (Only needed when level is instance)
    * @param mixed $user_id, livedesk agent id (only needed when leve is USER)
    */
    static function get_livedesk_stat_attendedposts($level, $blockid = null, $userid = null){  
    	global $CFG;
                                                                                                           
		$where = '';
       
		if($level == 'SYSTEM'){
            $where = ' WHERE ';  
		} elseif($level == 'INSTANCE'){
            if(!$blockid){
                print_error("instance_id must be provided");
            }
			$sql = "
				SELECT DISTINCT 
					cmid 
				FROM 
					{$CFG->prefix}block_livedesk_modules mp, 
            		{$CFG->prefix}block_livedesk_blocks br 
            	WHERE 
            		br.blockid = {$blockid} AND
            		br.livedeskid = mp.livedeskid
			";
   
            $monitoredplugins = get_records_sql($sql);
          
            if($monitoredplugins){
				$mp = array();
                foreach($monitoredplugins as $p){
                    $mp[] = $p->cmid;  
				}
                $mp_cs = implode("','",$mp);
            } 
           
            $where = " WHERE cmid IN ('{$mp_cs}') AND ";
        } elseif($level == 'USER'){
              if(!$userid){
                  print_error("Internal error : userid must be provided");
              }
              $where = " WHERE answerby = ".$userid." AND" ;
        }
          
        $sql = "
          	SELECT 
          		COUNT(*) AS counter 
          	FROM 
          		{$CFG->prefix}block_livedesk_queue 
          	$where
          		mstatus = 'answered'
        ";
          
        $result = get_record_sql($sql);
          
        return $result->counter;  
	}
            
	static function get_livedesk_stat_maxpostsession($level, $blockid = null, $userid = null){  
		global $CFG;
                                                                                                  
        $where = "";
       
		if ($level == "SYSTEM"){
            $where = " WHERE ";  
		} elseif($level == 'INSTANCE') {
			if(!$blockid){
                  print_error("instance_id must be provided");
            }
            $sql = "
            	SELECT DISTINCT 
            		cmid 
            	FROM 
            		{$CFG->prefix}block_livedesk_modules mp, 
            		{$CFG->prefix}block_livedesk_blocks br 
            	WHERE 
            		br.blockid = {$blockid} AND
            		br.livedeskid = mp.livedeskid
            ";
   
			$monitoredplugins = get_records_sql($sql);
          
			if($monitoredplugins){
				$mp = array();
                foreach($monitoredplugins as $p) {
                	$mp[] = $p->cmid;  
                }
                $mp_cs = implode("','",$mp);
            } 
           
        $where = " WHERE cmid IN ('{$mp_cs}') AND ";

		} else if($level == 'USER'){
            if(!$user_id){
            	print_error("userid must be provided");
            }
            $where = " WHERE answerby = {$userid} AND " ;
        }
          
        $sql = "
          	SELECT 
          		max(y.counter) AS maxcounter 
          	FROM (
          		SELECT 
          			COUNT(*) AS counter 
          		FROM 
          			{$CFG->prefix}block_livedesk_queue 
          			".$where." 
          			mstatus = 'answered' 
          		GROUP BY 
          			ans_session ) y 
		";
                    
        $result = get_record_sql($sql);
          
		if ($result){
			$max =  $result->maxcounter;
		} else {
			$max = 0;
		}
          
		return $result->maxcounter;            
	}

	/**
	*
	* @param int $level
	* @param int $blockid
	* @param int $userid
	*/ 
    static function get_livedesk_stat_avganswertime($level, $blockid = null, $userid = null){  
    	global $CFG;
                                                                                                           
		$where = '';
       
		if($level == 'SYSTEM'){
            $where = ' WHERE ';  
        } elseif ($level == 'INSTANCE'){
			if(!$blockid){
				print_error("instance_id must be provided");
			}

            $sql = "
            	SELECT DISTINCT 
            		cmid
            	FROM 
            		{$CFG->prefix}block_livedesk_modules mp, 
            		{$CFG->prefix}block_livedesk_blocks br 
            	WHERE 
            		br.blockid = {$blockid} AND
            		br.livedeskid = mp.livedeskid
			";
   
            $monitoredplugins = get_records_sql($sql);
          
            if($monitoredplugins){
				$mp = array();
				foreach($monitoredplugins as $p){
					$mp[] = $p->cmid;  
				}
				$mp_cs = implode("','",$mp);
			} 
           
			$where = " WHERE cmid IN ('{$mp_cs}') AND ";
        } else if($level == 'USER') {
            if(!$userid) {
            	print_error("userid must be provided");
            }
        	$where = " WHERE answerby = {$userid} AND" ;
        }
          
		$sql = "
          	SELECT 
          		id,
          		timeanswered,
          		timecreated 
          	FROM 
          		{$CFG->prefix}block_livedesk_queue 
          		".$where." 
          		mstatus = 'answered'
        ";
          
        $results = get_records_sql($sql);
          
		$sum = 0;
		$i = 0;
		$avg = 0;
		if ($results){  
			foreach($results as $r){
				if ($r->timeanswered == 0){
                     continue; 
				}
				$i++;
				$sum = $avg + $r->timeanswered - $r->timecreated;                  
			}

			if($i > 0){
              	$avg = $sum/$i; //secs avg
			} else {
				$avg = 0;
			}
		}
          
        return $avg;      
    }
    
    /**
    *
    * @param int $courseid
    * @param int $bid the block instance id
    * @param int $livedeskid the livedesk instance id
    */  
	static function keep_me_alive($courseid, $bid, $livedeskid){  
		global $USER;
		
		add_to_log($courseid, 'livedesk', 'run', 'run.php', $livedeskid, $bid, $USER->id);   
    }
      
	static function get_online_users_count($blockid){
		global $CFG, $USER;
          
		$timetoshowusers = 300; //Seconds default 
		$timefrom = 100 * floor((time()-$timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache
                       
		$livedesk_reference = get_record('block_livedesk_blocks', 'blockid', $blockid);
		$livedeskid = $livedesk_reference->livedeskid;
            
		//get the involved courses for this livedesk instance 
		$sql = "
			SELECT DISTINCT 
				course 
        	FROM 
        		{$CFG->prefix}course_modules 
        	WHERE 
        		id IN ( 
        			SELECT DISTINCT 
        				cmid 
        			FROM 
        				{$CFG->prefix}block_livedesk_modules mp, 
        				{$CFG->prefix}block_livedesk_blocks br 
        			WHERE 
        				br.blockid = {$blockid} AND
        				br.livedeskid = mp.livedeskid 
        		)
        ";
		//  print($sql );
		$records = get_records_sql($sql);
            
		if($records){
			foreach ($records as $rec){
                $courses_arr[] = $rec->course;
            }
		} else {                         
            $results['users_count'] = 0; //$usercount;
            $results['attenders_count'] = 0; //$attenders_count;
            return $results;            
		}
                        
		$courses_csv = implode(",",$courses_arr) ;
            
		$select = "
			SELECT 
				u.id, 
				u.username, 
				u.firstname, 
				u.lastname, 
				u.picture, 
				max(ul.timeaccess) AS lastaccess ";
        $from = "FROM {$CFG->prefix}user_lastaccess ul,
                      {$CFG->prefix}user u
                      ";
        $where =  "WHERE ul.timeaccess > $timefrom
                   AND u.id = ul.userid
                   AND ul.courseid in ($courses_csv)
                  ";
        $order = "ORDER BY lastaccess DESC ";
        $groupby = "GROUP BY u.id, u.username, u.firstname, u.lastname, u.picture ";
        
        $minutes  = floor($timetoshowusers/60);

        // $SQL = $select . $from . $where . $groupby . $order;

        $usercount = count_records_sql("SELECT COUNT(DISTINCT(u.id)) $from $where");
         
        $sql2 = "
        	SELECT DISTINCT 
				userid 
			FROM 
				{$CFG->prefix}log 
			WHERE
				time > $timefrom AND 
				action = 'run' AND 
				module = 'livedesk' AND 
				info = {$livedeskid}
		";
        $result2 = get_records_sql($sql2);
        
        if($result2){
            $attenders_count = count($result2);
            foreach($result2 as $uid => $notused){
            	$user = get_record('user', 'id', $uid, '', '', '', '', 'id,firstname,lastname');
            	$attender = new StdClass;
            	$attender->class = ($user->id == $USER->id) ? 'isme' : 'isnotme' ;
            	$attender->name = fullname($user);
            	$results['attenders'][] = $attender;
            }
        } else {
            $attenders_count = 0;
        }
      
        $results['users_count'] = $usercount;
        $results['attenders_count'] = $attenders_count;
                    
        return $results;
	}
      
    /**
	* get all inwtances of plugins (moodle activities) that are monitored.
	*
	*/
	static function get_monitored_plugins($livedeskid){
        global $CFG;
        
        $sql = "
        	SELECT 
        		mp.cmid,
        		mp.livedeskid,
        		m.name as plugintype,
        		cm.instance as plugininstance
        	FROM 
        		{$CFG->prefix}block_livedesk_modules mp,
        		{$CFG->prefix}course_modules cm,
        		{$CFG->prefix}modules m 
            WHERE  
        		cm.id = mp.cmid AND
				m.id = cm.module AND
            	mp.livedeskid = {$livedeskid}
      	";
           
		$monitoredplugins = get_records_sql($sql);
		$plugins_arr = array();
		if ($monitoredplugins){
			$i = 0;
			foreach($monitoredplugins as $plugin){
                
                if($plugin->plugintype == null){
                    continue;
                }
                
				$plugin_obj = get_record($plugin->plugintype, 'id', $plugin->plugininstance);
                $plugins_arr[$i]['name'] = $plugin_obj->name;
                $plugins_arr[$i]['id'] = $plugin_obj->id;
                $plugins_arr[$i]['cmid'] = $plugin->cmid;
                $i++;
            }
        }
          
        return $plugins_arr;
    }

	/**
	* for known types, retrieves adequately the "seen" information
	* 
	* TODO : extends by discovering in local implementation the way to get it
	* for contributive plugins*
	*
	* @param ref $module the moodle module definition
	* @param ref $moduleinstance if needed to get entry information, the effective instance record of the module
	* @param int $entryid
	* 
	* @return wether not handled, return true (seen by default)
	*/    
    function livedesk_get_seen_information(&$module, &$moduleinstance, $itemid){
    	
    	$seen = true;
    	
    	if ($module->name == 'forum'){
    		$seen = get_field('forum_posts', 'mailed', 'id', $itemid); 
    	}

		return $seen;
    }
    
    /**
    *  mark message as discarded 
    * @param ref $post_id post id to be discarded.
    * @param ref $discard_date if this parameter is given then we discard posts befre this date
    * @return boolean of the operation success or failure.
    */
    static function livedesk_discard_post($messageid,$discard_date=null){
       	global $CFG;
    
       	if($discard_date != null){
           	//this is a discard before date
           	$discard_date_linux = strtotime($discard_date); 
           	$sql = "
           		UPDATE 
           			{$CFG->prefix}block_livedesk_queue 
				SET 
					mstatus = 'discarded' 
				WHERE 
					timecreated < {$discard_date_linux}
                AND 
                	mstatus = 'new'
           ";
           execute_sql($sql,false);
           return;    
       	} 
        
       	$message = get_record('block_livedesk_queue', 'id', $messageid);
       
       	// invalid message
       	if(!$message){
           return 0;
       	}
       
       	$message->mstatus = 'discarded';
       	return update_record('block_livedesk_queue', addslashes_object($message));
    }
    
    
    /**
    *  get all messages that were not notified 
    * @param ref $post_id post id to be discarded.
    * @param ref $discard_date if this parameter is given then we discard posts befre this date
    * @return boolean of the operation success or failur.
    */
    static function get_unnotified_messages(){   
    	global $CFG, $USER;
        //get the running livedesks .
        
       	$livedesks_arr = self::get_running_livedesks($USER->id, 5);      
       
       	if(empty($livedesks_arr)){
           	return;
       	}
       
       	$livedesks_csv = implode(",", $livedesks_arr);
       
		$sql = "
       		SELECT 
       			q.*,
       			ldm.livedeskid
			FROM 
          		{$CFG->prefix}block_livedesk_queue q,
          		{$CFG->prefix}block_livedesk_modules ldm 
       		WHERE 
          		q.cmid = ldm.cmid 
        	AND 
           		ldm.livedeskid IN ({$livedesks_csv}) AND
           		q.mstatus NOT IN ('deleted', 'discarded', 'locked', 'answered')
        	AND 
           		q.notified = 0
       	";
    
	    $livedeskqueues = array();
       	if ($messages = get_records_sql($sql)){
         	foreach($messages as $m){   
	       		$livedeskqueues[$m->livedeskid][$m->id] = $m->message;
	       	}
       	}

       	if (!empty($livedeskqueues)){ 
         	foreach($livedeskqueues as $ldid => $ldq){   
         		$livedesk = get_record('block_livedesk_instance', 'id', $ldid);
         		$livedeskwin = livedesk::get_livedesk_window_name($livedesk);
         		$notification->message = '';
     			$notification->id = $ldid;
				
				// checking user can access from at least one block
     			$accessblockid = livedesk::find_block_by_instance_user($livedesk->id);
     			if (!$accessblockid){
     				continue;
     			}

				// checking opening/closing time of the livedesk
				if (isset($livedesk->servicestarttime) && isset($livedesk->serviceendtime)){
					$now = time();
					$currenthour = date('H', $now);
					$currentmins = date('i', $now);
					$currentstamp = $currenthour * 3600 + $currentmins * 60;
					if ($currentstamp < $livedesk->servicestarttime || $currentstamp > $livedesk->serviceendtime){
						continue;
					}
				}
				$num = count($ldq);
         		if ($num <= 3) {
         			// globalize message
         			$notification = new StdClass();
         			$mesbody = get_string('messagesinqueue', 'block_livedesk', $livedesk->name);
         			$notification->message .= "<a class=\"livedesk-notification-link\" href=\"{$CFG->wwwroot}/blocks/livedesk/run.php?bid={$accessblockid}\" target=\"$livedeskwin\">$mesbody</a><br/>";
         			foreach($ldq as $q){
	         			$notification->message .= $q.'<br/>';
	         		}
         		} else {
         			$e = new StdClass;
         			$e->count = $num;
         			$e->queue = $livedesk->name;
         			$mesbody = get_string('morethanmessagesinqueue', 'block_livedesk', $e);
         			$notification->message .= "<a class=\"livedesk-notification-link\" href=\"{$CFG->wwwroot}/blocks/livedesk/run.php?bid={$accessblockid}\" target=\"$livedeskwin\">$mesbody</a><br/>";
         		}
     			$messages_arr[] = $notification;       			
         	}
       	}
       
       	if (!empty($messages_arr)) return $messages_arr;
       	return '';
    }
    
    /**
    * extracts from log the livedesks the user has recent activity in
    */
    static function get_running_livedesks($user_id, $mins_span = 50){
    	global $CFG;
     
        $current_time = time();
        //timespan is 5 mins.
        $sql = "
        	SELECT 
                DISTINCT info from {$CFG->prefix}log 
            WHERE 
                userid = {$user_id} 
            AND 
              	module = 'livedesk' 
            AND 
              	action = 'run' 
            AND 
              	time BETWEEN ".($current_time - ($mins_span*60))." AND ".$current_time ."
        " ;
        
        $livedesks_arr = array();
        $results = get_records_sql($sql);
        
        if($results){
            foreach($results as $r){
                $livedesks_arr[] = $r->info;
            }
        }
        
        return $livedesks_arr;
        
    }
    
    /**
    * gets the list of monitorable plugins. 
    * by default, only forums are monitorable, but other plugins may be also.
    *
    */
    static function get_monitorable_plugins(){
    	global $CFG;
    	
    	$monitorable_plugins = array('forum');
	
		// add integrator defined plugins    	
    	if (@$CFG->livedesk_plugins){
			$monitorable_plugins = array_merge($monitorable_plugins, explode(',', $CFG->livedesk_plugins));			
    	}
    	
    	return $monitorable_plugins;
    }
    
    static function find_block_by_instance_course($livedeskid, $courseid){

		if (!$possible = get_records_menu('block_livedesk_blocks', 'livedeskid', $livedeskid, '', 'blockid,id')){
			return false;
		}
		$candidatesids = array_keys($possible);
		$candidates_ids_cs = implode("','", $candidatesids); 
		$select = " id IN ('$candidates_ids_cs') AND pageid = $courseid ";
		if ($candidates = get_records_select('block_instance', $select)){			
			$blockids = array_keys($candidates);
			return $blockids[0];
		}
		return false;
    }

	/**
	* finds any block giving appropriate access to current user in a livedesk instance
	*
	*/
    static function find_block_by_instance_user($livedeskid, $userid = 0){
    	global $USER;
    	
    	if ($userid == 0) $userid = $USER->id;

		if (!$possible = get_records_menu('block_livedesk_blocks', 'livedeskid', $livedeskid, '', 'blockid,id')){
			return false;
		}
		
		foreach(array_keys($possible) as $blockid){
			$blockcontext = get_context_instance(CONTEXT_BLOCK, $blockid);
			if (has_capability('block/livedesk:runlivedesk', $blockcontext, $userid)){
				return $blockid;
			}
		}
		
		return false;
    }

	/**
	* unifies names given to Livedesk windows
	*
	*/    
    static function get_livedesk_window_name(&$livedesk){
    	global $SITE;
    	
    	if (is_object($livedesk)){    	
    		$livedeskid = $livedesk->id;
    	} else {
    		$livedeskid = $livedesk;
    	}
    	
    	return $SITE->shortname.'_Livedesk_'.$livedeskid;
    }
    
    /**
    * getting valid blocks attached to a livedesk instance
    *
    *
    */
    static function get_blocks_attached(&$livedesk){
		global $CFG;
		    	
    	$sql = "
    		SELECT
    			lb.*
    		FROM
    			{$CFG->prefix}block_livedesk_blocks lb,
    			{$CFG->prefix}block_instance b
    		WHERE
    			lb.blockid = b.id AND
    			lb.livedeskid = {$livedesk->id}
    	";

		$blocks = get_record_sql($sql);
		return $blocks;
    }
}

?>