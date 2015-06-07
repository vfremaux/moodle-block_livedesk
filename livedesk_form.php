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

if (!defined('MOODLE_INTERNAL')) die ('You cannot use this script this way');

require_once ($CFG->dirroot.'/lib/formslib.php');
require_once ($CFG->dirroot.'/blocks/livedesk/classes/livedesk.class.php');

class livedesk_form extends moodleform {

    function definition() {
        global $CFG;

        $mform = & $this->_form;

        $mform->addElement('text', 'name', get_string('livedeskname', 'block_livedesk'));
        $mform->setType('name', PARAM_CLEANHTML);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('editor', 'description', get_string('livedeskdescription', 'block_livedesk'));

        $mform->addElement('text', 'resolvereleasedelay', get_string('resolvereleasedelay', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('resolvereleasedelay', @$CFG->block_livedesk_resolving_post_release);
        $mform->setType('resolvereleasedelay', PARAM_INT);

        $mform->addElement('text', 'attenderreleasetime', get_string('attenderreleasetime', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('attenderreleasetime', @$CFG->block_livedesk_attender_release_time);
        $mform->setType('attenderreleasetime', PARAM_INT);

        $mform->addElement('text', 'stackovertime', get_string('stackovertime', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('stackovertime', @$CFG->block_livedesk_stack_over_time);
        $mform->setType('stackovertime', PARAM_INT);

        $mform->addElement('text', 'maxstacksize', get_string('maxstacksize', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('maxstacksize', @$CFG->block_livedesk_max_stack_size);
        $mform->setType('maxstacksize', PARAM_INT);

        $mform->addElement('text', 'keepalivedelay', get_string('keepalivedelay', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('keepalivedelay', @$CFG->block_livedesk_keepalive_delay);
        $mform->setType('keepalivedelay', PARAM_INT);

        $mform->addElement('text', 'refresh', get_string('refresh', 'block_livedesk'), array('size' => 5, 'maxlength' => 5));
        $mform->setDefault('refresh', @$CFG->block_livedesk_refresh);
        $mform->setType('refresh', PARAM_INT);

        $hours = array();
        for ($i = 0; $i < 24 ; $i++) {
            $hours[$i] = $i;
        }

        $minrange = array();
        for ($i = 0; $i < 60 ; $i = $i + 5) {
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
        $mform->addElement('hidden', 'course');
        $mform->addElement('hidden', 'bid');

        $mform->addElement('checkbox', 'sendnotification', get_string('sendnotification', 'block_livedesk'), '', 0);

        $mform->addElement('text', 'notificationtitle', get_string('notificationtitle', 'block_livedesk'), array('size' => 80));
        $mform->setType('notificationtitle', PARAM_TEXT);

        $mform->addElement('textarea', 'notificationmail', get_string('notificationmail', 'block_livedesk'), array('cols' => 80, 'rows' => 10));
        $mform->setType('notificationmail', PARAM_CLEANHTML);

        $this->add_action_buttons();
    }

    function set_data($data) {
        if (!isset($data->servicestarttime)) {
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

    function validation($data, $files) {
        $errors = array();

        if (empty($data['name'])) {
            $errors['name'] = get_string('errornoname', 'block_livedesk');
        }

        return $errors;
    }

    // TODO : revert to moodle forms create checkbox element in groups with <br/> insertions
    function get_monitoredplugins_list(){  
        global $USER, $CFG, $OUTPUT, $DB;

        $table = "" ;
        $table .= '<table width="100%">';
        $table .= '<tr>';
        $table .= '<td>';
        $table .= $OUTPUT->heading(get_string('monitorableplugins', 'block_livedesk'), 3, 'main');
        $table .= '<div id="pluginlist-content" style="">';        

        //load block instance connected plugins .
        $livedeskid = required_param('livedeskid', PARAM_INT);
        $selected_plugins_arr = array();
        if ($livedeskid != 0) {
            $monitoredplugins = $DB->get_records('block_livedesk_modules', array('livedeskid' => $livedeskid));
            if ($monitoredplugins) {
                foreach($monitoredplugins as $plugin) {
                    $selected_plugins_arr[] = $plugin->cmid;
                }
            }
        }

        // Load courses user has access to.
        $courses = $DB->get_records('course', array('visible' => '1'), 'fullname');

        foreach ($courses as $id => $course) {
            $context = context_course::instance($id);
            if (!has_capability('moodle/course:view', $context, $USER->id)) {
                unset($courses[$id]);
            } else {
                // load the plugins
                $plugins = livedesk::get_monitorable_plugins();
                $modulenames_cs = implode("','", $plugins);
                $sql = "
                    SELECT 
                        cm.id,
                        cm.instance,
                        m.name as modulename
                    FROM 
                        {course_modules} cm,
                        {modules} m 
                    WHERE 
                         cm.course = {$course->id} AND
                         cm.module = m.id AND
                         m.name IN ('$modulenames_cs')
                     ORDER BY
                         m.name
                " ;
                if (!$plugins = $DB->get_records_sql($sql)) {
                    // Silently discard non significant enties
                    // $table .= get_string('nomonitorableplugins', 'block_livedesk');
                    continue;
                }
                $table .= '<div class="course-div">';
                $table .= $OUTPUT->heading($course->fullname, 3);

                // Add name of the activity for all matched plugins.
                foreach($plugins as $pid => $p) {
                    $plugins[$pid]->name = get_string('modulename', $p->modulename). ' : '.$DB->get_field($p->modulename, 'name', array('id' => $p->instance));
                }
                foreach ($plugins as $plugin) {
                    if (in_array($plugin->id, $selected_plugins_arr)) {
                        $checked = " checked='checked' ";
                    } else {
                        $checked = "";
                    }
                    $table .= "<div class=\"livedesk-pluginline\">
                    <input type=\"checkbox\" name=\"pluginids[]\" $checked value=\"$plugin->id\" />
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
