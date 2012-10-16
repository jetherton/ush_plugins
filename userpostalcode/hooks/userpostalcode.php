<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */

class userpostalcode {

	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));

		//use this to store the post data
		$this->post = null;
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		if(Router::$controller == "users")
		{
			
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.users_form_admin', array($this, '_add_user_view'));	 //add the UI for setting up alerts
				
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.users_add_admin', array($this, '_collect_post'));
			
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.user_edit', array($this, '_user_edited'));
			

		}
		
		if(Router::$controller == "login")
		{
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.login_new_user_form', array($this, '_add_user_view'));	 //add the UI for setting up alerts
		
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.users_add_login_form', array($this, '_collect_post'));
				
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.user_edit', array($this, '_user_edited'));
		
		}
		 	
		 else if(Router::$controller == "profile")
		 {
							
			//hook into the UI for user admin/edit
			Event::add('ui_admin.profile_shown', array($this, '_add_user_view'));	 //add the UI for setting up alerts

				
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.profile_add_admin', array($this, '_collect_post'));
			Event::add('ushahidi_action.profile_post_member', array($this, '_collect_post'));
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.profile_edit', array($this, '_user_edited'));
			Event::add('ushahidi_action.profile_edit_member', array($this, '_user_edited'));
			
		}


	}

	/**
	 * Adds the UI for to the user edit page
	 */
	public function _add_user_view()
	{

		$form = array('postalcode'=> '');

		//is this for a new user, or a previous user?
		if(Router::$controller == "profile") //the profile doesn't do us the courtesy of telling us the user's id, so we have to figure it out ourselves
		{
			if(isset($_SESSION['auth_user']))
			{
				$id = $_SESSION['auth_user']->id;
			}
			else
			{
				return;
			}
		}
		else
		{
			$id = Event::$data;
		}
		
		if($id)
		{ //figure out who this user is and what they're settings are
			$user_postalcode = ORM::factory('user_postalcode')
				->where('user_id', $id)
				->find();			
			//if the user has no admin alert settings
			if($user_postalcode->loaded)
			{
				$form = array('postalcode'=> $user_postalcode->postalcode);
			}
		}

		if(Router::$controller == "login")
		{
			$view = new View('userpostalcode/userpostalcode');
		}
		else
		{
			$view = new View('userpostalcode/admin/userpostalcode');
		}		
		$view->form = $form;
		echo $view;
	}

	/**
	 * This collects the contents of the HTTP post that has the details of the users
	 * alerts preferences that we want
	 */
	public function _collect_post()
	{
		$this->post = event::$data;
		
		//if this is an object add a validation rule
		if(is_object($this->post))
		{
			$this->post->add_rules('postalcode','required','length[1,24]');
		}
	}

	/**
	 * This grabs the details of the user that was just edited, primarily the user ID, that's what we
	 * really want. This also does all the work of saving things to the DB
	 */
	public function _user_edited()
	{
		$user = event::$data;
		$post = $this->post;

		//pull out the data we care about
		if(is_array($post))
		{
			$postalcode = $post['postalcode'];
		}
		else
		{
			$postalcode = $post->postalcode;
		}
		
		//grab the record in the DB if it exists
		$user_postalcode = ORM::factory('user_postalcode')
		->where('user_id', $user->id)
		->find();

		
		$user_postalcode->postalcode = $postalcode;
		$user_postalcode->user_id = $user->id;
		$user_postalcode->save();

	}


}

new userpostalcode;
