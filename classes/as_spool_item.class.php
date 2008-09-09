<?
require("as_spool_item_template.php");

class as_spool_item extends as_spool_item_template {

	function addTrack($as_account_id,$track_id)
	{
		$this->as_account_id=$as_account_id;
		$this->track_id=$track_id;
		$this->played = "NOW()";
		return($this->add());
	}	

}
?>
