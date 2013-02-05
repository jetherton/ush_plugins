<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Message HTTP Post Controller
 *
 * @author	   Etherton Tech <info@ethertontech.com> 
 * @package	   User Message plugin 
*/

class Usermsg_Controller extends Main_Controller {
	
	
	/**
	 * Handles sending messages to another user
	 * Expects all of the parameters to be passed
	 * in via HTTP POST
	 */
	public function send_msg_report()
	{
		$this->auto_render = false;
		
		header('Content-type: application/json; charset=utf-8');
		//make sure all the necessary fields are present
		if(!(isset($_POST['incident_id']) AND
				isset($_POST['name']) AND 
				isset($_POST['email']) AND
				isset($_POST['subject']) AND
				isset($_POST['content'])))
		{
			echo '{"status":"error"}';
					return;
		}
		
		//grab the incident and find out what user submitted it
		$user_id = ORM::factory('incident',$_POST['incident_id'])->user_id;
		$user = ORM::factory('user',$user_id);
		$from_user_id = null; 
		//see if we can figure out the who the sender is
		if(isset($_SESSION['auth_user']))
		{
			$from_user_id = $_SESSION['auth_user']->id;
		}
		
		//create the message
		$message = ORM::factory('usermsg');
		$message->to_user_id = $user_id;
		$message->from_user_id = $from_user_id;
		$message->msg_text = $_POST['content'];
		$message->email = $_POST['email'];
		$message->subject = $_POST['subject'];
		$message->date = date('Y-m-d G:i:s');
		$message->save();
		
		$this->send_email_alert($user_id);
		
		echo '{"status":"success"}';
	}
	
	
	/**
	 * Used to send replies from previous messages
	 * expects the subject, ID of the original message and text of the message
	 * to be in the $_POST.
	 */
	public function send_reply()
	{
		$this->auto_render = false;
	
		header('Content-type: application/json; charset=utf-8');
		//make sure all the necessary fields are present
		if(!(isset($_POST['id']) AND
				isset($_POST['subject']) AND
				isset($_POST['message'])))
		{
			echo '{"status":"error"}';
			return;
		}
		
		//get the ID of the current user
		$user_id = null;
		//see if we can figure out the who the sender is
		if(isset($_SESSION['auth_user']))
		{
			$user_id = $_SESSION['auth_user']->id;
		}
		else
		{
			echo '{"status":"error"}';
			return;
		}
		
		//get the message in question
		$message_id = intval($_POST['id']);
		$message = ORM::factory('usermsg', $message_id);
		if(!$message->loaded)
		{
			echo '{"status":"error"}';
			return;
		}

		//make sure the user can reply to this
		if($user_id != $message->to_user_id)
		{
			echo '{"status":"error"}';
			return;
		}
	
		//create the reply message
		$reply = ORM::factory('usermsg');
		$reply->to_user_id = $message->from_user_id;
		$reply->from_user_id = $user_id;
		$reply->msg_text = $_POST['message'];
		$reply->subject = $_POST['subject'];
		$reply->date = date('Y-m-d G:i:s');
		$reply->save();
		
		$this->send_email_alert($message->from_user_id);
	
		//send an email to the user who this message was sent to.
	
		echo '{"status":"success"}';
	}
	
	/**
	 * This function is used to create an inbox UI for people to check their messages.
	 */
	public function inbox()
	{
		
		//make sure the user is logged in
		if(isset($_SESSION['auth_user']))
		{
			$user_id = $_SESSION['auth_user']->id;
		}
		else
		{
			// This user isn't allowed to view an inbox
			url::redirect('/');
		}
		
		
		$this->themes->js = new View('usermsg/inbox_js');
		$this->template->content = new View('usermsg/inbox');
		//get messages for this user
		$messages = ORM::factory('usermsg')
			->where('to_user_id',$user_id)
			->orderby('date', 'DESC')
			->find_all();
		$this->template->content->messages = $messages; 
		
		$this->template->header->header_block = $this->themes->header_block();
	}
	
	
	public function getmsg()
	{
		$this->auto_render = false;
		
		//make sure the user is logged in
		if(isset($_SESSION['auth_user']))
		{
			$user_id = $_SESSION['auth_user']->id;
		}
		else
		{
			// This user isn't allowed to view an inbox
			echo Kohana::lang('usermsg.permission_denied');
			return;
		}
		
		//get the message id
		$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if($message_id == 0)
		{
			echo Kohana::lang('usermsg.message not found');
			return;
		}
		
		$message = ORM::factory('usermsg',$message_id);
		
		$view = new View('usermsg/view_message');
		$view->message = $message;
		echo $view;
	}
	
	
	private function send_email_alert($user_id)
	{
		//get the site name and email address
		$settings = kohana::config('settings');
		$site_name = $settings['site_name'];
		$alerts_email = ($settings['alerts_email']) ? $settings['alerts_email'] : $settings['site_email'];
		
		$user = ORM::factory('user',$user_id);

		
		$to = $user->email;
		$from = array();
		$from[] = $alerts_email;
		$from[] = $site_name;
		$subject = "[$site_name] ". Kohana::lang('usermsg.New message recieved');
		$message = Kohana::lang('usermsg.You have a new message waiting for you on').' ' .url::base().'usermsg/inbox';
		
		email::send($to, $from, $subject, $message, FALSE);
			
	}
}
