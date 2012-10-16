<?php defined('SYSPATH') or die('No direct script access.');
/**
 * score controller. Lets users vote things up or down.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */

class score_Controller extends Main_Controller {

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
	 * Used to grab the score for an incident
	 */
	public function getdata()
	{
		
		$this->template = '';
		$this->auto_render = FALSE;
		
		$incident_id = intval($_GET['id']);
		
		$up_votes_all = ORM::factory('score')
			->where('incident_id', $incident_id)
			->where('vote', 1)
			->count_all();
		$down_votes_all = ORM::factory('score')
		->where('incident_id', $incident_id)
		->where('vote', -1)
		->count_all();
		$up_votes_month = ORM::factory('score')
		->where('incident_id', $incident_id)
		->where('vote', 1)
		->where(array('date >='=> date("Y-m-01 00:00:00")))
		->count_all();
		$down_votes_month = ORM::factory('score')
		->where('incident_id', $incident_id)
		->where('vote', -1)
		->where(array('date >='=> date("Y-m-01 00:00:00")))
		->count_all();
		
		$your_vote = 0;
		if($this->logged_in)
		{
			$user = Auth::instance()->get_user();
			//is this your idea?
			$incident = ORM::factory('incident',$incident_id);
			if($incident->user_id == $user->id)
			{
				$your_vote = 3;
			}
			else
			{
				$your_vote_orm = ORM::factory('score')
					->where('user_id', $user->id)
					->where('incident_id', $incident_id)
					->find();
				if($your_vote_orm->loaded)
				{
					$your_vote = $your_vote_orm->vote;
				}
			}
		}
		
		
		$view = new View('score/data');
		$view->up_all = $up_votes_all;
		$view->down_all = $down_votes_all;
		$view->up_month = $up_votes_month;
		$view->down_month = $down_votes_month;
		$view->incident_id = $incident_id;
		$view->logged_in = $this->logged_in;
		$view->your_vote = intval($your_vote);
		echo $view;
		
	}
	
	
	
	
	
	/**
	 * Used to grab the score for an incident
	 */
	public function gettop()
	{
	
		$this->template = '';
		$this->auto_render = FALSE;
		
		$all_time_users = $this->all_time_users();
		$all_time_users_score = $all_time_users['id_to_score'];
		$all_time_users = $all_time_users['users'];
		
		$month_users = $this->month_users();
		$month_users_score = $month_users['id_to_score'];
		$month_users = $month_users['users'];
		
		$all_time_incidents = $this->all_time_incidents();
		$all_time_incidents_scores = $all_time_incidents['id_to_score'];
		$all_time_incidents_count = $all_time_incidents['id_to_count'];
		$all_time_incidents = $all_time_incidents['incidents'];
		
		$month_incidents = $this->month_incidents();
		$month_incidents_scores = $month_incidents['id_to_score'];
		$month_incidents_count = $month_incidents['id_to_count'];
		$month_incidents = $month_incidents['incidents'];
		
	
	
		$view = new View('score/topdata');
		$view->all_time_users_score = $all_time_users_score;
		$view->all_time_users = $all_time_users;
		$view->month_users_score = $month_users_score;
		$view->month_users = $month_users;
		$view->all_time_incidents_scores = $all_time_incidents_scores;
		$view->all_time_incidents_counts = $all_time_incidents_count;
		$view->all_time_incidents = $all_time_incidents;
		$view->month_incidents_scores = $month_incidents_scores;
		$view->month_incidents_counts = $month_incidents_count;
		$view->month_incidents = $month_incidents;
		echo $view;
	
	}
	
