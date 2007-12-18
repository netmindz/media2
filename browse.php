<? require("header.inc"); ?>
<?

$min_type = 3;

if(!$type) {
	?>
	<p>Please select a type</p>
	<?
}
elseif(!isset($id)) {
	if(!in_array($type,array("artist","album","genre"))) exit("invalid type passed");
	$list = new $type();
	$result_count = $list->getTypeList($min_type,999);
	$type_prefix = strtoupper($type);
	?>
	<p><?= $result_count ?> <?= $type ?>s found</p>
	<div id="listing">
	<table width="50%">
	<tr>
		<th colspan="2">Fav <?= ucwords($type) ?>s</th>
	</tr>
	<?
	$obj = new $type();
	$obj_pref = new type_pref($type);
	$fav_list = $obj_pref->getFavs(30);
	
	foreach($fav_list as $details) {
		$obj->get($details['type_id']);
		?>
		<tr><td colspan="2"><a href="<?= $PHP_SELF ?>?type=<?= $type ?>&id=<?= $obj->id ?>"><?= $obj->DN ?></a></td></tr>
		<?
	}
	?>
	<tr>
		<th><?= ucwords($type) ?></th>
		<th>&nbsp;</th>
	</tr>
	<?
	while($list->getNext()) {
		?>
		<tr>
			<td><a href="<?= $PHP_SELF ?>?type=<?= $type ?>&id=<?= $list->id ?>"><?= $list->DN ?></a></td>
			<td>[<a href="type_edit.php?type=<?= $type ?>&id=<?= $list->id ?>">edit</a>]</td>
		</tr>
		<?
	}
	?>
	</table>
	<hr>
	<table width="50%">
	<tr><th colspan="2">Limited <?= ucwords($type) ?>s</td></tr>
	<?
	$result_count = $list->getTypeList(0,$min_type);
	        while($list->getNext()) {
                ?>
                <tr>
                        <td><a href="<?= $PHP_SELF ?>?type=<?= $type ?>&id=<?= $list->id ?>"><?= $list->DN ?></a></td>
                        <td>[<a href="type_edit.php?type=<?= $type ?>&id=<?= $list->id ?>">edit</a>]</td>
                </tr>
                <?
        }

	?>
	</table>
	</div>
	<?
}
else {
	$typeObj = new $type();
	$typeObj->get($id);
	
	?>
	<table align="center" cellpadding="30">
	<tr>
		<td valign="top">
		
		<h2><a href="playlist.php?type=<?= $type ?>&id=<?= $id ?>"><?= ucwords($type) ?> - <?= $typeObj->DN ?><br />
		<img src="i/listen.png" border="0" title="Listen to <?= $typeObj->DN ?>" alt="Listen"></a></h2>
		
		<?php if($type == "artist") { ?><h3><a href="playlist.php?type=like&id=<?= $id ?>">Listen to artists like <?= $typeObj->DN ?></a></h2><?php } ?>
	
	 [ <a href="type_edit.php?type=<?= $type ?>&id=<?= $id ?>">edit</a> ]
		<? if(!$type_pref_updated) { ?><p align="center">Do you <a href="update_type_pref.php?type=<?= $type ?>&id=<?= $id ?>&mod=love"><img src="i/love.png" border="0" alt="love" title="Love this <?= $type ?>"></a> or <a href="update_type_pref.php?type=<?= $type ?>&id=<?= $id ?>&mod=hate"><img src="i/hate.png" border="0" alt="hate" 	title="Hate this <?= $type ?>"></a> this <?= $type ?></p><? }  else { ?><p align="center">Preferance Updated</p><? } ?>
	
		<?	if($typeObj->mb_id) { ?>
		<h4><a href="http://musicbrainz.org/<?= $type ?>/<?= $typeObj->mb_id ?>.html" target="mb_<?= $type ?>">Music Brainz Listing</a></h4>
		<? } ?>

		</td>
	<td>
	<?
	$artists = array();
	if($type == "album") {
		$amazon_details = amazon_getAlbum($artists,$typeObj->name,$typeObj->amazon_asin);
		$album_image = "";
		if((count($amazon_details) > 0)&&($amazon_details != '')) {
#			print_r_html($amazon_details);

			if(strlen(file_get_contents($amazon_details->ImageUrlMedium)) >= 1024) {
				$album_image = $amazon_details->ImageUrlMedium;
			}
			elseif(strlen(file_get_contents($amazon_details->ImageUrlLarge)) >= 1024) {
				$album_image = $amazon_details->ImageUrlLarge;
			}
			else {
			#	print "Error: Amazon returning null image urls<br>\n";
				$album_image = "http://images-eu.amazon.com/images/G/02/misc/no-img-lg-uk.gif";
			}
			print "<p align=\"center\"><a href=\"" . $amazon_details->Url . "\" target=\"_top\">Buy from Amazon.co.uk<br>";
			if($album_image) print "<img src=\"" . $album_image . "\" border=\"0\"></a><br>";
			print htmlspecialchars(utf8_decode($amazon_details->OurPrice)) . "</p>\n";
		}
	}
	elseif($type == "artist") {
		
		$as = new audioscrobbler();
		
		$similar_artists = $as->getSimilarArtist($typeObj->name,10);
		if(count($similar_artists)) {
			?>
			<div class="similar">Similar Artists<ul>
			<?
			foreach($similar_artists as $similar_artist) {
					?>
					<li><a href="browse.php?type=artist&id=<?= $similar_artist->id ?>"><?= $similar_artist->DN ?></a></li>
					<?
			}
			?>
			</ul></div>
			<?
		}
		
		$top_albums = $as->getArtistTopAlbums($typeObj->name,10);
		if(count($top_albums)) {
			?>
			<div class="similar">Top Albums<ul>
			<?
			foreach($top_albums as $top_albums) {
					?>	
					<li><a href="browse.php?type=album&id=<?= $top_album->id ?>"><?= $top_album->DN ?></a></li>
					<?
			}
			?>
			</ul></div>
			<?
		}
		
		$top_tracks = $as->getArtistTopTracks($typeObj->name,10);
		if(count($top_tracks)) {
			?>
			<div class="similar">Top Tracks<ul>
			<?
			foreach($top_tracks as $top_track) {
				$top_album = new album();
				$top_album->get($top_track->album_id);
					?>
					<li><a href="browse.php?type=album&id=<?= $top_track->album_id ?>" title="from <?= $top_album->DN ?>"><?= $top_track->DN ?></a></li>
					<?
			}
			?>
			</ul></div>
			<?
		}
	}

	?>
	</td>
	</tr>
	</table>
	<?
		track_header();
	?>
	<?

	$track = new music_list();
	$source = new source();
	$track->getTypeList($type,$id);
	while($track->getNext()) {
		$artists[] = $track->ARTIST_name;
		track_display($track);
	}
	
	track_footer();
	if((!$amazon_details)&&($type == "album")) amazon_getAlbum($artists,$typeObj->name,$typeObj->amazon_asin);
}


?>
<? require("footer.inc"); ?>
