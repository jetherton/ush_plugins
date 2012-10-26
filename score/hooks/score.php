<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Score - Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Score - http://ethertontech.com
 */

class score {

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
		
		//add some style... sheets
		plugin::add_stylesheet("score/css/score");
		
		if(Router::$controller == "reports" AND Router::$method == "view")
		{			
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.report_meta_after_time', array($this, '_add_js_report_view'));	 //add the UI for setting up alerts
		}
		
		if(Router::$controller == "main")
		{
			//hook into the UI for the front page
			Event::add('ushahidi_action.header_scripts', array($this, '_add_js_front_view'));	 //add the UI for setting up alerts
		}

		
		//hook into the nav bar
		Event::add('ushahidi_action.header_nav', array($this, '_add_score_nav_bar'));
		
	}
	
	/**
	 * Insert some Javascript that loads top scorers.
	 */
	public function _add_js_front_view()
	{
		echo '<script type="text/javascript">';
		echo'		
		$(document).ready(function() {
			//grab the data from the server via a HTTP GET
			$.get("'.url::base().'score/gettop", function(data) {
				$("#block-reports").after(data);
			});
		});
		
	';
		echo '</script>';
	}
	
	/**
	 * Insert some Javascript that loads in the vote up down system.
	 */
	public function _add_js_report_view()
	{
		echo '<script type="text/javascript">';
		echo'
		
		$(document).ready(function() {
			//drop everything in the current creditibility section
			$("div.credibility").empty();
			//now throw some place holder stuff there
			$("div.credibility").html("<h5>'.Kohana::lang('score.score_title').'</h5><div id=\"score_data\"><img src=\"'.url::base().'media/img/loading_g2.gif\"/></div>");
			//grab the data from the server via a HTTP GET
			$.get("'.url::base().'score/getdata?id='.Event::$data.'", function(data) {
  				$("#score_data").html(data);
  				
			});
		});
		
		function castVote(direction)
		{
			$("#vote_status").html("<img src=\"'.url::base().'media/img/loading_g2.gif\"/>");
			$.get("'.url::base().'score/castvote?id='.Event::$data.'&direction="+direction, function(data) {
				$("#score_data").empty();
  				$("#score_data").html(data);
  				
			});
		}
		
		';
		echo '</script>';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * This just adds a tab to the bookmark page on the nav bar
	 */
	public function _add_score_nav_bar()
	{
		//we need to figure out if they're logged in or not
		$loggedin_role = ( Auth::instance()->logged_in('member') ) ? "members" : "admin";
		$loggedin_user = Auth::instance()->get_user();
		
		if($loggedin_user != FALSE)
		{
			echo '<li><a href="'.url::base().$loggedin_role.'/score">'.Kohana::lang('score.score_title').'</a></li>';	
		}
		
	}
	


}

new score;
