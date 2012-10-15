<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Score installation class
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Score - http://ethertontech.com
 */

class score_Install {
	
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
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."score`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				incident_id bigint(20) unsigned NOT NULL,
				user_id int(11) unsigned NOT NULL,
				date datetime DEFAULT NULL,
  				vote tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : UP, -1 : DOWN',
				PRIMARY KEY (`id`),
				INDEX  (user_id),
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
			DROP TABLE ".Kohana::config('database.default.table_prefix')."incidentdeltanotify;
			");
		*/
	}
}