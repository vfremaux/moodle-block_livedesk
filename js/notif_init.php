<?php
require_once('../../../config.php')  ;
header("Content-type: text/javascript; charset=utf-8\n\n");

$id = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', "$id")){
	error("bad course ID");
}

require_login($course);

$newmessagesstr = get_string('newmessages', 'block_livedesk');

if (empty($CFG->block_livedesk_notification_refresh_time)){
	$CFG->block_livedesk_notification_refresh_time = 100000;
}

if (empty($CFG->block_livedesk_notification_onscreen_time)){
	$CFG->block_livedesk_notification_onscreen_time = 5000;
}

?>

$(document).ready(function(){
	$([window, document]).focusin(function(){

    	}).focusout(function(){

	//Your logic when the page gets inactive
	});

//periodic update 
     setInterval(function() {
        get_unnotified_messages();
     }, <?php echo $CFG->block_livedesk_notification_refresh_time ?>);
 
   
    function get_unnotified_messages(){
        
        url = '<?php echo $CFG->wwwroot; ?>/blocks/livedesk/serverside/service.php?action=get_unnotified_messages&course=<?php echo $course->id ?>';
        $.post(url, function(data) { 
        
	        if (data == ""){
	            return;
	        }
	        
	        $.noty.closeAll();
          
	        data = JSON.parse(data);
	        
	        for(key in data){
           		var id = data[key].id;
           		var message = data[key].message;
           		generateNoty("<input name=\'noty_id"+id+"\' type=\'hidden\' value="+id+" class=\'noty_message_num\' /><b><?php echo $newmessagesstr ?></b><div>"+message+"</div>", "information", id); 
        	}
        
        });
        
    }

    function generateNoty(text, type, extraparam) {
        var n = noty({
            text: text,
            timeout: <?php echo $CFG->block_livedesk_notification_onscreen_time ?>,
            closeWith: ['button'],
            type: type,
        	dismissQueue: true,
            layout: 'bottomRight',
            theme: 'defaultTheme'                  
        });
      
        return n;
    }

});  