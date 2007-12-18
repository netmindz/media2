<?
require("type_pref_template.php");

class type_pref extends type_pref_template {

	function type_pref($type,$id="")
	{
		$this->type_pref_template();
		$this->username = get_remote_username();
		$this->time = date("G");
		$this->type = $type;
		if(!$this->type) exit("no type for type_pref");
		$this->type_id = $id;
	}

	function lookupOrAdd()
	{
		if(!$this->getByOther(array("username"=>$this->username,"type"=>$this->type,"type_id"=>$this->type_id,"time"=>$this->time))) {
			if($this->type == "") exit("EAK !!! type_pref with no type !");
			if($this->type_id == "") exit("EAK !!! type_pref with no type_id !");
			if((!$this->time)&&(date("G") != 0)) exit("EAK !!! type_pref with no time !");
			$this->add();
		}
		return($this->id);
	}

	
	function updatePref($value,$inc_score_count="")
	{
		// don't bother with tracks with no id
		if(!$this->type_id) return(null);
		
		$this->lookupOrAdd();
		if($this->scorecount < 1) $this->scorecount = 1;
		if($inc_score_count) {
			// Average out the score
			$this->score = (($this->scorecount * $this->score) + $value) / $this->scorecount;
		}
		else {
			$this->score = $this->score + $value;
		}
		if($inc_score_count) $this->scorecount = $this->scorecount + 1;
		$this->update();
		system("echo \"score=$this->score updatePref($value,$inc_score_count) scorecount=$this->scorecount user=$this->username time=$this->time type=$this->type id=$this->type_id\" >> /tmp/type_pref.log");
	}		

	function getFavs($count)
	{
		$list = array();
		$this->database->query("select type_id, avg(score) as score from type_prefs where username='$this->username' and type='$this->type' group by type_id order by score desc,rand() desc limit 0,$count");
		while($row = $this->database->getNextRow()) {
			$list[] = $row;
		}
		return($list);
	}
}
?>
