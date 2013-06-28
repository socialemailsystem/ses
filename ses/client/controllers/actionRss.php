<?php
//session_start();
?><?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";

//include "../client_config.php";
//include "../client_functions.php";

require_once("../lib/FeedWriter/FeedTypes.php");



ses_init();



if(!isset($_GET["user"]) && !isset($_GET["id"]))
{
	die();
}



if(isset($_GET["user"])) { // RSS feed from a user

	$user = $_GET["user"];
	$server = ses_getserver($user);

	$js = ses_query_getfeed($server, $user, 0, 20, '');
	$tab = json_decode($js, true);

	//var_dump($tab);


	$TestFeed = new ATOMFeedWriter();

	$TestFeed->setTitle("SeS RSS Feed - $user");
	$TestFeed->setLink("http://$server/ses/client/index.php");

	$TestFeed->setChannelElement('updated', date(DATE_ATOM , time()));
	$TestFeed->setChannelElement('author', array('name'=>"$user"));

	
	foreach($tab as $t) { // for each SeMail of the feed

		$lastmsg = $t['msg'];

		if(count($lastmsg) >= 1) {
			$lastmsg = $lastmsg[count($lastmsg) - 1];
			$lmuser = $lastmsg['address'];
			$lastmsg = substr($lastmsg['content'],0,64) . (strlen($lastmsg['content']) > 64 ? " [...]" : "");
			$lastmsg = htmlentities($lastmsg,0,"UTF-8");
		}

		else {
			$lastmsg = '';
			$lmuser = '';
		}

		
		$id = $t['id'];
		
		$tags = $t['tags'];
		
		$mytags = '';
		
		$expl = explode(";", $tags);
		foreach($expl as $ta)
		{
			$ta = htmlentities(trim($ta),0,"UTF-8");
						
			if($ta != "")
			{
				$mytags .= "#$ta ";
			}
		}
		
		$ro = $t['readonly'] == '0' ? "No" : "Yes";
		$dcr = $t['datecreated'];
		$dac = $t['dateactive'];
		

		$newItem = $TestFeed->createNewItem();
	

		$newItem->setTitle("SeMail : $mytags");
		$newItem->setLink("http://$server/ses/client/index.php?displaypublic=$id&displayserver=$server");
		$newItem->setDate($dac/*$dcr*/);
		
		$newItem->setDescription("ID : $server;$id<br />Read only : $ro<br />Date created : $dcr<br /><br />" . ($lmuser == '' ? 'No message.' : "Last message the $dac by $lmuser :<br /><br />$lastmsg"));


		$TestFeed->addItem($newItem);
	}


	$TestFeed->generateFeed();
}


else { // RSS feed from a SeMail

	$id = $_GET["id"];
	$server = $_GET["server"];


	$js = ses_query_getpublic($server, $id);
	$tab = json_decode($js, true);


	//var_dump($tab);
	
	$user = $tab['owneraddress'];
	
	
	$tags = $tab['tags'];
		
	$mytags = '';
		
	$expl = explode(";", $tags);
	foreach($expl as $ta)
	{
		$ta = htmlentities(trim($ta),0,"UTF-8");
					
		if($ta != "")
		{
			$mytags .= "#$ta ";
		}
	}


	$tabmsg = array_reverse($tab['msg']);
	
	
	$TestFeed = new ATOMFeedWriter();

	$TestFeed->setTitle("SeMail RSS Feed - $user : $mytags");
	$TestFeed->setLink("http://$server/ses/client/index.php?displaypublic=$id&displayserver=$server");

	$TestFeed->setChannelElement('updated', date(DATE_ATOM , time()));
	$TestFeed->setChannelElement('author', array('name'=>"$user"));

	
	foreach($tabmsg as $t) { // for each message on the SeMail

		
		$content = $t['content'];
		$content = substr($content,0,64) . (strlen($content) > 64 ? " [...]" : "");
		$content = htmlentities($content,0,"UTF-8");

		$address = $t['address'];
		$datesent = $t['datesent'];
		

		$newItem = $TestFeed->createNewItem();
	

		$newItem->setTitle("Message the $datesent by $address");
		$newItem->setLink("http://$server/ses/client/index.php?displaypublic=$id&displayserver=$server");
		$newItem->setDate($datesent);
		
		$newItem->setDescription("$content");


		$TestFeed->addItem($newItem);
	}

	
	$TestFeed->generateFeed();

}



?>
