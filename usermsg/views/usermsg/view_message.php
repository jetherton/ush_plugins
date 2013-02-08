<?php defined('SYSPATH') or die('No direct script access.');
/**
 * View Message, the HTML for the user to view a message
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */
?>

<h3><?php echo Kohana::lang('usermsg.Message') . ' - '.  htmlentities($message->subject, ENT_QUOTES);?></h3>
<h5><?php echo Kohana::lang('usermsg.Sent'). ': '. date("F j, Y, g:i a",strtotime($message->date));?></h5>
<?php if($message->email != null AND $message->email != ""){?>
<h5><?php echo Kohana::lang('usermsg.From'). ': '. htmlentities($message->email, ENT_QUOTES);?></h5>
<?php }?>
<p>
<?php echo nl2br(htmlentities($message->msg_text, ENT_QUOTES)); ?>
</p>
<?php Event::run('usermsg.display_msg', $message);?>

<div id="usermsgFunctions">
<?php if (intval($message->from_user_id) != 0){?>

<input type="button" value="<?php echo Kohana::lang('usermsg.Reply')?>" onclick="userMsgReply(<?php echo $message->id?>); return false;"/>
<?php } else {
	echo Kohana::lang('usermsg.cant_reply'); 
}
$id = $message->id;
?>
<div id="usermsgReplyDiv_<?php echo $message->id;?>" class="replydiv" style="display:none;">
<form action="<?php echo url::base(); ?>usermsg/send_reply" method="post" id="userMsg_<?php echo $id?>" name="userMsg_<?php echo $id?>" enctype="multipart/form-data">
	<?php //figure out the RE situation
		$subject = htmlentities($message->subject, ENT_QUOTES);
		if(strtolower(substr($subject,0,3)) != "re:")
		{
			$subject = "Re:".$subject;
		}
	?>
	<?php echo Kohana::lang('usermsg.Subject:'). ' ' . Form::input('subject', $subject, 'id="subject_'.$id.'"');?>
	<br/><br/>
	<?php echo Kohana::lang('usermsg.Message:');?> 
	<br/>
	<?php echo Form::textarea('message', null, 'id="message_'.$id.'"');?>
	<?php Event::run('usermsg.display_message_submit');?>
	<input type="hidden" name="msg_id" value="<?php echo $id;?>"/>
	<input type="submit" value="<?php echo Kohana::lang('usermsg.Send Reply')?>" />
	<input type="button" value="<?php echo Kohana::lang('usermsg.Cancel')?>" onclick="userMsgSendReply(<?php echo $id?>, false); return false;"/>
	<span id="sendMessageWaitHolder"></span>
	</form>
</div>
</div>