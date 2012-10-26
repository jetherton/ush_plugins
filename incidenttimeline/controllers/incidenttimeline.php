<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline controller. Creates the XML the timeline needs
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Timeline - http://ethertontech.com
 */

class incidenttimeline_Controller extends Main_Controller {

	/**
	 * Whether an admin console user is logged in
	 * @var bool
	 */
	var $logged_in;

	public function __construct()
	{
		parent::__construct();
		$this->themes->validator_enabled = TRUE;

		// Is the user Logged In?
		$this->logged_in = Auth::instance()->logged_in();
	}
	
	/**
	 * first get a list of events that are going to happen in the next 30 days.
	 */
	public function homewidget()
	{
		
		
		//turn off the automatic themeing stuff. Where we're going we don't need themes.
		$this->template = '';
		$this->auto_render = FALSE;
		
		//grab all the timeline events in the next 30 days.
		$time_now = date( "Y-m-d H:i:s", strtotime('-30 minute'));
		//$time_30days = date( "Y-m-d H:i:s", strtotime('+30 day'));
		
		$milestones = ORM::factory('incidenttimeline')
			->select('*, incidenttimeline.id as milestone_id, incident.incident_title as incident_title')
			->join('incident', 'incidenttimeline.incident_id', 'incident.id')
			//->where(array('date >='=>$time_now, 'date <= '=>$time_30days))
			->where(array('date >='=>$time_now))
			->orderby('date', 'ASC')
			->find_all(30);
		
		
		$view = new View('incidenttimeline/homepage_widget');
		$view->milestones = $milestones;
		echo $view;
		
	}

	
	/**
	 * for subscribing in non-human readable JSON
	 */
	public function xml()
	{
		
		//turn off the automatic themeing stuff. Where we're going we don't need themes.
		$this->template = '';
		$this->auto_render = FALSE;
		
		$is_admin = false;
		
		//are they logged?
		if($this->logged_in)
		{
			$user = ORM::factory('user',$_SESSION['auth_user']->id);
			//are they admins
			if(admin::permissions($this->user, "reports_edit"))
			{
				$is_admin = true;
			}
		}
		
		
				
		//did they give us a incident_id
		if(isset($_GET['incident_id']) AND intval($_GET['incident_id']) != 0)
		{
			//get the incident id
			$incident_id = intval($_GET['incident_id']);

			//is this a valid incident_id?
			$incident = ORM::factory('incident', $incident_id);
			
			if(!$incident->loaded)
			{
				echo "<status>error</status>";
				return;
			}
			
		}
		else
		{
			echo "<status>error</status>";
			return;
		}
		
		
		//now lets get all the timeline events associated with this incident
		$timelines = ORM::factory('incidenttimeline')
			->where('incident_id', $incident_id)
			->find_all();
		
			
			

		
		
		//now lets start cranking out some XML
		echo '<data>';
		foreach($timelines as $timeline)
		{
			echo '<event start="'. date("M d Y H:i:s e", strtotime($timeline->date)).'" '.
					'end="'. date("M d Y H:i:s e", strtotime($timeline->date)).'" '.
					'title="'.htmlentities($timeline->description).'">';
			echo htmlentities($timeline->description);
			echo '</event>';
					
		}
		echo '</data>';
	}
	
}
