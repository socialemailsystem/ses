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



// call the view
include "../views/share.php";

?>
