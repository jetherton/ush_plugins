<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Performs install/uninstall methods for the FrontlineSMS Plugin
 *
 * @package    Ushahidi
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Zdsadminalerts_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required columns for the FrontlineSMS Plugin
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		// for remember who is signed up for what alert
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."zds_admin_alerts`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				user_id int(11) NOT NULL,
				sms tinyint(4) NOT NULL,
				email tinyint(4) NOT NULL,
				web tinyint(4) NOT NULL,
				admin tinyint(4) NOT NULL,
				api tinyint(4) NOT NULL,
				approved tinyint(4) NOT NULL,
				verified tinyint(4) NOT NULL,
				PRIMARY KEY (`id`)
			);
		");
		
		//for mapping categories to alerts
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."zds_admin_alerts_cat`
			(
				id int(15) unsigned NOT NULL AUTO_INCREMENT,
				alert_id int(15) NOT NULL,
				category_id int(11) NOT NULL,				
				PRIMARY KEY (`id`)
			);
		");
		// ****************************************
	}

	/**
	 * Drops the FrontlineSMS Tables
	 */
	public function uninstall()
	{
		/*Etherton: It scares me too much to give any old admin the ability to permanently blow away a table in the
		 * database, so I've commented this out.
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."zds_admin_alerts;
			");
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."zds_admin_alerts_cat;
			");
		*/
	}
}