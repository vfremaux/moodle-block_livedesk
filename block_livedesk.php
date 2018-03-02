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

defined('MOODLE_INTERNAL') || die();

/**
 * @package    block_livedesk
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @author     Wafa Adhams (admin@adham.ps)
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once $CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php';

// Pull out the noty libraries for all pages.

require_once $CFG->dirroot.'/blocks/moodleblock.class.php';
require_once $CFG->dirroot.'/blocks/livedesk/lib.php';

/**
 * Form for editing HTML block instances.
 *
 * @package   block_livedesk
 * @author 2012 Wafa adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_livedesk extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_livedesk');
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function has_config() {
        return true;
    }

    public function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('livedesk', 'block_livedesk'));
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function get_content() {
        global $CFG, $COURSE, $SITE, $OUTPUT;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $system_context = context_system::instance();
        $context = context_block::instance($this->instance->id);
        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        $filteropt->noclean = true;

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';
        if (has_capability('block/livedesk:runlivedesk', $context)) {
            if (!empty($this->config->livedeskid)) {
                $livedeskwindow = livedesk::get_livedesk_window_name($this->config->livedeskid);
                $this->content->text = '<p class="livedesk-text">';
                $params = array('course' => $COURSE->id, 'bid' => $this->instance->id);
                $linkurl = new moodle_url('/blocks/livedesk/run.php', $params);
                $this->content->text .= '<a target="'.$livedeskwindow.'" href="'.$linkurl.'">';
                $this->content->text .= $OUTPUT->pix_icon('logo', 'block_livedesk');
                $this->content->text .= '</a></p>';
            } else {
                $this->content->text = $OUTPUT->notification(get_string('instance_notbounded_to_livedesk', 'block_livedesk'), 'notifyproblem');
            }
        }
        if (has_capability('block/livedesk:managelivedesks', $system_context)) {
            $manageinstancesstr = get_string('manageinstances', 'block_livedesk');
            $this->content->text .= '<div>';
            $this->content->text .= "<ul class='livedeskmenu'>";
            $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/manage.php?course={$COURSE->id}\" ><b>{$manageinstancesstr}</b></a></li>";
        }
        if (has_capability('block/livedesk:createlivedesk', $system_context)) {
            $addlivedeskstr = get_string('adddeskinstances', 'block_livedesk');
            $this->content->text .= "<li><a href=\"{$CFG->wwwroot}/blocks/livedesk/edit_instance.php?livedeskid=0&course={$COURSE->id}\" >$addlivedeskstr</a></li>";
            $this->content->text .= '</ul>';
            $this->content->text .= '</div>';
        }

        unset($filteropt); // memory footprint

        return $this->content;
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
    public function instance_config_save($data, $nolongerused = false) {
        global $DB;

        $blockreference =  $DB->get_record('block_livedesk_blocks', array('blockid' => $this->instance->id));
        if(empty($blockreference)) {
            $blockreference = new stdClass();
            $blockreference->blockid = $this->instance->id;
            $blockreference->livedeskid = @$data->livedeskid;
            $DB->insert_record('block_livedesk_blocks', $blockreference);
        } else {
            // Just update
            $blockreference->livedeskid = @$data->livedeskid;
            $DB->update_record('block_livedesk_blocks', $blockreference);
        }
        parent::instance_config_save($data);
    }

    /**
    * processes some cron cleanup and sends unsent service notifications
    * locked messages for too long must be released
    * too old messages (stack over time) must be discarded
    * do a per livedsk processing with licedesk instance characteristics
    */
    public static function crontask() {
        global $CFG, $DB, $SITE;

        mtrace( "\n".'Starting Livedesk Block cron' );
        $livedesks = $DB->get_records('block_livedesk_instance');

        if (empty($livedesks)) {
            return;
        }
        
        $adminuser = get_admin();

        foreach ($livedesks as $livedesk) {

        // Check too old messages to discard them.

            mtrace( 'Removing messages older than '.$livedesk->stackovertime.' mins');
            // Get liveinstance monitored plugins.

            $monitored_plugins_cs = '';
            if ($monitored_plugins = $DB->get_records('block_livedesk_modules', array('livedeskid' => $livedesk->id))) {
                $monitored_plugins_arr = array();
                foreach ($monitored_plugins as $p) {
                    $monitored_plugins_arr[] = $p->cmid;
                }
                $monitored_plugins_cs = implode("','", $monitored_plugins_arr);
            } else {
                continue;
            }
            $stacktimeover = time() - $livedesk->stackovertime * 60; // Stack over time in minutes.

            $discard_count = 0;
            $select = "
                cmid IN ('$monitored_plugins_cs') AND
                timecreated < ? AND
                mstatus = 'new'
            ";
            if ($old_messages = $DB->get_records_select('block_livedesk_queue', $select, array($stacktimeover), 'id, message, mstatus')){
                foreach ($old_messages as $m) {
                    $m->mstatus = 'discarded';
                    $DB->update_record('block_livedesk_queue', $m);
                    $discard_count++;
                }
            }

            mtrace('... '.$discard_count." messages were discarded.");

            // Release locked messages from too long time.

            mtrace( 'Unlocking livedesk locked messages');
            // Load all locked queue records.
            $timelockover = time() - ($livedesk->resolvereleasedelay * 60);
            $oldlockedrecords = $DB->get_records_select('block_livedesk_queue', " cmid IN ('$monitored_plugins_cs') AND locked = 1 AND locktime < ? ", array($timelockover));
            $unlock_count = 0 ;

            if ($oldlockedrecords) {
                foreach ($oldlockedrecords as $m) {
                    // Unlock.
                    $rec = new StdClass;
                    $rec->locked = 0;
                    $rec->lockedby = null;
                    $rec->timelock = null;
                    $unlock_count++;
                    $DB->update_record('block_livedesk_queue', $m);
                }
            }

            mtrace('... '.$unlock_count." messages were unlocked.");

            if ($livedesk->maxstacksize) {
                mtrace("truncating queue to $livedesk->maxstacksize entries.");
                $sql = "
                    SELECT 
                        id,
                        timecreated,
                        timeanswered
                    FROM
                        {block_livedesk_queue}
                    WHERE
                        cmid IN ('$monitored_plugins_cs')
                    ORDER BY
                        timecreated DESC, 
                        timeanswered DESC,
                        id DESC
                ";
                if ($endstackentry = $DB->get_records_sql($sql, array(), $livedesk->maxstacksize, 1)) {
                    $entry = array_pop($endstackentry);
                    // Do never delete answered messages.
                    $select = " cmid IN ('$monitored_plugins_cs') AND id <= ? AND timeanswered = 0";
                    if (!empty($CFG->livedesk_delete_oversize)) {
                        $ret = $DB->delete_records_select('block_livedesk_queue', $select, array($entry->id));
                        mtrace("... full truncated $ret->_numOfRows entries.");
                    } else {
                        $sql = "
                            UPDATE
                               {block_livedesk_queue}
                            SET
                                mstatus = 'deleted'
                            WHERE
                                $select
                        ";
                        $DB->execute($sql, array($entry->id));
                        mtrace("... truncated entries.");
                    }
                }
            }

            if ($livedesk->sendnotification && !empty($livedesk->notificationmail)) {
                mtrace('Sending acknowledge notifications');

                $select = "
                    cmid IN ('$monitored_plugins_cs') AND
                    mailsent = 0
                ";

                if ($to_send_messages = $DB->get_records_select('block_livedesk_queue', $select, array($stacktimeover), 'id, callerid')) {
                    foreach ($to_send_messages as $mailsend) {
                        $caller = $DB->get_record('user', array('id' => $mailsend->callerid));
                        if ($CFG->debugsmtp) {
                            mtrace("... sending Livedesk Entry Notification to " . fullname($caller));
                        } else {
                            $notification = strip_tags($livedesk->notificationmail);
                            $notification_html = $livedesk->notificationmail;
                            mtrace("... sending Livedesk Entry Notification to " . fullname($caller));

                            $messagetitle = get_string('received', 'block_livedesk', $SITE->shortname.' : '.format_string($livedesk->name));
                            if (!empty($livedesk->notificationtitle)) {
                                $messagetitle = $livedesk->notificationtitle;
                            }

                            if (email_to_user($caller, $SITE->shortname, $messagetitle, $notification, $notification_html)) {
                                $DB->set_field('block_livedesk_queue', 'mailsent', 1, array('id' => $mailsend->id));
                            }
                        }
                    }
                }
            }
        }

        // Handle queue prioritization
        /*
        mtrace('Fixing queue priorities.');
        $all_records = $DB->get_records('block_livedesk_queue', null);
        foreach ($all_records as $rec){
            $post_period_min = (($current_time - $rec->timecreated) / 60);
               //new messages that have not been answered for long time should be pushed back in the stack.  
               if($rec->mstatus == 'new' && $post_period_min >= $CFG->block_livedesk_resolving_post_release){
                   $rec->priority = 5; //lower the priority
                   $DB->update_record('block_livedesk_queue', addslashes_object($rec));
               }
        }
        */
        mtrace( 'Finished Livedesk Block cron.' );
        return true;
    }

    /**
     *
     */
    static public function call_plugins_function($fname, $args = null, $plugin = null) {
        global $CFG;

        $otherplugins = livedesk::get_monitorable_plugins();
        if (!is_null($plugin)) {
            if (is_file($CFG->dirroot.'/mod/'.$plugin.'/livedesklib.php')) {
                include_once($CFG->dirroot.'/mod/'.$op.'/livedesklib.php');
                $plugindeletefunc = $plugin.'_'.$fname;
                return $plugindeletefunc($args);
            }
        }

        foreach ($otherplugins as $op) {
            if ($op == 'forum') {
                continue;
            }
            if (is_file($CFG->dirroot.'/mod/'.$op.'/livedesklib.php')) {
                include_once($CFG->dirroot.'/mod/'.$op.'/livedesklib.php');
                $plugindeletefunc = $op.'_'.$fname;
                $plugindeletefunc($args);
            }
        }
    }

    static public function check_jquery() {
        global $PAGE, $OUTPUT, $CFG, $JQUERYVERSION;

        if ($CFG->version >= 2013051400) {
            return; // Moodle 2.5 natively loads JQuery
        }

        $current = '1.8.2';

        if (empty($JQUERYVERSION)) {
            $JQUERYVERSION = '1.8.2';
            $PAGE->requires->js('/blocks/livedesk/js/jquery-'.$current.'.min.js', true);
        } else {
            if ($JQUERYVERSION < $current) {
                debugging('the previously loaded version of jquery is lower than required. This may cause issues to livedesk. Programmers might consider upgrading JQuery version in the component that preloads JQuery library.', DEBUG_DEVELOPER, array('notrace'));
            }
        }
    }
}

block_livedesk_setup_theme_requires();
