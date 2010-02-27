<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$track = new track();
$track->getList("where _update_id3='y'");
while($track->getNext()) {
	$artist = $track->getArtist();
	$album = $track->getAlbum();
	$genre = $track->getGenre();
	print $artist->name . " - " . $track->name . " (" . $genre->name . ")\n";
	$source = new source();
	$source->getTrackList($track);
	while($source->getNext()) {
		print "\t" . $source->path . "\n";
		$source->updateID3();
	}
}

?>
