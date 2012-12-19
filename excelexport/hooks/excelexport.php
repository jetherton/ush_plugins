<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Excel Export - The hook that sucks this into the rest of the system
 *
 * @author	   John Etherton <john@ethertontech.com>
 * @package	   Excel Export
 */

class excelexport {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		if(Router::$controller == "reports" AND Router::$method == "index")
		{
			Event::add('ushahidi_action.header_scripts', array($this, '_add_js'));
			plugin::add_stylesheet("excelexport/css/excelexport");
		}
	}
	
	public function _add_js()
	{
		$view = new View('excelexport/report_filter_js');
		$view->render(true);
	}
	
		

	
}//end class

new excelexport;
