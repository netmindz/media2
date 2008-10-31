<? require("header.php"); ?>
<?
$as_account = new as_account();
$as_account->getByUsername(get_remote_username());
?>
<html>
<head>
<title>Media Archive - Powered by Netmindz</title>
<style type="text/css">
<!--
@import url("default.css");
-->
</style>
</head>
<body>
<h1>Music Archive</h1>

<div id="nav">
<div id="navmain">
<a href="index.php">Home</a>
<a href="new.php">New Tracks</a>
<!-- <a href="as_users.php">Audio Scrobbler</a> -->
<? if($as_account->as_username) { ?>
<a href="http://www.last.fm/user/<?= $as_account->as_username ?>">Last FM</a>
<? } else { ?>
<a href="http://www.last.fm/">Last FM</a>
<? } ?>
<a href="as_users.php">Audio Scrobbler</a>
</div>
</div>
<div id="nav">
<div id="navbrowse">
<a href="browse.php?type=artist">Artist</a>
<a href="browse.php?type=album">Album</a>
<a href="browse.php?type=genre">Genre</a>
</div>
<form action="search.php" method="post">
Search <input type="text" name="keyword" value="<? if(isset($_POST['keyword'])) print $_POST['keyword'] ?>"><input type="submit" value=" Search ">
</form>
</div>
<?
if(ereg("index.php",$PHP_SELF)) { 
	// TODO - fix the problem of duplicate sources
	$db = new database();
	$db->query("select SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as total_time from tracks inner join sources on sources.track_id=tracks.id  where _archived != 'y' and sources.host_id in (select id from hosts where hosts.status='online')");
	$row = $db->getNextRow();
	$total_time = explode(":",$row['total_time']);
	$days = floor($total_time[0] / 24);
	$hours = ($total_time[0] - ($days * 24));

	?>
	<h4><?= $days ?> Days <?= $hours ?> Hours <?= $total_time[1] ?> Minutes of music currently online</h4>
	<?
}
?>
