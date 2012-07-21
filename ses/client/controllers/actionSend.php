
<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";

ses_init();



if(!isset($_GET["id"]) || !isset($_GET["msg"]))
{
	die();
}


$id = $_GET["id"];
$msg = $_GET["msg"];


$datesent = date("Y-m-d H:i:s");
$sender = $SES_ADDRESS;
//$sender = "imspeaking::$SES_SERVER";

$listservers = ses_listservers($id);

$keymsg = ses_prepare_message($sender, $id, $msg, $datesent); // create key

// for each participating server
foreach($listservers as $s)
{
	ses_query_message($s, $keymsg, $sender, $id, $msg, $datesent); // send message
}


?>
