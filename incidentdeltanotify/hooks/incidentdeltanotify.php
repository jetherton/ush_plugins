<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Delta Notify - Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Delta Notify - http://ethertontech.com
 */

class incidentdeltanotify {

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
		if(Router::$controller == "reports" AND Router::$method == "view")
		{
			//add some style... sheets
			plugin::add_stylesheet("incidentdeltanotify/css/incidentdeltanotify");
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.report_meta', array($this, '_add_user_view'));	 //add the UI for setting up alerts
		}
		
		//now add some hooks if someone is changing an incident
		if(Router::$controller == "reports" AND Router::$method == "edit")
		{
			//hook into the changes to incidents
			Event::add('ushahidi_action.report_edit', array($this, '_notify_change'));	 //add the UI for setting up alerts
			
		}
		
		if(Router::$controller == "incidenttimeline")
		{
			//hook into changes in timeline elements
			Event::add('incidenttimeline_action.timeline_delete',array($this, '_notify_timeline_change'));
			Event::add('incidenttimeline_action.timeline_edit',array($this, '_notify_timeline_change'));
		}

		//hook into the admin toolbar
		Event::add('ushahidi_action.nav_admin_main_top', array($this, '_add_bookmark_page_admin'));
		
		//hook into the nav bar
		Event::add('ushahidi_action.header_nav', array($this, '_add_bookmark_nav_bar'));
		
		Event::add('ushahidi_action.nav_members_main_top', array($this, '_add_bookmark_page_admin'));
	}
	
	/**
	 * This just adds a tab to the bookmark page on the nav bar
	 */
	public function _add_bookmark_nav_bar()
	{
		//we need to figure out if they're logged in or not
		$loggedin_role = ( Auth::instance()->logged_in('member') ) ? "members" : "admin";
		$loggedin_user = Auth::instance()->get_user();
		
		if($loggedin_user != FALSE)
		{
			echo '<li><a href="'.url::base().$loggedin_role.'/bookmarks">'.Kohana::lang('incidentdeltanotify.bookmarks').'</a></li>';	
		}
		
	}
	

	
	
	/**
	 * This just adds a tab to the bookmark page on the admin page
	 */
	public function _add_bookmark_page_admin()
	{
		Event::$data['bookmarks'] = Kohana::lang('incidentdeltanotify.bookmarks');
	}
	
	
	/**
	 * This function handles the saving/deleting of a timeline item notifies the necessary users of the change
	 */
	public function _notify_timeline_change()
	{
		
		
		$timeline = Event::$data;
		//make sure it's valid
		if(!$timeline->loaded)
		{return;}

		
		$incident = ORM::factory('incident', $timeline->incident_id);
		if(!$incident->loaded)
		{
			return;
		}
		
		$this->_handle_change($incident);
	}

	/** Does the sending of notifications
	 * 
	 * @param ORM incident model $incident
	 */
	private function _handle_change($incident)
	{
		//check and see if anyone has subscribed to this incident
		$notifies = ORM::factory('incidentdeltanotify')
		->where('incident_id', $incident->id)
		->find_all();
		
		//if it's blank bounce.
		if(count($notifies) == 0)
		{
			return;
		}
		//make a string of user IDs
		$user_ids = "";
		foreach($notifies as $notify)
		{
			if(strlen($user_ids) != 0)
			{
				$user_ids .= ',';
			}
			$user_ids .= $notify->user_id;
		}
			
		//now get all the users that want notifications
		$users = ORM::factory('user')
		->in('id', $user_ids)
		->find_all();
		
		
		//grab some settings
		$settings = kohana::config('settings');
		$site_name = $settings['site_name'];
		$alerts_email = ($settings['alerts_email']) ? $settings['alerts_email']: $settings['site_email'];
		
		//now create the email.
		$subject = $site_name .' - '. Kohana::lang('incidentdeltanotify.incident_changed'). '#'.$incident->id . '--'.$incident->incident_title;
		$body = Kohana::lang('incidentdeltanotify.wanted_to_let_you_know') .'#'.$incident->id . '--'.$incident->incident_title.
		Kohana::lang('incidentdeltanotify.has been updated').Kohana::lang('incidentdeltanotify.click_to_see').
		'<a href="'.url::base().'reports/view/'.$incident->id.'">'.url::base().'reports/view/'.$incident->id.'</a><br/><br/><br/>'.
		Kohana::lang('incidentdeltanotify.You_receive_because').
		'<a href="'.url::base().'incidentdeltanotify/unsubscribe?incident_id='.$incident->id.'">'.url::base().'incidentdeltanotify/unsubscribe_json?incident_id='.$incident->id.'</a>';
		
		$from = array($alerts_email, $site_name);
		
		foreach($users as $user)
		{
			//lets send some emails
			$to = $user->email;
			$personalized_body = $user->name . $body;
			$ret_val = email::send($to, $from, $subject, $personalized_body, true);
		}
	}
	
	/**
	 * This function handles the saving of an incident and notifies the necessary users of the change
	 */
	public function _notify_change()
	{
		$incident = Event::$data; 
		
		$this->_handle_change($incident);
	}
	
	/**
	 * Adds the incident UI
	 */
	public function _add_user_view()
	{
		//get the ID of this incicent
		$incident_id = Event::$data;
		
		//is the user logged in?
		if(isset($_SESSION['auth_user'])) //yes
		{
			//get the user's ID
			$id = $_SESSION['auth_user']->id;
			//now that we have their ID check and see if they've already subscribed.
			$notify = ORM::factory('incidentdeltanotify')
				->where('user_id', $id)
				->where('incident_id', $incident_id)
				->find();
			$is_following = false;
			//if we found an entry in the DB then we know they are following the idea
			if($notify->loaded)
			{
				$is_following = true;
			}
			
			$view = new View('incidentdeltanotify/report_logged_in');
			$view->user_id = $id;
			$view->incident_id = $incident_id;
			$view->is_following = $is_following;
		}
		else //no
		{
			$view = new View('incidentdeltanotify/report_not_logged_in');
			$view->incident_id = $incident_id;
		}
		echo $view;	
	}

}

new incidentdeltanotify;
