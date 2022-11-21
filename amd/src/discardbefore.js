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
 * Javascript controller for controlling the sections.
 *
 * @module     block_livedesk/livedeskdiscard
 * @package    blocks
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:false
// global dhtmlXCalendarObject
/* eslint-disable no-undef */
define(['jquery', 'core/config', 'core/log'], function ($, cfg, log) {

    var livedeskdiscard = {

        params: null,

        init: function(params) {

            livedeskdiscard.params = params;

            var calendar = new dhtmlXCalendarObject("discard_date");
            calendar.setDate(livedeskdiscard.params['creationdate']);
            calendar.setDateFormat("%d.%m.%Y %h:%i");

            $('#discard_btn').bind('click', this.discard_post_before_date);

            log.debug('AMD livedesk discard initialized');
        },

        discard_post_before_update: function() {
            var ddate = $('#discard_date').val();

            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php';
            url += '?action=discard_post';
            url += '&bid=' + livedeskdiscard.params['bid'];
            url += '&livedeskid=' + livedeskdiscard.params['livedeskid'];
            url += '&courseid=' + livedeskdiscard.params['courseid'];
            url += '&date=' + ddate;

            $.post(url, function() {
                window.discard_messages_window.close();
            });
        }
    };

    return livedeskdiscard;
});