<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Incident Follow installation class
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	  Incident Follow On - http://ethertontech.com
 */

class Incidentfollowon_Install {
	
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
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."incidentfollowon`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				incident_id bigint(20) unsigned NOT NULL,
				twitter char(255) DEFAULT NULL,
				facebook char(255) DEFAULT NULL,
				PRIMARY KEY (`id`),				
				INDEX  (incident_id)
			) 
			ENGINE = InnoDB;			
		");
		
	}

	/**
	 * Drops the Incidentdeltanotify Tables
	 */
	public function uninstall()
	{
		/*Etherton: It scares me too much to give any old admin the ability to permanently blow away a table in the
		 * database, so I've commented this out.
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."incidentfollowon;
			");
		*/
	}
}