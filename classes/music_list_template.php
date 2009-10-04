<?
class music_list_template
{
	var $TRACK_id, $ARTIST_id, $ALBUM_id, $GENRE_id, $TRACK_puid, $TRACK_tracknum, $TRACK_name, $TRACK_artist_id, $TRACK_album_id, $TRACK_genre_id, $TRACK_duration, $TRACK_bpm, $TRACK_volume_diff, $TRACK_year, $TRACK_last_lookup, $TRACK_mb_verified, $TRACK_mb_track_id, $TRACK__update_id3, $TRACK_added, $TRACK__archived, $ARTIST_mb_id, $ARTIST_name, $ALBUM_mb_id, $ALBUM_name, $ALBUM_cd_number, $ALBUM_album_artist_id, $ALBUM_amazon_asin, $GENRE_mb_id, $GENRE_name;
	
	var $database, $lastError, $DN;
	var $_PK, $_table;
	var $_field_descs;
	var $_labels;			 	//  used for custom form labels e.g GMCNO = GMC Member Number
	var $_form_label_ids;			//  used internaly for html labels. DO NOT SET MANUALLY !!!!
	var $_data_format;			//flags if object data is in 'db' or 'php' format for convertDBProperties
	
	
	/**
	 * @return void
	 * @desc This is the PHP4 constructor. It calles the PHP5 constructor __construct()
	 */
	function music_list_template()
	{
		$this->__construct();
	}//PHP4 constructor
	
	
	
