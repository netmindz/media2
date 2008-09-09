<?
require("album_template.php");

class album extends album_template {

	function lookupOrAdd($name,$skip_fuzzy="")
	{
		if(trim($name) == "") exit("Empty album name passed to album::lookuoradd()");

		if(eregi('-? ?\(?CD ?([0-9]+)\)?|-? \(?dis[ck] ?([0-9]+)\)?',$name,$matches)) {
                        if($matches[1]) { $number = $matches[1]; } else { $number = $matches[2]; }
                        $name = str_replace($matches[0],"",$name);
                        $this->cd_number = $number;
		}
		else {
			$this->cd_number = 0;
		}


		if($this->getList("where name=\"" . addslashes($name) ."\" and cd_number=" . $this->cd_number)) {
			return($this->getNext());
		}

		if($skip_fuzzy == "") {
			$last_match = 100;
			$similar = FuzzySearch::singleWordMatches($name,"albums","name");
			foreach($similar as $match_name=>$match_details) {
				if($match_details['avg_perc'] < $last_match) {
					$name = $match_name;
					$last_match = $match_details['avg_perc'];
				}
			}
		}
		if($this->getList("where name=\"" . addslashes($name) ."\" and cd_number=" . $this->cd_number)) {
			return($this->getNext());
		}
		else {
			$this->name = $name;
			return($this->add("addslashes"));
		}
		
	}

        function getTypeList($min_count,$max_count)
        {
                $type = "album";
                return($this->getList("JOIN tracks ON ( ${type}_id = ${type}s.id ) JOIN sources ON ( track_id = tracks.id ) JOIN  `hosts`  ON ( host_id = hosts.id ) WHERE hosts.status =  'online' group by ${type}s.id having ( count(sources.id) >= $min_count and count(sources.id) < $max_count)","order by ${type}s.name"));
        }

		  
		  function getByName($name)
		  {
				return($this->getByOther(array('name'=>$name)));
		  }

}
?>
