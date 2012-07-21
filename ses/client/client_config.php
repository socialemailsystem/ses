
<?php

// Client configuration file


//require_once("config.php");



$SES_TITLE = "Social eMail System - Beyond eMail and social network";

$SES_WELCOME = "Welcome to your Social eMail System server !";


$SES_CSS = array (
	"ses.css",
	"jquery.noty.css",
	"noty_theme_mitgux.css",
	"cupertino/jquery-ui-1.8.21.custom.css",
	"defaultnotif.css"
);

$SES_JS = array (
	"client_functions.js",
	"jquery-1.7.2.min.js",
	"jquery-ui-1.8.21.custom.min.js",
	"jquery.validate.min.js",
	"additional-methods.min.js",
	"promise.js",
	"jquery.noty.js",
	"notification.js",
	"jquery.caret.1.02.js"
);


$SES_ADDRESS = "tests::$SES_SERVER";
//$SES_ADDRESS = $_SESSION["SES_ADDRESS"];

$SES_FREQPING = 3500;


?>