	//get the data on all the incidents
	private function all_time_incidents()
	{
		$incidents = array();
		
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		//get the for votes
		$sql = 'SELECT incident.id as id, incident.incident_title as title, COUNT(incident.id) as score FROM `'.$table_prefix.'incident` as incident
		join '.$table_prefix.'score as score ON incident.id = score.incident_id
		WHERE score.vote = 1
		group by incident.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $incidents array
		foreach($query as $incident)
		{
			$incidents[$incident->id] = array('votes_against'=>0, 'votes_for'=>$incident->score, 'title'=>$incident->title);
		}
		
		//get the against votes
		$sql = 'SELECT incident.id as id, incident.incident_title as title, COUNT(incident.id) as score FROM `'.$table_prefix.'incident` as incident
		join '.$table_prefix.'score as score ON incident.id = score.incident_id
		WHERE score.vote = -1
		group by incident.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $incidents array
		foreach($query as $incident)
		{
			if(!isset($incidents[$incident->id]))
			{
				$incidents[$incident->id] = array('votes_for'=>0, 'title'=>$incident->title);
			}
			$incidents[$incident->id]['votes_against'] = $incident->score;
		}
		
		$id_to_score = array();
		$id_to_count = array();
		foreach($incidents as $id=>$incident)
		{
			if($incident['votes_for'] + $incident['votes_against'] > 0)
			{
				$id_to_score[$id] = ($incident['votes_for'] / ($incident['votes_for'] + $incident['votes_against'])) * 100;				 
			}
			
			$id_to_count[$id] = $incident['votes_for'] - $incident['votes_against'];
		}
		
		arsort($id_to_score);
		arsort($id_to_count);
		return array('id_to_count'=>$id_to_count,'id_to_score'=>$id_to_score, 'incidents'=>$incidents);
	}
	
	
	
	private function month_incidents()
	{
		$incidents = array();
	
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
	
		//get the for votes
		$sql = 'SELECT incident.id as id, incident.incident_title as title, COUNT(incident.id) as score FROM `'.$table_prefix.'incident` as incident
		join '.$table_prefix.'score as score ON incident.id = score.incident_id
		WHERE score.vote = 1
		AND score.date >= \''.date("Y-m-01 00:00:00").'\'
		group by incident.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $incidents array
		foreach($query as $incident)
		{
			$incidents[$incident->id] = array('votes_against'=>0, 'votes_for'=>$incident->score, 'title'=>$incident->title);
		}
	
		//get the against votes
		$sql = 'SELECT incident.id as id, incident.incident_title as title, COUNT(incident.id) as score FROM `'.$table_prefix.'incident` as incident
		join '.$table_prefix.'score as score ON incident.id = score.incident_id
		WHERE score.vote = -1
		AND score.date >= \''.date("Y-m-01 00:00:00").'\'
		group by incident.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $incidents array
		foreach($query as $incident)
		{
			if(!isset($incidents[$incident->id]))
			{
				$incidents[$incident->id] = array('votes_for'=>0, 'title'=>$incident->title);
			}
			$incidents[$incident->id]['votes_against'] = $incident->score;
		}
	
		$id_to_score = array();
		$id_to_count = array();
		foreach($incidents as $id=>$incident)
		{
			if($incident['votes_for'] + $incident['votes_against'] > 0)
			{
				$id_to_score[$id] = ($incident['votes_for'] / ($incident['votes_for'] + $incident['votes_against'])) * 100;				 
			}
			
			$id_to_count[$id] = $incident['votes_for'] - $incident['votes_against'];
		}
		
		arsort($id_to_score);
		arsort($id_to_count);
		return array('id_to_count'=>$id_to_count,'id_to_score'=>$id_to_score, 'incidents'=>$incidents);
	}
	
