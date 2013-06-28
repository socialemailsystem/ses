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



if(!isset($_GET["user"]) || !isset($_GET["contact"]))
{
	die();
}


$useraddress = /*$_GET["user"]*/ $SES_ADDRESS;
$contactaddress = $_GET["contact"];


echo (ses_isfollowing($useraddress, $contactaddress) ? "1" : "0");

?>
