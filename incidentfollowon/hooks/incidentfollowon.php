<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Follow On - Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Follow On - http://ethertontech.com
 */

class incidentfollowon {

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
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.report_meta', array($this, '_add_user_view'));	 //add the UI for setting up alerts
		}
		
		//now add some hooks if someone is changing an incident
		if(Router::$controller == "reports" AND Router::$method == "edit")
		{
			//hook into the changes to incidents
			Event::add('ushahidi_action.report_form_admin', array($this, '_render_form'));	 //add the UI for setting up alerts
			Event::add('ushahidi_action.report_submit_admin', array($this, '_grab_post'));
			Event::add('ushahidi_action.report_edit', array($this, '_save_data'));
		}
		
		//now add some hooks if someone is changing an incident
		if(Router::$controller == "reports" AND Router::$method == "submit")
		{
			//hook into the changes to incidents
			Event::add('ushahidi_action.report_form', array($this, '_render_form'));	 //add the UI for setting up alerts
			Event::add('ushahidi_action.report_submit', array($this, '_grab_post'));
			Event::add('ushahidi_action.report_add', array($this, '_save_data'));
		}
		

	}
	
	/**
	 * Used to save our data
	 */
	public function _save_data()
	{
		$incident = Event::$data;
		
		//try to load the current follow on data if there is any
		$followon = ORM::factory('incidentfollowon')
			->where('incident_id', $incident->id)
			->find();
		
		//save the data
		
		$followon->twitter = $this->post['ifo_twitter'];
		$followon->facebook = $this->post['ifo_facebook'];
		$followon->incident_id = $incident->id;
		$followon->save();
	}
	
	/**
	 * Used to grab the post data
	 */
	public function _grab_post()
	{
		$this->post = Event::$data;
		
	}
	
	/**
	 * This function handles the saving of an incident and notifies the necessary users of the change
	 */
	public function _render_form()
	{
		//setup the view
		if(Router::$method == "submit")
		{
			$view = new View('incidentfollowon/form_submit');
		}
		else
		{
			$view = new View('incidentfollowon/form');
		}
		$view->twitter = null;
		$view->facebook = null;
		
		$incident_id = Event::$data; 
		
		//check if we have a valid form id
		if(intval($incident_id) != 0)
		{
			$followon = ORM::factory('incidentfollowon')
				->where('incident_id', $incident_id)
				->find();
			if($followon->loaded)
			{
				$view->twitter = $followon->twitter;
				$view->facebook = $followon->facebook;
			}
		}
		
		echo $view;
	}
	
	/**
	 * Adds the incident UI
	 */
	public function _add_user_view()
	{
		
		$view = new View('incidentfollowon/show_buttons');
		$view->twitter = null;
		$view->facebook = null;
		
		$incident_id = Event::$data;
		
		//get any follon data if any
		$followon = ORM::factory('incidentfollowon')
			->where('incident_id', $incident_id)
			->find();
		if($followon->loaded)
		{
			$view->twitter = $followon->twitter;
			$view->facebook = $followon->facebook;
			if($followon->twitter OR $followon->facebook)
			{
				echo $view;
			}
		}
		else 
		{
			return;
		}
		
	}

}//end class

new incidentfollowon;

