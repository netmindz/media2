<? 
require("header.php"); 

$id = $_GET['id'];

$track = new music_list();
$track->get($id);

$source = new source();
$source->getBestSource($id);

header("content-type:audio/x-mpegurl");
header("content-disposition: filename=track_".$id.".m3u");
print "#EXTM3U\r\n";
print "#EXTINF:0,$track->ARTIST_name - $track->TRACK_name\r\n";
print "http://" . get_http_mount_point() . "/$id." . $source->type;
?>
