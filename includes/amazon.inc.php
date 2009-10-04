<?php

function call_amazon($method,$request) {

	$sec = 'fU6jiqSr7I/FOvl/DlcOXWb4pbfpaxJU5q65O/mT';
	$keyid = "00KSGTK690RP8B0NWBR2"; 

	$service = 'AWSECommerceService';
	$ts = gmdate("Y-m-d\TH:i:s\Z");
	$base_str = $method.$ts;

	$sg = base64_encode(hash_hmac('sha256', $base_str, $sec, true));

	$client = new SoapClient("https://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl");
	$hset = Array('AWSAccessKeyId' => $keyid, 'Timestamp' => $ts, 'Signature'=> $sg);
	$h = Array();
	foreach ($hset as $k => $v) {
		array_push($h, new SoapHeader('http://security.amazonaws.com/doc/2007-01-01/', $k, $v));
	}
	$sr= $client->__setSoapHeaders($h);

	$a = Array("Service" => 'AWSECommerceService',"AWSAccessKeyId" => $keyid,"Operation" => $method, "Request" => $request);
	try {
		$result = $client->__soapCall($method, array($a));
	} catch (SoapFault $sf) {
		print $sf->getMessage();
		return null;
	}
	return($result);
}

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

	if($asin) {
		$result = $result = call_amazon('ItemLookup',array('IdType'=>'ASIN','ItemId'=>$asin));
   	}
    else {    	
		$result = call_amazon('ItemSearch',array('Keywords'     => htmlspecialchars($album),'SearchIndex'       => 'Music'));
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
			print "====<br>\n";
			$item = array("Artist"=>array());
			foreach($node->ItemAttributes as $at=>$val) {
			if(!is_object($val)) print "$at=$val<br/>\n";
				if(($at == "Artist")&&(!is_array($val))) {
					$item[$at] = array($val); // might be one artist or many
				}
				else {
					$item[$at] = $val;
				}
			}
			if(eregi("^$album",$item['Title'])) {

				print "found album $album, trying to verify artist<br>\n";

				foreach($item['Artist'] as $artist) {
					$artist = strtolower($artist);
					if(in_array($artist,$artists)) {
						print "Found match in amazon<br>\n";
						$details = $item;
						$albumObj = new album();
						$albumObj->getByName($album);
						$albumObj->setField("amazon_asin",$node->ASIN);
						break;
					}
				}
			}
		}
	}
	return($details);
}
?>
