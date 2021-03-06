<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";

require_once("../lib/nbbc/nbbc.php");
//require_once("../lib/jbbcode-1.2.0/Parser.php");

ses_init();



if(!isset($_GET["id"]))
{
	die();
}


$id = $_GET["id"];

$shortid = substr($id,0,20);

// local SeMail (On Invite and Private)
if(!isset($_GET["server"]) || $_GET["server"] == "")
{
	if(user_get() == "")
		die();


	// verify if the user has the rights to view the SeMail


	// if the sender is not a participant or it's not a public SeMail --> gtfo
	$ip = ses_isparticipant($SES_ADDRESS, $id);
	$type = ses_gettype($id);


	if(!($ip || $type == 0))
	{
		die();
	}


	$server = "";
	$semail = Semail::find($id);

	$messages = Message::all(array('conditions' => array("semail_id = ?", $id)/*, 'order' => 'datesent'*/, 'order' => 'id'));
}

// Public distant SeMail
else
{
	$server = $_GET["server"];
	
	// get the SeMail in json format
	$r = ses_query_getpublic($server, $id);

	$r = json_decode($r, true);

	if($r == '' || $r == "''" || $r == null)
	{
		die();
	}

	
	// json -> SeMail object
	$semail = new Semail();
	$semail->id = $r["id"];
	$semail->type = $r["type"];
	$semail->readonly = $r["readonly"];
	$semail->owneraddress = $r["owneraddress"];
	$semail->list = $r["list"];
	$semail->tags = $r["tags"];
	$semail->datecreated = $r["datecreated"];
	$semail->dateactive = $r["dateactive"];
	
	$msg = $r["msg"];
	$messages = array();
	
	if($msg != "")
	{
		// json -> array of Message objects
		foreach($msg as $m)
		{
			$nm = new Message();
			$nm->id = $m["id"];
			$nm->content = $m["content"];
			$nm->address = $m["address"];
			$nm->datesent = $m["datesent"];
			$nm->semail_id = $m["semail_id"];
			
			$messages[] = $nm;
		}
	}
}


// type

$typ = $semail->type;

if($typ == "0")
	$type = "Public";
else if($typ == "1")
	$type = "On Invite";
else
	$type = "Private";
		

$isowner = ses_isowner($SES_ADDRESS, $id);
$readonly = !($semail->readonly == "0" || $isowner);


$listaddress = ses_listaddresses($id);


// call the view
include "../views/semail.php";

?>
