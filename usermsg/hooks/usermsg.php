<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Message hooks
 * Where the plugin hooks into the rest of the system
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   usermsg - http://ethertontech.com
 */

class usermsg {

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
		

		if(Router::$controller == "reports")
		{
			//create a "send report author a message" block to a report's page.
			Event::add('ushahidi_filter.comment_form_block', array($this, '_add_msg_report'));
		}
		
		Event::add('ushahidi_action.header_nav_bar', array($this, '_add_inbox_link'));

		if(Router::$controller == 'usermsg')
		{
			if(Router::$method == "inbox")
			{
				plugin::add_stylesheet("usermsg/css/usermsg");
				plugin::add_javascript("usermsg/js/jquery.tools.min.js");
			}
		}
	}
	
	
	/**
	 * Creates an inbox link
	 */
	public function _add_inbox_link()
	{
		//check if the user is logged in
		if(isset($_SESSION['auth_user']))
		{
			?>
			<hgroup >
				<ul style="padding: 0 100px;margin: 0;">
					<li style="list-style-type: none;display: inline;">
						<?php 	
						echo '<a style="height:30px;line-height:30px;" href="'.url::base().'usermsg/inbox">'.Kohana::lang('usermsg.Inbox').'</a>';
						?>
					</li>
				</ul>
			</hgroup>
			
			<?php 
		}
	}

	
	
	//creates the UI for sending messages to users who added an incident.
	public function _add_msg_report()
	{
		$output = Event::$data;
		//make sure the report ID exists and that it's a number
		if(!isset(Router::$arguments[0]) OR intval(Router::$arguments[0]) == 0)
		{
			return;
		}
		//get the report id		
		$incident_id =  Router::$arguments[0];
		
		$view = new View('usermsg/report_send');
		$view->incident_id = $incident_id;
		
		echo $view;
		
		return $output;
	}

} //end usermsg hook class

new usermsg;
