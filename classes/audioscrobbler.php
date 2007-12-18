<?
require("/usr/share/pear/XML/Unserializer.php");

class audioscrobbler
{
	var $url, $lastError, $user, $u, $p;
	var $_handshake;

	function audioscrobbler($user=NULL,$as_username=NULL,$password=NULL)
	{
			$this->user = $user;
			$this->u = $as_username;
#			print "password=$password\n";
			$this->p = md5($password);
	}	


	function send()
	{
		$sql = "select artist_id,as_spool_items.id as as_id,artists.name as artist,tracks.name as track_name, albums.name as album,trm,time_to_sec(duration) as length, played 
		from tracks,as_spool_items,as_accounts,artists,albums
		where tracks.artist_id = artists.id and albums.id=album_id and tracks.id=as_spool_items.track_id and as_spool_items.as_account_id = as_accounts.id
		and as_accounts.username = '$this->user' and tracks.name != '' and artists.name != '' and albums.name != ''
		order by played limit 0,5";
		
		/*$sql = "select artist_id,as_spool_items.id as as_id,artists.name as artist,tracks.name as track_name, albums.name as album,trm,time_to_sec(duration) as length, played from tracks,as_spool_items,as_accounts
		join artists on ( `tracks`.`artist_id` = `artists`.`id` ) join albums on (albums.id=album_id)
		where tracks.id=as_spool_items.track_id and as_spool_items.as_account_id = as_accounts.id
		and as_accounts.username = '$this->user' and tracks.name != '' and artists.name != '' and albums.name != ''
		order by played limit 0,5";*/
		$result = mysql_query($sql);
		print "sql = $sql\n";
		print mysql_error();
		if(mysql_num_rows($result) > 0) {
			if($this->_handshake()) {
				$i = 0;
				$as_list = array();	
				$post["u"] = utf8_encode($this->u);
				$post["s"] = utf8_encode(md5($this->p . $this->_handshake));
				while($row = @mysql_fetch_array($result)) {
					if($row['artist'] && $row['album'] && $row['track_name']) {
				
						$played = date("Y-m-d H:i:s",(strtotime($row['played']) + date("Z")));
				
						$post["a[$i]"] = utf8_encode($row['artist']);
						$post["t[$i]"] = utf8_encode($row['track_name']);
						$post["b[$i]"] = utf8_encode($row['album']);
						$post["m[$i]"] = utf8_encode($row['trm']);
						$post["l[$i]"] = utf8_encode($row['length']);
						$post["i[$i]"] = utf8_encode($played);
						$as_list[] = $row['as_id'];
						$i++;
					}
					else {
						print "Skipping track missing details\n";
						print_r($row);
					}
				}

				print_r($post);
				if($i) {	
					if($result = fake_post	($this->url,$post)) {
						$lines = explode("\n",$result);
						if($lines[1]) sleep($lines[1]);
						if($lines[0] == "OK") {
							print "$i results uploaded\n";
							mysql_query("delete from as_spool_items where id in (" . implode(",",$as_list) . ")");
							return(1);
						}
						elseif($lines[0] == "FAILED Plugin bug: Not all request variables are set") {
							print "Bad Data, removing first item\n";
							mysql_query("delete from as_spool_items where id=" . $as_list[0]);
						}
						elseif($lines[0] == "BADPASS") {
							$this->lastError = "Bad username/password";
						}
						else {
							$this->lastError = $lines[0];
						}
					}
				}
				else {
					$this->lastError = "no valid items in queue for $this->u";						
				}
			}
		}
		else {
			$this->lastError = "no items in queue for $this->u";
		}
	}
	

	function _handshake()
	{
		$fh = @fopen("http://post.audioscrobbler.com?hs=true&p=1.1&c=mdn&v=2.2&u=$this->u",r);
		if(!$fh) {
			$this->lastError = "failed to handshake with server";
			return(0);
		}
		while(!feof($fh)) {
			$lines[] = trim(fgets($fh,255));
		}
		if($lines[0] == "UPTODATE") {
			list($null,$sleep) = explode(" ",$lines[2]);
			sleep($sleep);
			$this->url = $lines[2];
			$this->_handshake = $lines[1];
			return(1);
		}
		else {
			$this->lastError = "_handshake returned $lines[0]";
		}
	}
	

	function getSimilarArtist($artist_name,$limit)
	{
		$artist = new artist();
		$artists = array();
		$list = @file("http://ws.audioscrobbler.com/1.0/artist/" . urlencode($artist_name) . "/similar.txt");
		foreach($list as $item) {
			$details = explode(",",trim($item));
			if(($details[1]) && ($artist->getByOther(array("mb_id"=>$details[1])))) {
				$artists[$details[0]] = clone($artist);
			}			
			elseif($artist->getList("where name='" . addslashes($details[2]) . "'")) {
				while($artist->getNext()) {
					$artists[$details[0]] = clone($artist);
				}
			}
			if(count($artists) > $limit) break;			
		}
		return($artists);
	}

	function getArtistTopAlbums($artist_name,$limit)
	{
		return($this->_getArtistTopX("album",$artist_name,$limit));
	}

	function getArtistTopTracks($artist_name,$limit)
	{
		return($this->_getArtistTopX("track",$artist_name,$limit));
	}


	function _getArtistTopX($type,$artist_name,$limit)
	{
		$item = new $type();
		$artist = new artist();
		$artist->getByName($artist_name);
		$items = array();
		$names_found = array();
		$xmlObj = new XML_Unserializer();
		if(PEAR::isError($xmlObj->unSerialize("http://ws.audioscrobbler.com/1.0/artist/" . urlencode($artist_name) . "/top" . $type . "s.xml",true))) return($items);
		$data = $xmlObj->getUnserializedData();
		if(PEAR::isError($data)) return($items);
		if($type != "track") {
			$artist_prefix = $type . "_";
		}
		else {
			$artist_prefix = "";
		}
		if(isset($data[$type])) {
			foreach($data[$type] as $details) {
				if($item->getList("where name='" . addslashes($details['name']) . "' and ${artist_prefix}artist_id=$artist->id")) {
					while($item->getNext()) {
						$items[] = clone($item);
						// as we may have more than one copy of this track ...
						$names_found[$details['name']] = true;
					}
				}
				if(count($names_found) > $limit) break;			
			}
		}
		return($items);
	}

}



function fake_post($url, $params=array())
{
		foreach($params as $key => $value) {
			if($post) $post .= '&'; 
//			$post .= $key .'=' . htmlentities($value);
			$post .= $key .'=' . urlencode($value);
		}
	
		print "connecting to $url\n";
		
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
		return(curl_exec($curl));
	}

?>
