<?
function amazon_getAlbum($artists,$album,$asin)
{
	if((!count($artists))&&(!$asin)) {
//		print "no artists supplied and no asin either<br>\n";
		return(false);
	}
	
	foreach($artists as $key=>$tmp) {
		$artists[$key] = strtolower($tmp);
	}
	
	
	$album = eregi_replace(" - CD ?[0-9]","",$album);

	#$soapclient = new soapclient("http://soap-eu.amazon.com/schemas3/AmazonWebServices.wsdl?locale=uk");
	$soapclient = new soapclient("http://ecs.amazonaws.com/AWSECommerceService/2008-04-07/UK/AWSECommerceService.wsdl");
	
	#print_r_html($soapclient->__getFunctions());
	
	
	$params = array (
		'Request'=>array(
			'Keywords'	=> htmlspecialchars($album),
			'SearchIndex'	=> 'Music',
		),
		'AWSAccessKeyId'	=> '00KSGTK690RP8B0NWBR2',
	);

	try {	
		if($asin) {
	                $result = $soapclient->ItemLookup(array('AWSAccessKeyId'=>'00KSGTK690RP8B0NWBR2','Request'=>array('IdType'=>'ASIN','ItemId'=>$asin)));
        	}
        	else {
			# print_r_html($params);
			$result = $soapclient->ItemSearch($params);
		}
	
	} Catch (SoapFault $soapError) {
		print_r($soapError);
		return(false);	
	}
	
	if(!isset($result->Items->Item)) return(false);

	$items = $result->Items->Item;
	$details = array();
	foreach($items as $item) {
		if($item->ASIN == $asin) {
                	$details = $item;
                        break;
		}
	}
	if(!$details) {
		foreach($items as $node) {
			$item = array("Artist"=>array());
			foreach($node->ItemAttributes as $at=>$val) {
				if(($at == "Artist")&&(!is_array($val))) {
					$item[$at] = array($val); // might be one artist or many
				}
				else {
					$item[$at] = $val;
				}
			}
			if(eregi("^$album",$item['Title'])) {

#				print "found album $album, trying to verify artist<br>\n";

				foreach($item['Artist'] as $artist) {
					$artist = strtolower($artist);
					if(in_array($artist,$artists)) {
						print "Found match in amazon<br>\n";
						$details = $item;
						$albumObj = new album();
						$albumObj->getByName($album);
						$albumObj->amazon_asin = $node->ASIN;
						$albumObj->set("amazon_asin");
						break;
					}
				}
			}
		}
	}
	return($details);
}
?>
