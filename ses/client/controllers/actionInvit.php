<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";


if(user_get() == "")
	die();

ses_init();



if(!isset($_GET["id"]) || !isset($_GET["list"]))
{
	die();
}


$id = $_GET["id"];
$list = $_GET["list"];

$sender = $SES_ADDRESS;
$dateinvited = date("Y-m-d H:i:s");

$server = ses_getserver($sender);

$listservers = ses_listservers($id);
$listnewservers = array();

// make the list of new servers
$expl = explode(";", $list);
foreach($expl as $p)
{
	$pa = trim($p);
	$s = ses_getserver($pa);

	if($s != "" && !in_array($s, $listservers) && !in_array($s, $listnewservers))
	{
		$listnewservers[] = $s;
	}
}


// send invites to "old" servers
foreach($expl as $p)
{
	$pa = trim($p);
		
	if(($pa != "") && ses_isaddress($pa))
	{
		$keyinvit = ses_prepare_invit($sender, $id, $pa, $dateinvited);

		foreach($listservers as $s)
		{
			ses_query_invit($s, $keyinvit, $sender, $id, $pa, $dateinvited);
		}
	}
}

// send all to new servers
foreach($listnewservers as $s)
{
	ses_query_all_semail($s, $id);
}


	


?>
