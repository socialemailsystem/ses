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



$user = $SES_ADDRESS;


// call the view
include "../views/inputfollow.php";

?>
