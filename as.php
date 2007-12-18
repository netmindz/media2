	<?
require_once("header.php");

if(basename($PHP_SELF) == "as.php") {
	?>
	<html>
	<head>
	<meta http-equiv="refresh" content="60">
	</head>
	<body>
	<?= date("r"); ?>
	<pre>
	<?
	$as_account = new as_account();
	$as_account->getList();
	while($as_account->getNext()) {
		print "Sending $as_account->username spool\n";
		flush();
		$as = new audioscrobbler($as_account->username,$as_account->as_username,$as_account->as_password);
		
		$as_result = $as->send();
		if($as_result != 1) {
			print "error = $as->lastError<br>($as_result)\n";
		}
		print "\n";
	}
	?>
	</pre>
	</body>
	</html>
	<?
}
?>
