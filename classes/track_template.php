<?
class track_template
{
	var $id, $trm, $tracknum, $name, $artist_id, $album_id, $genre_id, $duration, $bpm, $volume_diff, $year, $last_lookup, $mb_verified, $mb_track_id, $_update_id3, $added, $_archived;
	
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
	function track_template()
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
		$this->id = 0;

		$this->trm = "";
		$this->tracknum = "";
		$this->name = "";
		$this->artist_id = "";
		$this->album_id = "";
		$this->genre_id = "";
		$this->duration = "";
		$this->bpm = "";
		$this->volume_diff = "";
		$this->year = "";
		$this->last_lookup = "";
		$this->mb_verified = "";
		$this->mb_track_id = "";
		$this->_update_id3 = "";
		$this->added = "";
		$this->_archived = "";
		
		$this->database = new database();
		$this->_PK = 'id';
		$this->_PKs = array('id');
		$this->_table = 'tracks';
		$this->_data_format = 'php';
		$this->_labels = array(); 
		$this->_form_label_ids = array();
		$this->_table_data = array(
			'albums'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'artists'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_accounts'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_spool_items'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'cached_trms'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'dead_sources'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'genres'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'hosts'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'sources'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'track_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'tracks'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'type_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
		);

		$this->_field_descs['id'] = array ("pk" => "1", "auto" => "1", "type" => "int(10) unsigned", "length" => "10", "gen_type" => "int");
		$this->_field_descs['trm'] = array ("type" => "varchar(50)", "length" => "50", "gen_type" => "string");
		$this->_field_descs['tracknum'] = array ("type" => "int(2) unsigned zerofill", "length" => "2", "gen_type" => "int");
		$this->_field_descs['name'] = array ("type" => "varchar(255)", "length" => "255", "gen_type" => "string");
		$this->_field_descs['artist_id'] = array ("type" => "int(10) unsigned", "length" => "10", "fk" => "artist", "gen_type" => "int");
		$this->_field_descs['album_id'] = array ("type" => "int(10) unsigned", "length" => "10", "fk" => "album", "gen_type" => "int");
		$this->_field_descs['genre_id'] = array ("type" => "int(10) unsigned", "length" => "10", "fk" => "genre", "gen_type" => "int");
		$this->_field_descs['duration'] = array ("type" => "time", "gen_type" => "datetime");
		$this->_field_descs['bpm'] = array ("type" => "float unsigned", "gen_type" => "number");
		$this->_field_descs['volume_diff'] = array ("type" => "float unsigned", "gen_type" => "number");
		$this->_field_descs['year'] = array ("type" => "int(4)", "length" => "4", "gen_type" => "int");
		$this->_field_descs['last_lookup'] = array ("type" => "date", "gen_type" => "datetime");
		$this->_field_descs['mb_verified'] = array ("type" => "enum('n','y')", "default" => "n", "values" => array('n','y',), "gen_type" => "enum");
		$this->_field_descs['mb_track_id'] = array ("type" => "varchar(150)", "length" => "150", "gen_type" => "string");
		$this->_field_descs['_update_id3'] = array ("type" => "enum('n','y')", "default" => "n", "values" => array('n','y',), "gen_type" => "enum");
		$this->_field_descs['added'] = array ("type" => "datetime", "gen_type" => "datetime");
		$this->_field_descs['_archived'] = array ("type" => "enum('n','y')", "default" => "n", "values" => array('n','y',), "gen_type" => "enum");

	}//__constructor
	
	

	
	
	/**
	 * @return bool - false on fail, new ID on success, or true if no auto-inc primary key
	 * @desc This generic method enters all the current values of the properties into the database as a new record
	 */
	function add() {
		
		if($this->id != (int)$this->id && $this->id!='NOW()' && $this->id!='NULL'){
			trigger_error("wrong type for track->id",E_USER_WARNING);
			settype($this->id,"int");
		}//IF


		if($this->tracknum != (int)$this->tracknum && $this->tracknum!='NOW()' && $this->tracknum!='NULL'){
			trigger_error("wrong type for track->tracknum",E_USER_WARNING);
			settype($this->tracknum,"int");
		}//IF


		if($this->artist_id != (int)$this->artist_id && $this->artist_id!='NOW()' && $this->artist_id!='NULL'){
			trigger_error("wrong type for track->artist_id",E_USER_WARNING);
			settype($this->artist_id,"int");
		}//IF


		if($this->album_id != (int)$this->album_id && $this->album_id!='NOW()' && $this->album_id!='NULL'){
			trigger_error("wrong type for track->album_id",E_USER_WARNING);
			settype($this->album_id,"int");
		}//IF


		if($this->genre_id != (int)$this->genre_id && $this->genre_id!='NOW()' && $this->genre_id!='NULL'){
			trigger_error("wrong type for track->genre_id",E_USER_WARNING);
			settype($this->genre_id,"int");
		}//IF


		if($this->year != (int)$this->year && $this->year!='NOW()' && $this->year!='NULL'){
			trigger_error("wrong type for track->year",E_USER_WARNING);
			settype($this->year,"int");
		}//IF


		if(!in_array($this->mb_verified,$this->_field_descs['mb_verified']['values']) && $this->mb_verified!='NULL') {
			if($this->mb_verified!='') trigger_error("Invalid enum value ".$this->mb_verified." for track->mb_verified, using default",E_USER_WARNING);
			$this->mb_verified = $this->_field_descs['mb_verified']['default'];
		}//IF

		if(!in_array($this->_update_id3,$this->_field_descs['_update_id3']['values']) && $this->_update_id3!='NULL') {
			if($this->_update_id3!='') trigger_error("Invalid enum value ".$this->_update_id3." for track->_update_id3, using default",E_USER_WARNING);
			$this->_update_id3 = $this->_field_descs['_update_id3']['default'];
		}//IF

		if(!in_array($this->_archived,$this->_field_descs['_archived']['values']) && $this->_archived!='NULL') {
			if($this->_archived!='') trigger_error("Invalid enum value ".$this->_archived." for track->_archived, using default",E_USER_WARNING);
			$this->_archived = $this->_field_descs['_archived']['default'];
		}//IF

		
		$raw_sql  = "INSERT INTO tracks (`trm`, `tracknum`, `name`, `artist_id`, `album_id`, `genre_id`, `duration`, `bpm`, `volume_diff`, `year`, `last_lookup`, `mb_verified`, `mb_track_id`, `_update_id3`, `added`, `_archived`)";
		
		$raw_sql.= " VALUES ('".$this->database->escape($this->trm)."', '".$this->database->escape($this->tracknum)."', '".$this->database->escape($this->name)."', '".$this->database->escape($this->artist_id)."', '".$this->database->escape($this->album_id)."', '".$this->database->escape($this->genre_id)."', '".$this->database->escape($this->duration)."', '".$this->database->escape($this->bpm)."', '".$this->database->escape($this->volume_diff)."', '".$this->database->escape($this->year)."', '".$this->database->escape($this->last_lookup)."', '".$this->database->escape($this->mb_verified)."', '".$this->database->escape($this->mb_track_id)."', '".$this->database->escape($this->_update_id3)."', '".$this->database->escape($this->added)."', '".$this->database->escape($this->_archived)."')";
		
		$raw_sql = str_replace("'NOW()'", "NOW()", $raw_sql);		//remove quotes
		$sql = str_replace("'NULL'", "NULL", $raw_sql);			//remove quotes
		
		
		if ($this->database->query($sql)) {
			$this->id = $this->database->InsertedID;
			
			return $this->database->InsertedID;
		
		}else{
			return false;
		}
	}//add
	
	
	
	/**
	 * @return unknown
	 * @desc This generic method updates the database to reflect the current values of the objects properties
	 */
	function update()
	{
	
		if($this->id != (int)$this->id && $this->id!='NOW()' && $this->id!='NULL'){
			trigger_error("wrong type for track->id",E_USER_WARNING);
			settype($this->id,"int");
		}//IF


		if($this->tracknum != (int)$this->tracknum && $this->tracknum!='NOW()' && $this->tracknum!='NULL'){
			trigger_error("wrong type for track->tracknum",E_USER_WARNING);
			settype($this->tracknum,"int");
		}//IF


		if($this->artist_id != (int)$this->artist_id && $this->artist_id!='NOW()' && $this->artist_id!='NULL'){
			trigger_error("wrong type for track->artist_id",E_USER_WARNING);
			settype($this->artist_id,"int");
		}//IF


		if($this->album_id != (int)$this->album_id && $this->album_id!='NOW()' && $this->album_id!='NULL'){
			trigger_error("wrong type for track->album_id",E_USER_WARNING);
			settype($this->album_id,"int");
		}//IF


		if($this->genre_id != (int)$this->genre_id && $this->genre_id!='NOW()' && $this->genre_id!='NULL'){
			trigger_error("wrong type for track->genre_id",E_USER_WARNING);
			settype($this->genre_id,"int");
		}//IF


		if($this->year != (int)$this->year && $this->year!='NOW()' && $this->year!='NULL'){
			trigger_error("wrong type for track->year",E_USER_WARNING);
			settype($this->year,"int");
		}//IF


		if(!in_array($this->mb_verified,$this->_field_descs['mb_verified']['values']) && $this->mb_verified!='NULL') {
			if($this->mb_verified!='') trigger_error("Invalid enum value ".$this->mb_verified." for track->mb_verified, using default",E_USER_WARNING);
			$this->mb_verified = $this->_field_descs['mb_verified']['default'];
		}//IF

		if(!in_array($this->_update_id3,$this->_field_descs['_update_id3']['values']) && $this->_update_id3!='NULL') {
			if($this->_update_id3!='') trigger_error("Invalid enum value ".$this->_update_id3." for track->_update_id3, using default",E_USER_WARNING);
			$this->_update_id3 = $this->_field_descs['_update_id3']['default'];
		}//IF

		if(!in_array($this->_archived,$this->_field_descs['_archived']['values']) && $this->_archived!='NULL') {
			if($this->_archived!='') trigger_error("Invalid enum value ".$this->_archived." for track->_archived, using default",E_USER_WARNING);
			$this->_archived = $this->_field_descs['_archived']['default'];
		}//IF

		$raw_sql  = "UPDATE tracks SET ";
		$raw_sql.= "`trm`='".$this->database->escape($this->trm)."', `tracknum`='".$this->database->escape($this->tracknum)."', `name`='".$this->database->escape($this->name)."', `artist_id`='".$this->database->escape($this->artist_id)."', `album_id`='".$this->database->escape($this->album_id)."', `genre_id`='".$this->database->escape($this->genre_id)."', `duration`='".$this->database->escape($this->duration)."', `bpm`='".$this->database->escape($this->bpm)."', `volume_diff`='".$this->database->escape($this->volume_diff)."', `year`='".$this->database->escape($this->year)."', `last_lookup`='".$this->database->escape($this->last_lookup)."', `mb_verified`='".$this->database->escape($this->mb_verified)."', `mb_track_id`='".$this->database->escape($this->mb_track_id)."', `_update_id3`='".$this->database->escape($this->_update_id3)."', `added`='".$this->database->escape($this->added)."', `_archived`='".$this->database->escape($this->_archived)."'";
		$raw_sql.= " WHERE 1

		AND id = '$this->id' ";
		
		$raw_sql = str_replace("'NOW()'", "NOW()", $raw_sql);		//remove quotes
		$sql = str_replace("'NULL'", "NULL", $raw_sql);			//remove quotes
		
		$this->database->query($sql);
		
		//return($this->id);		<-- used to be this, but should not effect anything? rs 12/08/04
		return true;
		
	}//Update
	
	
	
	/////////////////////////////////////
	//	set($fieldname)
	//	Rob S - 08/11/03
	/**
	* @return bool
	* @param string $fieldname		-	The exact name of the field in the table / object property
	* @desc Sets individual fields in the record, allowing special cases to be executed (eg. sess_expires), and leaving others unchanged.
 	*/
	function set($fieldname) {
		
		//define the SQL to use to UPDATE the field...
		if ($this->_field_descs[$fieldname]['gen_type'] == 'int' || $this->$fieldname == "NULL" || $this->$fieldname == "NOW()")
			$sql = "UPDATE tracks SET $fieldname = ".$this->$fieldname;
		else
			$sql = "UPDATE tracks SET $fieldname = '".$this->database->escape($this->$fieldname)."'";
		
		
		//Now add the WHERE clause
		$sql.= " WHERE 1

		AND id = '$this->id' ";
		
		if ($this->database->query($sql))
			return true;
		else
			return false;
		
	}//set
	
	
	/**
	* @return bool
	* @param string $fieldname		-	The exact name of the field in the table / object property
	* @param string $value		-	The value of the field in the table / object property
	* @desc Wrapper that calls setProperties for the supplied pair and calls set()
 	*/
	function setField($field,$value)
	{
		$this->setProperties(array($field=>$value));
		return($this->set($field));
	}
	
	
	/**
	 * @return void
	 * @param int $id		-	primary key of record

	 * @param int 		-	id of record to delete
	 * @desc This generic method deletes a specified record from the database
	 */
	function delete($id)
	{
		$sql = "DELETE FROM tracks WHERE 1

		AND id = '$id' ";
		
		if ($this->database->query($sql))
			return true;
		else
			return false;
		
	}//delete
	
	
	
	/**
	 * @return mixed	-	The number of rows found, or FALSE on query fail
	 * @param string $where = ""		-	The Where clause SQL
	 * @param string $order = ""		-	The Order By SQL
	 * @param string $limit = ""		-	The Limit SQL
	 * @desc This generic method runs a database query with the optional WHERE statement, sorted as defined in the global $CONF array or overridden with a order parameter. There is also an optional limit parameter
	 */
	function getList($where="", $order="", $limit="")
	{
		if(!$order) $order = "order by tracknum";
		$select = "SELECT tracks.* FROM tracks ";
		if ($this->database->query("$select $where $order $limit")) {
			return($this->database->RowCount);
		}else{
			return false;
		}//IF
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
			/*if (empty($tmp['id']))		//something wrong if PKs are missing
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
				$this->DN = "$this->id";
			return true;
			
		}
		else {
			return false;
		}
	}//getNext
	
	
	
	
	/**
	 * @return unknown
	 * @param int $id		-	primary key of record

	 * @desc Extracts the requested record from the database and puts it into the properties of the object
	 */
	function get($id)
	{ 
		
		$sql = "WHERE 1
		AND id = '$id'";
		
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
				
				return $this->id;		//single PK, so returns pk value
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
					
	                        $child->_setPropertiesLinkages("track", $this->id, array_keys($value));
                        
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
			print "<option value=\"$this->id\"";
			if($this->id == $value) print " SELECTED";
			print ">$this->DN</option>\n";
			$used[] = $this->id;
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
				
						$html .= $fk->createMatrix($input_name,"track",$this->id);
						
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
						(isset($CONF['track'][$property]['max']) && $CONF['track'][$property]['max']==1) ){		//or setting the max value to 1 presumes a boolean
					$html.= "<input type=\"radio\" name=\"$input_name\" value=\"1\" id=\"$html_id\"";
					if($property_value)	//allow any possible value for True
						$html.= " checked";
					$html.= " $extra>Yes ";
					
					$html.= "<input type=\"radio\" name=\"$input_name\" value=\"0\" id=\"$html_id\"";
					if(!$property_value)	//allow any possible value for True
						$html.= " checked";
					$html.= " $extra>No";
					
					break;	//escape SWITCH statement
					
				}elseif (isset($CONF['track'][$property]['max']) && $CONF['track'][$property]['max']){
					$min = ($CONF['track'][$property]['min'])? $CONF['track'][$property]['min'] : 0;
					$step = ($CONF['track'][$property]['step'])? $CONF['track'][$property]['step'] : 1;
					if ($empty=='-None-')
						$empty = '--';
					$html.= createNumberSelect($input_name, $property_value, $min, $CONF['track'][$property]['max'], $step, $empty);
					break;	//escape SWITCH statement
				}//IF integer is a Boolean
				
				//ELSE .... carry on and treat as a STRING.....
				
			  case 'string' :
				preg_match ("/\((\d+),?(\d+)?\)/", $this->_field_descs[$property]['type'], $matches);		//get field length
				if (preg_match ("/decimal/", $this->_field_descs[$property]['type']) )		//decimal
					$maxlength = $matches[1] + $matches[2] + 1;	//need to add space for decimalpoint!
				else
					$maxlength = $matches[1];
				
				if (isset($CONF['track'][$property]['size']))
					$size = $CONF['track'][$property]['size'];
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
					
					if (isset($CONF['track'][$property]['past']))
						$past = $CONF['track'][$property]['past'];
					
					if (isset($CONF['track'][$property]['future']))
						$future = $CONF['track'][$property]['future'];
					
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