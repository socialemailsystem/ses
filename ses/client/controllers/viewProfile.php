<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

require_once("../lib/nbbc/nbbc.php");


if(user_get() == "")
	die();


ses_init();


if(!isset($_GET["addr"]))
{
	die();
}



$addr = htmlentities($_GET["addr"]); // address

$server = ses_getserver($addr);


$tab = json_decode(ses_query_getprofile($addr), true); // get the profile

if(count($tab) == 0)
	die();



$about = /*htmlentities(*/$tab['descr']/*,0,"UTF-8")*/;


$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);



// call the view
include "../views/profile.php";

?>
