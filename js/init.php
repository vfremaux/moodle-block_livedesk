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

require('../../../config.php')  ;
header("Content-type: text/javascript; charset=utf-8");  ;

$id = required_param('id', PARAM_INT); // courseid
// $courseid = optional_param('course', 1, PARAM_INT);
if (!$course = $DB->get_record('course', array('id' => "$id"))){
    print_error('coursemisconf');
}

/// Security

require_login($course);
$systemcontext = context_system::instance();
$coursecontext = context_course::instance($id);

/// Other params

$keepalive = required_param('keepalive', PARAM_INT);
$keepalive = $keepalive * 1000; // Javascript uses milliseconds
$refresh = required_param('refresh', PARAM_INT);
$refresh = $refresh * 1000; // Javascript uses milliseconds

$livedeskid = optional_param('ldid', null, PARAM_INT);
$bid = optional_param('bid', null, PARAM_INT);

$i = 3;
$viewsettingsbutton = '';
if (has_capability('moodle/course:manageactivities', $coursecontext)){
    $viewsettingsbutton = 'toolbar.addButton(\'settings\','.$i.', \''.get_string('configurations', 'block_livedesk').'\', \'settings.png\', null); ';
    $i++;
}

$viewstatisticsbutton = '';
if (has_any_capability(array('block/livedesk:viewuserstatistics', 
                             'block/livedesk:viewinstancestatistics', 
                             'block/livedesk:viewlivedeskstatistics'), $systemcontext)){
    $viewstatisticsbutton = 'toolbar.addButton(\'view_statistics\','.$i.', \''.get_string('statistics', 'block_livedesk').'\', \'statistics.png\', null); ';
    $i++;
}

$viewmanagementbutton = '';
if (has_capability('block/livedesk:managelivedesks', $systemcontext)){
    $viewmanagementbutton = 'toolbar.addButton(\'manage_livedesks\','.$i.', \''.get_string('manage_livedesks', 'block_livedesk').'\', \'manage.png\', null); ';
    $i++;
}

$showhideansweredtooltip = get_string('showhideanswered', 'block_livedesk');
$showhidediscardedtooltip = get_string('showhidediscarded', 'block_livedesk');
$showhidelockedtooltip = get_string('showhidelocked', 'block_livedesk');

if (!isset($SESSION->livedesk)) $SESSION->livedesk = new StdClass;
if(!isset($SESSION->livedesk->show_answered)) $SESSION->livedesk->show_answered = true;
if(!isset($SESSION->livedesk->show_discarded)) $SESSION->livedesk->show_discarded = true;
if(!isset($SESSION->livedesk->show_locked)) $SESSION->livedesk->show_locked = true;

if($SESSION->livedesk->show_answered){
    $setinitialstateanswered = ' toolbar.setItemState(\'show_answered\', true); ';
    $setinitialstateanswered .= ' toolbar.setItemImage(\'show_answered\', \'show_answered_on.png\'); ';
} else {
    $setinitialstateanswered = ' toolbar.setItemState(\'show_answered\', false); ';
    $setinitialstateanswered .= ' toolbar.setItemImage(\'show_answered\', \'show_answered_off.png\'); ';
}

if($SESSION->livedesk->show_discarded){
    $setinitialstatediscarded = ' toolbar.setItemState(\'show_discarded\', true); ';
    $setinitialstatediscarded .= ' toolbar.setItemImage(\'show_discarded\', \'show_discarded_on.png\'); ';
} else {
    $setinitialstatediscarded = ' toolbar.setItemState(\'show_discarded\', false); ';
    $setinitialstatediscarded .= ' toolbar.setItemImage(\'show_discarded\', \'show_discarded_off.png\'); ';
}

if($SESSION->livedesk->show_locked){
    $setinitialstatelocked = ' toolbar.setItemState(\'show_locked\', true); ';
    $setinitialstatelocked .= ' toolbar.setItemImage(\'show_locked\', \'show_locked_on.png\'); ';
} else {
    $setinitialstatelocked = ' toolbar.setItemState(\'show_locked\', false); ';
    $setinitialstatelocked .= ' toolbar.setItemImage(\'show_locked\', \'show_locked_off.png\'); ';
}

