<? require("header.inc"); ?>
<? require("as.php"); ?>
<h1>AudioScrobbler</h1>
<?
$as = new as_account();
$as->getByUsername(get_remote_username());
?>
<h3><?= $as->username ?></h3>
<?
if($_POST['new']) {
	$as = set_properties($as,$_POST['new']);
	$as_connection = new audioscrobbler(get_remote_username(),$as->as_username,$as->as_password);
	if($as_connection->_handshake() == 1) {
		if($as->id) {
			$as->update();
		}
		else {
			$as->username = get_remote_username();
			$as->add();
		}
		?>
		<h3>Update Complete</h3>
		<p><a href="as_users.php">View AudioScrobbler users</a></p>
		<?
	}
	else {
		?>
		<h3>Update Failed</h3>
		<p>Audioscrobbler responded: <?= $as_connection->lastError ?></p>
		<?
	}
}
else {
?>
	<form method="post">
	<table>
	<tr>
		<th><?= $as->CreateFormLabel("as_username"); ?></th>
		<td><?= $as->CreateFormObject("as_username","new[]"); ?></td>
	</tr>
	<tr>
		<th><?= $as->CreateFormLabel("as_password"); ?></th>
		<td><?= $as->CreateFormObject("as_password","new[]"); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value=" Update Account "></td>
	</tr>
	</table>
	</form>
<? } ?>
<? require("footer.inc"); ?>
