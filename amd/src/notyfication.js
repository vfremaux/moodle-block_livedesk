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
 * @module     block_livedesk/notyfication
 * @package    blocks
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:false
// global dhtmlXCalendarObject
/* eslint-disable no-undef */
define(['jquery', 'core/config', 'core/str', 'core/log'], function ($, cfg, str, log) {

    var ldskn;

    ldskn = {

        params: null,

        init: function(params) {

            ldskn.params = params;

            var stringdefs = [
                {key:'newmessages', component:'block_livedesk'}
            ];

            str.get_strings(stringdefs).done(function(s) {
                ldskn.strs = s;
            });

            log.debug('AMD Block Livedesk Notyfication initialized');

        },

        start: function() {
            setInterval(ldskn.get_unnotified_messages, ldskn.params['notification_refresh_time']);
            log.debug('AMD Block Livedesk Notifycation started');
        },

        get_unnotified_messages: function() {
            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=get_unnotified_messages&';
            url += 'course=' + ldskn.params['courseid'];

            $.post(url, function(data) {

                if (data == '') {
                    return;
                }

                if (Noty !== undefined) {
                    Noty.closeAll();
                }
                var dataarr = JSON.parse(data);

                for (var key = 0 ; key < dataarr.length ; key++) {
                       var id = dataarr[key].id;
                       var message = dataarr[key].message;
                       var notification = '<input name="noty_id' + id;
                       notification += '" type="hidden" value=' + id + ' class="noty_message_num" />';
                       notification += '<b>' + ldskn.strs[0] + '</b>';
                       notification += '<div>' + message + '</div>';
                       ldskn.generateNoty(notification, 'information', id);
                }
            });
        },

        generateNoty: function (text, type) {
            var n = new Noty({
                text: text,
                timeout: ldskn.params['notification_onscreen_time'],
                closeWith: ['button'],
                type: type,
                dismissQueue: true,
                layout: ldskn.params['layout'],
                theme: ldskn.params['theme']
            }).show();
            return n;
        },
    };

    return ldskn;
});
