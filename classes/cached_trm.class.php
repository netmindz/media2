<?
require("cached_puid_template.php");

class cached_puid extends cached_puid_template {

	function lookup($path)
	{
		return($this->getByOther(array("path"=>$path)));
	}
	
	function cache($path,$puid)
	{
		$this->setProperties(array('path'=>$path,'puid'=>$puid),"addslashed");
		return($this->add());
	}

}
?>
