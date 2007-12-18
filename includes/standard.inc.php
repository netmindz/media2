<?
require("premier_common.php");
require("site_config.inc.php");
require("database.php");
require("artist.php");
require("album.php");
require("genre.php");
require("track.php");
require("source.php");
require("dead_source.php");
require("track_pref.php");
require("type_pref.php");
require("host.php");
require("music_list.php");
require("audioscrobbler.php");

function exp_rand($min, $max)
{
        $range = ($max - $min)+1;
        // get a random value between 0 and 1
        $rand = lcg_value();
        $tmp = ($rand * pow($range,2));
        $n = floor(sqrt($tmp) + $min);
        return($n);
}
/*
function get_remote_username()
{
	$user_ip = ip_get_visitor();
	if(ereg('^(.+)\.[0-9]$',$_SERVER["SERVER_ADDR"],$matches)) {
		$network_address = $matches[1];
	}
	if(strstr($network_address,$user_ip)) {
		require_once("Net/Ident.php");
		if(class_exists("Net_Ident")) {
			$ident = new Net_Ident($user_ip);
			$result = $ident->getUser();
			if(!is_object($result)) {
				$result = strtolower($result);
				if(!in_array($result,array('default','other','unknown','nobody','apache'))) {
					$username = $result;
				#	mail("root","ident","username from ident=$username host=" . gethostbyaddr(ip_get_visitor()));
				}
			}
		}
	}
	if(!$username) {
		$hostname = gethostbyaddr(ip_get_visitor());
		list($username) = explode(".",$hostname);
	}
	return($username);
}
*/
function get_http_mount_point()
{
	//if(!ereg('^192\.168\.1\.|^192\.168\.42|^192\.168\.1\.188',ip_get_visitor())) {
		$mount = "myworkpc.netmindz.net/music2";
	/*}
	else {
		$mount = "wtatam.premierit.com/music2";
	}*/
	return($mount);
}

$track_type_list = array("artist","album","genre");

$CONF['rpms'] = array("libmusicbrainz-cli","libtunepimp","gjay","php-pear-XML-Serializer","libtunepimp-tools");
?>