	//calculates the all time top scoreing users
	private function all_time_users()
	{
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		$users = array();
		//get the info for the score based on number of ideas
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id) * 10 as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'incident as incident ON '.$table_prefix.'incident.user_id = users.id
		group by users.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			$users[$user->id] = array('score'=>$user->score, 'name'=>$user->name, 'username'=>$user->username);
		}
		
		//now get the scores for votes on your own ideas
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id) * 2 as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'incident as incident ON incident.user_id = users.id
		join '.$table_prefix.'score as score ON score.incident_id = incident.id
		WHERE score.vote = 1
		group by users.id';
		
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			if(!isset($users[$user->id]))
			{
				$users[$user->id] = array('score'=>0, 'name'=>$user->name, 'username'=>$user->username);
			}
			$users[$user->id]['score'] = $users[$user->id]['score'] + $user->score;
		}
		
		//now get points for your votes
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id)  as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'score as score ON score.user_id = users.id
		group by users.id';
		
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			if(!isset($users[$user->id]))
			{
				$users[$user->id] = array('score'=>0, 'name'=>$user->name, 'username'=>$user->username);
			}
			$users[$user->id]['score'] = $users[$user->id]['score'] + $user->score;
		}
		
		//now create an array mapping ids to scores so we can sort it
		$id_to_score = array();
		
		foreach($users as $key=>$value)
		{
			$id_to_score[$key] = $value['score'];
		}
		
		arsort($id_to_score);
		
		return array('id_to_score'=>$id_to_score, 'users'=>$users);
		
	}
	
	
	//calculates this months top scoreing users
	private function month_users()
	{
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		$users = array();
		//get the info for the score based on number of ideas
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id) * 10 as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'incident as incident ON '.$table_prefix.'incident.user_id = users.id
		WHERE incident.incident_dateadd >= \''.date("Y-m-01 00:00:00").'\'
		group by users.id';
		$db = new Database();
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			$users[$user->id] = array('score'=>$user->score, 'name'=>$user->name, 'username'=>$user->username);
		}
	
		//now get the scores for votes on your own ideas
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id) * 2 as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'incident as incident ON incident.user_id = users.id
		join '.$table_prefix.'score as score ON score.incident_id = incident.id
		WHERE score.vote = 1
		AND score.date >= \''.date("Y-m-01 00:00:00").'\'
		group by users.id';
	
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			if(!isset($users[$user->id]))
			{
				$users[$user->id] = array('score'=>0, 'name'=>$user->name, 'username'=>$user->username);
			}
			$users[$user->id]['score'] = $users[$user->id]['score'] + $user->score;
		}
	
		//now get points for your votes
		$sql = 'SELECT users.id as id, users.name as name, users.username as username,COUNT(users.id)  as score FROM `'.$table_prefix.'users` as users
		join '.$table_prefix.'score as score ON score.user_id = users.id
		WHERE score.date >= \''.date("Y-m-01 00:00:00").'\'
		group by users.id';
	
		$query = $db->query($sql);
		//loop over the data and add it to the $users array
		foreach($query as $user)
		{
			if(!isset($users[$user->id]))
			{
				$users[$user->id] = array('score'=>0, 'name'=>$user->name, 'username'=>$user->username);
			}
			$users[$user->id]['score'] = $users[$user->id]['score'] + $user->score;
		}
	
		//now create an array mapping ids to scores so we can sort it
		$id_to_score = array();
	
		foreach($users as $key=>$value)
		{
			$id_to_score[$key] = $value['score'];
		}
	
		arsort($id_to_score);
	
		return array('id_to_score'=>$id_to_score, 'users'=>$users);
	
	}
	
	
	/**
	 * Used to grab the score for an incident
	 */
	public function castvote()
	{
	
		$this->template = '';
		$this->auto_render = FALSE;
	
		$incident_id = intval($_GET['id']);
		$direction = intval($_GET['direction']);
		
		//make sure you're logged in
		if(!$this->logged_in)
		{
			url::redirect('score/getdata?id='.$incdient_id);
		}
		//get user info
		$user = Auth::instance()->get_user();
		
		//delete any previous vote
		ORM::factory('score')
		->where('user_id', $user->id)
		->where('incident_id', $incident_id)
		->delete_all();
		
		//create vote
		$vote = ORM::factory('score');
		$vote->incident_id = $incident_id;
		$vote->user_id = $user->id;
		$vote->date = date("Y-m-d G:i:s");
		$vote->vote = $direction;
		$vote->save();
	
		//url::redirect('score/getdata?id='.$incdient_id);
		$this->getdata();
		
			
	}
	
}
