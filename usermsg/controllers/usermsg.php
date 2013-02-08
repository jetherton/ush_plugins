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
	
		$view = new View('usermsg/report_sent');
		$this->template->content = $view;
		//make sure all the necessary fields are present
		if(!( 
				isset($_POST['msg_incident_id']) AND
				isset($_POST['msg_email']) AND
				isset($_POST['msg_subject']) AND
				isset($_POST['msg_content'])
			))
		{
			url::redirect(url::site().'main');
		}
		$view->incident_id = $_POST['msg_incident_id'];
		//grab the incident and find out what user submitted it
		$user_id = ORM::factory('incident',$_POST['msg_incident_id'])->user_id;
		$user = ORM::factory('user',$user_id);
		$from_user_id = null; 
		//see if we can figure out the who the sender is
		if(isset($_SESSION['auth_user']))
		{
			$from_user_id = $_SESSION['auth_user']->id;
		}
		//check the length of the email
		if(strlen($_POST['msg_email']) > 254)
		{
			$view->msg = Kohana::lang('usermsg.email_too_long');
			$view->success = false;
			return;
		}
		//check the length of the subject
		if(strlen($_POST['msg_subject']) > 254)
		{
			$view->msg = Kohana::lang('usermsg.subject_too_long');
			$view->success = false;
			return;
		}
					
		//create the message
		$message = ORM::factory('usermsg');
		$message->to_user_id = $user_id;
		$message->from_user_id = $from_user_id;
		$message->msg_text = $_POST['msg_content'];
		$message->email = $_POST['msg_email'];
		$message->subject = $_POST['msg_subject'];
		$message->date = date('Y-m-d G:i:s');
		$message->save();
		
		$this->send_email_alert($user_id);
		
		$view->msg = 'Message sent successfully';
		$view->success = true;
		
		Event::run('usermsg.process_incoming_msg',$message);
	}
	
	
	/**
	 * Used to send replies from previous messages
	 * expects the subject, ID of the original message and text of the message
	 * to be in the $_POST.
	 */
	public function send_reply()
	{

		//see if we can figure out the who the sender is
		if(isset($_SESSION['auth_user']))
		{
			$from_user_id = $_SESSION['auth_user']->id;
		}
		else
		{
			//you can't send when you're not logged in
			url::redirect(url::site().'main');
		}
		
		$view = new View('usermsg/report_sent');
		$this->template->content = $view;
		//make sure all the necessary fields are present
		if(!( 
				isset($_POST['msg_id']) AND
				isset($_POST['subject']) AND
				isset($_POST['message'])
			))
		{
			print_r($_POST);
			url::redirect(url::site().'main');
		}
		$view->incident_id = -1;

		//grab the message we're replying too
		$reply_to_msg = ORM::factory('usermsg', $_POST['msg_id']);
		if(!$reply_to_msg->loaded)
		{
			url::redirect(url::site().'main');
		}
		//make sure it was for you to reply too in the first place
		if($from_user_id != $reply_to_msg->to_user_id)
		{
			url::redirect(url::site().'main');
		}
		
		
		//check the length of the subject
		if(strlen($_POST['subject']) > 254)
		{
			$view->msg = Kohana::lang('usermsg.subject_too_long');
			$view->success = false;
			return;
		}
					
		//create the message
		$message = ORM::factory('usermsg');
		$message->to_user_id = $reply_to_msg->from_user_id;
		$message->from_user_id = $from_user_id;
		$message->msg_text = $_POST['message'];
		$message->email = null;
		$message->subject = $_POST['subject'];
		$message->date = date('Y-m-d G:i:s');
		$message->save();
		
		$this->send_email_alert($user_id);
		
		$view->msg = 'Message sent successfully';
		$view->success = true;
		
		Event::run('usermsg.process_incoming_msg',$message);
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
		
		if($_POST)
		{
			$message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
			if($message_id != 0)
			{
				$message = ORM::factory('usermsg',$message_id);
				Event::run('usermsg.delete', $message);
				$message->delete();
			}
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
