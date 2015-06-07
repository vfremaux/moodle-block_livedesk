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
 * @copyright 2012 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
if (!defined('MOODLE_INTERNAL')) die ('You cannot use this script this way');

class block_livedesk_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $DB, $CFG;

        // Fields for editing HTML block title and contents.

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_livedesk'));
        $mform->setType('config_title', PARAM_MULTILANG);
    
        $livedeskinstancesoptions = $DB->get_records_menu('block_livedesk_instance', array(), '', 'id,name');
        $mform->addElement('select', 'config_livedeskid', get_string('livedeskref', 'block_livedesk'), $livedeskinstancesoptions);

        // Print create instance side
        $location = $CFG->wwwroot."/blocks/livedesk/edit_instance.php?livedeskid=0";

        $system_context = context_system::instance();
        if (has_capability('block/livedesk:createlivedesk', $system_context)) {
            $html = '<input type="button" name="create_new_instance" value="'.get_string('createnewinstance', 'block_livedesk').'" onClick="window.location=\''.$location.'\'"  />';
        }

        $mform->addELement('html', $html);
    }

    function set_data($defaults, &$files = null) {

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }

        parent::set_data($defaults);
        if (isset($title)) {
            // Reset the preserved title
            $this->block->config->title = $title;
        }
    }
}
