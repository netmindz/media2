<?
class pref_template
{
	var $id, $username, $file_id, $score, $updated;
	
	var $database, $lastError, $DN;
	var $_PK, $_table;
	var $_field_types;
	
	/**
	 * @return void
	 * @desc This is the PHP5 constructor. It calles the PHP4 constructor pref_template()
	 */
	function __construct()
	{
		$this->pref_template();
	}
	
	function pref_template()
	{
		$this->id = 0;
		
		$this->username = "";
		$this->file_id = "";
		$this->score = "";
		$this->updated = "";
		
		$this->database = new database();
		
		$this->_PK = "id";
		$this->_table = "prefs";
		
		$this->_table_data = array(
			'albums'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'artists'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_accounts'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'as_spool_items'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'genres'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'hosts'	=>	array ("pk"	=>	"id", "comment"	=>	"InnoDB free: 5120 kB"),
			'music'	=>	array ("pk"	=>	"id", "comment"	=>	"InnoDB free: 5120 kB"),
			'prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'sources'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'track_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'tracks'	=>	array ("pk"	=>	"id", "comment"	=>	""),
			'type_prefs'	=>	array ("pk"	=>	"id", "comment"	=>	""),
		);

		$this->_field_descs['id'] = array ("type" => "int(10) unsigned", "pk" => "1", "gen_type" => "int");
		$this->_field_descs['username'] = array ("type" => "varchar(125)", "gen_type" => "string");
		$this->_field_descs['file_id'] = array ("type" => "int(11)", "gen_type" => "int");
		$this->_field_descs['score'] = array ("type" => "int(11)", "gen_type" => "int");
		$this->_field_descs['updated'] = array ("type" => "timestamp", "gen_type" => "timestamp");

	}
	
	
	/**
	 * @return unknown
	 * @param id int
	 * @param addslashes = "" unknown
	 * @desc This generic mthod used to ex could easily tract the requested record from the database and puts it into the properties of the object
	 */
	function get($id, $addslashes = "")
	{ 
		settype($id,"int");
		$this->getList("WHERE id = $id");
		$this->getNext($addslashes);
		
		if($this->id)
			return(1);
	}//get
	
	
	
	
	/////////////////////////////////////
	//	getByOther()
	//	Rob S - 07/11/03
	/**
	 * @return bool
	 * @param $fields mixed		-	A name value associative array of fields and values
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
			
			$sql.= " AND $fieldname = '".addslashes($value)."'";
		}//FOREACH
		
		//retrieve all fields from the table and map to user object
		$this->getList($sql);
		$this->getNext();
		
		return $this->id;
	}//getByOther

	
	
	/**
	 * @return bool			-	True on Success, False otherwise
	 * @param array $properties		-	An array of the property values with which to update the object
	 * @desc sets an objects properties off the back of an array - filters out any irrelevent properties.
	 */
	function setProperties($properties)	{
		
		if(is_array($properties)) {
			$object_props = get_object_vars($this);		//retrieve array of properties
			
			foreach ($properties as $key => $value) {
				if(array_key_exists($key, $object_props))
					$this->$key = $value;
			}//FOREACH element
			
			return true;
			
		}else{	//not array
			return false;
		}//IF is array
		
	}//setProperties
	
	
	
	
	/**
	 * @return void
	 * @param addslashes int		-	If True, addslashes to all fields before adding record
	 * @desc This generic method enters all the current values of the properties into the database as a new record
	 */
	function add($addslashes=0) {
			if($this->file_id != (int)$this->file_id) trigger_error("wrong type for ->file_id",E_USER_NOTICE);
		settype($this->file_id,"int");
		if($this->score != (int)$this->score) trigger_error("wrong type for ->score",E_USER_NOTICE);
		settype($this->score,"int");

		$raw_sql  = "INSERT INTO prefs (`username`, `file_id`, `score`, `updated`)";
		if ($addslashes) {
			$raw_sql.= " VALUES ('".addslashes($this->username)."', '".addslashes($this->file_id)."', '".addslashes($this->score)."', '".addslashes($this->updated)."')";
		}else{
			$raw_sql.= " VALUES ('$this->username', '$this->file_id', '$this->score', '$this->updated')";
		}//IF slashes
		
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
	 * @param addslashes int		-	If True, addslashes to all fields before updating
	 * @desc This generic method updates the database to reflect the current values of the objects properties
	 */
	function update($addslashes=0)
	{
			if($this->file_id != (int)$this->file_id) trigger_error("wrong type for ->file_id",E_USER_NOTICE);
		settype($this->file_id,"int");
		if($this->score != (int)$this->score) trigger_error("wrong type for ->score",E_USER_NOTICE);
		settype($this->score,"int");

		$raw_sql  = "UPDATE prefs SET ";
		if($addslashes) {
			$raw_sql.= "`username`='".addslashes($this->username)."', `file_id`=".addslashes($this->file_id).", `score`=".addslashes($this->score)."";
		}else{
			$raw_sql.= "`username`='$this->username', `file_id`=$this->file_id, `score`=$this->score";
		}//IF
		
		$raw_sql.= " WHERE id='$this->id'";
		$raw_sql = str_replace("'NOW()'", "NOW()", $raw_sql);		//remove quotes
		$sql = str_replace("'NULL'", "NULL", $raw_sql);			//remove quotes
		
		$this->database->query($sql);
		return($this->id);
	}
	
	
	
	/////////////////////////////////////
	//	set($fieldname)
	//	Rob S - 08/11/03
	/**
	* @return void
	* @param string $fieldname		-	The exact name of the field in the table / object property
	* @param int $addslashes		-	If True, addslashes to the field before updating
	* @desc Sets individual fields in the record, allowing special cases to be executed (eg. sess_expires), and leaving others unchanged.
 	*/
	function set($fieldname, $addslashes=0) {
		//define the SQL to use to UPDATE the field...
		if ($this->_field_descs[$fieldname]['gen_type'] == 'int' || $this->$fieldname == "NULL" || $this->$fieldname == "NOW()")
			$sql = "UPDATE prefs SET $fieldname = ".$this->$fieldname;
		elseif ($addslashes)
			$sql = "UPDATE prefs SET $fieldname = '".addslashes($this->$fieldname)."'";
		else
			$sql = "UPDATE prefs SET $fieldname = '".$this->$fieldname."'";
		
		
		//Now add the WHERE clause
		$sql.= " WHERE id = '".$this->id."'";
		
		$this->database->query($sql);
		
		return (1);
	}//set
	
	
	
	/**
	 * @return void
	 * @param int id		-	id of record to delete
	 * @desc This generic method deletes a specified record from the database
	 */
	function delete($id)
	{
		$this->database->query("DELETE FROM prefs WHERE id = '$id'");
	}
	
	
	
	/**
	 * @return int
	 * @param where = "" unknown		-	The Where clause SQL
	 * @param order = "" unknown		-	The Order By SQL
	 * @param limit = "" unknown		-	The Limit SQL
	 * @desc This generic method runs a database query with the optional WHERE statement, sorted as defined in the global $CONF array or overridden with a order parameter. There is also an optional limit parameter
	 */
	function getList($where="", $order="", $limit="")
	{
		if(!$order) $order = "" ;
		$select = "SELECT prefs.* FROM prefs ";
		if ($this->database->query("$select $where $order $limit")) {
			return($this->database->RowCount);
		}else{
			return false;
		}
	}//getList
	
	
	
	/**
	 * @return unknown
	 * @desc This generic method gets the next result from the last database query and loads the values into the properties of the object
	 */
	function getNext($addslashes = "")
	{
		$tmp = $this->database->getNextRow();
		
		$this->DN = "";
		if($tmp["id"] != "") {
			// TODO - rewrite this bit to work with meta tables, e.g
			// class::get{field}CB
			$this = set_properties($this, $tmp, $addslashes,"get");
			
			//check for conversion method, and execute (used to transform DB props such as MySQL date into PHP friendly ones (ie unix timestamp))
			if (method_exists($this, "convertDBProperties"))
				$this->convertDBProperties('from');
			if (isset($this->name) && ($this->name))
				$this->DN = $this->name;
			elseif (isset($this->title) && ($this->title))
				$this->DN = $this->title;
			else
				$this->DN = $this->id;
			return(1);
			
		}
		else {
			return(0);
		}
	}
	
	
	/**
	 * @return int
	 * @param keyword unknown
	 * @param field_list unknown
	 * @param type = "" unknown
	 * @desc This generic method searches the comma delimited field list for the specified keyword. types are begins_with or ends_with
	 */
	function search($keyword, $field_list, $type="")
	{
		if($type != "begins_with") $start = "%";
		if($type != "ends_with") $end = "%";
		if(!is_array($field_list)) {
			$field_list = split(",", $field_list);
		}
		foreach($field_list as $key => $field) {
			$sql_array[] = "$field  like '$start$keyword$end' ";
		}
		return($this->getList("where " . implode(" OR ", $sql_array) . ""));
	}//search
	
	
	
	/**
	 * @return void
	 * @param select_name string
	 * @param value = "" unknown
	 * @param where = "" unknown
	 * @param extra = "" unknown
	 * @desc This generic method will create a select box
	 */
	function select($select_name, $value="", $where="", $extra="")
	{
		print "<select name=\"$select_name\" $extra>\n";
		print "<option value=\"\">-None-</option>\n";
		$this->getList($where);
		$used = array();
		while($this->getNext()) {
			print "<option value=\"$this->id\"";
			if($this->id == $value) print " SELECTED";
			print ">$this->DN</option>\n";
			$used[] = $this->id;
		}
		if((!in_array($value, $used))&&($value)) print "<option value=\"$value\" selected>$value</option>\n";
		print "</select>\n";
	}
	
	/**
	 * @return see select function
	 * @desc Alias for select
	 */
	function createSelect($select_name, $value="", $where="", $extra="") {
		$this->select($select_name, $value, $where, $extra);
	}//createSelect
	
	
	
	
	
	/**
	 * @return string	-	The field label
	 * @param string $property			-	The name of the object property to be used
	 * @desc This generic method will create a user friendly name for an objects property name suitable for use on a form
	 */
	function createFormLabel($property)
	{
		
		//check for specified label name
		if(isset($this->_labels[$property])){
			return($this->_labels[$property]);
		}
		else {	//generate label from fieldname
			if(isset($this->_field_descs[$property]['fk'] )) {
				$property = $this->_field_descs[$property]['fk'];
			}
			$name = str_replace("_"," ",$property);
			$name = ucwords($name);
			
			return($name);
		}
	}//createFormLabel
	
	
	
	
	/**
	 * @return string
	 * @param string $property			-	The name of the object property to be used
	 * @param string $input_name		-	The name of the HTML input field, usually an array ie. properties[]
	 * @param unknown $value			-	The current value of the input field
	 * @param string $empty 			-	Defines the text for the "Empty" option in Select and other input boxes
	 * @param string $extra				-	Any extra parameters to be set inside the form object, or javascript etc.
	 * @desc This generic method will create a HTML form object dependent on the type of field submitted.
	 */
	function createFormObject ($property, $input_name, $value="***ROGUE_VALUE***", $empty="-None-", $extra="") {
		$html = "";
		$property_value = ($value=='***ROGUE_VALUE***')? $this->$property : $value;	//check if $value was passed or not
		if(is_array($property_value)){
			foreach ($property_value as $arr_name => $arr_value)
				$property_value[$arr_name] = htmlspecialchars($arr_value);
		}else{
			$property_value = htmlspecialchars($property_value);
		}//IF
		
		if (substr($input_name, -2)=="[]")	//autocomplete the array key, if none was specified!
			$input_name = substr($input_name, 0,-2)."[$property]";
		
/*		
		if (strpos(strtolower($property), "_fk") !== FALSE) {	//we have found a foreign key
				$table_key = substr($property, 0, -3);
			
				foreach ($this->_table_data as $tmp_tbl=>$props) {
					if ($props['pk'] == $table_key)
						$fk_class = premier_table_to_class($tmp_tbl);
				}//FOREACH
*/
		if (isset($this->_field_descs[$property]['fk'] )){	//we have found a foreign key
			$fk_class = $this->_field_descs[$property]['fk'];
			
			if (!$fk_class) {
				$html.= "couldnt match foreign key $fieldname to table";
			}else{
				$fk_class = new $fk_class();
				$html.= $fk_class->createSelect($input_name, $property_value);		//NB: available third parameter is $where
			}//IF foreign key matched to table
			
			
		} else {	//not a Foreign Key field...
			switch ($this->_field_descs[$property]['gen_type']) {
			  case 'int' :
				preg_match ("/\((\d+)\)/", $this->_field_descs[$property]['type'], $matches);		//get field length
				if ($matches[1] ==1){	//a tiny int of display length 1 char is presumed to be a boolean
					$html.= "<input type=\"checkbox\" name=\"$input_name\" value=\"1\"";
					if($property_value)	//allow any possible value for True
						$html.= " checked";
					$html.= " $extra>";
					break;	//escape SWITCH statement
				}//IF integer is a Boolean
				
				//ELSE .... carry on and treat as a STRING.....
				
				
			  case 'string' :
				preg_match ("/\((\d+)\)/", $this->_field_descs[$property]['type'], $matches);		//get field length
				$maxlength = $matches[1];
				
				if ($maxlength <= 30)
					$size = $maxlength+1;
				elseif ($maxlength <= 50)
					$size = 40;
				else
					$size = 60;
				
				if (strpos(strtolower($property), "password") !== FALSE || strpos(strtolower($property), "pwd") !== FALSE)
					$html.= "<input type=\"password\" name=\"$input_name\" value=\"$property_value\" size=\"10\" maxlength=\"20\" $extra>";
				else
					$html.= "<input type=\"text\" name=\"$input_name\" value=\"$property_value\" size=\"$size\" maxlength=\"$maxlength\" $extra>";
				break;
				
				
			  case 'text' :
				//get field length
				if (strpos($this->_field_descs[$property]['type'], "medium") || strpos($this->_field_descs[$property]['type'], "long")) {
					$cols = 50;
					$rows = 12;
				}else{
					$cols = 40;
					$rows = 6;
				}//IF
				
				$html.= "<textarea name=\"$input_name\" cols=\"$cols\" rows=\"$rows\" $extra>$property_value</textarea>\n";
				break;
				
				
			  case 'enum' :
				$enums = explode("','", substr($this->_field_descs[$property]['type'], 6, -2) );
				
				if (sizeof($enums) < 4){

					if (sizeof($enums)==1){ //Use a check box as we only have one option
						foreach($enums as $i=>$type) {
							$html.= "<input type=\"checkbox\" name=\"$input_name\" value=\"$type\"";
							if(strstr($property_value, $type))
								$html.= " checked";
							$html.= " $extra> $type&nbsp;&nbsp; ";
						}//FOREACH
						$html.= "\n";
						
					}else{ //use Radio buttons
						if($empty!="-None-") {
							$html.= "<input type=\"radio\" name=\"$input_name\" value=\"\"";
							if(empty($property_value))
								$html.= " checked";
							$html.= " $extra> $empty&nbsp;&nbsp; ";
						}//IF
						
						foreach($enums as $i=>$type) {
							$html.= "<input type=\"radio\" name=\"$input_name\" value=\"$type\"";
							if($property_value == $type)
								$html.= " checked";
							$html.= " $extra> $type&nbsp;&nbsp; ";
						}//FOREACH
						$html.= "\n";
					}//IF only one Enum option
					
				}else{	//many options so use SELECT list
					
					$html.= "<select name=\"$input_name\" $extra>\n";
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
				//check for date part...
				if (strpos($this->_field_descs[$property]['type'], "date") !== FALSE) {
					if (strpos(strtolower($property), "dob") !== FALSE || strpos(strtolower($property), "birth") !== FALSE) {
						$past = 85;
						$future = 0;
					}else{
						$past = 5;
						$future = 5;
					}//IF date of birth field
					
					$html.= createDateSelect($input_name, $property_value, $past, $future);
					$separator = " @ ";
				}//IF date
				
				
				//check for time part...
				if (strpos($this->_field_descs[$property]['type'], "time") !== FALSE){
					$html.= $separator.createTimeSelect($input_name, $property_value, 5);
				}//IF date
				
				break;
				
			  default:
				$html.= "ERROR - outside switch for MySQL type '".$this->_field_descs[$property]['type']."' from field '$property' in createFormObject()";
				$html.= $this->_field_descs[$property]['gen_type'];
				$html.= "<br>\nPossibly a misspelt fieldname?\n";
				break;
			}//SWITCH
		}//IF foreign key field
		
		return $html;
	}//createFormObject
	
	
	
	/**
	 * @return void
	 * @desc This is the template PHP5 destructor. It calls $this->database->finish()
	 */
	function  __destruct()
	{
		$this->database->finish();
	}

}