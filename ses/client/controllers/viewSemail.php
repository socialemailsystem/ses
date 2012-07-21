
<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";

ses_init();



if(!isset($_GET["id"]))
{
	die();
}


$id = $_GET["id"];

$shortid = substr($id,0,20);

$semail = Semail::find($id);
$messages = Message::all(array('conditions' => array("semail_id = ?", $id), 'order' => 'datesent'));


// type

$typ = $semail->type;

if($typ == "0")
	$type = "Public";
else if($typ == "1")
	$type = "On Invit";
else
	$type = "Private";
		

$readonly = !($semail->readonly == "0" || ses_isowner($SES_ADDRESS, $id));


$listaddress = ses_listaddresses($id);


// call the view
include "../views/semail.php";

?>
