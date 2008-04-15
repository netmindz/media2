<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");

foreach($CONF['rpms'] as $rpm) {
	exec("rpm -q $rpm",$result,$error);
	if($error) {
		print "ERROR: $rpm is required\n";
	#	exit(1);
	}
}

set_time_limit(0);

function scan_directory($dir,$host_id)
{
	global $CONF;
	
	$supported_types = array("ogg","mp3");

	print "\nscanning $dir ";
	if(!$hd = dir($dir)) {
		echo("failed to open $dir\n");
		return(false);
	}
	while($filename = $hd->read()) {
		$path = "$dir/$filename";
		print ".";
		if((is_dir($path))&&(!ereg('^\.',$filename))&&($filename != "shares")) {
			scan_directory($path,$host_id);
		}
		elseif(!ereg('^\.',$filename)) {
			list($type) = array_reverse(explode(".",$filename));
			$type = strtolower($type);
			if(in_array($type,$supported_types)) {
				$source = new source();
				$source->lookupOrAdd($path,$host_id);
			}
			else {
				print "skipping $filename\n";
			}
		}
	}
	$hd->close();
	print "\n";
}

function check_existing_files($host_id)
{
	$a_limit = 20;
	$a = 0;
	print "\nchecking existing files for $host_id ";
	$source = new source();
	$track = new music_list();
	$source->getList("where host_id='$host_id'");
	while($source->getNext()) {
		if(!is_file($source->path)) {	
			print $source->path . "\tDELETED\n";
			$s = new source();
			$s->delete($source->id);
		}
		else {
			# don't both loading if we have disabled the all the code in this loop
			$track->get($source->track_id);
			// TEMP *************************************
			
			if($track->TRACK_duration == "00:00:00") {
				$t = new track();
				$t->get($track->TRACK_id,"addslashes");
				$s = new source();
				$s->getBestSource($t->id);
				$details = $t->lookupMeta($s->path);
				$t->setField('duration',$details['duration']);
				print "dur=" . $t->duration . "\n";
			}
			
			if(!$track->TRACK_bpm && $a < $a_limit) {
				$t = new track();
				$t->get($track->TRACK_id,"addslashes");
				$s = new source();
				$s->getBestSource($t->id);
				if($t->analyse($s->path)) $a++;
			}
			elseif(!$track->TRACK_bpm) {
				print "skipping analyse ";
			}

			// /Temp ************************************
			
			// Disabled till i work our how to run the update only once for every source of the track in question
			/*
			if($track->TRACK__update_id3 == "y") {
				print "updating ID3";
				if($source->type == "mp3"){
					print system("mp3info -a \"$track->ARTIST_name\" -g \"$track->GENRE_name\" -l \"$track->ALBUM_name\" -n \"$track->TRACK_tracknum\" -t \"$track->TRACK_name\" \"$source->path\"");
				}
			}
			*/
			print "#";
		}
	}

}

if(!is_dir($CONF['music_base'])) exit("You have not defined a valid music_base");


print "Deleting old dead_sources ";
$dead_source = new dead_source();
$dead_source->database->query("delete from dead_sources where updated < date_sub(now(), interval 30 day)");
print $dead_source->database->RowCount;
print "\n\n";


$db = new Database();
print "Deleting tracks orphened by artist or album ... ";
$db->query("delete from tracks where artist_id not in (select id from artists) or album_id not in (select id from albums)");
print $db->RowCount . " tracks affected\n";

$db = new Database();
print "Deleting track_prefs orphened by track ... ";
$db->query("delete from track_prefs where track_id not in (select id from tracks)");
print $db->RowCount . " track_prefs affected\n";

$db = new Database();
print "Deleting type_prefs orphened by artist, album or genre ... ";
$db->query("delete from type_prefs where (type = 'artist' and type_id not in (select id from artists)) or (type = 'album' and type_id not in (select id from albums))  or (type = 'genres' and type_id not in (select id from genres)) ");
print $db->RowCount . " track_prefs affected\n";

$db = new Database();
print "Deleting sources orphened by host ";
$db->query("select host_id from sources left join hosts on host_id=hosts.id where hostname is null group by host_id");
print $db->RowCount . " missing hosts\n";
while($row = $db->getNextRow()) {
	$db2 = new Database();
	$db2->query("delete from sources where host_id=" . $row['host_id']);
	print $db2->RowCount . " sources removed\n";
}


$db = new Database();
$track = new track();
$db->query("select tracks.id from tracks left join sources on tracks.id=track_id where sources.id is null and _archived !='y'");
print "archiving stale tracks missing source\n";
while($row = $db->getNextRow()) {
	$track->get($row['id']);
	$track->setField("_archived",'y');
	print  "t" . $row['id'] . " ";
}
print "\n\n";

$db = new Database();
$source = new source();
$db->query("select sources.id as source_id from sources left join tracks on track_id=tracks.id where tracks.trm is null");
print "deleting stale sources missing track";
while($row = $db->getNextRow()) {
	$source->delete($row['source_id']);
	print  "s" . $row['source_id'] . " ";
}
print "\n\n";

$db = new Database();
$dead_source = new dead_source();
$db->query("select dead_sources.id as source_id from dead_sources left join tracks on dead_sources.notes=tracks.trm where tracks.trm is not null");
print "deleting dead sources where track now exists";
while($row = $db->getNextRow()) {
	$dead_source->delete($row['source_id']);
	print  $row['source_id'] . " ";
}
print "\n\n";

foreach(array('artist','album','genre') as $type) {
	print "Checking for ${type}s without any tracks\n";
	$db = new Database();
	$db->query("select ${type}s.id,${type}s.name,count(${type}_id) as track_count from ${type}s left join tracks on ${type}_id=${type}s.id group by ${type}s.id having track_count = 0");
	while($row = $db->getNextRow()) {
		$db2 = new Database();
		$db2->query("delete from ${type}s where id=" . $row['id']);
		print "Deleting $type " . $row['name'] . "\n";
	}
}

if((!$argv[1])||($argv[1] == "localhost")) {
	if(!$argv[1]) {
		print "scanning all hosts\n";
		$host = new host();
		$host->getOnlineHosts("order by rand()");
		while($host->getNext()) {
			$url_base = ereg_replace('/$','',$host->url_base);
			scan_directory($CONF['music_base'] . "/" . $url_base,$host->id);
			check_existing_files($host->id);
		}
	}
	$host = new host();
	$host_parts = explode(".",`hostname`);
	$hostname = array_shift($host_parts);
	print "localhost = $hostname\n";
	$host->getByOther(array("hostname"=>$hostname));
	if(!$host->id) {
		$host->setProperties(array("hostname"=>$hostname));
		$host->add();
	}
	print "scanning local host ($host->id)\n";
	scan_directory($CONF['music_base'],$host->id);
	check_existing_files($host->id);
}
else {
	$host = new host();
	if($host->getByOther(array("hostname"=>$argv[1]))) {
		print "\n\nscanning host $host->DN\n";
		$url_base = ereg_replace('/$','',$host->url_base);
		scan_directory($CONF['music_base'] . "/" . $url_base,$host->id);
		check_existing_files($host->id);
	}	
}

print "\nHacking DB\n";

$db->query("update tracks set duration=0 where duration > '2:30:00'");

print "\n\nscan complete\n";
?>
