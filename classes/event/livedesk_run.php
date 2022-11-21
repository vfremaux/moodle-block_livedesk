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
 * This file contains an event for when a pdcertificate document is issued.
 *
 * @package    block_livedesk
 * @copyright  2016 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_livedesk\event;

use \context_system;

defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a livedesk board is open.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      @type int instanceid as livedesk id.
 * }
 *
 * @package    block_livedesk
 * @since      Moodle 3.5
 * @copyright  2015 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class livedesk_run extends \core\event\base {

    public static function create_from_livedesk($livedesk) {
        $data = array(
            'contextid' => context_system::instance()->id,
            'objectid' => $livedesk->id,
        );
        /** @var deliverable_created $event */
        $event = self::create($data);
        return $event;
    }

    protected function get_legacy_logdata() {
        $livedeskid = $this->other['objectid'];
        return array(SITEID, 'livedesk', 'run', '/blocks/livedesk/run.php?id='.$livedeskid, $this->other['username']);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventlivedeskrun', 'block_livedesk');
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'block_livedesk_instance';
    }
}

