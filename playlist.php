<? 
require("header.php"); 

$type = $_REQUEST['type'];
if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}
else {
	$id = 0;
}

$bpm = null;
if(isset($_REQUEST['bpm'])) {
	$bpm = $_REQUEST['bpm'];
}

$track = new music_list();
if(isset($_GET['debug'])) $track->debug = $_GET['debug'];
if($type == "like") {
	$list = $track->getTypeLikePlaylist($id,25);
}
elseif($type != "random") {
	$type_pref = new type_pref($type,$id);
	$type_pref->updatePref(10);
	$list = $track->getTypeListPlaylist($type,$id);
}
else {
	$list = $track->getRandomList(20,get_remote_username(),$bpm);
}

$pls = array();
foreach($list as $details) {
	$source = new source();
	if($source->getBestSource($details['id'])) {
		# bug in xmms, been reported as 1723
		$pls[] = "#EXTINF:" . time_to_secs($details['duration']) . ",$details[artist] - $details[name]\r\nhttp://" . get_http_mount_point() ."/" . $details['id'] . "." . $source->type;
	}
	else {
		$pls[] = "# failed to find source for " . $details['id'];
	}
}

if(count($pls)) {
	header("content-type:audio/x-mpegurl");
	header("content-disposition: filename=playlist_" . $type . "_".$id.".m3u");
	print "#EXTM3U\r\n";
	print implode("\r\n",$pls);
}
else {
	print "no sources found";
}


?>
