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
 * @module     block_livedesk/notification
 * @package    blocks
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:false
// global dhtmlXCalendarObject
/* eslint-disable no-undef */
define(['jquery', 'core/config', 'core/str', 'core/log'], function ($, cfg, str, log) {

    var livedesk = {

        dhxWins: '',

        selectedRow: 0,

        menu: null,

        toolbar: null,

        mygrid: null,

        init: function(params) {

            var url;
            livedesk.dhxWins = new dhtmlXWindows();
            livedesk.selectedRow = null;

            livedesk.params = params;
            livedesk.strs = [];

            var stringdefs = [
                {key: 'reply', component:'block_livedesk'},  // 0
                {key: 'discard', component:'block_livedesk'}, // 1
                {key: 'discardbefore', component:'block_livedesk'}, // 2
                {key: 'emailuser', component:'block_livedesk'}, // 3
                {key: 'message', component:'block_livedesk'}, // 4
                {key: 'user', component:'block_livedesk'}, // 5
                {key: 'messagetime', component:'block_livedesk'}, // 6
                {key: 'origin', component:'block_livedesk'}, // 7
                {key: 'lockedby', component:'block_livedesk'}, // 8
                {key: 'livequeue', component:'block_livedesk'}, // 9
                {key: 'livedeskinfo', component:'block_livedesk'}, // 10
                {key: 'onlineusers', component:'block_livedesk'}, // 11
                {key: 'monitoredplugins', component:'block_livedesk'}, // 12
                {key: 'refreshposts', component:'block_livedesk'}, // 13
                {key: 'onlineuserscount', component:'block_livedesk'}, // 14
                {key: 'onlineattenderscount', component:'block_livedesk'}, // 15
                {key: 'showhideanswered', component:'block_livedesk'}, // 16
                {key: 'showhidediscarded', component:'block_livedesk'}, // 17
                {key: 'showhidelocked', component:'block_livedesk'}, // 18
                {key: 'configurations', component:'block_livedesk'}, // 19
                {key: 'statistics', component:'block_livedesk'}, // 20
                {key: 'managelivedesks', component:'block_livedesk'}, // 21
            ];

            str.get_strings(stringdefs).done(function(s) {
                livedesk.strs = s;

                log.debug(JSON.stringify(livedesk.strs));
                log.debug(JSON.stringify(params));

                log.debug('Start Livedesk construction');

                // Menu.
                livedesk.menu = new dhtmlXMenuObject();
                livedesk.menu.setIconsPath('pix/');
                livedesk.menu.setSkin('dhx_web');
                livedesk.menu.renderAsContextMenu();

                // initing;
                livedesk.menu.addNewChild(null, 1, "reply_post", livedesk.strs[0], false, 'reply_mail.png');
                livedesk.menu.addNewChild(null, 2, "discard_post", livedesk.strs[1], false, "discard_mail.png");
                livedesk.menu.addNewChild(null, 3, 'discard_posts_before', livedesk.strs[2], false, "discard_mail.png");
                livedesk.menu.addNewChild(null, 4, 'send_email', livedesk.strs[3], false, "mail.png");

                // Context menu event.
                livedesk.menu.attachEvent('onClick', livedesk.onContextMenuClick);

                log.debug('Start Grid construction');

                // Init Grid
                livedesk.mygrid = new dhtmlXGridObject('livedesk-postsgrid');
                livedesk.mygrid.setImagePath('js/dhtmlx/3.0/dhtmlxGrid/codebase/imgs/'); // ¨Path to images required by grid.
                var headers = "#,," + livedesk.strs[4] + "," + livedesk.strs[5];
                headers += "," + livedesk.strs[6] + "," + livedesk.strs[7] + "," + livedesk.strs[8];
                livedesk.mygrid.setHeader(headers); // Set column names.
                livedesk.mygrid.setInitWidths('30,40,320,100,150,200,*'); // Set column width in px.
                livedesk.mygrid.setColAlign('right,left,left,left,left,left,left'); // Set column values align.
                livedesk.mygrid.setColTypes('ro,ro,ro,ro,ro,ro,ro'); // Set column types.
                livedesk.mygrid.setColSorting('int,str,str,str,str,str,str'); // Set sorting.
                livedesk.mygrid.init(); // Initialize grid.
                livedesk.mygrid.setSkin('dhx_web'); // Set grid skin.
                livedesk.mygrid.enableContextMenu(livedesk.menu);

                log.debug('Loading posts');

                // load all posts.
                url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
                url += 'action=load_liveentries&';
                url += 'bid=' + livedesk.params['bid'];
                livedesk.mygrid.load(url, 'xml');
                livedesk.mygrid.attachEvent('onBeforeContextMenu', livedesk.onBeforeContextMenuHandler);
                livedesk.mygrid.attachEvent('onRowDblClicked', livedesk.onRowDblClicked);
                livedesk.mygrid.attachEvent('onBeforeContextMenu', livedesk.onBeforeContextMenu);

                log.debug('Creating layout');

                // Setup the layout
                var layout = new dhtmlXLayoutObject('livedesk-masterlayout', '4C');
                layout.setSkin('dhx_web');

                layout.cells("a").setWidth("800");
                layout.cells("a").setHeight(800);
                layout.cells("b").setWidth("250");
                layout.cells("c").setWidth("250");
                layout.cells("d").setWidth("250");
                layout.cells("b").setHeight("90");
                layout.cells("c").setHeight(130);
                layout.cells("a").attachObject("livedesk-postsgrid");
                layout.cells("c").attachObject("livedesk-onlineuserscount");
                layout.cells("b").attachObject("livedesk-info");
                layout.cells("d").attachObject("livedesk-plugins");
                layout.cells("a").setText(livedesk.strs[9]);
                layout.cells("b").setText(livedesk.strs[10]);
                layout.cells("c").setText(livedesk.strs[11]);
                layout.cells("d").setText(livedesk.strs[12]);

                log.debug('Creating toolbar');

                // Init Toolbar.
                var toolbar = layout.attachToolbar();
                toolbar.setIconsPath('pix/');
                toolbar.setSkin('dhx_web');
                toolbar.addButton('posts_refresh', 2, livedesk.strs[13], 'refresh.png');
                var bti = 3;
                if (livedesk.params['canviewsettings']) {
                    toolbar.addButton('settings', bti, livedesk.strs[19], 'settings.png', null);
                    bti++;
                }
                if (livedesk.params['canviewstats']) {
                    toolbar.addButton('view_statistics', bti, livedesk.strs[20], 'statistics.png', null);
                    bti++;
                }
                if (livedesk.params['canmanage']) {
                    toolbar.addButton('manage_livedesks', bti, livedesk.strs[21], 'manage.png', null);
                    bti++;
                }
                toolbar.addButtonTwoState('show_answered', bti, '', 'show_answered_on.png', 'show_answered_off.png');
                bti++;
                toolbar.setItemToolTip('show_answered', livedesk.strs[16]);

                if (livedesk.params['show_answered']) {
                    toolbar.setItemState('show_answered', true);
                    toolbar.setItemImage('show_answered', 'show_answered_on.png');
                } else {
                    toolbar.setItemState('show_answered', false);
                    toolbar.setItemImage('show_answered', 'show_answered_off.png');
                }

                toolbar.addButtonTwoState('show_discarded', bti, '', 'show_discarded_on.png', 'show_discarded_off.png');
                bti++;
                toolbar.setItemToolTip('show_discarded', livedesk.strs[17]);

                if (livedesk.params['show_discarded']) {
                    toolbar.setItemState('show_discarded', true);
                    toolbar.setItemImage('show_discarded', 'show_discarded_on.png');
                } else {
                    toolbar.setItemState('show_discarded', false);
                    toolbar.setItemImage('show_discarded', 'show_discarded_off.png');
                }

                toolbar.addButtonTwoState('show_locked', bti, '', 'show_locked_on.png', 'show_locked_off.png');
                bti++;
                toolbar.setItemToolTip('show_locked', livedesk.strs[18]);

                if (livedesk.params['show_locked']) {
                    toolbar.setItemState('show_locked', true);
                    toolbar.setItemImage('show_locked', 'show_locked_on.png');
                } else {
                    toolbar.setItemState('show_locked', false);
                    toolbar.setItemImage('show_locked', 'show_locked_off.png');
                }

                toolbar.attachEvent('onClick', livedesk.toolbarClickHandler);
                toolbar.attachEvent('onStateChange', livedesk.toolbarStateChange);
                livedesk.toolbar = toolbar;

                // Calendar on the discard messages before page.
                // var calendar = new dhtmlXCalendarObject('discard_date');
                livedesk.getMonitoredPlugins();
                livedesk.refreshOnlineUsersCount();

                // Periodic update.
                if (livedesk.params['refresh'] > 0) {
                    log.debug('Installing livedesk updater');
                    setInterval(function() {
                        livedesk.refreshGrid();
                        livedesk.refreshOnlineUsersCount();
                    }, livedesk.params['refresh']);
                }

                if (livedesk.params['keepalive'] > 0) {
                    log.debug('Installing livedesk keepalive signal');
                    setInterval(function() {
                        livedesk.keepMeAlive();
                    }, livedesk.params['keepalive']);
                }

                $('.noty_message').bind('click', function() {
                    var related_message_id =  $(this).find('.noty_message_num').val();
                    if (related_message_id == '') {
                        return;
                    }
                });

                log.debug('Block Livedesk AMD initialized');

            });
        },

        onBeforeContextMenu: function(id) {
            livedesk.selectedRow = id;
            return true;
        },

        /**
         * Grid context menu event handles.
         */
        onContextMenuClick: function(id) {

            var selId;

            switch (id) {
                case 'reply_post': {
                    selId = livedesk.selectedRow;
                    livedesk.reply_post(selId);
                    return true;
                    break;
                }

                case 'discard_post': {
                    livedesk.discard_post(livedesk.selectedRow);
                    break;
                }

                case 'discard_posts_before': {
                    var timecreated = livedesk.mygrid.getUserData(livedesk.selectedRow, 'timecreated');
                    livedesk.dhxWins.createWindow('discard_messages', 400, 100, 400, 130);
                    livedesk.dhxWins.window('discard_messages').setModal(true);
                    var discardiconurl = cfg.wwwroot + '/blocks/livedesk/pix/discard_mail.png';
                    livedesk.dhxWins.window('discard_messages').setIcon(discardiconurl);
                    livedesk.dhxWins.window('discard_messages').setText(livedesk.strs[2]);
                    var url = cfg.wwwroot + '/blocks/livedesk/discard_messages_before.php?';
                    url += 'bid=' + livedesk.params['bid'] + '&';
                    url += 'date=' + timecreated;
                    livedesk.dhxWins.window('discard_messages').attachURL(url, true);
                    return true;
                    break;
                }
            }
        },

        /**
        * Event Handlers
        */
        onBeforeContextMenuHandler: function (id) {
            var statusid =  livedesk.mygrid.getUserData(id, 'status_id');
            if (statusid == 0) {
                 livedesk.menu.hideItem('disable_post');
                 livedesk.menu.showItem('enable_post');
            } else {
                 livedesk.menu.hideItem('enable_post');
                 livedesk.menu.showItem('disable_post');
            }
            return true;
        },

        /**
        * This function handles the toolbar buttons click events.
        */
        toolbarClickHandler: function (item) {
            var url;

            switch(item) {
                case 'posts_refresh': {
                    livedesk.refreshGrid();
                    break;
                }

                case 'view_statistics': {
                    var win = livedesk.dhxWins.createWindow('view_statistics', 400, 100, 850, 400);
                    win.setModal(true);
                    // dhxWins.window('view_statistics').setModal(true);
                    var statsiconurl = cfg.wwwroot + '/blocks/livedesk/pix/statistics.png';
                    win.setIcon(statsiconurl);
                    // dhxWins.window('view_statistics').setIcon(statsiconurl);
                    win.setText('Statistics');
                    // dhxWins.window('view_statistics').setText('Statistics');
                    var statsurl = cfg.wwwroot + '/blocks/livedesk/statistics.php?';
                    statsurl += 'bid=' + livedesk.params['bid'];
                    win.attachURL(statsurl, true);
                    // dhxWins.window('view_statistics').attachURL(statsurl, true);
                    return true;
                }

                case 'settings': {
                    url = cfg.wwwroot + '/course/view.php?';
                    url += 'id=' + livedesk.params['courseid'];
                    url += '&bui_editid=' + livedesk.params['bid'];
                    url += '&sesskey=' + cfg.sesskey;
                    window.location = url;
                    return true;
                }

                case "manage_livedesks": {
                    url = cfg.wwwroot + '/blocks/livedesk/manage.php';
                    window.location = url;
                    return true;
                }
            }
        },

        /*
        * Sending state to server for SESSION storing and
        * use in next refreshs.
        */
        toolbarStateChange: function (item, state) {
            if (state) {
                livedesk.toolbar.setItemImage(item, item + '_on.png');
            } else {
                livedesk.toolbar.setItemImage(item, item + '_off.png');
            }
            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=change_state&';
            url += 'item=' + item + '&';
            url += 'state=' + state + '&';
            url += 'bid=' + livedesk.params['bid'];
            /* url = 'ajax/service.php?action=change_state&item=' + item + '&state=' + state + '&bid=' + bid; */
            $.post(url, function() {});
            livedesk.refreshGrid();
            return true;
        },

        /**
        * Refresh the grid.
        */
        refreshGrid: function () {

            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=load_liveentries&';
            url += 'bid=' + livedesk.params['bid'];

            if (_isIE) { //IE)
                // var current_rows_count = livedesk.mygrid.getRowsNum();
                // var fetched_rows_count = 0; //data[rows].length;
                livedesk.mygrid.clearAndLoad(url, "xml");
            } else { //Other browsers
                    $.post(url, function(data) {
                    // var current_rows_count = livedesk.mygrid.getRowsNum();
                    // var fetched_rows_count = 0; //data[rows].length;
                    livedesk.mygrid.clearAll();
                    livedesk.mygrid.parse(data);
                });
            }

            return true;
        },

        /**
        * Double click event handler
        */
        onRowDblClicked: function (rId) {
            livedesk.reply_post(rId);
        },

        /**
         * Initiate the window for replying to new message
         */
        reply_post: function (rowId) {
            var itemid = livedesk.mygrid.getUserData(rowId, 'itemid');
            var messageid = livedesk.mygrid.getUserData(rowId, 'messageid');
            var cmid = livedesk.mygrid.getUserData(rowId, 'cmid');
            var randX = Math.floor(Math.random() * (450 - 350 + 1)) + 350;
            var randY = Math.floor(Math.random() * (150 - 50 + 1)) + 50;
            var win = livedesk.dhxWins.createWindow('reply_post' + rowId, randX, randY, 800, 450);
            win.messageid = messageid;
            win.closing = false;
            win.setModal(false);
            win.setText('Reply');
            win.attachURL('reply.php?reply=' + itemid + '&cmid=' + cmid, false);
            livedesk.dhxWins.attachEvent('onClose', livedesk.unlockItem);
        },

        /**
         * Generates popup for new incoming messages.
         */
        generateNoty: function (text, type) {
            var n = noty({
                text: text,
                timeout: 5000,
                closeWith: ['button'],
                type: type,
                dismissQueue: true,
                layout: 'bottomRight',
                theme: 'defaultTheme'
            });
            return n;
        },

        refreshOnlineUsersCount: function () {
            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=get_online_users_count&';
            url += 'bid=' + livedesk.params['bid'];

            $.post(url, function(data) {
                data = JSON.parse(data);

                var htmlbuffer = '<div class="online_users">';
                htmlbuffer += '<img src="pix/user.png" /> ';
                htmlbuffer += livedesk.strs[14] + ' : ' + data.users_count;
                htmlbuffer += '</div>';
                $('#livedesk-onlineuserscont').html(htmlbuffer);

                htmlbuffer = '<div class="online_users">';
                htmlbuffer += '<img src="pix/user-black.png" /> ';
                htmlbuffer += livedesk.strs[15] + " : " + data.attenders_count;
                htmlbuffer += '</div>';
                $('#livedesk-onlineuserscount').append(htmlbuffer);

                if (data.attenders_count > 0) {
                    var userlist = '<ul class="livedesk-userlist">';
                    for (var i = 0 ; i < data.attenders.length ; i++) {
                        userlist += '<li class="online_users ' + data.attenders[i].className + '">';
                        userlist += data.attenders[i].name;
                        userlist += '</li>';
                    }
                    $('#livedesk-onlineuserscount').append(userlist + '</ul>');
                }
            });
        },

        keepMeAlive: function () {
            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=keep_me_live&';
            url += 'bid=' + livedesk.params['bid'] + '&';
            url += 'livedeskid=' + livedesk.params['livedeskid'] + '&';
            url += 'courseid=' + livedesk.params['courseid'];
            $.post(url, function(data) {
                  $('#livedesk-onlineuserscount').html(livedesk.strs[11] + data);
            });
        },

        getMonitoredPlugins: function () {

            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=get_monitored_plugins&';
            url += 'livedeskid=' + livedesk.params['livedeskid'] + '&';
            url += 'bid=' + livedesk.params['bid'];
            $.post(url, function(data) {

                data = JSON.parse(data);

                $('#livedesk-plugins').html('');
                var blockpixurl = cfg.wwwroot + '/blocks/livedesk/pix/block.png';
                var htmlbuffer;

                for (var key in data) {
                    htmlbuffer = '<div class="online_users">';
                    htmlbuffer += '<img src="' + blockpixurl + '" />';
                    var linkurl = cfg.wwwroot + "/mod/forum/view.php?id=" + data[key].cmid;
                    htmlbuffer += "<a target=\"_blank\" href=\"" + linkurl + "\" > ";
                    htmlbuffer += data[key].name;
                    htmlbuffer += '</a>';
                    htmlbuffer += '</div>';
                    $('#livedesk-plugins').append(htmlbuffer);
                }
            });
        },

        unlockItem: function(win) {

            if (!win.closing) {
                win.closing = true;
                var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
                url += 'action=unlock_item&';
                url += 'bid=' + livedesk.params['bid'] + '&';
                url += 'livedeskid=' + livedesk.params['livedeskid'] + '&';
                url += 'courseid=' + livedesk.params['courseid'] + '&';
                url += 'messageid=' + win.messageid;
                $.post(url, function() {
                    livedesk.refreshGrid();
                });
            }
            return true;
        },

        discard_post: function() {
            var postid = livedesk.mygrid.getUserData(livedesk.selectedRow, 'messageid');
            var url = cfg.wwwroot + '/blocks/livedesk/ajax/service.php?';
            url += 'action=discard_post&';
            url += 'bid=' + livedesk.params['bid'] + '&';
            url += 'livedeskid=' + livedesk.params['livedeskid'] + '&';
            url += 'courseid=' + livedesk.params['courseid'] + '&';
            url += 'messageid=' + postid;
            $.post(url, function() {
                livedesk.refreshGrid();
            });
        }
    };

    return livedesk;

});