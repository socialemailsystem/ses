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



if(!isset($_GET["user"]) || !isset($_GET["semail"]) || !isset($_GET["server"]) || !isset($_GET["tags"]))
{
	die();
}




$useraddress = /*$_GET["user"]*/ $SES_ADDRESS;
$semail = $_GET["semail"];
$server = $_GET["server"];
$tags = $_GET["tags"];


if(!ses_isfavorite($useraddress, $semail))
{
	ses_favorite($useraddress, $semail, $tags, $server);
}

else
{
	ses_unfavorite($useraddress, $semail);
}

?>
