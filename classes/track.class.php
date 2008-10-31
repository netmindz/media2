<?
require_once("artist.class.php");
require_once("album.class.php");
require_once("genre.class.php");

require("track_template.php");

class track extends track_template {
		
	function lookupOrAdd($trm,$path)
	{
#		print "Looking up $trm for $path\n";
		if($this->getByOther(array('trm'=>$trm))) {
			$this->lookupTRM($path);
			return(true);
		}
		else {
			$this->trm = $trm;
			if($this->lookupTRM($path)) {
				$this->added = "NOW()";
				return($this->add("addslashes"));
			}
		}
		
	}		

	function lookupTRM($path, $mb_only="")
	{
		$details = $this->lookupMeta($path);
		exec("gettrm $this->trm",$results);
		print "~";
		
		$trm_details_list = array();
		if(count($results) && trim($results[0]) == "That TRM is not in the database") {
			print "Uknown TRM: $this->trm\n";
			$results = array();
			if($mb_only) return(0);
		}
		else {
			$use_details = false;	
			foreach($results as $line) {
				if(eregi('[ ]*([^:]+): \'?(.+)$',$line,$matches)) {
					// it's late ...
					$matches[2] = ereg_replace('\'$',"",$matches[2]);
					$key = 		strtolower($matches[1]);
					$value = $matches[2];
					$version[$key]++;
					$trm_details_list[$version[$key]][$key] = $matches[2];
				}
			}
			
#			print_r($trm_details_list);	
			foreach($trm_details_list as $trm_details_key=>$trm_details) {
				foreach($trm_details as $key=>$value) {
					$format_method = "format$key";
						if(method_exists($this,$format_method)) {
						$value = $this->$format_method($value);
							$details[$key] = $value;
					}
	
						$perc = FuzzySearch::calculateLevenshteinPercentage($value,$details[$key]);
#					print "perc = $perc [$key]($value/$details[$key])\n";
						$percs[$key] = $perc;
				}
				if( (($percs['artist'] < 20)&&($percs['album'] < 20)&&($percs['track'] < 20)) || (!$details['title']) ) {
					$use_details = $trm_details_key;
				}
			}
			if($use_details) {
				print "\n\n *** found MB entry, merging data\n";
				#print_r($details);
				#print_r($trm_details_list[$use_details]);
				
				$details = array_merge($details,$trm_details_list[$use_details]);
				$details['mb_verified'] = 'y';
				print "  *** Givies\n";
				print_r($details);
				print "  ***\n";
			}
			else {
				$details['mb_verified'] = 'n';
				print "use details is false\n";
			}
		}


		print_r($details);
		#print_r($results);
		#print_r($trm_details_list);

		// print_r($details);
		if(trim($details['title']) != "") {
			if($details['mb_verified'] == 'y' || $mb_only == "") {
				$this->saveDetails($details,$addslashes);
				return(true);
			}
			else {
				return(false);
			}
		}
		else {
			print "\nEAK !!!!\n";
			print "failed to find any details for $path\n\n";
			#$details['track'] = basename($path);
			#$this->saveDetails($details,$addslashes);
			#return(true);
		}
			
		
	
	}
	
	function lookupMeta($path)
	{
		print "lookupMeta($path)\n";
		$details = array("album"=>'unknown','artist'=>'unknown','tracknum'=>0);
		list($type) = array_reverse(explode(".",$path));
		$type = strtolower($type);
		if($type == "ogg") {
			exec("ogginfo \"$path\"",$results);
			foreach($results as $line) {
				if(ereg('([^=]+)=(.+)$',$line,$matches)) {
					$details[strtolower(trim($matches[1]))] = $matches[2];
				}
				if(ereg("Playback length: (.+)",$line,$matches)) {
					$details['duration'] = "00:" .ereg_replace('\.[0-9]*s',"", $matches[1]);
					$details['duration'] = "00:" .ereg_replace(':[0-9]*m',"", $details['duration']);
					print "duration=" . $details['duration'] . "\n";
				}
			}
						
			$details['tracknum'] = $details['tracknumber'];
			unset($details['tracknumber']);
			
			$details['year'] = $details['date'];
			unset($details['date']);
		}
		elseif($type == "mp3") {
			
			# Try ID3v2 first as mp3info also gives duration
			exec("mp3info -p \"artist=%a\nalbum=%l\ntitle=%t\ngenre=%g\nduration=00:%m:%s\" \"$path\"",$results);
			foreach($results as $line) {
				if(ereg('^([^=]+)=(.+)$',$line,$matches)) {
					$details[$matches[1]] = $matches[2];
				}
			}
			unset($results);
			
			$map = array(
			'Title/songname/content description'=>'title',
			'Lead performer(s)/Soloist(s)'=>'artist',
			'Album/Movie/Show title'=>'album',
			'Track number/Position in set'=>'tracknum',
			'Content type'=>'genre',
			'Year'=>'year',
			);
			exec("id3info \"$path\"",$results);
			foreach($results as $line) {
				if(ereg('\((.+)\): ([^(].+)',$line,$matches)) {
					$details[$map[$matches[1]]] = $matches[2];
				}
			}
			list($details['tracknum']) = explode("/",$details['tracknum']);
#			print_r($details);
#			print_r($results);
		}
		else {
				print "get_track_details does not support the filetype of $type\n";
		}

		if(ereg('^([0-9]+)[ .-]*(.+)',$details['title'],$matches)) {
			print "Stripping track number from title " . $details['title'] . "\n";
			#print_r($matches);
			$details['tracknum'] = $matches[1];
			$details['track'] = $matches[2];
		}

		foreach($details as $key=>$value) {
			$format_method = "format$key";
			if(method_exists($this,$format_method)) {
				$details[$key] = $this->$format_method($value);
			}
		}
		return($details);		
	}
	
