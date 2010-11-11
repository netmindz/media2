<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$db = new database();

$tracks = new track();
$tracks->getList("where bpm=0");
while($tracks->getNext()) {
	$source = new source();
	if($source->getTrackList($tracks)) {
		$source->getNext();
		print $source->path . "\t";
		$cmd =  "bpmcount ".escapeshellarg($source->path)." | tail -n 1 | awk '{ print $1}'";
		$bpm = trim(`$cmd`);
		print $bpm . "\n";
		$track = $source->getTrack();
		$track->setProperties(array('bpm'=>$bpm));
		$track->update();
	}
}	
?>
