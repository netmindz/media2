<?
require("track_pref_template.php");
require_once("as_account.class.php");
require_once("as_spool_item.class.php");

class track_pref extends track_pref_template {

	function track_pref()
	{
		$this->track_pref_template();
		$this->username = get_remote_username();
		$this->time = date("G");
	}
	
	function lookupOrAdd($track_id)
	{
		if(!$this->getByOther(array("username"=>$this->username,"time"=>$this->time,"track_id"=>$track_id))) {
			$this->track_id = $track_id;
			if(!$this->track_id) exit("track_pref::lookupOrAdd failed as no track_id");
			if((!$this->time)&&(date("G") != 0)) exit("track_pref::lookupOrAdd failed as no time");
			if(!$this->username) exit("track_pref::lookupOrAdd failed as no username");
			$this->add();
		}
		return($this->id);
	}
	
	function updatePref($track_id,$value)
	{
		$this->lookupOrAdd($track_id);
		$this->score = $this->score + $value;
		$this->update();
	}		
		
	function incPlayCount($track_id="")
	{
		if($track_id) $this->track_id = $track_id;
		$this->lookupOrAdd($this->track_id);
		if(!$this->id) exit("track_pref::incPlayCount could not locate the track_id");
		$this->playcount++;
		$this->update();
	}

	function log50percent($track_id="")
	{
		if($track_id) $this->track_id=$track_id;
		$this->lookupOrAdd($track_id);
		$as_account = new as_account();
		$as_account->getByUsername($this->username);
		
		if($as_account->id) {
			$as_spool_item = new as_spool_item();
			$as_spool_item->addTrack($as_account->id,$this->track_id);
		}
		
	}
	
	function getRecentList($count)
	{
		settype($count,"int");
		return($this->getList("where username=\"$this->username\"","order by last_update desc","limit 0,$count"));
	}
	
	function getOnlineUsers($count)
	{
		settype($count,"int");
		$list = array();
		// $this->database->query("select username,track_id,sec_to_time(time_to_sec(now()) - time_to_sec(track_prefs.last_update)) as how_old from track_prefs where last_update > date_sub(NOW(),interval 5 minute) and username !='$this->username' group by username order by last_update desc limit 0,$count ");
		$this->database->query("select track_prefs.*,sec_to_time(time_to_sec(now()) - time_to_sec(track_prefs.last_update)) as how_old from track_prefs left join tracks on track_id=tracks.id where sec_to_time(time_to_sec(now()) - time_to_sec(track_prefs.last_update)) < duration and to_days(track_prefs.last_update) = to_days(now()) and username != '$this->username' order by username,how_old asc,track_prefs.last_update desc limit 0,$count");
		// $this->database->query("select track_prefs.*,sec_to_time(time_to_sec(now()) - time_to_sec(track_prefs.last_update)) as how_old from track_prefs left join tracks on track_id=tracks.id having how_old < 600 and username != '$this->username' order by username,how_old asc,track_prefs.last_update desc limit 0,$count");
		while($row = $this->database->getNextRow()) {
			if(!isset($list[$row['username']])) {
				$list[$row['username']] = $row['track_id'];
			}
		}
		return($list);
	}

}
?>
