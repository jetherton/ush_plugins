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
	<form action="<?php echo url::base(); ?>usermsg/send_msg_report" method="post" id="userMsg" name="userMsg" enctype="multipart/form-data">
	
	
		<input type="hidden" name="msg_incident_id"  id="msg_incident_id" value="<?php echo $incident_id;?>">

		<div id="sendMessageWaitHolder" style="float:right;"></div>
		
		
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
		<?php Event::run('usermsg.display_message_submit');?>
		<div class="report_row">
			<input name="msg_submit" id="msg_submit" type="submit" value="<?php echo Kohana::lang('usermsg.send_message'); ?> <?php echo Kohana::lang('ui_main.comment'); ?>" class="btn_blue" />
		</div>
	<?php echo form::close();?>

</div>
<br/><br/>
