
<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";

ses_init();


if(!isset($_GET["nbrsemails"]))
{
	die();
}

$nbrsemails = intval($_GET["nbrsemails"]);



$lastsemails = ses_getlastsemails("$SES_ADDRESS", 0, $nbrsemails);



// call the view
include "../views/main.php";

?>
