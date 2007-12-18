<? require("header.inc"); ?>
<h1>New Tracks</h1>
<? track_header(); ?>
<?
$list = new music_list();
$list->getNewTracksList(100);
while($list->getNext()) {
	track_display($list);
}
?>
<? track_footer(); ?>
<? require("footer.inc"); ?>
