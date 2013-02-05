<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Message installation class
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */

class Usermsg_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required table for the User Message plugin
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		// for remembering what postal code goes with what user
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."usermsg`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				to_user_id int(11) unsigned NOT NULL,
				from_user_id int(11) unsigned NULL,
				msg_text text NOT NULL,
				date TIMESTAMP NOT NULL,
				email char(255) NULL,
				subject char(255) NULL,
				PRIMARY KEY (`id`),
				INDEX  (to_user_id),
				INDEX  (from_user_id),
				INDEX  (date)
			) 
			ENGINE = InnoDB;");			
		
	}

	/**
	 * Drops the User Message tables
	 */
	public function uninstall()
	{
		/*Etherton: It scares me too much to give any old admin the ability to permanently blow away a table in the
		 * database, so I've commented this out.
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."usermsg;
			");
		*/
	}
}