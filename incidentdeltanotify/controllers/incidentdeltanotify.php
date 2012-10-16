<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident delta notify controller. Lets users subscribe and unsubscribe from incidents.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */

class incidentdeltanotify_Controller extends Main_Controller {

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
	 * unsubscribe via human readable HTML
	 */
	public function unsubscribe()
	{
		$this->template->header->this_page = 'unsubscribe from idea';
		

		$this->template->header->page_title .= Kohana::lang('incidentdeltanotify.unsubscribe');

		//so first we want to see if the user is logged in?
		if($this->logged_in)
		{
			//we're logged in, so first we need to know if they specified a incident id that they want to be unsubscribed from
			//if they didn't bounce them home.
			$incident_id = null;
			if(isset($_GET['incident_id']))
			{
				$incident_id = intval($_GET['incident_id']);
			}
			else
			{
				//bounce home because this shouldn't happen unless someone is being sneaky.
				url::redirect('main');
			}
			//get the user's id
			$user_id = $_SESSION['auth_user']->id;
			
			//check and see if such a subscription exists
			$subscription = ORM::factory('incidentdeltanotify')
				->where('user_id', $user_id)
				->where('incident_id', $incident_id)
				->find();
			
			if($subscription->loaded)
			{
				$subscription->delete();
				$this->template->content = new View('incidentdeltanotify/unsubscribe_successful');
				$this->template->content->user = ORM::factory('user', $user_id);
				$this->template->content->incident = ORM::factory('incident', $incident_id);
			}
			else
			{
				$this->template->content = new View('incidentdeltanotify/invalid_unsubscribe');
			}
			
			
			
		}
		else //they aren't logged in so tell them to login first
		{
			$this->template->content = new View('incidentdeltanotify/unsubscribe_log_in_first');
		}

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}
	
	/**
	 * for subscribing in non-human readable JSON
	 */
	public function subscribe()
	{
		//turn off the automatic themeing stuff. Where we're going we don't need themes.
		$this->template = '';
		$this->auto_render = FALSE;
		
		//are they logged?
		if(!$this->logged_in)
		{
			echo json_encode(array('status'=>'error'));
			return;
		}
		//get the user id
		$user_id = $_SESSION['auth_user']->id;
		
		//did they give us a incident_id
		if(isset($_GET['incident_id']) AND intval($_GET['incident_id']) != 0)
		{
			//get the incident id
			$incident_id = intval($_GET['incident_id']);

			//is this a valid incident_id?
			$incident = ORM::factory('incident', $incident_id);
			
			if(!$incident->loaded)
			{
				echo json_encode(array('status'=>'error'));
				return;
			}
			
			//does this subscription already exists
			$subscription = ORM::factory('incidentdeltanotify')
				->where('user_id', $user_id)
				->where('incident_id', $incident_id)
				->find();
			if($subscription->loaded)
			{
				echo json_encode(array('status'=>'already exists'));
				return;
			}
			
			//ok so let's subscribe this mother
			$subscription = ORM::factory('incidentdeltanotify');
			$subscription->user_id = $user_id;
			$subscription->incident_id = $incident_id;
			$subscription->save();
			
			echo json_encode(array('status'=>'success', 'user_id'=>$user_id, 'incident_id'=>$incident_id));
			return;
		}
		else
		{
			echo json_encode(array('status'=>'error'));
			return;
		}
		
	}
	
	
	/**
	 * for unsubscribing in non-human readable JSON
	 */
	public function unsubscribe_json()
	{
		//turn off the automatic themeing stuff. Where we're going we don't need themes.
		$this->template = '';
		$this->auto_render = FALSE;
	
		//are they logged?
		if(!$this->logged_in)
		{
			echo json_encode(array('status'=>'error'));
			return;
		}
		//get the user id
		$user_id = $_SESSION['auth_user']->id;
	
		//did they give us a incident_id
		if(isset($_GET['incident_id']) AND intval($_GET['incident_id']) != 0)
		{
			//get the incident id
			$incident_id = intval($_GET['incident_id']);
	
				
			//does this subscription already exists
			$subscription = ORM::factory('incidentdeltanotify')
			->where('user_id', $user_id)
			->where('incident_id', $incident_id)
			->find();
			if(!$subscription->loaded)
			{
				echo json_encode(array('status'=>'doesn\'t exists'));
				return;
			}
			$subscription->delete();
							
			echo json_encode(array('status'=>'success', 'user_id'=>$user_id, 'incident_id'=>$incident_id));
			return;
		}
		else
		{
			echo json_encode(array('status'=>'error'));
			return;
		}
	
	}

}
