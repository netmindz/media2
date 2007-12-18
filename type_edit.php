<? require("header.inc"); ?>
<h1>Update <?= $type ?></h1>
<?
if($_POST['process']) {
	$details = $_POST['details'];
	$typeObj = new $type();
	$typeObj->get($id,"addslashes");

	$typeObj= set_properties($typeObj,$details);
	$typeObj->update();
	
	foreach($batch as $property=>$value) {
		$tracks = new track();
		if($value) {
			$count = $tracks->batchUpdate($property,$value,"${type}_id=$id");
			print "updated $count tracks' " . $tracks->createFormLabel($property) . " <br>\n";
		}
	}
	?>
	<h3><?= ucwords($type) ?> Update Complete</h3>
	<p><a href="javascript:history.go(-2)">Back to Listing</a></p>
	<?
}
else {
	$typeObj = new $type();
	$typeObj->get($_GET['id']);
	?>

	<div id="listing">
	<form method="post">
	<input type="hidden" name="process" value="1">
	<input type="hidden" name="id" value="<?= $typeObj->id ?>">
	<input type="hidden" name="type" value="<?= $type ?>">
	<table cellpadding="5" border=1>
	<tr>
		<th><?= $typeObj->createFormLabel("name") ?></th>
		<td>
			<?= 	$typeObj->createFormObject("name","details[]",$typeObj->name); ?>
		</td>
	</tr>
	<?
	if($type == "album") {
	$genre = new genre();
		?>
		<tr>
			<th><?= $typeObj->createFormLabel("cd_number") ?></th>
                <td>
                        <?=     $typeObj->createFormObject("cd_number","details[]",$typeObj->cd_number); ?>
                </td>
		</tr>
		<tr>
			<th><?= $typeObj->createFormLabel("album_artist_id") ?></th>
                <td>
                        <?=     $typeObj->createFormObject("album_artist_id","details[]",$typeObj->album_artist_id); ?>
                </td>
		</tr>
		<tr>
			<th>Genre</th>
			<td>
				<?= 	$genre->select("batch[genre_id]"); ?>
			</td>
		</tr>
      <tr>
			<th><?= $typeObj->createFormLabel("amazon_asin") ?></th>
			<td><?= $typeObj->createFormObject("amazon_asin","details[]",$typeObj->amazon_asin); ?></td>
      </tr>

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
