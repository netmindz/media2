<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$db = new database();

foreach(array("Unknown","Various","VA","Various Artists","Other") as $name) {
	foreach($track_type_list as $type)  {

		$typeObj = new $type();
		if($typeObj->getByName($name)) {
			$db->query("delete from type_prefs where type='$type' and type_id=" . $typeObj->id );
			print "Deleted $db->RowCount type preferences for $name $type\n";
		}
	}
}	
?>
