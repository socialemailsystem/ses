<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["registeraddr"]) || !isset($_GET["registerpwd"]) || !isset($_GET["registermail"]))
{
	die();
}


$registeraddr = $_GET["registeraddr"]."::$SES_SERVER";
$registerpwd = $_GET["registerpwd"];
$registermail = $_GET["registermail"];


$user = User::find(array('conditions' => array("address = ?", $registeraddr)));

if($user == null && ses_isaddress($registeraddr))
{
	$o = User::create(array("address" => $registeraddr, "pwd" => hash("sha256",$registerpwd), "mail" => $registermail));
	$o->save();
	
	$_SESSION["ses_message"] = "Address $registeraddr successfully created.";
}

else
{
	if($user != null)
		$_SESSION["ses_message"] = "Error while creating address $registeraddr : the user already exists.";
	else
		$_SESSION["ses_message"] = "Error while creating address $registeraddr : bad address.";
}


header('Location: ../index.php'); 


?>
