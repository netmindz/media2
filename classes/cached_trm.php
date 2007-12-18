<?
require("cached_trm_template.php");

class cached_trm extends cached_trm_template {

	function lookup($path)
	{
		return($this->getByOther(array("path"=>$path)));
	}
	
	function cache($path,$trm)
	{
		$this->setProperties(array('path'=>$path,'trm'=>$trm),"addslashed");
		return($this->add());
	}

}
?>
