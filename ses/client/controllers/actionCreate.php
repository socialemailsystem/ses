<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["type"]) || !isset($_GET["tags"]) || !isset($_GET["readonly"]))
{
	die();
}


$type = $_GET["type"];
$tags = $_GET["tags"];
$readonly = $_GET["readonly"];


$sender = $SES_ADDRESS;
$list = "";
$datecreated = date("Y-m-d H:i:s");

$idk = ses_prepare_create($sender, $type, $list, $readonly, $tags, $datecreated);

//echo "key : $idk[0]<br />";
//echo "id : $idk[1]<br /><br />";


ses_query_create($SES_SERVER, $idk[0], $sender, $idk[1], $type, $list, $readonly, $tags, $datecreated);


echo $idk[1];


?>
