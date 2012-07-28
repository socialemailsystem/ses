<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();


if(!isset($_GET["nbrsemails"]) || !isset($_GET["nbrfeeds"]))
{
	die();
}

$nbrsemails = intval($_GET["nbrsemails"]);
$nbrfeeds = intval($_GET["nbrfeeds"]);



$lastsemails = ses_getlastsemails("$SES_ADDRESS", 0, $nbrsemails);
$feedsemails = ses_getfeeds($SES_ADDRESS, 0, $nbrfeeds);



// call the view
include "../views/main.php";

?>
