<? require("header.inc"); ?>

<!-- <h3>Welcome to the <?= $_SERVER['HTTP_HOST'] ?> music server</h3> -->

<p>
<b>What is it ?</b><br>
This site is an online music jukebox that has scanned the local network for all the music it can find.<br>
This is then presented to you in what is hoped is an intuative user interface.<br>
This system tracks your listening tastes so as to offer music to you that you are most likely to enjoy.</p>

<h3><a href="playlist.php?type=random">Listen Now</a></h3>
<h4>Your most recent tracks</h4>
<? track_header(); ?>
<?
$track = new music_list();
$track_prefs = new track_pref();
$track_prefs->getRecentList(10);
while($track_prefs->getNext()) {
	$track->get($track_prefs->track_id);
	track_display($track);
}
?>
<? track_footer(); ?>
<h4>Other Users Online</h4>
<div id="listing">
<table width="80%" cellpadding="5" cellspacing="1">
<tr>
	<th>User</th>
	<th>Track Name</th>
	<th>Artist</th>
	<th>Album</th>
</tr>
<?
$track = new music_list();
$source = new source();
$track_prefs = new track_pref();
$users = $track_prefs->getOnlineUsers(10);
if(!count($users)) {
	?>
		<td colspan="4">No other users online</td>
	<?
}
else {
	foreach($users as $username=>$track_id) {
		$track->get($track_id);
		$hosts = $source->getSourceHostsList($track_id);
	?>
	<tr>
		<td align="center"><?= $username ?></td>
		<td><a href="file.php?id=<?= $track_id ?>" title="Provided by <?= implode(", ",$hosts); ?>"><?= $track->TRACK_name ?></a></td>
		<td><a href="browse.php?type=artist&id=<?= $track->ARTIST_id ?>"><?= $track->ARTIST_name ?></a></td>
		<td><a href="browse.php?type=album&id=<?= $track->ALBUM_id ?>"><?= $track->ALBUM_name ?></a></td>
	</tr>
	<?
	}
}
?>
</table>
</div>
<p class="small">You are logged in as <?= get_remote_username() ?></p>
<? require("footer.inc"); ?>
