<?
ini_set("include_path",".:../classes:../includes:" . ini_get('include_path'));
require("standard.inc.php");


$albums = new album();
$albums->getList("where name like '%CD%' or name like '%disk%' or name like '%disc%'");
while($albums->getNext()) {
	if(eregi('-? ?\(?CD ?([0-9]+)\)?|-? \(?dis[ck] ?([0-9]+)\)?',$albums->name,$matches)) {
			print_r($matches);
			if($matches[1]) { $number = $matches[1]; } else { $number = $matches[2]; }
			$album = new album();
			$album->get($albums->id,"addslashes");
			$album->name = str_replace($matches[0],"",$album->name);
			$album->cd_number = $number;
			if($album->cd_number) $album->update();
			print "Changing '$albums->name' to '$album->name' cd number '$number'\n";
	}
	else {
		print "parse fail: $albums->name\n";
	}
	
}

$albums = new album();
$albums->getList("where name like '% -'");
while($albums->getNext()) {
		$album = new album();
		$album->get($albums->id,"addslashes");
		$album->name = str_replace(" -","",$album->name);
		$album->update();	
		print "Cleaning name $albums->name to $album->name\n";
}


?>
