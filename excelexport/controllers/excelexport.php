<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Excel Export - Controller to download an Excel dump of everything
 *
 * @author	   John Etherton <john@ethertontech.com>
 * @package	   Excel Export
 */


class excelexport_Controller extends Controller
{
	
	/**
	 * Maintains the list of parameters used for fetching incidents
	 * in the fetch_incidents method
	 * @var array
	 */
	public static $params = array();

	public static $offset = 100;
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	public function index()
	{
		
		if(isset($_GET['debug'])){
			$start = microtime(true);
		}
		
		
		if(isset($_GET['html']))
		{
			//set the header types
			header("Content-type: text/html");
			header('Cache-Control: max-age=0');
			header('Content-Disposition: attachment; filename="watertracker.html"');
			echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
			xmlns:x="urn:schemas-microsoft-com:office:excel"
			xmlns="http://www.w3.org/TR/REC-html40">
			    <head>
			        <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
			        <meta name="ProgId content=Excel.Sheet">
			        <meta name="Generator" content="Microsoft Excel 10">
			        <style>
						table
						{
						border-collapse:collapse;
						}
						td
						{
						border: solid 1px black;
						mso-number-format:"\@";/*force text*/
						}
						th
						{
						border: solid 1px black;
						}
						.odd{
							background-color:#CEE6FF;
						}
						.header{
							background-color:#999999;
							font-size:14pt;
						}
						.cff
						{
							background-color:#FFCCCC;
						}
						.cat
						{
							background-color:#CCFFCC;
							mso-number-format:"0";
						}
						.catodd
						{
							background-color:#CEE6FF;
							mso-number-format:"0";
						}
					</style>
					</header>
					<table>';
		}
		else
		{
			//set the header types
			header("Content-type: text/x-csv");
			header('Cache-Control: max-age=0');
			header('Content-Disposition: attachment; filename="watertracker.csv"');
		}
		
		flush();
		
		//start working on the header
		if(isset($_GET['html']))
		{
			echo '<tr><th class="header">Link</th>';
		}
		else
		{				
			echo 'Link';
		}	
		
		
		
		//set the hard coded values we capture
		$static_values = array('incident_id'=>'B',
				'incident_title'=>'C',
				'incident_description'=>'D',
				'incident_date'=>'E',
				'incident_active'=>'F',
				'incident_verified'=>'G',
				'location_name'=>'H',
				'latitude'=>'I',
				'longitude'=>'J',
				'incident_date_added'=>'K',
				'incident_date_last_modified'=>'L',
				);
		
		foreach($static_values as $title=>$column)
		{
			$name = ucwords(str_replace('_', ' ', $title));
			if(isset($_GET['html']))
			{
				echo '<th class="header">'.$name.'</th>';
			}
			else
			{
				echo ','.$name;
			}
		}
		
		//get the custom form fields
		$cffs = ORM::factory('form_field')->orderby('field_position', 'ASC')->find_all();
		$cff_values = array();		
		//loop over the custom form fields		
		foreach($cffs as $c)
		{
			if($c->field_type == '9')
			{
				continue;
			}
			$cff_values[$c->id] = $c->id;
			if(isset($_GET['html']))
			{
				echo '<th class="header">'.$c->field_name.'</th>';
			}
			else
			{
				echo ',"'.str_replace('"', '"""', $c->field_name).'"';
			}
		}
		

		//get the categories
		$cats = ORM::factory('category')->orderby('category_position', 'ASC')->find_all();
		$cat_values = array();
		//loop over the custom form fields
		foreach($cats as $c)
		{
			$cat_values[$c->id] = $c->id;
			if(isset($_GET['html']))
			{
				echo '<th class="header">'.$c->category_title.'</th>';
			}
			else
			{
				echo ',"'.str_replace('"', '"""', $c->category_title).'"';
			}
		}
		
		
		if(isset($_GET['html']))
		{
			echo '</tr>';
		}
		
		$offset = 0;
		$repeat = true;		
		//we loop over things as we pull in 1000 reports at a time
		while($repeat)
		{
			//get the incidents
			if(isset($_GET['debug'])){$total__dbtime = microtime(true);}
		 	//grab the incidents that we're working on this go around
			$incidents = self::fetch_incidents(false, $offset);
			//make the in_str
		 	$in_str = implode(',',array_keys($incidents));
		 	//grab the custom form field and category data
		 	$cff_data = self::get_custom_form_field_values($in_str);
		 	$cat_data = self::get_category_values($in_str);
		 	
		 	
		 	
		 	if(isset($_GET['debug'])){$temp = microtime(true) - $total__cattime;echo "\r\n<br/><br/><br/><br/> time to fetch Cat incidents: " . $temp;$temp = microtime(true) - $total__dbtime;echo "\r\n<br/><br/><br/><br/> time to fetch all incidents: " . $temp;}
		 	
		 	//keep repeating if we maxed out the number of reports returned
			if(count($incidents) == self::$offset)
			{
				$repeat = true;
			}
			else
			{
				$repeat = false;
			}
			$offset += self::$offset;
			
			
	
		 	if(isset($_GET['debug'])){$total__sheettime = microtime(true);}
	
		 	//loop over incidents and build the spread sheet	
		 	$i = 0; 	
		 	foreach($incidents as $incident_id=>$incident)
		 	{	
				$i++;
				$class = '';
				if($i % 2)
				{
					$class = 'class="odd"';  
				}
		 		$link = url::base().'reports/view/'.$incident['incident_id'];
		 		
		 		if(isset($_GET['html']))
		 		{
		 			echo '<tr>';
		 			echo '<td '.$class.'><a href="'.$link.'">'.$link.'</a></td>';
		 		}
		 		else
		 		{
		 			echo "\r\n"; //new line
		 			echo '"'.str_replace('"', '"""', $link).'"';
		 		}
		 		
		 		
		 		
		 		//do the static values
		 		foreach($static_values as $key=>$column)
		 		{
		 			if(isset($_GET['html']))
		 			{
		 				echo '<td '.$class.'>'.$incident[$key].'</td>';
		 			}
		 			else
		 			{
		 				echo ',"'.str_replace('"', '"""', $incident[$key]).'"';
		 			}
		 		}
		 		
		 		
		 		//do the CFF values
		 		foreach($cff_values as $key=>$column)
		 		{
		 			
		 			
		 			
		 			//change up the odd even stuff when we're doing cff
			 		if($i % 2)
					{
						$class = 'class="odd"';  
					}
					else
					{
						$class = 'class="cff"';
					}
					
					//check to make sure the value is set
					$value = "";
					if(isset($cff_data[$incident_id]) AND isset($cff_data[$incident_id][$key]))
					{
						
						$value = $cff_data[$incident_id][$key];
					}
					
					
					//write out to the file
		 			if(isset($_GET['html']))
		 			{
		 				echo '<td '.$class.'>'.$value.'</td>';
		 			}
		 			else
		 			{
		 				echo ',"'.str_replace('"', '"""', $value).'"';
		 			}
		 		}
		 		
		 		
		 		//do the category values
		 		foreach($cat_values as $key=>$column)
		 		{
		 			if($i % 2)
		 			{
		 				$class = 'class="catodd"';
		 			}
		 			else
		 			{
		 				$class = 'class="cat"';
		 			}
		 			
		 			//check to make sure the value is set
		 			$value = "";
		 			if(isset($cat_data[$incident_id]) AND isset($cat_data[$incident_id][$key]))
		 			{
		 			
		 				$value = $cat_data[$incident_id][$key];
		 			}
		 			
		 			if(isset($_GET['html']))
		 			{
		 				echo '<td '.$class.'>'.$value.'</td>';
		 			}
		 			else
		 			{
		 				echo ',"'.str_replace('"', '"""', $value).'"';
		 			}
		 		}
		 		
		 		
		 		if(isset($_GET['html']))
		 		{
		 			echo "</tr>";
		 		}
		 				 		
		 		unset($incidents[$incident_id]);
		 		
		 		
		 	}
		 	flush();
		}
	 	
	 	
	 	if(isset($_GET['html']))
	 	{
	 		echo "</table>";
	 	}
	 	
	 	if(isset($_GET['debug'])){
	 		echo "\r\n<br/><br/> Time too loop over things: " . (microtime(true) - $total__sheettime);
	 	}


	 	exit;
	 			
	}//end index method

	
	
	
	
