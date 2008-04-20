<?
#$CONF['db_host'] = "wtatam.premierit.com";
$CONF['db_name'] = "music";
$CONF['db_username'] = "music";
$CONF['db_pass'] = "musicpass";

$CONF['artist']['order'] = "order by name";
$CONF['album']['order'] = "order by name";
$CONF['album']['DN'] = '<?php if($this->cd_number) { $this->DN = "$this->name - CD$this->cd_number" ; } else { $this->DN = $this->name; }  ?>';
$CONF['genre']['order'] = "order by name";
$CONF['track']['order'] = "order by tracknum";

$CONF['music_list']['order'] = "order by ALBUM_name,ALBUM_cd_number,TRACK_tracknum,ARTIST_name,TRACK_name";


$CONF['meta_tables'] = array (
        "music_lists"=> array(
                "master" => "tracks",
                "tables"=> array (
                        "artists"=>array(
                                "link"=>"tracks.artist_id",
                        ),
                        "albums"=>array(
                                "link"=>"tracks.album_id",
                        ),
                        "genres"=>array(
                                "link"=>"tracks.genre_id",
                        ),
                ),
        ),
);


$CONF['music_base'] = "/music";
?>
