<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline - Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Inciden Timeline - http://ethertontech.com
 */

class incidenttimeline {

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
			//add the needed js
			plugin::add_javascript('incidenttimeline/js/timeline-api');		
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.report_extra', array($this, '_add_user_view'));	 //add the UI for setting up alerts
		}
		
		//now add some hooks if someone is changing an incident
		if(Router::$controller == "reports" AND Router::$method == "edit")
		{
			//add the needed js
			plugin::add_javascript('incidenttimeline/js/timeline-api');
			//hook into the changes to incidents
			Event::add('ushahidi_action.report_form_admin', array($this, '_render_form'));	 //add the UI for setting up alerts
			//Event::add('ushahidi_action.report_submit_admin', array($this, '_grab_post'));
			//Event::add('ushahidi_action.report_edit', array($this, '_save_data'));
		}
		
		

	}
	
	
	
	
	/**
	 * This function handles the saving of an incident and notifies the necessary users of the change
	 */
	public function _render_form()
	{
		$incident_id = intval(Event::$data);
		
		if($incident_id == 0)
		{
			return;
		}
		
		//setup the view
		$view = new View('incidenttimeline/incident_form');

		//get the currenttimeline items
		$timelines = ORM::factory('incidenttimeline')
			->where('incident_id', $incident_id)
			->find_all(); 
		
		$view->timelines = $timelines;
		$view->incident_id = $incident_id;
		$view->count = count($timelines);
		
		$this->_setup_timeline($view, $incident_id);
		
		echo $view;
	}
	
	/**
	 * Adds the incident UI
	 */
	public function _add_user_view()
	{
		//get the incident id
		$incident_id = Event::$data;
		//get the count of currenttimeline items
		$count = ORM::factory('incidenttimeline')
		->where('incident_id', $incident_id)
		->count_all();
		//bounce if it's zero
		if($count == 0)
		{
			return;
		}
		//start up the view
		$view = new View('incidenttimeline/timeline');
		$this->_setup_timeline($view, $incident_id);
		echo $view;
		
	}
	
	
	private function _setup_timeline ($view, $incident_id)
	{
		//get the incident
		$incident = ORM::factory('incident',$incident_id);
		//are they allowed to edit things
		$can_edit = false;
		$edit_url = url::base();
		//are they logged?
		if(isset($_SESSION['auth_user']))
		{
			$user = ORM::factory('user',$_SESSION['auth_user']->id);
			//are they admins
			if(admin::permissions($user, "reports_edit") OR $incident->user_id == $user->id)
			{
				$can_edit = true;
				if (admin::permissions($user, "reports_edit"))
				{
					$edit_url .= 'admin/incidenttimeline/edit/';
				}
				else
				{
					$edit_url .= 'members/incidenttimeline/edit/';
				}
			}
				
		}
		$view->can_edit = $can_edit;
		//now lets get all the timeline events associated with this incident
		$timelines = ORM::factory('incidenttimeline')
		->where('incident_id', $incident_id)
		->find_all();
		//now lets make this into some json
		$events = array();
		foreach($timelines as $timeline)
		{
			$event = array();
			$event['start']  = date("D M d Y H:i:s", strtotime($timeline->date)). " GMT-0000";
			//$event['end'] = date("M d Y H:i:s", strtotime($timeline->date));
			$event['title'] = $timeline->title;
			if($timeline->photo != null)
			{
				$event['image'] = url::convert_uploaded_to_abs($timeline->photo);
			}
			$event['caption'] = $timeline->title;
			//setup the fields
			$event['description'] = "<strong>".Kohana::lang('incidenttimeline.description').':</strong><br/>'. $timeline->description.'<br/><br/>';
			
			if($timeline->people != null)
			{
				$event['description'] .= "<strong>".Kohana::lang('incidenttimeline.people_needed').':</strong><br/>'.$timeline->people.'<br/><br/>';
			}
			if($timeline->resources != null)
			{
				$event['description'] .= "<strong>".Kohana::lang('incidenttimeline.resources_needed').':</strong><br/>'.$timeline->resources.'<br/><br/>';
			}
			if($timeline->link != null)
			{
				$event['description'] .= '<a href="'.$timeline->link.'">'.Kohana::lang('incidenttimeline.more_info').'</a><br/><br/>';				
			}
			
			if(intval($timeline->is_completed) == 1)
			{
				$event['description'] .= "<strong>".Kohana::lang('incidenttimeline.is_this_complete').':</strong><br/>'.Kohana::lang('incidenttimeline.complete').'<br/><br/>';
				$event['color'] = 'blue';
				$event['icon'] = url::base().'plugins/incidenttimeline/js/images/dark-blue-circle.png';
			}
			else
			{
				$event['description'] .= "<strong>".Kohana::lang('incidenttimeline.is_this_complete').':</strong><br/>'.Kohana::lang('incidenttimeline.not_complete').'<br/><br/>';
				$event['color'] = 'green';
				$event['icon'] = url::base().'plugins/incidenttimeline/js/images/dark-green-circle.png';
			
			}
			
			
			Event::run('incidenttimeline_action.display_timeline_object', $timeline);
			Event::run('incidenttimeline_action.display_timeline_event', $event);
			
			if($can_edit)
			{
				$event['description'] .= '<br/><a href="'.$edit_url.$timeline->id.'">'.Kohana::lang('incidenttimeline.edit').'</a>';
			}
			
			$events[] = $event;
		}
		$data = array('events'=>$events );
		
		$view->data = json_encode($data);
		
		$view->incident_id = $incident_id;
	}

}//end class

new incidenttimeline;

