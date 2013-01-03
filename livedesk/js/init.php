<?php
require_once('../../../config.php')  ;
header("Content-type: text/javascript; charset=utf-8");  ;

print('
$(document).ready(function(){
    var dhxWins = new dhtmlXWindows();   

    //menu
    var menu = new dhtmlXMenuObject();
    menu.setIconsPath("pix/");
    menu.setSkin("dhx_web");
    menu.renderAsContextMenu();
    // initing;
    menu.addNewChild(null, 1, "reply_post","'.get_string("reply", "block_livedesk").'", false,\'reply_mail.png\');
    menu.addNewChild(null, 2, "discard_post","'.get_string("discard", "block_livedesk").'", false,"discard_mail.png");
    menu.addNewChild(null, 3, "send_email","'.get_string("email_user", "block_livedesk").'", false,"mail.png");

    //context menu event
    menu.attachEvent("onClick",onContextMenuClick); 
    //** init Grid
    mygrid = new dhtmlXGridObject(\'postsgrid\');
    mygrid.setImagePath("js/dhtmlx/3.0/dhtmlxGrid/codebase/imgs/");//path to images required by grid
    mygrid.setHeader("#,,'.get_string("message","block_livedesk").','.get_string("user","block_livedesk").','.get_string("message_time","block_livedesk").','.get_string("origin","block_livedesk").','.get_string("lockedby","block_livedesk").'");//set column names
    mygrid.setInitWidths("30,40,320,100,150,200,*");//set column width in px
    mygrid.setColAlign("right,left,left,left,left,left,*");//set column values align
    mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");//set column types
    mygrid.setColSorting("int,str,str,str,str,str");//set sorting
    mygrid.init();//initialize grid
    mygrid.setSkin("dhx_web");//set grid skin
    mygrid.enableContextMenu(menu);
    //load all posts .
    mygrid.load("serverside/service.php?action=load_liveentries&bid="+bid,"xml");
    mygrid.attachEvent("onBeforeContextMenu",onBeforeContextMenuHandler);
    mygrid.attachEvent("onRowDblClicked",onRowDblClicked);

    //** setup the layout .
    var layout = new dhtmlXLayoutObject("masterlayout","4C");
    layout.cells("b").setWidth(250);    
    layout.cells("b").setHeight(90);    
    layout.cells("c").setHeight(90);    
    layout.cells("a").attachObject("postsgrid");    
    layout.cells("c").attachObject("onlineuserscont");    
    layout.cells("b").attachObject("livedesk_info");    
    layout.cells("d").attachObject("plugins");    
     
    layout.setSkin("dhx_web");
    layout.cells("a").setText("'.get_string("live_queue","block_livedesk").'") ;
    layout.cells("b").setText("'.get_string("livedesk_info","block_livedesk").'") ;
    layout.cells("c").setText("'.get_string("online_users","block_livedesk").'") ;
    layout.cells("d").setText("'.get_string("monitoredplugins","block_livedesk").'") ;
    
    //init Toolbar.
    var toolbar = layout.attachToolbar();
    toolbar.setIconsPath("pix/");
    toolbar.setSkin("dhx_web");
    toolbar.addButton(\'posts_refresh\', 2, \''.get_string("refresh_posts","block_livedesk").'\', \'refresh.png\'); 
    toolbar.addButton(\'settings\',4, \''.get_string("configurations","block_livedesk").'\', \'settings.png\', null); 
    toolbar.addButton(\'view_statistics\',5, \''.get_string("statistics","block_livedesk").'\', \'statistics.png\', null); 
  
    toolbar.attachEvent(\'onClick\', toolbarClickHandler);
    
    getMonitoredPlugins();
    refreshOnlineUsersCount();  
     
    //periodic update 
    setInterval(function() {
        refreshGrid();
        refreshOnlineUsersCount();  
     }, 10000);
     
     setInterval(function() {
       keepMeAlive();
     }, 300000);

    var success = generateNoty(\'<b>Welcome to LiveDesk!</b>\',\'information\');
    
    /**
    * Grid context menu event handles.
    */
    function onContextMenuClick (id, zoneId, casState){
       
		switch (id) {
			case "reply_post" :   
				selId = mygrid.getSelectedId();
            	reply_post(selId);
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
                      
            case "settings": 
            	url = moodle_cfg.wwwroot+"/course/view.php?id="+courseid+"&instanceid="+bid+"&blockaction=config&sesskey='.sesskey().'";
	            window.location = url;
	            return true;            
        }
    }
     
    /**
    * Refresh the grid.
    */
    function refreshGrid(){
        url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=load_liveentries&bid=\'+bid;
        $.post(url, function(data) {
           
             var current_rows_count = mygrid.getRowsNum();
             var fetched_rows_count = 0; //data[rows].length;
             mygrid.clearAll();
             mygrid.parse(data); 
            
        },\'xml\');
        
        mygrid.forEachRow(function(id){ // function that gets id of the row as an incoming argument
            // here id - id of the row
	        var notified = mygrid.getUserData(id,"notified"); 
               
	       if (notified == 0 || notified == ""){
	           var message = mygrid.cells(id,2).getValue();
	           generateNoty("<input name=\'noty_id"+id+"\' type=\'hidden\' value="+id+" class=\'noty_message_num\' /><b>'.get_string('newmessage', 'block_livedesk').'</b><div>"+message+"</div>","information",id);
			} else {
			//      console.log(id+" "+notified);
			}
            
		});
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
        var plugininstance = mygrid.getUserData(rowId, "plugininstance"); 
        var randX = Math.floor(Math.random() * (450 - 350 + 1)) + 350;
        var randY = Math.floor(Math.random() * (150 - 50 + 1)) + 50;
         
        var win = dhxWins.createWindow(\'reply_post\'+rowId, randX, randY, 800, 450);
        win.itemid = itemid;
        win.closing = false;
   
        win.setModal(false);            
        win.setText("Reply");
        win.attachURL("reply.php?reply="+itemid+"&course=22&plugininstance="+plugininstance, false);
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
      		$(\'#onlineuserscont\').html("<div class=\'online_users\'><img src=\'pix/user.png\' /> '.get_string("online_users_count", "block_livedesk").' "+data.users_count+"</div>");
      		$(\'#onlineuserscont\').append("<div class=\'online_users\'><img src=\'pix/user-black.png\' /> '.get_string("online_attenderes_count", "block_livedesk").' "+data.attenders_count+"</div>");
      	});        
    }
    
    function keepMeAlive() {
    	url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=keep_me_live\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid;
		$.post(url, function(data) {
      		$(\'#onlineuserscont\').html("Online Users: "+data);
      	});   
    }
    
    function getMonitoredPlugins() {
    	url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=get_monitored_plugins\'+\'&livedeskid=\'+livedeskid+\'&bid=\'+bid;
		$.post(url, function(data) {
			$(\'#plugins\').html(\'\');
           	data =   JSON.parse(data);
           	for (key in data) {
             	$(\'#plugins\').append(\'<div class="online_users"><img src="pix/block.png" /><a target="_blank" href="../../mod/forum/view.php?id=\'+data[key].cmid+\'" > \'+data[key].name+\'</a></div>\');
           	}
      	});   
    }    

    function unlockItem(win) {
    	if (!win.closing){
    		win.closing = true;
    		url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=unlock_item\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid+\'&itemid=\'+win.itemid;
			$.post(url, function(data) {
				refreshGrid();
	      	});   
		}
		return true;
    }
           
});');

?>