<?
require("header.php");

$id = $_REQUEST['id'];
$type = $_REQUEST['type'];
$mod = $_REQUEST['mod'];

$type_pref = new type_pref($type,$id);

if($mod == "love") {
	$val = 20;
}
elseif($mod == "hate") {
	$val = -20;
}
else {
	trigger_error("invalid mod");
}
$type_pref->updatePref($val);
header("Location: " . $_SERVER['HTTP_REFERER'] . "&type_pref_updated=1");
print "update complete";

?>
