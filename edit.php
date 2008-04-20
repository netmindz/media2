<? require("header.inc"); ?>
<?
$properties = array("tracknum","name","artist_id","album_id","genre_id","year");
settype($id,"int");

if(isset($_POST['process'])) {
	$details = $_POST['details'];
	$track = new track();
	$track->get($id,"addslashes");
	foreach($_POST['new'] as $key=>$value) {
		if($value != "") {
			$class = $track->_field_descs[$key]['fk'];
			$tmp = new $class();
			$tmp->lookupOrAdd($value,"skip fuzzy");
			$details[$key] = $tmp->id;
			print "setting $key to new value of $value ($details[$key])<br>\n";
		}
	}
		
	$track = set_properties($track,$details);
	$track->mb_verified = 'n';
	$track->_update_id3 = 'y';
	$track->update();
	?>
	<h3>Update Complete</h3>
	<p><a href="javascript:history.go(-2)">Back to tracks</a></p>
	<?
}
else {
	$track = new track();
	$track->get($id);
	?>
	<div id="listing">
	<form method="post">
	<input type="hidden" name="process" value="1">
	<input type="hidden" name="id" value="<?= $id ?>">
	<table cellpadding="5" border=1>
	<?
	foreach($properties as $prop_name) { ?>
	<tr>
		<th><?= $track->createFormLabel($prop_name,"details[]") ?></th>
		<td>
			<?= 	$track->createFormObject($prop_name,"details[]"); ?>
			<? if(isset($track->_field_descs[$prop_name]['fk'])) {  ?><br>new <input type="text" name="new[<?= $prop_name ?>]" size="30"><? } ?>
		</td>
	</tr>
	<? if($prop_name == "artist") exit("eak"); ?>
	<? } ?>
	<tr>
		<th>&nbsp;</th>
		<td align="left"><input type="submit" value=" Save Details "></td>
	</tr>
	</table>
	</form>
	</div>
	<?
}
?>
<? require("footer.inc"); ?>
