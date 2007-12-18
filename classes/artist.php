<?
require("artist_template.php");
require_once("fuzzysearch.php");

class artist extends artist_template {

	function lookupOrAdd($name,$skip_fuzzy="")
	{
		if($this->getList("where name=\"". addslashes($name) ."\"")) {
			return($this->getNext());
		}

		if($skip_fuzzy == "") {
			echo "skipping wanky album matching\n";
			/*
			$last_match = 100;
			$similar = FuzzySearch::singleWordMatches($name,"artists","name");
			foreach($similar as $match_name=>$match_details) {
				if($match_details['avg_perc'] < $last_match) {
					$name = $match_name;
					$last_match = $match_details['avg_perc'];
				}
			}
			*/
		}
		if($this->getList("where name=\"". addslashes($name) ."\"")) {
			return($this->getNext());
		}
		else {
			$this->name = $name;
			return($this->add("addslashes"));
		}
		
	}
		
	function getTypeList($min_count,$max_count)
	{	
		$type = "artist";
		return($this->getList("JOIN tracks ON ( ${type}_id = ${type}s.id ) JOIN sources ON ( track_id = tracks.id ) JOIN  `hosts`  ON ( host_id = hosts.id ) WHERE hosts.status =  'online' group by ${type}s.id having ( count(sources.id) >= $min_count and count(sources.id) < $max_count)","order by ${type}s.name"));
	}

  function getByName($name)
  {
		return($this->getByOther(array('name'=>$name)));
  }

}
?>
