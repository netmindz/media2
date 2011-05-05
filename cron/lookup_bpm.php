<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

$wav = "/tmp/lookupbpm." . getmypid() . ".wav";

$db = new database();

$tracks = new track();
$tracks->getList("where bpm=0","order by rand()");
while($tracks->getNext()) {
	if($tracks->bpm > 0) {
		print ".";
		continue;
	}
	$source = new source();
	if($source->getTrackList($tracks)) {
		$result = array();
		$source->getNext();
		print $source->path . "\t";
		#$cmd =  "bpmcount ".escapeshellarg($source->path)." | tail -n 1 | awk '{ print $1}'";
		$cmd = "mplayer -endpos 180 -ao pcm:file=$wav ".escapeshellarg($source->path);
		exec($cmd . " 2>&1", $result, $return);
		if($return) {
			print "Command line: $cmd\n";	
			print_r($result);
			continue;
		}
		$cmd =  "soundstretch $wav -bpm 2>&1 | tee /tmp/soundstretch.log | grep 'Detected BPM rate ' | awk '{ print $4}'";
		$bpm = trim(`$cmd`);
		if($bpm > 0) {
			print $bpm . "\n";
			$track = $source->getTrack();
			$track->setProperties(array('bpm'=>$bpm));
			$track->update();
			if(strtolower($source->type) == "mp3") {
				exec("id3v2 --TBPM $bpm " .  escapeshellarg($source->path));
			}
			elseif(strtolower($source->type) == "ogg") {
				exec("vorbiscomment -a -t \"BPM=$bpm\" " .  escapeshellarg($source->path));
			}
			else {
				print "Can't update metadata for $source->type\n";
			}
		}
		else {
			print "FAILED\n";
		}
		@unlink($wav);
	}
}	
?>
