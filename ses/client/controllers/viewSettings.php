<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();


$addr = user_get();
$user = User::find(array('conditions' => array("address = ?", $addr)));


if($user == null)
	die();
	



// call the view
include "../views/settings.php";

?>