print('
var discard_messages_window;
$(document).ready(function(){

    var dhxWins = new dhtmlXWindows();   
    var selectedRow = null;

    //menu
    var menu = new dhtmlXMenuObject();
    menu.setIconsPath("pix/");
    menu.setSkin("dhx_web");
    menu.renderAsContextMenu();

    // initing;
    menu.addNewChild(null, 1, "reply_post","'.get_string('reply', 'block_livedesk').'", false,\'reply_mail.png\');
    menu.addNewChild(null, 2, "discard_post","'.get_string('discard', 'block_livedesk').'", false,"discard_mail.png");
    menu.addNewChild(null, 3, "discard_posts_before","'.get_string('discard_before', 'block_livedesk').'", false,"discard_mail.png");
    menu.addNewChild(null, 4, "send_email","'.get_string('email_user', 'block_livedesk').'", false,"mail.png");

    //context menu event
    menu.attachEvent("onClick",onContextMenuClick); 

    //** init Grid
    mygrid = new dhtmlXGridObject(\'livedesk-postsgrid\');
    mygrid.setImagePath("js/dhtmlx/3.0/dhtmlxGrid/codebase/imgs/");//path to images required by grid
    mygrid.setHeader("#,,'.get_string('message', 'block_livedesk').','.get_string('user', 'block_livedesk').','.get_string('message_time', 'block_livedesk').','.get_string('origin', 'block_livedesk').','.get_string('lockedby', 'block_livedesk').'");//set column names
    mygrid.setInitWidths("30,40,320,100,150,200,*");//set column width in px
    mygrid.setColAlign("right,left,left,left,left,left,left");//set column values align
    mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");//set column types
    mygrid.setColSorting("int,str,str,str,str,str,str");//set sorting
    mygrid.init();//initialize grid
    mygrid.setSkin("dhx_web");//set grid skin
    mygrid.enableContextMenu(menu);

    //load all posts .
    mygrid.load(wwwroot + "/blocks/livedesk/serverside/service.php?action=load_liveentries&bid="+bid,"xml");
    mygrid.attachEvent("onBeforeContextMenu", onBeforeContextMenuHandler);
    mygrid.attachEvent("onRowDblClicked", onRowDblClicked);
    mygrid.attachEvent("onBeforeContextMenu", onBeforeContextMenu);

    //** setup the layout .
    var layout = new dhtmlXLayoutObject("livedesk-masterlayout", "4C");
    layout.setSkin("dhx_web");

    layout.cells("a").setWidth("800");    
    layout.cells("a").setHeight(800);    
    layout.cells("b").setWidth("250");    
    layout.cells("c").setWidth("250");    
    layout.cells("d").setWidth("250");    
    layout.cells("b").setHeight("90");   
    layout.cells("c").setHeight(130);    
    layout.cells("a").attachObject("livedesk-postsgrid");    
    layout.cells("c").attachObject("livedesk-onlineuserscont");    
    layout.cells("b").attachObject("livedesk-info");    
    layout.cells("d").attachObject("livedesk-plugins");    
    layout.cells("a").setText("'.get_string('live_queue', 'block_livedesk').'") ;
    layout.cells("b").setText("'.get_string('livedesk_info', 'block_livedesk').'") ;
    layout.cells("c").setText("'.get_string('online_users', 'block_livedesk').'") ;
    layout.cells("d").setText("'.get_string('monitoredplugins', 'block_livedesk').'") ;

    //init Toolbar.
    var toolbar = layout.attachToolbar();
    toolbar.setIconsPath("pix/");
    toolbar.setSkin("dhx_web");
    toolbar.addButton(\'posts_refresh\', 2, \''.get_string('refresh_posts', 'block_livedesk').'\', \'refresh.png\'); 
    '.$viewsettingsbutton.' 
    '.$viewstatisticsbutton.'
    '.$viewmanagementbutton.'
    toolbar.addButtonTwoState(\'show_answered\', '.$i.', \'\', \'show_answered_on.png\', \'show_answered_off.png\'); 
    toolbar.setItemToolTip(\'show_answered\', \''.$showhideansweredtooltip.'\'); 
    '.$setinitialstateanswered.'
    toolbar.addButtonTwoState(\'show_discarded\', '.($i + 1).', \'\', \'show_discarded_on.png\', \'show_discarded_off.png\'); 
    toolbar.setItemToolTip(\'show_discarded\', \''.$showhidediscardedtooltip.'\'); 
    '.$setinitialstatediscarded.'
    toolbar.addButtonTwoState(\'show_locked\', '.($i + 2).', \'\', \'show_locked_on.png\', \'show_locked_off.png\'); 
    toolbar.setItemToolTip(\'show_locked\', \''.$showhidelockedtooltip.'\'); 
    '.$setinitialstatelocked.'
    toolbar.attachEvent(\'onClick\', toolbarClickHandler);
     toolbar.attachEvent(\'onStateChange\', toolbarStateChange);
    
    //calendar on the discard mesages before page .
    calendar = new dhtmlXCalendarObject("discard_date");
    getMonitoredPlugins();
    refreshOnlineUsersCount();  

    //periodic update 
    setInterval(function() {
      refreshGrid();
      refreshOnlineUsersCount();  
     }, '.$refresh.');

     setInterval(function() {
      keepMeAlive();
     }, '.$keepalive.');


    function onBeforeContextMenu(id,ind,obj){
        selectedRow = id;
        return true;
    }

    /**
    * Grid context menu event handles.
    */
    function onContextMenuClick (id, zoneId, casState){
        switch (id) {
            case "reply_post":
                selId = selectedRow;
                reply_post(selId);
                return true;
              break;
            case "discard_post":
            discard_post(selectedRow);
            break;
            case "discard_posts_before":
                timecreated = mygrid.getUserData(selectedRow, "timecreated");
                discard_messages_window = dhxWins.createWindow(\'discard_messages\', 400, 100, 400, 130);
                dhxWins.window(\'discard_messages\').setModal(true);
                dhxWins.window(\'discard_messages\').setIcon(\'../../pix/discard_mail.png\');
                dhxWins.window(\'discard_messages\').setText(\''.get_string('discard_before', 'block_livedesk').'\');
                dhxWins.window(\'discard_messages\').attachURL("discard_messages_before.php?bid="+bid+"&date="+timecreated, true);
                return true;
            break;
          }
    }

    /**
    * Event Handlers
    */
    function onBeforeContextMenuHandler(id, ind, obj) {
        var status_id =  mygrid.getUserData(id, "status_id");
        if (status_id == 0) {
             menu.hideItem(\'disable_post\');
             menu.showItem(\'enable_post\');
        } else {
             menu.hideItem(\'enable_post\');
             menu.showItem(\'disable_post\');
        }
        return true;
    }

    /**
    * This function handles the toolbar buttons click events.
    */
    function toolbarClickHandler(item, event) {
        switch(item) {
            case "posts_refresh":
                refreshGrid();
                break;
            case "view_statistics":
                var win = dhxWins.createWindow(\'view_statistics\', 400, 100, 850, 400);
                dhxWins.window(\'view_statistics\').setModal(true);
                dhxWins.window(\'view_statistics\').setIcon(\'../../pix/statistics.png\');
                dhxWins.window(\'view_statistics\').setText("Statistics");
                dhxWins.window(\'view_statistics\').attachURL("statistics.php?bid="+bid, true);
                return true;
                break;
            case "settings":
                url = wwwroot+"/course/view.php?id="+courseid+"&bui_editid="+bid+"&sesskey='.sesskey().'";
                window.location = url;
                return true;
                break;
            case "manage_livedesks":
                url = wwwroot+"/blocks/livedesk/manage.php";
                window.location = url;
                return true;
            break;
        }
    }

    /*
    * Sending state to server for SESSION storing and
    * use in next refreshs.
    */
    function toolbarStateChange(item, state){
        if(state){
            toolbar.setItemImage(item, item+\'_on.png\');
        } else {
            toolbar.setItemImage(item, item+\'_off.png\');
        }
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=change_state&item=\'+item+\'&state=\'+state+\'&bid=\'+bid;
        /* url = \'serverside/service.php?action=change_state&item=\'+item+\'&state=\'+state+\'&bid=\'+bid; */
           $.post(url, function(data) {});
        refreshGrid();
        return true;
    }

    /**
    * Refresh the grid.
    */
    function refreshGrid(){

        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=load_liveentries&bid=\'+bid;
        // url = \'serverside/service.php?action=load_liveentries&bid=\'+bid;

        if (_isIE){ //IE)
           var current_rows_count = mygrid.getRowsNum();
           var fetched_rows_count = 0; //data[rows].length;
           mygrid.clearAndLoad(url, "xml");
        } else { //Other browsers   
           $.post(url, function(data) {
             var current_rows_count = mygrid.getRowsNum();
             var fetched_rows_count = 0; //data[rows].length;
             mygrid.clearAll();
             mygrid.parse(data); 
        });
        }

        return true;
    }

    /**
    * Double click event handler
    */
    function onRowDblClicked(rId,cInd){
        reply_post(rId);
    }

    /**
    * Initiate the window for replying to new message
    */
    function reply_post(rowId){
        var itemid = mygrid.getUserData(rowId, "itemid");  
        var messageid = mygrid.getUserData(rowId, "messageid");  
        var cmid = mygrid.getUserData(rowId, "cmid");    
        var randX = Math.floor(Math.random() * (450 - 350 + 1)) + 350;
        var randY = Math.floor(Math.random() * (150 - 50 + 1)) + 50;
        var win = dhxWins.createWindow(\'reply_post\'+rowId, randX, randY, 800, 450);
        win.messageid = messageid;
        win.closing = false;
        win.setModal(false);            
        win.setText("Reply");
        win.attachURL("reply.php?reply="+itemid+"&cmid="+cmid, false);
        dhxWins.attachEvent(\'onClose\', unlockItem);
    }

    /**
    * Generates popup for new incoming messages.
    */
    function generateNoty(text,type,extraparam) {
        var n = noty({
            text: text,
            timeout: 5000,
            closeWith: [\'button\'],
            type: type,
        dismissQueue: true,
            layout: \'bottomRight\',
            theme: \'defaultTheme\'
        });
        return n;
    }

    $(\'.noty_message\').click(function(){
         var related_message_id =  $(this).find(\'.noty_message_num\').val();        
         if (related_message_id == ""){
             return;
         } else {        
         }
    });

    function refreshOnlineUsersCount(){
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=get_online_users_count\'+\'&bid=\'+bid;   
          $.post(url, function(data) {
              data = JSON.parse(data);
              $(\'#livedesk-onlineuserscont\').html("<div class=\'online_users\'><img src=\'pix/user.png\' /> '.get_string('online_users_count', 'block_livedesk').' "+data.users_count+"</div>");
              $(\'#livedesk-onlineuserscont\').append("<div class=\'online_users\'><img src=\'pix/user-black.png\' /> '.get_string('online_attenderes_count', 'block_livedesk').' "+data.attenders_count+"</div>");
              if (data.attenders_count > 0){
                  var userlist = "<ul class=\'livedesk-userlist\'>";
                  for(i = 0 ; i < data.attenders.length ; i++){
                      userlist += "<li class=\'online_users "+data.attenders[i].className+"\'>"+data.attenders[i].name+"</li>";
                  } 
                  $(\'#livedesk-onlineuserscont\').append(userlist+"</ul>");
              }
          });        
    }

    function keepMeAlive() {
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=keep_me_live\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid;
        $.post(url, function(data) {
              $(\'#livedesk-onlineuserscont\').html("Online Users: "+data);
          });   
    }

    function getMonitoredPlugins() {
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=get_monitored_plugins\'+\'&livedeskid=\'+livedeskid+\'&bid=\'+bid;
        $.post(url, function(data) {
            $(\'#livedesk-plugins\').html(\'\');
               data =   JSON.parse(data);
               for (key in data) {
                 $(\'#livedesk-plugins\').append(\'<div class="online_users"><img src="pix/block.png" /><a target="_blank" href="../../mod/forum/view.php?id=\'+data[key].cmid+\'" > \'+data[key].name+\'</a></div>\');
               }
          });   
    }    

    function unlockItem(win) {
        if (!win.closing){
            win.closing = true;
            url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=unlock_item\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid+\'&messageid=\'+win.messageid;
            $.post(url, function(data) {
                refreshGrid();
              });   
        }
        return true;
    }

    function discard_post(){
        var postid = mygrid.getUserData(selectedRow, "messageid");
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=discard_post\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid+\'&messageid=\'+postid;
        $.post(url, function(data) {
            refreshGrid();
        }); 
    }
});');
