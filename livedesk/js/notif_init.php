<?php
require_once('../../../config.php')  ;
header("Content-type: text/javascript; charset=utf-8");  ;

$id = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', "$id")){
	error("bad course ID");
}

require_login($course);

$newmessagesstr = get_string('newmessages', 'block_livedesk');

?>

$(document).ready(function(){
	$([window, document]).focusin(function(){
    	//Your logic when the page gets active
    	console.log("am here");

    	}).focusout(function(){

	//Your logic when the page gets inactive
	});

//periodic update 
     setInterval(function() {
        get_unnotified_messages();
     }, 10000);
 
   
    function get_unnotified_messages(){
        
        url = '/blocks/livedesk/serverside/service.php?action=get_unnotified_messages&course=<?php echo $course->id ?>';
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
            timeout: 5000,
            closeWith: ['button'],
            type: type,
        	dismissQueue: true,
            layout: 'bottomRight',
            theme: 'defaultTheme'                  
        });
      
        return n;
    }

});  