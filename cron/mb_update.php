<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

#set_time_limit(0);

function mb_find($type,$name)
{
	$name = strtolower($name);
	exec("find$type \"$name\"",$results);
#	print_r($results);
	$list = array();
	foreach($results as $line) {
		if(eregi("$type: '(.+)'",$line,$matches)) {
			$last_item = strtolower($matches[1]);
		}
		elseif(eregi("${type}Id: '(.+)'",$line,$matches)) {
			$list[$last_item] = $matches[1];
		}
		else {
			$last_item = "";
		}
	}
#	print_r($list);
	if(isset($list[$name])) return($list[$name]);
}

// albums can be by a specific artist
// $type_list = array("artist","album");
$type_list = array("artist");

foreach($type_list as $type) {

	$list = new $type();
	$count = $list->getList("where mb_id=''","order by rand()");
	print "Finding music brainz IDs for $count ${type}s\n ";
	while($list->getNext()) {
		print "searching " . strtolower($list->name) . "\t";
		$mb_id = mb_find($type,$list->name);
		if($mb_id) {
			print " updating to be $mb_id\n";
			$item = new $type();
			$item->get($list->id,"addslashes");
			$item->mb_id = $mb_id;
			$item->update();
			print ".";
		}
		else {
			print "not found\n";
		}
	}
}

?>
