<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();


if(!isset($_GET["id"]) || !isset($_GET["server"]))
{
	die();
}

$id = $_GET["id"];
$server = $_GET["server"];

/*if($server == '')
	$server == $SES_SERVER;*/


// call the view
include "../views/share.php";

?>
