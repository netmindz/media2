<? require("header.inc"); ?>
<h4>Browse by <a href="browse.php?type=artist">Artist</a> || <a href="browse.php?type=album">Album</a></h4>
<form action="fuzzy_test.php" method="post">
Keywords <input type="text" name="keyword" value="<?= $_POST['keyword'] ?>"><input type="submit" value=" Search">
</form>
<?
require_once("fuzzysearch.php");
print_r_html(FuzzySearch::singleWordMatches($_POST['keyword'],"artists","name",$classname));
?>
<? require("footer.inc"); ?>
