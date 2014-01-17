<?
require("music_list_template.php");

class music_list extends music_list_template {
		
	var $debug;
		
	function get($TRACK_id, $addslashes = "")
	{ 
		settype($TRACK_id,"int");
		$this->getList("WHERE tracks.id = $TRACK_id");
		$this->getNext($addslashes);
		
		if($this->TRACK_id)
			return(1);
	}

	function debugPrint($level,$value)
	{
		if($this->debug >= $level) {
			print_r($value);
			flush();
		}
	}
		

		function getTypeList($type,$id)
		{
			return($this->getList("where {$type}_id='$id' and _archived != 'y'"));
		}

		function getTypeListPlaylist($type,$id)
		{
			if($type != "album") { $order = "order by rand()"; } else { $order = ""; }
			$this->getList("where {$type}_id='$id'",$order);
			while($this->getNext()) {
				$row = array();
				$row['id'] = $this->TRACK_id;
				$row['artist'] = $this->ARTIST_name;
				$row['name'] = $this->TRACK_name;
				$row['duration'] = $this->TRACK_duration;
				$list[] = $row;
			}
			return($list);
		}

		function getTypeLikePlaylist($id,$limit)
		{
			$artist = new artist();
			$artist->get($id);
			$as = new audioscrobbler();
			$artists = array($id);
			$s_artists = $as->getSimilarArtist($artist->name,10);
			foreach($s_artists as $s_artist) {
				$artists[] = $s_artist->id;
			}
			$this->getList("where artist_id in (".implode(",",$artists).") and _archived != 'y'","order by rand()","limit 0,$limit");
			while($this->getNext()) {
				$row = array();
				$row['id'] = $this->TRACK_id;
				$row['artist'] = $this->ARTIST_name;
				$row['name'] = $this->TRACK_name;
				$row['duration'] = $this->TRACK_duration;
				$list[] = $row;
			}
			return($list);
		}

		
		function getRandomList($limit,$username,$bpm=null)
		{
			if($bpm) $bpm_limit = "AND tracks.bpm between " . ($bpm - 5) . " AND " . ($bpm + 5);
			if(!$username) exit("no username supplied to music_list::getRandomList()");
			if(!$this->debug) $use_tempory_table = "temporary";
			if($this->debug) {
				header("Content-Type: text/plain");
			}
			// ************************************************************************************************************************************
			//	 This chunk of code creates the tempoary summary tables required fro the join
			//	 MySQL does not support sub-selects or 'select into' so i create a table then insert into ... select
			//  The drop table is required in case you turned debug on and so created no temporary tables or php is reussing db connection
			// ************************************************************************************************************************************
			$type_tables= array("artist","album","genre");
			$mysql_safe_username = eregi_replace("[^a-z]","",$username);
			$this->debugPrint(1,"Creating temporary tables ");
			foreach($type_tables as $type) {
				// average - all hours
				$scores_tables[$type] = "tmp_${mysql_safe_username}_${type}s_prefs";

				$this->debugPrint(1,$scores_tables[$type] . "\n");

				$this->database->query("drop table if exists $scores_tables[$type] ");
				$this->database->query("create $use_tempory_table table $scores_tables[$type] (id int not null, score float,primary key(id))");
				$this->database->query("insert into $scores_tables[$type] (id,score) select type_id, avg(score) from type_prefs where username='$username' and type='$type' group by type_id");

				// this hour

				$scores_tables_hour[$type] = "tmp_${mysql_safe_username}_${type}s_hour_prefs";

				$this->debugPrint(1,$scores_tables_hour[$type] . "\n");

				$this->database->query("drop table if exists $scores_tables_hour[$type] ");
				$this->database->query("create $use_tempory_table table $scores_tables_hour[$type] (id int not null, score float,primary key(id))");
				$this->database->query("insert into $scores_tables_hour[$type] (id,score) select type_id, score from type_prefs where username='$username' and type='$type' and time=hour(NOW()) group by type_id");


			}
			$this->debugPrint(1,"\n");

			$avg_track_pref_table = "tmp_${mysql_safe_username}_tracks_prefs_avg";
			$this->debugPrint(1,"$avg_track_pref_table ");
			$this->database->query("drop table if exists $avg_track_pref_table");
			$this->database->query("create $use_tempory_table table $avg_track_pref_table (id int not null, score float, primary key(id))");
			$this->database->query("insert into $avg_track_pref_table (id,score) select track_id, avg(score) from track_prefs where username='$username' group by track_id");

			$sum_track_pref_table = "tmp_${mysql_safe_username}_tracks_prefs_sum";
			$this->debugPrint(1,"$sum_track_pref_table ");
			$this->database->query("drop table if exists $sum_track_pref_table");
			$this->database->query("create $use_tempory_table table $sum_track_pref_table (id int not null, playcount float, primary key(id))");
			$this->database->query("insert into $sum_track_pref_table (id,playcount) select track_id, sum(playcount) from track_prefs where username='$username' group by track_id");
			$this->debugPrint(1,"\n");
			// ************************************************************************************************************************************
		
			$select_sql = "select tracks.id as id, tracks.name as name, artists.name as artist , tracks.duration as duration, 
			if(avg_track_prefs.score is null,0,avg_track_prefs.score) as track_score,";
			foreach($type_tables as $type) {
				$select_sql .= "((if(${type}_prefs.score is null,0,${type}_prefs.score) + if(${type}_prefs_hour.score is null,0,${type}_prefs_hour.score))/2) as ${type}_score,";
			}			
			$select_sql .= " if(playcount is null,0,playcount) as playcount
			from tracks
			left join artists on (artist_id=artists.id) left join albums on (album_id=albums.id) left join genres on (genre_id=genres.id)
			left join $sum_track_pref_table as sum_track_prefs on (tracks.id=sum_track_prefs.id)
			left join $avg_track_pref_table as avg_track_prefs on (tracks.id=avg_track_prefs.id) ";

			foreach($type_tables as $type) {
				$select_sql .= "left join " . $scores_tables[$type] . " as ${type}_prefs on (tracks.${type}_id=${type}_prefs.id)\n";
				$select_sql .= "left join " . $scores_tables_hour[$type] . " as ${type}_prefs_hour on (tracks.${type}_id=${type}_prefs_hour.id)\n	";
			}
			$select_sql .= " where tracks._archived != 'y' $bpm_limit";


			// ***********************************************************
			// 'fav' tracks
			// ***********************************************************
			$sql = $select_sql ." order by  artist_score desc, track_score desc, album_score desc, genre_score desc, rand() ";
			$sql .=  " limit 0," . ($limit * 1);
			$this->debugPrint(1,"getting 'fav' tracks\n");	
			$this->database->query($sql);
			$this->debugPrint(1,$this->database->RowCount . " rows\n");
			$this->debugPrint(1,$this->database->LastQuery . "\n");
			while($row = $this->database->getNextRow()) {
				$this->debugPrint(2,$row);
				$all_tracks[] = $row;
				$this->debugPrint(1,".");
			}
			$this->debugPrint(1,"\n");
			// ***********************************************************
			
			
			// ***********************************************************
			// 'virgin' tracks
			// ***********************************************************
			$sql =  $select_sql . " and avg_track_prefs.score is null";
			$sql .= " order by  artist_score desc, genre_score desc, album_score desc, rand() ";
			$sql .=  " limit 0," . ($limit * 1);
			$this->debugPrint(1,"getting 'virgin' tracks\n");
			$this->database->query($sql);
			$this->debugPrint(1,$this->database->RowCount . " rows\n");
			$this->debugPrint(1,$this->database->LastQuery . "\n");
			while($row = $this->database->getNextRow()) {
				$this->debugPrint(2,$row);
				$all_tracks[] = $row;
				$this->debugPrint(1,".");
			}
			$this->debugPrint(1,"\n");
			// ***********************************************************

			// ***********************************************************
			// Main selection
			// ***********************************************************
			$sql =  $select_sql . " and (avg_track_prefs.score > -30 or avg_track_prefs.score is null)";
			$sql .= "having artist_score > -30 and album_score > - 30 ";
			$sql .= "order by  playcount asc, track_score desc, artist_score desc, album_score desc, genre_score desc, rand() ";
			$sql .=  "limit 0," . ($limit * 100);
			$this->debugPrint(1,"getting 'main' tracks");
			$this->database->query($sql);
			$this->debugPrint(1,$this->database->RowCount . " rows\n");
			$this->debugPrint(1,$this->database->LastQuery . "\n");
			while($row = $this->database->getNextRow()) {
				$this->debugPrint(2,$row);
				$all_tracks[] = $row;
				$this->debugPrint(1,".");
			}
			// ***********************************************************


			$this->debugPrint(2,$all_tracks);


			// ************************************************************************************************************************************
			
			$min = 0;
			$max = count($all_tracks);
			$this->debugPrint(1,"\nThere are " . count($all_tracks) . " to choose from\n");
			if($max <= $limit) {
				$this->debugPrint(1,"There are less then or equal to $limit tracks to adding them all\n");
				$selection = $all_tracks;
			}
			else {
				$this->debugPrint(1,"There are more than $limit tracks, choosing randomly\n");
				
				if($max > ($limit * 2)) {
					// All the stuff at the very bottom is the stuff you don't like, so don't scrape the bottom of the barrel
					$max = round($max/2);
					$this->debugPrint(1,"There are more than twice the number of required tracks, min=$min,max=$max\n");
				}
				else {
					$this->debugPrint(1,"There are less than twice the number of required tracks, min=$min,max=$max\n");
				}
			
				$used_selection = array();
				$selection = array();
				$sanity = 0;
				while((count($selection) < $limit)&&($sanity < 10000)) {	
//					$this->debugPrint(1,"There are less than twice the number of required tracks, min=$min\n");
					// get random track id
					$key = exp_rand($min,$max);

					// if this is not already in our list
					if((!in_array($key, $used_selection))) {
						$selection[] = $all_tracks[$key];
						$used_selection[] = $key;
					}
					else {
						$this->debugPrint(1,"all_tracks item $key is already in our playlist\n");
					}
					$sanity++;
				}
			}
			$this->debugPrint(1,$selection);
			// ************************************************************************************************************************************
			return($selection);
		}

		function getNewTracksList($count)
		{
			return($this->getList("where tracks.added != '0000-00-00 00:00:00'","order by TRACK_added desc","limit 0,$count"));
		}
}
?>
