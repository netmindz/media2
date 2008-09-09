<?
require("dead_source_template.php");

class dead_source extends dead_source_template {
		
	function isDead($path)
	{
		return($this->getList("where path=\"$path\""));
	}

}
?>
