<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Creates the HTML to render an inbox for users
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */
?>

<div id="content">
	<div class="content-bg">
		<!-- start contacts block -->
		<div class="big-block">
			<h1><?php echo Kohana::lang('usermsg.Inbox')?></h1>
			<table id="userMsgInbox">
				<tr>
					<th>Subject</th>
					<th>Date</th>
					<th>Action</th>
				</tr>
			<?php 
			$i = 0;
			foreach($messages as $message){
			$i++;
			$class = $i % 2 == 1 ? 'odd' : '';
				?>
				<tr>
					<td class="<?php echo $class;?>">
						<a rel="#overlay" href="<?php echo url::base()?>usermsg/getmsg?id=<?php echo $message->id?>" ><?php echo $message->subject;?></a>						
					</td>
					<td class="<?php echo $class;?>"><?php echo date("F j, Y, g:i a",strtotime($message->date));?></td>
					<td class="<?php echo $class;?>">
						<a href="" onclick="deleteMsg(<?php echo $message->id;?>); return false;">delete</a>  
						<a rel="#overlay" href="<?php echo url::base()?>usermsg/getmsg?id=<?php echo $message->id?>">view</a>
					</td>
				</tr>
			<?php }?>
			</table>						
		</div>
		<!-- end contacts block -->
	</div>
</div>


<!-- overlayed element -->
<div class="apple_overlay" id="overlay">
  <!-- the external content is loaded inside this tag -->
  <div class="contentWrap"></div>
</div>

<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'inboxForm', 'name' => 'inboxForm')); ?>
<input type="hidden" value="0" id="message_id" name="message_id"/>
<?php print form::close();?>