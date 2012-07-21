<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";

ses_init();



if(!isset($_GET["user"]) || !isset($_GET["contact"]))
{
	die();
}


$useraddress = $_GET["user"];
$contactaddress = $_GET["contact"];


ses_follow($useraddress, $contactaddress);

?>