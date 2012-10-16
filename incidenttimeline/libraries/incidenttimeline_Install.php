<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Timeline installation class
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	  Incident Timeline - http://ethertontech.com
 */

class Incidenttimeline_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required columns for the User Postal Code Plugin
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		// for remembering what postal code goes with what user
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."incidenttimeline`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				incident_id bigint(20) unsigned NOT NULL,
				date datetime NOT NULL,
				description longtext NOT NULL,
				people longtext DEFAULT NULL,
				resources longtext DEFAULT NULL,
				link longtext DEFAULT NULL,
				is_completed tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - Not completed, 1 - completed',
				photo char(255) DEFAULT NULL,
				PRIMARY KEY (`id`),				
				INDEX  (incident_id),
				INDEX  (date)
			) 
			ENGINE = InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores timeline data for the Incident Timline plugin';			
		");
		
		//check if the needed columns exists
		$results = $this->db->query("SHOW COLUMNS FROM ".Kohana::config('database.default.table_prefix')."incidenttimeline");
		$title_found = false;
		foreach($results as $r)
		{
			if($r->Field == "title")
			{
				$title_found = true;
				break;
			}
		}
		if(!$title_found)
		{		
			$this->db->query("ALTER TABLE  `".Kohana::config('database.default.table_prefix')."incidenttimeline` ADD  `title` LONGTEXT NULL DEFAULT NULL AFTER  `description`");
		}
		
	}

	/**
	 * Drops the Incidentdeltanotify Tables
	 */
	public function uninstall()
	{
		/*Etherton: It scares me too much to give any old admin the ability to permanently blow away a table in the
		 * database, so I've commented this out.
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."incidenttimeline;
			");
		*/
	}
}