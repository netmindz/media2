<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$artists = new artist();
$artists->getList();
while($artists->getNext()) {
	$artist_ids[$artists->name] = $artists->id;
}

$va_id = $artist_ids['Various Artists'];

if(!$va_id) exit("failed to find artist 'Various Artists'");

print "va_id=$va_id\n";

$db = new database();
$artists->getList("where name like 'Various%'");
while($artists->getNext()) {
   $db->query("update tracks set artist_id=$va_id where artist_id=$artists->id");
	if($artists->id != $va_id) $db->query("delete from artists where id=$artists->id");
}


$tracks = new track();
$tracks->getList("where artist_id=$va_id");
while($tracks->getNext()) {
	$artist_id = 0;
	if(ereg('^/?([0-9]*) ?-? ?(.+) [/-] (.+)$',$tracks->name,$matches)) {
		$artist = $matches[2];
		$artist_id = $artist_ids[$artist];
		$name = $matches[3];
		if(!$artist_id) {
			$artist = $matches[3];
			$artist_id = $artist_ids[$artist];
			$name = $matches[2];
		}
		if($artist_id) {
			$track = new track();
			$track->get($tracks->id,"addslashes");
			$track->name = addslashes($name);
			$track->artist_id = $artist_id;
			$track->update();
			print "Changing '$tracks->name' to '$name' by '$artist'\n";
		}
	}
	else {
		print "parse fail: $tracks->name\n";
	}
	
}
?>
