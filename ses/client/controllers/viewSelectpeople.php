<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["id"]))
{
	die();
}


$id = $_GET["id"];
$shortid = substr($id,0,20);


$user = $SES_ADDRESS;

$listcontact = ses_getcontacts($user);



// call the view
include "../views/selectpeople.php";

?>
