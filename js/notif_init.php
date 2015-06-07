<?php
require_once('../../../config.php')  ;
header("Content-type: text/javascript; charset=utf-8");  ;

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => "$id"))){
	print_error('invalidcourseid');
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

    function get_unnotified_messages(){
        url = '<?php echo $CFG->wwwroot; ?>/blocks/livedesk/serverside/service.php?action=get_unnotified_messages&course=<?php echo $course->id ?>';
        $.post(url, function(data) { 

	        if (data == ""){
	            return;
	        }
	        
	        $.noty.closeAll();
	        dataarr = JSON.parse(data);

	        for(key = 0 ; key < dataarr.length ; key++){
           		var id = dataarr[key].id;
           		var message = dataarr[key].message;
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

	setInterval(get_unnotified_messages, <?php echo $CFG->block_livedesk_notification_refresh_time ?>);

});  