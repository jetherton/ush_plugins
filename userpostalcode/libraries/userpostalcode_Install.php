<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code installation class
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */

class Userpostalcode_Install {
	
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
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."user_postalcode`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				user_id int(11) unsigned NOT NULL,
				postalcode CHAR(25) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX  (user_id),
				INDEX  (postalcode)
			) 
			ENGINE = InnoDB;			
		");
		
	}

	/**
	 * Drops the User Postal Code Tables
	 */
	public function uninstall()
	{
		/*Etherton: It scares me too much to give any old admin the ability to permanently blow away a table in the
		 * database, so I've commented this out.
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."user_postalcode;
			");
		*/
	}
}