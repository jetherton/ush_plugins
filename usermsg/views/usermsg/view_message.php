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


<div id="usermsgFunctions">
<?php if (intval($message->from_user_id) != 0){?>

<input type="button" value="<?php echo Kohana::lang('usermsg.Reply')?>" onclick="userMsgReply(<?php echo $message->id?>); return false;"/>
<?php } else {
	echo Kohana::lang('usermsg.cant_reply'); 
}?>
<div id="usermsgReplyDiv_<?php echo $message->id;?>" class="replydiv" style="display:none;">
	<?php //figure out the RE situation
		$subject = htmlentities($message->subject, ENT_QUOTES);
		if(strtolower(substr($subject,0,3)) != "re:")
		{
			$subject = "Re:".$subject;
		}
		$id = $message->id;
	?>
	<?php echo Kohana::lang('usermsg.Subject:'). ' ' . Form::input('subject_'.$id, $subject, 'id="subject_'.$id.'"');?>
	<br/><br/>
	<?php echo Kohana::lang('usermsg.Message:');?> 
	<br/>
	<?php echo Form::textarea('message_'.$id, null, 'id="message_'.$id.'"');?>
	<input type="button" value="<?php echo Kohana::lang('usermsg.Send Reply')?>" onclick="userMsgSendReply(<?php echo $id?>, true); return false;"/>
	<input type="button" value="<?php echo Kohana::lang('usermsg.Cancel')?>" onclick="userMsgSendReply(<?php echo $id?>, false); return false;"/>
	<span id="sendMessageWaitHolder"></span>
</div>
</div>