<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["loginaddr"]) || !isset($_GET["loginpwd"]))
{
	die();
}


$loginaddr = $_GET["loginaddr"]."::$SES_SERVER";
$loginpwd = $_GET["loginpwd"];


$user = User::find(array('conditions' => array("address = ?", $loginaddr)));

// user exists
if($user != null)
{
	$pwddb = $user->pwd;
	$pwd = hash("sha256", $loginpwd);
	
	// the password is good
	if($pwddb == $pwd)
	{
		user_login($loginaddr);
		
		$_SESSION["ses_message"] = "Logged in with address $loginaddr.";
	}
	
	else
	{
		$_SESSION["ses_message"] = "Password invalid for address $loginaddr.";
	}
}

else
{
	$_SESSION["ses_message"] = "Address $loginaddr does not exist on the server.";
}


header('Location: ../index.php'); 


?>
