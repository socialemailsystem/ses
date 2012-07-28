<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["id"]) || !isset($_GET["msg"]))
{
	die();
}


$id = $_GET["id"];
$msg = $_GET["msg"];


$o = Semail::find($id);
if($o != null)
{
	$type = $o->type;
	
	$datesent = date("Y-m-d H:i:s");
	$sender = $SES_ADDRESS;
	
	$keymsg = ses_prepare_message($sender, $id, $msg, $datesent); // create key
	
	if($type == "0")
	{
		$s = ses_getserver($o->owneraddress);
		ses_query_message($s, $keymsg, $sender, $id, $msg, $datesent); // send message
	}
	
	else
	{
		$listservers = ses_listservers($id);

		// for each participating server
		foreach($listservers as $s)
		{
			ses_query_message($s, $keymsg, $sender, $id, $msg, $datesent); // send message
		}
	}
}


?>
