<?php

/**
 * run.php
 * 
 * This file provides code for livedesk main screen.
 *
 * @package block-livedesk
 * @category blocks
 * @author Wafa Adham <admin@adham.ps>, Valery Fremaux <valery.fremaux@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * 
 */

    require_once('../../config.php');
   
    $bid = optional_param('bid', null, PARAM_INT);
    $courseid = optional_param('course', 1, PARAM_INT);
    
    if (!$course = get_record('course', 'id', "$courseid")) error('Error : Bad course ID');
    
    if ($course->id == SITEID){
	    require_login();
	} else {
	    require_login($course);
	}
  
    $livedesk_reference = get_record('block_livedesk_blocks', 'blockid', $bid);
        
    if(empty($livedesk_reference)){
        print(get_string('instance_notbounded_to_livedesk','block_livedesk'));
        exit;
    }
        
    $livedeskid = $livedesk_reference->livedeskid;
    $livedesk = get_record('block_livedesk_instance', 'id', $livedeskid);
    
    if(empty($livedesk)){
        print(get_string('invalid_livedesk','block_livedesk'));
        exit;
    }
    
    $context = get_context_instance(CONTEXT_BLOCK, $bid);
    require_capability('block/livedesk:runlivedesk', $context);
    
    //some variables neededby the js 
    print('<script type="text/javascript">var bid = '.$bid.'</script>'); 
    print('<script type="text/javascript">var courseid = '.$courseid.'</script>'); 
    print('<script type="text/javascript">var livedeskid = '.$livedeskid.'</script>'); 
    print('<script type="text/javascript">var wwwroot = \''.$CFG->wwwroot.'\'</script>'); 
  
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgrid.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxlayout.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxVault/codebase/dhtmlxvault.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_web.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/skins/dhtmlxtoolbar_dhx_web.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_web.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxwindows.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/skins/dhtmlxwindows_dhx_web.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/dhtmlxcalendar.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_dhx_skyblue.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/skins/dhtmlxlayout_dhx_web.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxTabbar/codebase/dhtmlxtabbar.css">');
    print('<link rel="stylesheet" type="text/css" href="/blocks/livedesk/js/dhtmlx/3.0/dhtmlxTabbar/codebase/skins/dhtmlxtabbar_dhx_web.css">');
   
    //Grid
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxcommon.js'); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgrid.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/dhtmlxgridcell.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/ext/dhtmlxgrid_group.js' );

    //Vault
    //require_js('/blocks/livedesk/js/dhtmlx/3.0/dhtmlxVault/codebase/dhtmlxvault.js');

    //Toolbar
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxcommon.js' );   
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/dhtmlxmenu.js' );   
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js' ); 

    //Menu 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxcommon.js' );   
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxToolbar/codebase/dhtmlxtoolbar.js' ); 
    
        //Layout
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxcommon.js' );   
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxcontainer.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase/dhtmlxlayout.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxLayout/codebase//patterns/dhtmlxlayout_pattern4c.js' ); 
    
    //Windows
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxcommon.js' );   
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxcontainer.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxWindows/codebase/dhtmlxwindows.js' ); 

    //Calendar
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxCalendar/codebase/dhtmlxcalendar.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js' );
    
    //TabBar
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxTabbar/codebase/dhtmlxcommon.js' ); 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/dhtmlx/3.0/dhtmlxTabbar/codebase/dhtmlxtabbar.js' ); 
    
    //JQuery
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery-1.8.2.min.js' );
    
    //noty jquery plugin 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/jquery.noty.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottom.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomCenter.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/bottomRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/center.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/centerLeft.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/centerRight.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/inline.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/top.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topCenter.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topLeft.js' );
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/layouts/topRight.js' );
    
    require_js($CFG->wwwroot.'/blocks/livedesk/js/jquery_plugins/noty/themes/default.js' );
 
    require_js($CFG->wwwroot.'/blocks/livedesk/js/init.php' );

    $strtitle = get_string('livedesk', 'livedesk');
    require_login();
    
    $system_context = get_context_instance(CONTEXT_SYSTEM);
    
    $course = get_record('course','id',$courseid);
        
   /* 
    $PAGE->set_pagelayout('plain');
    $PAGE->set_context($system_context);
    $PAGE->set_title($strtitle);
    $PAGE->set_heading($SITE->fullname);
   
    $PAGE->navbar->add($strtitle,'run.php','misc');
    
    
    $PAGE->set_focuscontrol('');
    $PAGE->set_cacheable(false);
    $PAGE->set_button('');
    $PAGE->set_headingmenu('');

    $url = new moodle_url('/blocks/liveforum/livedesk.php');
    $PAGE->set_url($url);
    
    echo $OUTPUT->header();   */
    global $NOJQUERY;
    $NOJQUERY = true;
    print_header();
    
   // print('<div class="header">');
    print('<div class="headerlogo" >
    <img src="pix/logo.png" title="LiveDesk" />
    </div>');
    
    print('<div style="  margin-left: 20px;position: relative;">');
    print('<div id="MainToolbar"></div>'); 
    print('<div id="toolbar" style="margin-bottom:33px;"></div>');
    print("<br>");
    print('<div id="masterlayout" style="width:100%; height:100%;top:33px;">');
    print('</div>');
    
    print('<div id="PostsContainer">');
   
    print('<div id="postsgrid" width="100%" height="100%" style = "background-color:white;"></div>  ');
    print('</div>');
    print('<div id="onlineuserscont" width="100%" height="100%" style = "background-color:white;"></div>  ');
    print('<div id="plugins" width="100%" height="100%" style = "background-color:white;"></div>  ');
    print('<div id="livedesk_info" width="100%" height="100%" style = "background-color:white;">
    <b>'.$livedesk->name.'</b>
    </div>  ');
    
    print('</div>');
    
    print('<style>th, td { padding: 0px; } </style>') ;
    
	add_to_log($courseid,'livedesk', 'run', 'run.php', $livedeskid, $bid, $USER->id);

	print_footer(); 
  
?>