<?
require("as_account_template.php");

class as_account extends as_account_template {
		
		function getByUsername($username)
		{
			return($this->getByOther(array('username'=>$username)));
		}

		function getASUsersList()
		{
			$tmp = new as_account();
			$tmp->getList();
			$list = array();
			while($tmp->getNext()) {
				$list[$tmp->as_username] = $tmp->username;
			}
			return($list);
		}

}
?>