	/**
	 * @return void
	 * @param 
	 * @desc This is the PHP5 constructor.
	 */
	function __construct()
	{
		$this->TRACK_id = 0;
		$this->ARTIST_id = 0;
		$this->ALBUM_id = 0;
		$this->GENRE_id = 0;

		$this->TRACK_puid = "";
		$this->TRACK_tracknum = "";
		$this->TRACK_name = "";
		$this->TRACK_artist_id = "";
		$this->TRACK_album_id = "";
		$this->TRACK_genre_id = "";
		$this->TRACK_duration = "";
		$this->TRACK_bpm = "";
		$this->TRACK_volume_diff = "";
		$this->TRACK_year = "";
		$this->TRACK_last_lookup = "";
		$this->TRACK_mb_verified = "";
		$this->TRACK_mb_track_id = "";
		$this->TRACK__update_id3 = "";
		$this->TRACK_added = "";
		$this->TRACK__archived = "";
		$this->ARTIST_mb_id = "";
		$this->ARTIST_name = "";
		$this->ALBUM_mb_id = "";
		$this->ALBUM_name = "";
		$this->ALBUM_cd_number = "";
		$this->ALBUM_album_artist_id = "";
		$this->ALBUM_amazon_asin = "";
		$this->GENRE_mb_id = "";
		$this->GENRE_name = "";
		
		$this->database = new database();
		$this->_PK = 'TRACK_id';
		$this->_PKs = array('TRACK_id','ARTIST_id','ALBUM_id','GENRE_id');
		$this->_table = 'music_lists';
		$this->_data_format = 'php';
		$this->_labels = array(); 
		$this->_form_label_ids = array();
		$this->_table_data = array(
			'albums'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'artists'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_accounts'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_spool_items'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'cached_puids'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'dead_sources'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'genres'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'hosts'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'sources'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'track_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'tracks'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'type_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
		);

		$this->_field_descs['TRACK_id'] = array ("pk" => "1", "type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['TRACK_puid'] = array ("type" => "varchar(50)", "length" => "50", "gen_type" => "string");
		$this->_field_descs['TRACK_tracknum'] = array ("type" => "int(2) unsigned zerofill", "length" => "2", "gen_type" => "int");
		$this->_field_descs['TRACK_name'] = array ("type" => "varchar(255)", "length" => "255", "gen_type" => "string");
		$this->_field_descs['TRACK_artist_id'] = array ("type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['TRACK_album_id'] = array ("type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['TRACK_genre_id'] = array ("type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['TRACK_duration'] = array ("type" => "time", "gen_type" => "datetime");
		$this->_field_descs['TRACK_bpm'] = array ("type" => "float unsigned", "gen_type" => "number");
		$this->_field_descs['TRACK_volume_diff'] = array ("type" => "float unsigned", "gen_type" => "number");
		$this->_field_descs['TRACK_year'] = array ("type" => "int(4)", "length" => "4", "gen_type" => "int");
		$this->_field_descs['TRACK_last_lookup'] = array ("type" => "date", "gen_type" => "datetime");
		$this->_field_descs['TRACK_mb_verified'] = array ("type" => "enum('n','y')", "default" => "", "values" => array('n','y',), "gen_type" => "enum");
		$this->_field_descs['TRACK_mb_track_id'] = array ("type" => "varchar(150)", "length" => "150", "gen_type" => "string");
		$this->_field_descs['TRACK__update_id3'] = array ("type" => "enum('n','y')", "default" => "", "values" => array('n','y',), "gen_type" => "enum");
		$this->_field_descs['TRACK_added'] = array ("type" => "datetime", "gen_type" => "datetime");
		$this->_field_descs['TRACK__archived'] = array ("type" => "enum('n','y')", "default" => "", "values" => array('n','y',), "gen_type" => "enum");
		$this->_field_descs['ARTIST_id'] = array ("pk" => "1", "type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['ARTIST_mb_id'] = array ("type" => "varchar(125)", "length" => "125", "gen_type" => "string");
		$this->_field_descs['ARTIST_name'] = array ("type" => "varchar(255)", "length" => "255", "gen_type" => "string");
		$this->_field_descs['ALBUM_id'] = array ("pk" => "1", "type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['ALBUM_mb_id'] = array ("type" => "varchar(125)", "length" => "125", "gen_type" => "string");
		$this->_field_descs['ALBUM_name'] = array ("type" => "varchar(255)", "length" => "255", "gen_type" => "string");
		$this->_field_descs['ALBUM_cd_number'] = array ("type" => "int(2)", "length" => "2", "gen_type" => "int");
		$this->_field_descs['ALBUM_album_artist_id'] = array ("type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['ALBUM_amazon_asin'] = array ("type" => "varchar(15)", "length" => "15", "gen_type" => "string");
		$this->_field_descs['GENRE_id'] = array ("pk" => "1", "type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['GENRE_mb_id'] = array ("type" => "varchar(125)", "length" => "125", "gen_type" => "string");
		$this->_field_descs['GENRE_name'] = array ("type" => "varchar(125)", "length" => "125", "gen_type" => "string");

	}//__constructor
	
	

	/**
	 * @return int	-	The number of rows found
	 * @param string $where = ""		-	The Where clause SQL
	 * @param string $order = ""		-	The Order By SQL
	 * @param string $limit = ""		-	The Limit SQL
	 * @param array $joins = ""			-	An associative array of additional joins for the table, where the key is the table name to be joined.
	 * @desc This generic method runs a database query with the optional WHERE statement, sorted as defined in the global $CONF array or overridden with a order parameter. There is also an optional limit parameter
	 */
	function getList($where="",$order="",$limit="", $joins="")
	{
		if(!$order)
			$order = "order by ALBUM_name,ALBUM_cd_number,TRACK_tracknum,ARTIST_name,TRACK_name" ;
		
		if ($joins && !is_array($joins))
			trigger_error('Non-array submitted for \$joins paramter of '.$this.'->getList', E_USER_WARNING);
		
		$select = "SELECT tracks.id as TRACK_id, tracks.puid as TRACK_puid, tracks.tracknum as TRACK_tracknum, tracks.name as TRACK_name, tracks.artist_id as TRACK_artist_id, tracks.album_id as TRACK_album_id, tracks.genre_id as TRACK_genre_id, tracks.duration as TRACK_duration, tracks.bpm as TRACK_bpm, tracks.volume_diff as TRACK_volume_diff, tracks.year as TRACK_year, tracks.last_lookup as TRACK_last_lookup, tracks.mb_verified as TRACK_mb_verified, tracks.mb_track_id as TRACK_mb_track_id, tracks._update_id3 as TRACK__update_id3, tracks.added as TRACK_added, tracks._archived as TRACK__archived, artists.id as ARTIST_id
				, artists.mb_id as ARTIST_mb_id, artists.name as ARTIST_name, albums.id as ALBUM_id
				, albums.mb_id as ALBUM_mb_id, albums.name as ALBUM_name, albums.cd_number as ALBUM_cd_number, albums.album_artist_id as ALBUM_album_artist_id, albums.amazon_asin as ALBUM_amazon_asin, genres.id as GENRE_id
				, genres.mb_id as GENRE_mb_id, genres.name as GENRE_name
				 FROM tracks 
				 LEFT JOIN artists ON (tracks.artist_id=artists.id ";
		if (isset($joins['artists'])) $select.=$joins['artists'];
		$select.=")
				 LEFT JOIN albums ON (tracks.album_id=albums.id ";
		if (isset($joins['albums'])) $select.=$joins['albums'];
		$select.=")
				 LEFT JOIN genres ON (tracks.genre_id=genres.id ";
		if (isset($joins['genres'])) $select.=$joins['genres'];
		$select.=")";
		
		$this->database->query("$select $where $order $limit");
		
		return($this->database->RowCount);	
	}//getList
		
	
	
	/**
	 * @return unknown
	 * @desc This generic method gets the next result from the last database query and loads the values into the properties of the object
	 */
	function getNext()
	{
		$tmp = $this->database->getNextRow();
		
		$this->DN = "";
		if($tmp) {
			/*if (empty($tmp['TRACK_id']) || empty($tmp['ARTIST_id']) || empty($tmp['ALBUM_id']) || empty($tmp['GENRE_id']))		//something wrong if PKs are missing
				trigger_error ('Some primary keys missing from meta table '.get_class($this), E_USER_NOTICE);*/
			
			// TODO - rewrite this bit to work with meta tables, e.g
			// class::get{field}CB
			
			$this->setProperties($tmp);
			
			//convert from DB properties
			$this->convertDBProperties('from');		//needs to be changed to 'php' when legacy stuff is removed

			if (isset($this->name) && ($this->name))
				$this->DN = $this->name;
			elseif (isset($this->title) && ($this->title))
				$this->DN = $this->title;
			else
				$this->DN = "$this->TRACK_id / $this->ARTIST_id / $this->ALBUM_id / $this->GENRE_id";
			return true;
			
		}
		else {
			return false;
		}
	}//getNext
	
	
	
	
	/**
	 * @return unknown
	 * @param int $TRACK_id		-	primary key of record
	 * @param int $ARTIST_id		-	primary key of record
	 * @param int $ALBUM_id		-	primary key of record
	 * @param int $GENRE_id		-	primary key of record

	 * @desc Extracts the requested record from the database and puts it into the properties of the object
	 */
	function get($TRACK_id, $ARTIST_id, $ALBUM_id, $GENRE_id)
	{ 
		
		$sql = "WHERE 1
		AND track.id = '$TRACK_id'
		AND artist.id = '$ARTIST_id'
		AND album.id = '$ALBUM_id'
		AND genre.id = '$GENRE_id'";
		
		$count = $this->getList($sql);
		
		//retrieve all fields from the table and map to user object
		//Also, confirm we have only retrieved a unique record
		if ($count > 1){
			trigger_error("More than one record returned using non-unique PK values", E_USER_ERROR);
			return false;
			
		}else{
			if ($this->getNext())
				return true;
			else
				return false;
			
		}//IF non-unique get
		
	}//get
	
	
	
	
	/////////////////////////////////////
	//	getByOther()
	//	Rob S - 07/11/03
	/**
	 * @return bool
	 * @param mixed $fields		-	A name value associative array of fields and values
	 * @desc This method is used to extract the requested record from the database by field(s) other than ID, and populates the object properties
	 */
	function getByOther($field_array) {
		$sql = "WHERE 1";
		foreach ($field_array as $fieldname => $value) {
			/*if ($this->_field_descs[$fieldname]['gen_type'] == 'int')
				$sql.= " AND $fieldname = $value";
			else
				$sql.= " AND $fieldname = '$value' ";*/
			//^cant trust that supplied data is numeric for INT fields, so....
			
			$sql.= " AND $fieldname = '".$this->database->escape($value)."'";
		}//FOREACH
		
		//retrieve all fields from the table and map to user object
		//Also, confirm we have only retrieved a unique record
		$count = $this->getList($sql);
		switch ($count){
			case 0:		//no record
				return false;
				break;
				
			case 1:		//match found
				$this->getNext();
				
				return $this->TRACK_id;		//single PK, so returns pk value
				break;
				
			default:	//non-unique getByOther
				trigger_error("More than one record returned using non-unique \$field_array values", E_USER_WARNING);
				return false;
				break;
		}//SWITCH record count
		
	}//getByOther
	
	
	
	
	/**
	 * @return bool			-	True on Success, False otherwise
	 * @param array $properties		-	An array of the property values with which to update the object
	 * @param array $addSlashes		-	does what it says on the tin!
	 * @desc sets an objects properties off the back of an array - filters out any irrelevent properties.
	 */
	function setProperties($properties, $addSlashes=0)	{
		
		if(is_array($properties)) {
			$object_props = get_object_vars($this);		//retrieve array of properties
			
			foreach ($properties as $key => $value) {
				if(isset($this->_field_descs[$key]['fk'])) {
					$child_class = $this->_field_descs[$key]['fk'];
					
					if(!class_exists($child_class)) {
						# Todo - Write so this can be done without @
						@include "$child_class.php";		//attempt to load class file, but suppress errors if not found
						@include "$child_class.class.php";		//attempt to load class file, but suppress errors if not found
					}
					$child = new $child_class();
					if($this->_field_descs[$key]['gen_type'] == "many2many") {
					
	                        $child->_setPropertiesLinkages("music_list", $this->TRACK_id, array_keys($value));
                        
					}
					else {
						if((isset($_FILES[$key]))&&($_FILES[$key]["size"])) {
							if($value) {
								$child->delete($value);
							}
							$this->$key = $child->upload($_FILES[$key]["tmp_name"],$_FILES[$key]["name"]);
						}
						else {
							// use old value
							$this->$key = $value;
						}
					}
				}
				else {
					if(array_key_exists($key, $object_props)){
						if(is_array($value)) {
							if(isset($value['month']) && isset($value['year']) ) {
								$value = $this->mysqlDateJoin($value);
							}
							else {
								trigger_error("::setProperties can't set $key to be an array",E_USER_WARNING);
							}
						}
						// provided by PHPOF
						if(($this->_field_descs[$key]['gen_type'] == "string")&&(class_exists("XString"))) {
		                                        $value = XString::FilterMS_ASCII($value);
                               			}
						$this->$key = $value;
					}//IF key matched
				}
			}//FOREACH element
			
			return true;
			
		}else{	//not array
			return false;
		}//IF is array
		
	}//setProperties

	///////////////////////////////////////////////////////////////////
	//      rob s - 04/12/03
	/**
	 * @return array        -       An array of seperate date/time fields
	 * @param $mysql_date           - Accepts an array of type defined by mysqlDateSplit function
	 * @param $timestamp            - If set, a MySQL timestamp is created, rather than a MySQL DATETIME
	 * @desc This function takes a Date array created by mysqlDateSplit and joins it into a MySQL DATEIME or TIMESTAMP field
	 */
	function mysqlDateJoin ($mysql_date, $timestamp=0) {
	        if (!$timestamp) {      //therefore is DATETIME
	                $date_string = $mysql_date['year']."-".$mysql_date['month']."-".$mysql_date['day'];
	                if (isset($mysql_date['hour']))
	                        $date_string.= " ".$mysql_date['hour'].":".$mysql_date['min'].":".$mysql_date['sec'];

        	}else{  //is TIMESTAMP
	                return $mysql_date['year'].$mysql_date['month'].$mysql_date['day'].$mysql_date['hour'].$mysql_date['min'].$mysql_date['sec'];
	                $date_string = $mysql_date['year'].$mysql_date['month'].$mysql_date['day'];
	                if (isset($mysql_date['hour']))
	                        $date_string.= $mysql_date['hour'].$mysql_date['min'].$mysql_date['sec'];
	        }//IF DATETIME

	        return $date_string;
	}//mysqlDateJoin
	
	
	
	/**
	 * @return bool			-	False if no format is specified, or format already matches.
	 * @param enum $format	-	Either 'php' or 'db' depending on if we are converting to PHP or MySQL formats
	 * @desc Abstract Method! converts object properties to MySQL data or Vice Versa
	 */
	//PHP5:
	//protected abstract function convertDBProperties();
	
	//PHP4:
	function convertDBProperties($format) {
		/*require_once 'premier_common.php';
		
		//legacy conversions...
		if ($format=='from') $format = 'php';
		if ($format=='to') $format = 'db';
		
		
		if ($format=='db' && $this->_data_format=='php') {
			...
			
			$this->_data_format='db';
			
		}elseif ($format=='php' && $this->_data_format=='db'){
			...
			
			$this->_data_format='php';
			
		}else{	//either no $format entered, or format already matches
			trigger_error('Invalid situation for convertDBProperties call. $format='.$format.'; $this->_data_format='.$this->_data_format.';', E_USER_NOTICE);
			return false;
		}//IF*/
	}//convertDBProperties
	
	
	
	
	/**
	 * @return int
	 * @param string $keyword			-	Search criteria
	 * @param string $field_list		-	Comma separated list
	 * @param string $type = ""			-	Enum: "begins_with", "ends_with"
	 * @param string $where=""			-	A basic where clause to be included
	 * @desc This generic method searches the comma delimited field list for the specified keyword. types are begins_with or ends_with
	 */
	function search($keyword, $field_list, $type=null, $where=null)
	{
		if($type != "begins_with") $start = "%";
		if($type != "ends_with") $end = "%";
		if(!is_array($field_list)) {
			$field_list = split(",", $field_list);
		}//IF
		foreach($field_list as $key => $field) {
			$sql_array[] = "$field  like '$start$keyword$end' ";
		}
		
		
		if ($where)
			$where .= "\nAND (".implode("\n\tOR ", $sql_array).")\n";
		else
			$where .= "WHERE ".implode("\nOR ", $sql_array);
		
		return($this->getList($where));
	}//search
	
	
	
	
	/**
	 * @return void
	 * @param string $select_name		-	The select box name
	 * @param string $value = ""		-	Select value
	 * @param string $where = ""		-	The Where clause of the getList method call
	 * @param string $extra = ""		-	Any extra HTML code to insert inside the <select>
	 * @desc This generic method will create a select box. Single Primary Keys only!
	 */
	function select($select_name, $value="", $where="", $extra="")
	{
		//NB: Still a single Primary key function!...
		
		print "<select name=\"$select_name\" $extra>\n";
		print "<option value=\"\">-None-</option>\n";
		$this->getList($where);
		$used = array();
		while($this->getNext()) {
			print "<option value=\"$this->TRACK_id\"";
			if($this->TRACK_id == $value) print " SELECTED";
			print ">$this->DN</option>\n";
			$used[] = $this->TRACK_id;
		}//WHILE
		if((!in_array($value, $used))&&($value))
			print "<option value=\"$value\" selected>$value</option>\n";
		print "</select>\n";
	}//select
	
	
	/**
	 * @return see select function
	 * @desc Alias for select
	 */
	function createSelect($select_name, $value="", $where="", $extra="") 
	{
		$this->select($select_name, $value, $where, $extra);
	}//createSelect



	
	
	
	
	/**
	 * @return string	-	The field label
	 * @param string $property			-	The name of the object property to be used
	 * @param string $input_name		-	The name of the HTML input field that this matches up with when calling createFormObject
	 * @desc This generic method will create a user friendly name for an objects property name suitable for use on a form
	 */
	function createFormLabel($property, $input_name="")
	{
		//if (substr($input_name, -2)=="[]" && stristr($extra, 'multiple')===false)	//autocomplete the array key, if none was specified (and its not a multi select object which requires an array)!
		if (substr($input_name, -2)=="[]")	//autocomplete the array key, if none was specified (and its not a multi select object which requires an array)!
			$input_name = substr($input_name, 0,-2)."[$property]";
		
		//check for specified label name
		if(isset($this->_labels[$property])) {
			$name = $this->_labels[$property];		//all object properties now have a _label!
			
		}else{
			//generate label from fieldname
			if(isset($this->_field_descs[$property]['fkl'] )) {
				$property = str_replace("_FKL","",$property);
			}
			elseif(isset($this->_field_descs[$property]['fk'] )) {
				$property = $this->_field_descs[$property]['fk'];
			}//IF fk
			$name = ucwords(str_replace("_"," ",$property));
		}//IF
		
		if($input_name) {
			$html_id = $this->_createFormObjectID($input_name);
			$name = "<label for=\"$html_id\">" . ucwords($name) . "</label>";
		}//label is used on Form object
		
		
		return($name);
		
	}//createFormLabel
	
	
	
	/**
	 * @return string
	 * @param string $property			-	The name of the object property to be used
	 * @param string $input_name		-	The name of the HTML input field, usually an array ie. properties[]
	 * @param unknown $value			-	The current value of the input field, defaults to the current value of property
	 * @param string $empty 			-	Defines the text for the "Empty" option in Select and other input boxes
	 * @param string $extra				-	Any extra parameters to be set inside the form object, or javascript etc.
	 * @param string $where				-	The Where clause for any createSelect's to be used
	 * @desc This generic method will create a HTML form object dependent on the type of field submitted.
	 */
	function createFormObject ($property, $input_name, $value='***ROGUE_VALUE***', $empty='-None-', $extra='', $where='') {
		global $CONF;
		$html = "";
		
		$property_value = ($value=='***ROGUE_VALUE***')? $this->$property : $value;	//check if $value was passed or not
		if(is_array($property_value)){
			foreach ($property_value as $arr_name => $arr_value)
				$property_value[$arr_name] = htmlspecialchars($arr_value);
		}else{
			$property_value = htmlspecialchars($property_value);
		}//IF
		
		if (substr($input_name, -2)=="[]" && stristr($extra, 'multiple')===false)	//autocomplete the array key, if none was specified (and its not a multi select object which requires an array)!
			$input_name = substr($input_name, 0,-2)."[$property]";
		
		$html_id = $this->_createFormObjectID($input_name);
		
		//begin filtering appropriate field type...
		if (isset($this->_field_descs[$property]['fk'] )){	//we have found a foreign key
			$fk_class = $this->_field_descs[$property]['fk'];
			
			if (!$fk_class) {
				$html.= "couldnt match foreign key $fieldname to a table";
			}else{
				if(!class_exists($fk_class)) {
					# Todo - Write so this can be done without @
					@include "$fk_class.php";		//attempt to load class file, but suppress errors if not found
					@include "$fk_class.class.php";		//attempt to load class file, but suppress errors if not found
				}
				$fk = new $fk_class();
				if($this->_field_descs[$property]['gen_type'] == "many2many") {
				
						$html .= $fk->createMatrix($input_name,"music_list",$this->TRACK_id);
						
				}
				elseif($fk_class == "image") {
					$fk->get($value);
                                        if($fk->id) {
                                        	print "$fk->name ";
                                       	}
                                        $html .= "<input type=\"hidden\" name=\"" . $input_name  ."\" value=\"$value\"><br>\n";
                                   	$html .= "<input type=\"file\" name=\"".$property."\">";
				}
				else {
					$fk_obj = new $fk_class();
					ob_start();
					$extra .= " id=\"$html_id\" ";
					$fk_obj->createSelect($input_name, $property_value, $where, $extra);
					$html.= ob_get_contents();
					ob_end_clean();
				}
			}//IF foreign key matched to table
			
			
		} else {	//not a Foreign Key field...
			switch ($this->_field_descs[$property]['gen_type']) {
                          case 'blob' :
					$html .= 'Binary Data';
					break;
                          case 'timestamp' :
					$html .= $this->$property;
					break;
			  case 'int' :
			  case 'number' :
				preg_match ("/\((\d+)\)/", $this->_field_descs[$property]['type'], $matches);		//get field length
				if ($matches[1] ==1 || 			//a tiny int of display length 1 char is presumed to be a boolean
						(isset($CONF['music_list'][$property]['max']) && $CONF['music_list'][$property]['max']==1) ){		//or setting the max value to 1 presumes a boolean
					$html.= "<input type=\"radio\" name=\"$input_name\" value=\"1\" id=\"$html_id\"";
					if($property_value)	//allow any possible value for True
						$html.= " checked";
					$html.= " $extra>Yes ";
					
					$html.= "<input type=\"radio\" name=\"$input_name\" value=\"0\" id=\"$html_id\"";
					if(!$property_value)	//allow any possible value for True
						$html.= " checked";
					$html.= " $extra>No";
					
					break;	//escape SWITCH statement
					
				}elseif (isset($CONF['music_list'][$property]['max']) && $CONF['music_list'][$property]['max']){
					$min = ($CONF['music_list'][$property]['min'])? $CONF['music_list'][$property]['min'] : 0;
					$step = ($CONF['music_list'][$property]['step'])? $CONF['music_list'][$property]['step'] : 1;
					if ($empty=='-None-')
						$empty = '--';
					$html.= createNumberSelect($input_name, $property_value, $min, $CONF['music_list'][$property]['max'], $step, $empty);
					break;	//escape SWITCH statement
				}//IF integer is a Boolean
				
				//ELSE .... carry on and treat as a STRING.....
				
			  case 'string' :
				preg_match ("/\((\d+),?(\d+)?\)/", $this->_field_descs[$property]['type'], $matches);		//get field length
				if (preg_match ("/decimal/", $this->_field_descs[$property]['type']) )		//decimal
					$maxlength = $matches[1] + $matches[2] + 1;	//need to add space for decimalpoint!
				else
					$maxlength = $matches[1];
				
				if (isset($CONF['music_list'][$property]['size']))
					$size = $CONF['music_list'][$property]['size'];
				elseif($maxlength <= 30)
					$size = $maxlength+1;
				elseif ($maxlength <= 50)
					$size = 40;
				else
					$size = 60;
				
				if (strpos(strtolower($property), "password") !== FALSE || strpos(strtolower($property), "pwd") !== FALSE)
					$html.= "<input type=\"password\" name=\"$input_name\" value=\"$property_value\" size=\"10\" maxlength=\"20\" id=\"$html_id\" $extra>";
				else
					$html.= "<input type=\"text\" name=\"$input_name\" value=\"$property_value\" size=\"$size\" maxlength=\"$maxlength\" id=\"$html_id\" $extra>";
				break;
				
				
			  case 'text' :
				//get field length
				if (strpos($this->_field_descs[$property]['type'], "medium") || strpos($this->_field_descs[$property]['type'], "long") || ereg('^text$',$this->_field_descs[$property]['type']) ) {
					$cols = 50;
					$rows = 12;
				}else{
					$cols = 40;
					$rows = 6;
				}//IF
				
				$html.= "<textarea name=\"$input_name\" cols=\"$cols\" rows=\"$rows\" id=\"$html_id\" $extra>$property_value</textarea>\n";
				break;
				
				
			  case 'enum' :
				$enums = $this->_field_descs[$property]['values'];
				
				if (sizeof($enums) < 4){
					
					if (sizeof($enums)==1){ //Use a check box as we only have one option
						$html.= "<input type=\"radio\" name=\"$input_name\" value=\"1\" id=\"$html_id\"";
						if($property_value)	//allow any possible value for True
							$html.= " checked";
						$html.= " $extra>Yes ";
						
						$html.= "<input type=\"radio\" name=\"$input_name\" value=\"0\" id=\"$html_id\"";
						if(!$property_value)	//allow any possible value for True
							$html.= " checked";
						$html.= " $extra>No\n";
						
					}else{ //use Radio buttons
						if($empty!="-None-") {
							$radio_id = $html_id . "none";
							$html.= "<input type=\"radio\" name=\"$input_name\" value=\"\" id=\"$radio_id\"";
							if(empty($property_value))
								$html.= " checked";
							$html.= " $extra> <label for=\"$radio_id\">$empty</label>&nbsp;&nbsp; ";
						}//IF
						
						foreach($enums as $i=>$type) {
							$radio_id = $html_id . eregi_replace("[^a-z0-9_-]","",$type);
							$html.= "<input type=\"radio\" name=\"$input_name\" value=\"$type\" id=\"$radio_id\"";
							if($property_value == $type)
								$html.= " checked";
							$html.= " $extra> <label for=\"$radio_id\">$type</label>&nbsp;&nbsp; ";
						}//FOREACH
						$html.= "\n";
					}//IF only one Enum option
					
				}else{	//many options so use SELECT list
					
					$html.= "<select name=\"$input_name\" id=\"$html_id\" $extra>\n";
					$html.= "<option value=\"\">$empty</option>\n";
					foreach($enums as $i=>$enum_value) {
						$html.= "<option value=\"$enum_value\"";
						if( $enum_value==$property_value ||
							(is_array($property_value) && in_array($enum_value, $property_value)) )
							$html.= " selected";
						$html.= ">$enum_value</option>\n";
					}//FOREACH
					$html.= "</select>\n";
				}//IF less than 4 options
				break;
				
				
			  case 'datetime' :
				$seperator = '';
				//check that date is an array...
				if (!is_array($property_value)){	//then value needs to be converted to standard date array
					$property_value = mysqlDateSplit($property_value);		//function from premier_common
				}//IF
				
				
				//check for date part
				if (strpos($this->_field_descs[$property]['type'], "date") !== FALSE) {
					if	(strpos(strtolower($property), "dob") !== FALSE || strpos(strtolower($property), "birth") !== FALSE || strpos(strtolower($property), "b_day") !== FALSE) {
						$past = 85;
						$future = 0;
					}else{
						$past = 5;
						$future = 5;
					}//IF date of birth field
					
					if (isset($CONF['music_list'][$property]['past']))
						$past = $CONF['music_list'][$property]['past'];
					
					if (isset($CONF['music_list'][$property]['future']))
						$future = $CONF['music_list'][$property]['future'];
					
					$html.= createDateSelect($input_name, $property_value, $past, $future);
					$separator = " @ ";
				}//IF date
				
				
				//check for time part...
				if (strpos($this->_field_descs[$property]['type'], "time") !== FALSE){
					$html.= $separator.createTimeSelect($input_name, $property_value, 1);
				}//IF date
				
				break;
				
			  default:
				$html.= "<b>ERROR</b> - outside switch for MySQL type '".$this->_field_descs[$property]['type']."' from field '$property' in createFormObject()<br>\n";
				if ($this->_field_descs[$property]['gen_type'])
					$html.= "gen-type: ".$this->_field_descs[$property]['gen_type'];
				$html.= "<br>\nPossibly misspelt fieldname / newly added db field?\n";
				break;
			}//SWITCH
		}//IF foreign key field
		
		return $html;
	}//createFormObject
	
	
	
	
	/**
	 * @return void
	 * @desc x
	 */
	function _createFormObjectID($input_name)
	{
		$input_name = eregi_replace("[^a-z0-9_-]","",$input_name);
		if(!isset($this->_form_label_ids[$input_name])) {
			$this->_form_label_ids[$input_name]  = $input_name . "_" . substr(microtime(),-4) . "_" .  rand(0,99);
		}
		return($this->_form_label_ids[$input_name]);
	}
	
	
	
	
	/**
	 * @return void
	 * @desc This is the template PHP5 destructor. It calls $this->database->finish()
	 */
	function  __destruct()
	{
		$this->database->finish();
		foreach ($this as $property=>$value)
			$this->$property = null;
	}

}