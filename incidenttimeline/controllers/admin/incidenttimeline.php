<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline - Controller, where the user goes to add timeline activities
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Inciden Timeline - http://ethertontech.com
 */

class Incidenttimeline_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'reports';
	}


	/**
	 * Edit a report
	 * @param bool|int $id The id no. of the report
	 * @param bool|string $saved
	 */
	public function edit($id = FALSE, $saved = FALSE)
	{

		//first we need to make sure we have the necessary information
		$incident_id = isset($_GET['incident_id']) ? intval($_GET['incident_id']) : 0;
		$incident = null;
		if($incident_id != 0)
		{
			$incident = ORM::factory('incident', $incident_id);
		}
		
		//now do we have all the info we need
		if(!$id AND ($incident== null OR !$incident->loaded))
		{
			//No we don't have anything
			//so bounce
			url::redirect(url::site().'admin/dashboard');
		}
		
		//if we do have a timeline ID then grab it
		$timeline = ORM::factory('incidenttimeline');
		if($id)
		{
			$timeline = ORM::factory('incidenttimeline', $id);
			if(!$timeline->loaded)
			{
				//if this isn't a valid timeline obj, then bounce
				url::redirect(url::site().'admin/dashboard');
			}
			//load in info on the incident
			$incident = ORM::factory('incident', $timeline->incident_id);
		}
		
		//now lets see if we even have permission to be here
		
		//get the user's ID
		$user_id = $_SESSION['auth_user']->id; //I'm not checking if this is set, because you shouldn't be able to get here if you're not logged in.

		// If user doesn't have access, redirect to dashboard
		if (! admin::permissions($this->user, "reports_edit") AND $user_id != $incident->user_id)
		{
			url::redirect(url::site().'admin/dashboard');
		}
		
		//so they seem to have access. Sweet. Let's move on.

		$this->template->content = new View('incidenttimeline/admin/timeline_form');
		$this->template->content->title = Kohana::lang('incidenttimeline.timeline_a_e');

		// Setup and initialize form field names
		$form = array(
			'id' => '',
			'incident_id' => '',
			'date' => '',
			'hour' => '',
			'minute' => '',
			'ampm' => '',
			'description' => '',
			'title'=>'',
			'people' => '',
			'resources' => '',
			'link' => '',
			'is_completed' => '',
			'photo' => '',			
		);

		// Copy the form as errors, so the errors will be stored with keys
		// corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');

		$form['date'] = date("m/d/Y",time());
		$form['hour'] = date('h');
		$form['minute'] = date('i');
		$form['ampm'] = date('a');
		

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = array_merge($_POST,$_FILES);
			
			Event::run('incidenttimeline_action.timeline_edit_post',$post);
			
			//create the validation object
			$post = Validation::factory($post);
			//add some validation rules
			$post->add_rules('description','required');
			$post->add_rules('title','required');
			$post->add_rules('is_complete','between[0,1]');
			$post->add_rules('link', 'url');
			//check if we're supposed to delete this
			if($post['save'] == 'delete')
			{
				//if there's a photo delete it
				// Delete Files from Directory
				if ($timeline->photo != null)
				{
					unlink(Kohana::config('upload.directory', TRUE) . $timeline->photo);
				}
				Event::run('incidenttimeline_action.timeline_delete',$timeline);
				$timeline->delete();

				
				url::redirect('admin/reports/edit/'.$incident->id);
			}
			elseif($post->validate())
			{
				//parse the time and date
				$time = $post->hour . ":" . $post->minute . ":00 " . $post->ampm;
				$time = date( "Y-m-d H:i:s", strtotime($post->date . " " . $time) );
				
				//save things
				$timeline->incident_id = $incident->id;
				$timeline->date = $time;
				$timeline->description = $post->description;
				$timeline->title = $post->title;
				$timeline->people = $post->people;
				$timeline->resources = $post->resources;
				$timeline->link = $post->link;
				$timeline->is_completed = $post->is_completed;
				$timeline->save();
				
				
				//handle photos if any
				if(isset($_FILES['photo']))
				{
					$filenames = upload::save('photo');
				
					foreach ($filenames as $filename)
					{
						$new_filename = 'incidenttimeline_'.$timeline->id;
							
						$file_type = strrev(substr(strrev($filename),0,4));
							

							

						Image::factory($filename)->resize(320,200,Image::HEIGHT)
						->save(Kohana::config('upload.directory', TRUE).$new_filename.$file_type);
							
						// Name the files for the DB
						$media_link = $new_filename.$file_type;


						// Save to DB
						$timeline->photo = $media_link;
						$timeline->save();
					}
				}
				Event::run('incidenttimeline_action.timeline_edit',$timeline);
				
				
				// SAVE AND CLOSE?
				switch ($post->save)
				{
					case 1:
					case 'dontclose':
						// Save but don't close
						url::redirect('admin/incidenttimeline/edit/'. $timeline->id .'/saved');
						break;
					case 'addnew':
						// Save and add new
						url::redirect('admin/reports/edit/0/saved');
						break;
					default:
						// Save and close
						url::redirect('admin/reports/edit/'.$incident->id);
				}
			}
			
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else //if we're not reacting to a POST then see if we've got a valid timeline element on our hands
		{ //and populate some data 
			if ($timeline->loaded)
			{

				// Combine Everything
				$incident_arr = array(
					'incident_id' => $timeline->incident_id,
					'date' => date('m/d/Y', strtotime($timeline->date)),
					'hour' => date('h', strtotime($timeline->date)),
					'minute' => date('i', strtotime($timeline->date)),
					'ampm' => date('a', strtotime($timeline->date)),
					'description' => $timeline->description,
					'title'=>$timeline->title,
					'people' => $timeline->people,
					'resources' => $timeline->resources,
					'link' => $timeline->link,
					'is_completed' => $timeline->is_completed,
					'photo' => $timeline->photo,
				);

				// Merge To Form Array For Display
				$form = arr::overwrite($form, $incident_arr);			
			}
		}

		$this->template->content->id = $id;
		$this->template->content->incident_id = $incident->id;
		$this->template->content->incident = $incident;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->url = 'admin';
		
		$this->template->content->date_picker_js = $this->_date_picker_js();
		
		// Time formatting
		$this->template->content->hour_array = $this->_hour_array();
		$this->template->content->minute_array = $this->_minute_array();
		$this->template->content->ampm_array = $this->_ampm_array();
		
		$this->template->js = new View('incidenttimeline/admin/timeline_form_js');
		
	}
	
	
	
	
	
	
	/**
	 * Delete Photo
	 * @param int $id The unique id of the photo to be deleted
	 */
	function deletePhoto ( $id )
	{
		$this->auto_render = FALSE;
		$this->template = "";
	
		if ( $id )
		{
			$timeline = ORM::factory('incidenttimeline', $id);
			$photo = $timeline->photo;
	
			// Delete Files from Directory
			if ( ! empty($photo))
			{
				unlink(Kohana::config('upload.directory', TRUE) . $photo);
			}
				
			$timeline->photo = null;
			$timeline->save();
			Event::run('incidenttimeline_action.timeline_edit',$timeline);
		}
	}
	
	
	
	// Time functions
	private function _hour_array()
	{
		for ($i=1; $i <= 12 ; $i++)
		{
			$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);		// Add Leading Zero
		}
		return $hour_array;
	}
	
	private function _minute_array()
	{
		for ($j=0; $j <= 59 ; $j++)
		{
			$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	// Add Leading Zero
		}
		return $minute_array;
	}
	
	private function _ampm_array()
	{
		return $ampm_array = array('pm'=>Kohana::lang('ui_admin.pm'),'am'=>Kohana::lang('ui_admin.am'));
	}
	
	
	
	
	private function _date_picker_js()
	{
	return "<script type=\"text/javascript\">
	$(document).ready(function() {
	$(\"#incident_date\").datepicker({
			showOn: \"both\",
			buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
			buttonImageOnly: true
	});
	});
	</script>";
	}


}
