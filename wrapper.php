<?
ini_set("include_path",".:classes/:includes/:/home/www/codebase/:/usr/share/pear/");

require("standard.inc.php");
$username = get_remote_username();
 
system("echo \"username=$username request=" . $_SERVER['REQUEST_URI'] ."\" >> /tmp/wrapper.log");

if(ereg('/([0-9]+)/?\.?',$_SERVER["REQUEST_URI"],$matches)) {
	$track_id=$matches[1];
}

$track = new track();
$track->get($track_id);
if(!$track->id) exit("failed to find track");


$source = new source();
$source->getBestSource($track_id);

if(!$source->path) {
#	mail("root","404","path=$source->path id=$track_id");
	header("Location: http://flat.netmindz.net/~will/media2/splat.mp3");
	exit();
}


if(!is_readable($source->path)) {
	header("HTTP/1.0 404 Not Found");
	print "source file $source->path was not found for track $track_id";
	system("echo \"(404)\t" . addslashes($source->path) . "\" >> /tmp/music_error.log");
//	print_r($source);
	exit();
}

$track_pref = new track_pref();
$artist_pref = new type_pref("artist",$track->artist_id);
$album_pref = new type_pref("album",$track->album_id);
$genre_pref = new type_pref("genre",$track->genre_id);

$fh = fopen($source->path,'r');
$filename = basename($source->path);
$mime = mime_type($source->path);
if(!$mime) exit("no mime type");
system("echo \"user=$username $source->path opened, sending header\" >> /tmp/wrapper.log");
header("Content-Type: $mime");
header("Content-Length: ".filesize($source->path));
header("Content-Disposition: attachment;filename=" . basename($source->path));

$headers = getallheaders();
if ( isset( $headers['Range'] ) )
{
	$range =$headers['Range'];
        // The range field should look something like this:
        //      bytes=7483940-.
        // It will always start with bytes=.  We don't care about that so
        // We chop that off.  The trailing - just means from that point on.
        // we don't need that either.
        $range_len = strlen( $range );
        $pre_len = 6;
        $range = substr( $range, $pre_len, ( $range_len - $pre_len ) - 1 );
}



$chunk_size = 8192;

$chunks = 0;
$logged = 0;

$no_chunks = floor(filesize($source->path) / $chunk_size);

$mp3 = fopen($source->path, "r" );
fseek($mp3,$range);
$data = fread( $mp3, $chunk_size);
while( strlen( $data ) > 0 ) {
	if((!isset($headers['Range']))||($headers['Range'] == "bytes=0-")) {
	 	if(($chunks > 1)&&(!$logged)) {
			$track_pref->incPlayCount($track_id);
			$track_pref->updatePref($track_id,-10);

			$artist_pref->updatePref(-5,"inc");
			$album_pref->updatePref(-5,"inc");
			$genre_pref->updatePref(-5,"inc");
			
			system("echo \"log $username start $source->path\" >> /tmp/wrapper.log");
			$logged = 0.1;
		}
		elseif(($chunks > ($no_chunks/5))&&($logged < 0.2)) {
			$logged = 0.2;
			system("echo \"log $username 20% $source->path\" >> /tmp/wrapper.log");
		}
		elseif(($chunks > ($no_chunks/2))&&($logged < 0.5)) {

			$track_pref->log50percent($track_id);
			$track_pref->updatePref($track_id,7);

			$artist_pref->updatePref(4);
			$album_pref->updatePref(4);
			$genre_pref->updatePref(4);

			$logged = 0.5;
			system("echo \"log $username half $source->path\" >> /tmp/wrapper.log");
		}
		elseif($chunks == $no_chunks) {
			$track_pref->updatePref($track_id,6);

			$artist_pref->updatePref(2);
			$album_pref->updatePref(2);
			$genre_pref->updatePref(2);

			$logged = 1;
			system("echo \"log $username complete $source->path\" >> /tmp/wrapper.log");
		}
	}
	else {
		system("echo \"Sending chunk but Range is set to [" . $headers['Range'] . "] for $username $source->path\" >> /tmp/wrapper.log");
	}	
#	system("echo \"sending chunk for " . $_SERVER["REQUEST_URI"] . "\" >> /tmp/wrapper.log");
	echo $data;
	$data = fread( $mp3, $chunk_size);
	$chunks++;
}

 
?>
