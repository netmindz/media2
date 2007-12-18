<?
exit("screwed");

ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$track_list = new track();
$track = new track();
$track_list->getList("where mb_verified !='y' and last_lookup < date_sub(NOW(), interval 15 day)","order by added desc");
while($track_list->getNext()) {
	// hack - confilicting db resource
	$track->get($track_list->id);

	$source = new source();
	if($source->getBestSource($track->id)) {
		if($track->lookupTRM($source->path, "MB_ONLY")) {
			print "lookup $track->trm ok\n";
			$track->update("addslashes");
		}
		else {
			print "lookup $track->trm FAILED\n";
			$track->setField("last_lookup",date("Y-m-d"));
		}
	}
	else {
		print "no source for $track->id\n";
	}
}

/*
$track_list->getList("where name=''");
while($track_list->getNext()) {
	$track->get($track_list->id,"addslashes");
	$source = new source();
	if($source->getBestSource($track->id)) {
		print "analyse source for $track->id $source->path\n";
	}
}
*/

?>
