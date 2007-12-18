<? require("header.inc"); ?>
<h1>AudioScrobbler</h1>
<?
$as = new as_account();
$users = $as->getASUsersList();
foreach($users as $as_username=>$username) {
?>
<h3><?= $username ?></h3>
<p>
<img src="http://www.netmindz.net/media/as/nowplaying.php?user=<?= $as_username ?>"><br>
<a href="http://www.audioscrobbler.com/user/<?= $as_username ?>/">Goto Profile</a></p>
<?
}
?>
<p><a href="as_edit.php">Edit my AudioScrobber account details</a></p>
<? require("footer.inc"); ?>