	function formatDuration($duration)
	{
			#print "\tpre formatDuration($duration)\n";
			$pre_duration = $duration;
			$s_in_min = 60;
			$s_in_hour = 60 * $s_in_min;
			if(ereg("([0-9]+) ms",$duration,$matches)) {
				$seconds = $matches[1] / 1000;
			}
			elseif(ereg('^([0-9]+):([0-9]+):([0-9]+)$',$duration,$matches)) {
				$seconds = $matches[3];
				$seconds += ($matches[2] * $s_in_min);
				$seconds += ($matches[1] * $s_in_hour);
			}
			else {
				print "duration parse fail for $duration\n";
				print "\tdefaulting to 00:00:00\n";
				$duration = "00:00:00";
				sleep(3);
			}
			// HACK !!!!! date('H:i:s',0) returns 01:00:00 not 00:00:00
			$duration = date('H:i:s',$seconds);
			$parts = explode(":",$duration);
			$parts[0] = $parts[0] - 1;
			foreach($parts as $i=>$value) {
				$parts[$i] = sprintf("%02d",$value);
			}
			$duration = implode(":",$parts);

			if($parts[0] > 2) {
				sleep(5);
				print "\nEAK !!!! Track longer than two hours, probably an errror, pre=$pre_duration, post=$duration\n";
				$duration = "00:00:00";
			}
						
			#print "\tpost formatDuration($duration)\n";
			return($duration);
	}
	
	function saveDetails($details)
	{

			print "\nsaving details\n";
			print_r($details);

			
			if($details['artist']) {
				$artist = new artist();
				$artist->lookupOrAdd($details['artist']);
				$this->artist_id = $artist->id;		
			}
			else {
				$this->artist_id = 0;		
			}

			if($details['album']) {
				$album = new album();
				$album->lookupOrAdd($details['album']);
				$this->album_id = $album->id;
			}
			else {
				$this->album_id = 0;
			}

			if(isset($details['genre'])) {
				$genre = new genre();
				$genre->lookupOrAdd($details['genre']);
				$this->genre_id = $genre->id;
			}
			else {
				$this->genre_id = 0;
			}
			
			if(!isset($details['name'])) {
				$this->name = $details['title'];
			}
			if(!$this->name) {
				$this->name = $details['track'];
			}

			if(isset($details['year'])) $this->year =  $details['year'];


			$this->tracknum  = $details['tracknum'];

			if(isset($details['duration'])) $this->duration = $details['duration'];
			
			if(isset($details['mb_verified'])) $this->mb_verified = $details['mb_verified'];
			
			if(isset($details['musicbrainz_trackid'])) $this->mb_track_id =  $details['musicbrainz_trackid'];
			
			return(1);
		}

	function batchUpdate($property,$value,$where)
	{
		$this->database->Query("update tracks set $property=$value where $where");
		return($this->database->RowCount);
	}		

	function getTrackByPath($path)
	{
		$source = new source();
		if(!$source->getByOther(array("path"=>$path))) {
			echo("$path not recognised as a valid source\n");
			return(false);
		}
		
		return($this->get($source->track_id));
	}
	
	function analyse($path)
	{
		$details = array();
		exec("nice -n 5 gjay --analyze-standalone \"$path\"",$results,$error_code);
		if($error_code) {
			print "track::analyse($path)\n";
			print_r($results);
		}
		else {
			print "A";
			$meta = ereg_replace("\n","",implode("",$results));
			if(ereg('<bpm>([0-9.]+)</bpm',$meta,$matches)) {
				$this->setField('bpm',$matches[1]);
#				print "Analyse = $this->bpm for $path\n";
			}
			if(ereg('volume_diff="([0-9.]+)"',$meta,$matches)) {
				$this->setField('volume_diff',$matches[1]);
			}
		}

		if(!$this->bpm) {
			print "FAILED: analyse($path)\n";
			$this->setField('bpm',1);
		}
		else {
			return(true);
		}
	}
}
?>
