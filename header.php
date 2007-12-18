<? ini_set("include_path",".:classes:includes:" . ini_get("include_path")); ?>
<?
require("includes/standard.inc.php");
require("includes/amazon.inc.php");

function track_header()
{
?>
<div id="listing">
<table width="80%" cellpadding="5" cellspacing="1">
	<tr>
		<th>Track No</th>
		<th>Track Name</th>
		<th>Artist</th>
		<th>Album</th>
		<th>Edit</th>
	</tr>
<?
}

function track_display($track)
{
		$source = new source();
		$hosts = $source->getSourceHostsList($track->TRACK_id);
		$source->getBestSource($track->TRACK_id);
		// print_r($track);
		?>
		<tr>
			<td width="1%" align="center"><?= $track->TRACK_tracknum ?></td>
			<?
			if($source->id) { ?>
				<td><a href="file.php?id=<?= $track->TRACK_id ?>" title="Provided by <?= implode(", ",$hosts) ?>"><?= $track->TRACK_name ?></a></td>
			<? } else { ?>
				<td><?= $track->TRACK_name ?></td>
			<? } ?>
			<td align="center"><a href="browse.php?type=artist&id=<?= $track->ARTIST_id ?>"><?= $track->ARTIST_name ?></a></td>
			<td align="center"><a href="browse.php?type=album&id=<?= $track->ALBUM_id ?>"><?= $track->ALBUM_name ?><? if($track->ALBUM_cd_number) echo " - CD $track->ALBUM_cd_number"; ?></a></td>
			<td align="center">[&nbsp;<a href="edit.php?id=<?= $track->TRACK_id ?>">edit</a>&nbsp;]</td>
		</tr>
		<?
}
function track_footer()
{
?>
	</table>
	</div>
<?
}
?>