	/**
	 * Helper function to fetch and optionally paginate the list of
	 * incidents/reports via the Incident Model using one or all of the
	 * following URL parameters
	 *	- category
	 *	- location bounds
	 *	- incident mode
	 *	- media
	 *	- location radius
	 *
	 * @param boolean $show_categories If true then we return the DB list with categories. If false we return the DB list with custom form fields
	 * @param int $offset How far into the search results we want to go
	 * @return Database_Result
	 */
	private static function fetch_incidents($show_categories = false, $offset)
	{
	// Reset the paramters
	self::$params = array();
	
	// Initialize the category id
	$category_id = 0;
	
	$table_prefix = Kohana::config('database.default.table_prefix');
	
	// Fetch the URL data into a local variable
	$url_data = array_merge($_GET);
	
	// Check if some parameter values are separated by "," except the location bounds
	$exclude_params = array('c' => '', 'v' => '', 'm' => '', 'mode' => '', 'sw'=> '', 'ne'=> '');
	foreach ($url_data as $key => $value)
	{
		if (array_key_exists($key, $exclude_params) AND !is_array($value))
		{
			if (is_array(explode(",", $value)))
			{
				$url_data[$key] = explode(",", $value);
			}
		}
	}
	//echo Kohana::debug($url_data);
	//> BEGIN PARAMETER FETCH
	
	//
	// Check for the category parameter
	//
	if ( isset($url_data['c']) AND !is_array($url_data['c']) AND intval($url_data['c']) > 0)
	{
	// Get the category ID
		$category_id = intval($_GET['c']);
			
		// Add category parameter to the parameter list
		array_push(self::$params,
		'(c.id = '.$category_id.' OR c.parent_id = '.$category_id.')',
				'c.category_visible = 1'
		);
	}
	elseif (isset($url_data['c']) AND is_array($url_data['c']))
	{
	// Sanitize each of the category ids
		$category_ids = array();
		foreach ($url_data['c'] as $c_id)
		{
		if (intval($c_id) > 0)
		{
				$category_ids[] = intval($c_id);
		}
		}
			
		// Check if there are any category ids
		if (count($category_ids) > 0)
		{
			$category_ids = implode(",", $category_ids);
				
			array_push(self::$params,
				'(c.id IN ('.$category_ids.') OR c.parent_id IN ('.$category_ids.'))',
				'c.category_visible = 1'
				);
			}
		}
	
		//
		// Incident modes
		//
		if (isset($url_data['mode']) AND is_array($url_data['mode']))
		{
		$incident_modes = array();
			
		// Sanitize the modes
		foreach ($url_data['mode'] as $mode)
		{
			if (intval($mode) > 0)
			{
				$incident_modes[] = intval($mode);
			}
		}
			
		// Check if any modes exist and add them to the parameter list
		if (count($incident_modes) > 0)
		{
		array_push(self::$params,
		'i.incident_mode IN ('.implode(",", $incident_modes).')'
		);
		}
		}
	
		//
		// Location bounds parameters
		//
		if (isset($url_data['sw']) AND isset($url_data['ne']))
		{
		$southwest = $url_data['sw'];
		$northeast = $url_data['ne'];
			
		if ( count($southwest) == 2 AND count($northeast) == 2 )
		{
		$lon_min = (float) $southwest[0];
		$lon_max = (float) $northeast[0];
		$lat_min = (float) $southwest[1];
		$lat_max = (float) $northeast[1];
			
		// Add the location conditions to the parameter list
		array_push(self::$params,
		'l.latitude >= '.$lat_min,
		'l.latitude <= '.$lat_max,
		'l.longitude >= '.$lon_min,
		'l.longitude <= '.$lon_max
		);
		}
		}
	
		//
		// Location bounds - based on start location and radius
		//
		if (isset($url_data['radius']) AND isset($url_data['start_loc']))
		{
		//if $url_data['start_loc'] is just comma delimited strings, then make it into an array
		if(!is_array($url_data['start_loc']))
		{
		$url_data['start_loc'] = explode(",", $url_data['start_loc']);
		}
			if (intval($url_data['radius']) > 0 AND is_array($url_data['start_loc']))
			{
				$bounds = $url_data['start_loc'];
				if (count($bounds) == 2 AND is_numeric($bounds[0]) AND is_numeric($bounds[1]))
				{
				self::$params['radius'] = array(
						'distance' => intval($url_data['radius']),
					'latitude' => $bounds[0],
					'longitude' => $bounds[1]
					);
				}
				}
				}
	
				//
				// Check for incident date range parameters
				//
				if (isset($url_data['from']) AND isset($url_data['to']))
				{
						$date_from = date('Y-m-d', strtotime($url_data['from']));
						$date_to = date('Y-m-d', strtotime($url_data['to']));
						
					array_push(self::$params,
					'i.incident_date >= "'.$date_from.'"',
					'i.incident_date <= "'.$date_to.'"'
					);
					}
	
					/**
					* ---------------------------
					* NOTES: E.Kala July 13, 2011
					* ---------------------------
					* Additional checks for date parameters specified in timestamp format
					* This only affects those submitted from the main page
					*/
	
					// Start Date
					if (isset($_GET['s']) AND intval($_GET['s']) > 0)
					{
					$start_date = intval($_GET['s']);
					array_push(self::$params,
					'i.incident_date >= "'.date("Y-m-d H:i:s", $start_date).'"'
					);
		}
	
					// End Date
					if (isset($_GET['e']) AND intval($_GET['e']))
					{
					$end_date = intval($_GET['e']);
					array_push(self::$params,
							'i.incident_date <= "'.date("Y-m-d H:i:s", $end_date).'"'
					);
		}
	
					//
					// Check for media type parameter
					//
					if (isset($url_data['m']) AND is_array($url_data['m']))
					{
					// An array of media filters has been specified
					// Validate the media types
					$media_types = array();
					foreach ($url_data['m'] as $media_type)
					{
					if (intval($media_type) > 0)
						{
					$media_types[] = intval($media_type);
					}
					}
					if (count($media_types) > 0)
					{
					array_push(self::$params,
							'i.id IN (SELECT DISTINCT incident_id FROM '.$table_prefix.'media WHERE media_type IN ('.implode(",", $media_types).'))'
							);
					}
						
					}
					elseif (isset($url_data['m']) AND !is_array($url_data['m']))
					{
							// A single media filter has been specified
							$media_type = $url_data['m'];
						
					// Sanitization
					if (intval($media_type) > 0)
					{
					array_push(self::$params,
						'i.id IN (SELECT DISTINCT incident_id FROM '.$table_prefix.'media WHERE media_type = '.$media_type.')'
						);
					}
	}
	
	//
	// Check if the verification status has been specified
	//
	if (isset($url_data['v']) AND is_array($url_data['v']))
	{
	$verified_status = array();
	foreach ($url_data['v'] as $verified)
	{
	if (intval($verified) >= 0)
	{
		$verified_status[] = intval($verified);
	}
	}
		
	if (count($verified_status) > 0)
	{
	array_push(self::$params,
	'i.incident_verified IN ('.implode(",", $verified_status).')'
	);
	}
	}
		elseif (isset($url_data['v']) AND !is_array($url_data['v']) AND intval($url_data) >= 0)
		{
		array_push(self::$param,
		'i.incident_verified = '.intval($url_data['v'])
		);
		}
	
	
		//
		// Check if they're filtering over custom form fields
		//
		if (isset($url_data['cff']) AND is_array($url_data['cff']))
		{
		$whereText = "";
		$i = 0;
	
			
		foreach($url_data['cff'] as $field)
					{
					$field_id = $field[0];
		if(intval($field_id) < 1)
		{
		continue;
	}
			
		$field_value = $field[1];
		if(is_array($field_value))
					{
					$field_value = implode(",", $field_value);
		}
	
		$i++;
		if($i > 1)
		{
		$whereText .= " OR ";
		}
			$whereText .= "(form_field_id = ".intval($field_id)." AND form_response = '".mysql_real_escape_string(trim($field_value))."')";
		}
			
		//make sure there was some valid input in there
		if($i > 0)
		{
		// I run a database query here because it's way faster to get the valid IDs in a seperate database query than it is
		//to run this query nested in the main query.
		$db = new Database();
		$rows = $db->query('SELECT DISTINCT incident_id FROM '.$table_prefix.'form_response WHERE '.$whereText. ' GROUP BY incident_id HAVING COUNT(*) = '.$i);
			$incident_ids = '';
			foreach($rows as $row)
			{
			if($incident_ids != ''){
			$incident_ids .= ',';
		}
			$incident_ids .= $row->incident_id;
		}
			if($incident_ids != '')
			{
				array_push(self::$params, 'i.id IN ('.$incident_ids.')');
			}
						else
						{
						array_push(self::$params, 'i.id IN (0)');
		}
	
		}
			
		} //end of handling cff
	
		//in case a plugin or something wants to get in on the parameter fetching fun
		Event::run('ushahidi_filter.fetch_incidents_set_params', self::$params);
	
		//> END PARAMETER FETCH
	
		// Fetch all the incidents
		$all_incidents = self::get_incidents(self::$params, $show_categories, $offset);
		
		return $all_incidents;
		
	}//end fetch_incidents
	
	
	/**
	 * Gets the reports that match the conditions specified in the $where parameter
	 * The conditions must relate to columns in the incident, location, incident_category
	 * category and media tables
	 *
	 * @param array $where List of conditions to apply to the query
	 * @param boolean $show_categories If true then we return the DB list with categories. If false we return the DB list with custom form fields
	 * @param int $offset How far into the results we want to go	 
	 * @return Database_Result
	 */
	private static function get_incidents($where = array(), $show_categories = false, $offset)
	{
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
	
		// To store radius parameters
		$radius = array();
		$having_clause = "";
		if (array_key_exists('radius', $where))
		{
			// Grab the radius parameter
			$radius = $where['radius'];
	
			// Delete radius parameter from the list of predicates
			unset ($where['radius']);
		}
		
		// Query
		$sql = 'SELECT DISTINCT i.id AS incident_id,';
		$sql .= ' i.incident_title AS incident_title,';
		$sql .= ' i.incident_description AS incident_description,';
		$sql .= ' i.incident_date AS incident_date,';
		$sql .= ' i.incident_active AS incident_active,';
		$sql .= ' i.incident_verified AS incident_verified,';
		$sql .= ' l.location_name AS location_name,';
		$sql .= ' l.latitude AS latitude,';
		$sql .= ' l.longitude AS longitude,';
		$sql .= ' i.incident_dateadd AS incident_date_added,';
		$sql .= ' i.incident_datemodify AS incident_date_last_modified ';
		

	
		// Check if all the parameters exist
		if (count($radius) > 0 AND array_key_exists('latitude', $radius) AND array_key_exists('longitude', $radius)
				AND array_key_exists('distance', $radius))
		{
			// Calculate the distance of each point from the starting point
			$sql .= ", ((ACOS(SIN(%s * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS(%s * PI() / 180) * "
			. "	COS(l.`latitude` * PI() / 180) * COS((%s - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance ";
	
			$sql = sprintf($sql, $radius['latitude'], $radius['latitude'], $radius['longitude']);
	
			// Set the "HAVING" clause
			$having_clause = "HAVING distance <= ".intval($radius['distance'])." ";
		}
	
		$sql .=  'FROM '.$table_prefix.'incident i '
		. 'LEFT JOIN '.$table_prefix.'location l ON (i.location_id = l.id) '
		. 'INNER JOIN '.$table_prefix.'incident_category ic ON (ic.incident_id = i.id) '
		. 'INNER JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) '
		. 'WHERE i.incident_active = 1 ';
		// . 'AND c.category_visible = 1 ';
	
		// Check for the additional conditions for the query
		if ( ! empty($where) AND count($where) > 0)
		{
			foreach ($where as $predicate)
			{
				$sql .= 'AND '.$predicate.' ';
			}
		}
	
		// Add the having clause
		$sql .= $having_clause;
	
		// Check for the order field and sort parameters
		if ( ! empty($order_field) AND ! empty($sort) AND (strtoupper($sort) == 'ASC' OR strtoupper($sort) == 'DESC'))
		{
			$sql .= 'ORDER BY '.$order_field.' '.$sort.' ';
		}
		else
		{
			$sql .= 'ORDER BY i.incident_date DESC ';
		}
	
		
		//make sure we grab things incrementally
		$sql .= 'LIMIT '.$offset.', '.self::$offset;
		
		
		$user_name = Kohana::config('database.default.connection.user');
		$password = Kohana::config('database.default.connection.pass');
		$database = Kohana::config('database.default.connection.database');
		$server = Kohana::config('database.default.connection.host');
		$db_handle = mysql_connect($server, $user_name, $password);
		$db_found = mysql_select_db($database, $db_handle);
		
		$result = mysql_query($sql);
		
		//read all the data into an array
		$incidents = array();
		while ($incident = mysql_fetch_assoc($result))
		{
			$incidents[$incident['incident_id']] = $incident;
		}
		mysql_close($db_handle);
	
		return $incidents;
	}
	
	
	
