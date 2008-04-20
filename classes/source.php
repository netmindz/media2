<?
require("cached_trm.php");


require("source_template.php");

class source extends source_template {

	function lookupOrAdd($path,$host_id)
	{
		if($this->getList("where path=\"$path\"")) {
			return($this->getNext());
		}
		else {
			$dead_source = new dead_source();
			if($dead_source->isDead($path)) {
					print "\nSkipping known dead souce $path";
					return(false);
			}
			
			$this->path = $path;
			$this->host_id = $host_id;
			
			//*****************************************
			if(is_dir($this->path)) {
				$this->type = "dir";
			}
			else {
				if(ereg('\.([^.]+)$',$this->path,$matches)) {
					$this->type = $matches[1];
				}
				else {
					$this->type = "???";
				}
			}
			//*****************************************
			if($this->type != "dir") $this->getBitrate();
			
			$trm = $this->getTrm();
			
			if($trm) {
				$track = new track();
				$track->lookupOrAdd($trm,$this->path);
				$this->track_id = $track->id;
			
				if($this->track_id)	{
					$track->setProperties(array('_archived'=>'n'));
					$track->set("_archived");
					return($this->add("addslashes"));
				}
				else {
					$this->logDeadSource($path,"track_id failed",$trm);
				}
			}
			else {
				$this->logDeadSource($path,"trm failed");
			}
		}		
	}		
	
	function logDeadSource($path,$reason,$notes="")
	{
		$dead = new dead_source();
		$dead->path = $path;
		$dead->reason = $reason;
		$dead->notes = $notes;
		$dead->add("addslashes");
	}
		
	function getBitrate()
	{
		print "getBitrate()";
		if(eregi('\.mp3$',$this->path)){
			exec("id3info \"$this->path\" | grep -i bitrate",$results);
			if(eregi('bitrate\: ([0-9.]+)',$results[0],$matches)) {
				$this->bitrate = $matches[1];
			}
			else {
				print "parse fail for bitrate ";
				print_r($results);
			}
		}
		elseif(eregi('\.ogg',$this->path)) {
			exec("ogginfo \"$this->path\" | grep 'bitrate: '",$results);
			if(ereg('bitrate\: ([0-9.]+)',$results[0],$matches)) {
				$this->bitrate = $matches[1];
			}
			else {
				print "parse fail ";
				print_r($results);
			}
		}
		else {
			print "Get_Bitrate : unknown file type ($this->path)\n";
		}
		return($this->bitrate);
	}
	
	function getTRM()
	{
		exec("id3info \"$this->path\" | grep 'MusicBrainz TRM Id' | awk '{ print $10}'",$id3results);
		if(count($id3results)) {
			$trm = trim($id3results[0]);
		}
		else {
			$trm = "";
		}
		if(ereg('^[0-9a-z-]+$',$trm)) {
			print "got trm from ID3 ($trm)\n";
			return($trm);
		}
		else {
			if(count($id3results)) print_r($id3results);
		}
		
		$cached_trm = new cached_trm();
		if($cached_trm->lookup($this->path)) {
			print "got trm from cache ($cached_trm->trm)\n";
			return($cached_trm->trm);
		}
		else {
			$path = $this->path;
			$is_tmp = false;
			if(!eregi('ogg$',$path)) {
				#print "Decoding $path\n";	
				$path = "/tmp/" . eregi_replace('[^a-z0-9]','',basename($this->path)) . ".wav";
				exec("mpg321 -q -w \"$path\" \"$this->path\"");
				$is_tmp = true;
			}
			$sanity = 0;
			$results = array("");
			while((($results[0] == "")||(eregi("too busy",$results[0])))&&($sanity < 10)) { 
				unset($results);
				#print "getTRM($this->path)";
				print "getTRM()";
				exec("trm \"$path\" 2>&1",$results,$return);
				if(($return == 0)&&(ereg('^[0-9a-z_-]+$',trim($results[0])))) {
					$trm = $results[0];
					$cached_trm->cache($this->path,$trm);
					if($is_tmp) unlink($path);
					return($trm);
				}
				else {
					print_r($results);
				}
				print "Waiting to retry trm ...\n";
				sleep(5);
				$sanity++;
			}
			if($is_tmp) unlink($path);
		}
	}
	
	function getBestSource($track_id)
	{
		// TODO - detect user agent, use oggs rather than mp3 where possible
		if($this->database->query("select sources.* from sources,hosts where hosts.status='online' and host_id=hosts.id and track_id='$track_id' order by bitrate desc")) 
			return($this->getNext());
		else
			return false;
	}
	function getSourceHostsList($track_id)
	{
		if($this->database->query("select hostname from sources,hosts where hosts.status='online' and host_id=hosts.id and track_id='$track_id' order by bitrate desc")) {
			$hosts = array();
			while($row = $this->database->getNextRow()) {
				$hosts[] = $row['hostname'];
			}
			return($hosts);
		}
		else
			return false;
	}
}
?>
