<? require("header.inc"); ?>
<? /*
<form action="search.php" method="post">
Keywords <input type="text" name="keyword" value="<?= $_POST['keyword'] ?>"><input type="submit" value=" Search">
</form>
*/ ?>
<?
if(isset($_POST['keyword'])) {
	foreach($track_type_list as $type) {
		$obj = new $type();
		$count = $obj->search($_POST['keyword'],"name");
		?>
		<div id="listing">
		<table width="80%" cellpadding="5" cellspacing="1">
		<tr>
			<th><?= premier_class_to_table($type) ?></th>
		</tr>
		<?
		if(!$count) {
			?>
			<tr>
				<td>No Results</td>
			</tr>
			<?
		}
		while($obj->getNext()) {
			?>
			<tr>
				<td><a href="browse.php?type=<?= $type ?>&id=<?= $obj->id ?>"><?= $obj->DN ?></a></td>
			</tr>
			<?
		}
		?>
		</table>
		</div>
		<?
	}
}

$music_list = new music_list();
if((isset($_POST['keyword']))&&($_POST['keyword'])) $count = $music_list->search($_POST['keyword'],"tracks.name");
if(!$count) {
	?>
	<p>No tracks found</p>
	<?
}
else {
	?>
	<h3><?= $count ?> Tracks</h3>
	<div id="listing">
	<table width="80%" cellpadding="5" cellspacing="1">
	<tr>
		<th>Num</th>
		<th>Track</th>
		<th>Artist</th>
		<th>Album</th>
	</tr>
	<?
	while($music_list->getNext()) {
		if($music_list->TRACK__archived != 'y') {
			?>
				<tr>
					<td><?= $music_list->TRACK_tracknum ?></td>
					<td><a href="file.php?id=<?= $music_list->TRACK_id ?>"><?= $music_list->TRACK_name ?></a></td>
					<td><a href="browse.php?type=artist&id=<?= $music_list->ARTIST_id ?>"><?= $music_list->ARTIST_name ?></a></td>
					<td><a href="browse.php?type=album&id=<?= $music_list->ALBUM_id ?>"><?= $music_list->ALBUM_name ?></a></td>
				</tr>
			<?
		}
	}
	?>
	</table>
	</div>
	<?
}
?>
<? require("footer.inc"); ?>
