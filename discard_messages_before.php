<?php
	require_once('../../config.php');
	
	require_login();
	
	$timecreated = required_param('date', PARAM_TEXT);

	print('<div style="font-size:12px;">'.get_string('discard_before_txt','block_livedesk').'</div>');
	print('<div style="font-size:12px;">'.get_string('discard_date','block_livedesk').' <input id="discard_date" type="text" value="'.date("d.m.Y h:i", $timecreated).'" /></div>');
	print('<div style="margin-top:10px;width:100%;text-align:center;"><input id="discard_btn" type="button" value="'.get_string('confirmdiscard', 'block_livedesk').'" /></div>');
	print('<script>');
	print('calendar = new dhtmlXCalendarObject("discard_date");') ;
	print('calendar.setDate(\''.date("d.m.Y h:i", $timecreated).'\');') ;
	print('calendar.setDateFormat("%d.%m.%Y %h:%i");') ;
	print('
	$(\'#discard_btn\').click(function(){
    	discard_post_before_date();
  	});

  	function discard_post_before_date(){
       var ddate = $(\'#discard_date\').val();
       url = wwwroot+\'/blocks/livedesk/serverside/service.php?action=discard_post\'+\'&bid=\'+bid+\'&livedeskid=\'+livedeskid+\'&courseid=\'+courseid+\'&date=\'+ddate;
       $.post(url, function(data) {
       window.discard_messages_window.close();
        }); 
  	}
  
  	');
  	print('</script>');
  
?>
