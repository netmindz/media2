<?
require("host_template.php");

class host extends host_template {

	function getOnlineHosts($order="")
	{
		return($this->getList("where status='online' and url_base != ''",$order));
	}		

}
?>