	/**
	 * This function grabs all the custom form field data for a given in string
	 * @param unknown_type $in_str The string for a MySQL IN statement
	 */
	private static function get_custom_form_field_values($in_str)
	{
		$user_name = Kohana::config('database.default.connection.user');
		$password = Kohana::config('database.default.connection.pass');
		$database = Kohana::config('database.default.connection.database');
		$server = Kohana::config('database.default.connection.host');
		$db_handle = mysql_connect($server, $user_name, $password);
		$db_found = mysql_select_db($database, $db_handle);
		
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		$sql = 'SELECT * FROM '.$table_prefix.'form_response WHERE incident_id IN('.$in_str.') ORDER BY incident_id ASC';
		
		$result = mysql_query($sql);		
		$cff_data = array();
		$current_incident_id = -1;
		while ($data = mysql_fetch_assoc($result))
		{
			//handle a new id
			if($current_incident_id != $data['incident_id'])
			{
				$cff_data[$data['incident_id']] = array();
				$current_incident_id = $data['incident_id'];
			}
			$cff_data[$data['incident_id']][$data['form_field_id']] = $data['form_response'];
		}
		mysql_close($db_handle);
		
	
		return $cff_data;
		
	}
	
	
	
	/**
	 * This function grabs all the custom form field data for a given in string
	 * @param unknown_type $in_str The string for a MySQL IN statement
	 */
	private static function get_category_values($in_str)
	{
		$user_name = Kohana::config('database.default.connection.user');
		$password = Kohana::config('database.default.connection.pass');
		$database = Kohana::config('database.default.connection.database');
		$server = Kohana::config('database.default.connection.host');
		$db_handle = mysql_connect($server, $user_name, $password);
		$db_found = mysql_select_db($database, $db_handle);
	
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
	
		$sql = 'SELECT * FROM '.$table_prefix.'incident_category WHERE incident_id IN('.$in_str.') ORDER BY incident_id ASC';
	
		$result = mysql_query($sql);
		$cff_data = array();
		$current_incident_id = -1;
		while ($data = mysql_fetch_assoc($result))
		{
			//handle a new id
			if($current_incident_id != $data['incident_id'])
			{
				$cff_data[$data['incident_id']] = array();
				$current_incident_id = $data['incident_id'];
			}
			$cff_data[$data['incident_id']][$data['category_id']] = TRUE;
		}
		mysql_close($db_handle);
	
	
		return $cff_data;
	
	}
	
	

	
}