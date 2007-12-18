<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$tracks = new track();
$tracks->getList("where name like '% - %'");
while($tracks->getNext()) {
	if(ereg('^([0-9]+) - ',$tracks->name,$matches)) {
			$track = new track();
			$track->get($tracks->id,"addslashes");
			$track->name = str_replace($matches[0],"",$track->name);
			$track->tracknum = $matches[1];
			$track->update();
			print "Changing '$tracks->name' to '$track->name' track number '$matches[1]'\n";
	}
	else {
		print "parse fail: $tracks->name\n";
	}
	
}
?>
