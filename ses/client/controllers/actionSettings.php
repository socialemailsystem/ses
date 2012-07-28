<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

ses_init();



if(!isset($_GET["settingsaddr"]) || !isset($_GET["settingspwd"]) || !isset($_GET["settingsmail"]))
{
	die();
}


$settingsaddr = $_GET["settingsaddr"];
$settingspwd = $_GET["settingspwd"];
$settingsmail = $_GET["settingsmail"];


$user = User::find(array('conditions' => array("address = ?", $settingsaddr)));

if($user != null && ses_isaddress($settingsaddr) && user_get() == $settingsaddr)
{
	if($settingspwd != "")
		$user->pwd = hash("sha256",$settingspwd);
	$user->mail = $settingsmail;
	$user->save();
	
	echo "Address $settingsaddr successfully updated.";
}

else
{
	echo "Error while updating address $settingsaddr.";
}

//header('Location: ../index.php'); 



?>
