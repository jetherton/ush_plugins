<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Message, the HTML for the content that lets you
 * write a message to a report's author
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */
?>

<div class="comment-block" id="userMsg">


<h5><?php echo Kohana::lang('usermsg.send_author')?></h5>
	<?php print form::open(NULL, array('id' => 'userMsg', 'name' => 'userMsg')); ?>
	
		<input type="hidden" name="msg_incident_id"  id="msg_incident_id" value="<?php echo $incident_id;?>">

		<div id="sendMessageWaitHolder" style="float:right;"></div>
		
		<div class="report_row">
			<strong><?php echo Kohana::lang('usermsg.Name:')?></strong>(<?php echo Kohana::lang('usermsg.optional');?>)<br>
			<?php print form::input('msg_name',null, ' class="text" id="msg_name"'); ?>			
		</div>
		<div class="report_row" <?php 
		
		if(isset($_SESSION['auth_user']))
		{
			echo 'style="display:none;"';
		}
		?>>
			<strong><?php echo Kohana::lang('usermsg.Email:')?></strong>(<?php echo Kohana::lang('usermsg.optional');?>)<br>
			<?php print form::input('msg_email',null, ' class="text" id="msg_email"'); ?>			
		</div>
		<div class="report_row">
			<strong><?php echo Kohana::lang('usermsg.Subject:')?></strong><br>
			<?php print form::input('msg_subject',null, ' class="text" id="msg_subject"'); ?>			
		</div>
		<div class="report_row">
			<strong><?php echo Kohana::lang('usermsg.Message:')?></strong><br>
			<?php print form::textarea('msg_content', null, ' rows="4" cols="40" class="textarea long" id="msg_content" ') ?>			
		</div>
		<div class="report_row">
			<input name="msg_submit" id="msg_submit" type="button" value="<?php echo Kohana::lang('usermsg.send_message'); ?> <?php echo Kohana::lang('ui_main.comment'); ?>" class="btn_blue" />
		</div>
	<?php echo form::close();?>

</div>
<br/><br/>
<script type="text/javascript">
	$("#msg_submit").click(function(){
		var incident_id = $("#msg_incident_id").val();
		var name = $("#msg_name").val();
		var email = $("#msg_email").val();
		var subject = $("#msg_subject").val();
		var content = $("#msg_content").val();

		if(subject == null || typeof subject == "undifined" || subject == "")
		{
				alert("Please specify a subject");
				return;
		}
		
		//turn on the waiter
		$("#sendMessageWaitHolder").html('<img src="<?php echo url::base();?>media/img/loading_g2.gif" />');
		
		//send the data to the server
		$.post("<?php echo url::base()?>usermsg/send_msg_report", { "incident_id":incident_id,
				"name":name,
				"email":email,
				"subject":subject,
				"content":content },
				  function(data){
					$("#sendMessageWaitHolder").html(''); //turn off waiter
				    if(data.status == "success")
				    {
					    alert("Message sent successfully");
					    //clear out the fields
					    $("#msg_name").val('');
						$("#msg_email").val('');
						$("#msg_subject").val('');
						$("#msg_content").val('');
				    }
				    else
				    {
					    alert("Error sending. Please try again.");
				    }
				  }, "json");	
	});
</script>