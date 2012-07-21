<?php


// main controller



$actions = array("create", "invit", "getfeed", "message", "validate");

if(isset($_GET["action"]) && in_array($_GET["action"], $actions)
   && isset($_GET["key"]) && isset($_GET["sender"]) && isset($_GET["id"]))
{
    require_once("ses.php");
	
	
	//echo $_SERVER["QUERY_STRING"]."<br />";
	
    $action = $_GET["action"]; // command name
	$key = $_GET["key"]; // key
	$sender = $_GET["sender"]; // sender address
	$server = ses_getserver($sender); // its server
	$id = $_GET["id"]; // semail id

	
	ses_init();
	
	
	// create a new SeMail
	if($action == "create" && isset($_GET["type"]) && isset($_GET["list"]) && isset($_GET["readonly"]) && isset($_GET["tags"]) && isset($_GET["datecreated"]))
	{
	    $type = $_GET["type"]; // semail type (0 : public, 1 : on invit, 2 : private)
		$list = $_GET["list"]; // list of addresses (ex : "johndoe::server1.com ; darkvador::server2.com")
		$readonly = $_GET["readonly"]; // read only (only the owner can send messages)
		$tags = $_GET["tags"]; // tags
		$datecreated = $_GET["datecreated"]; // date of creation

		if(ses_validate(ses_sum("create::".$type."::".$list."::".$readonly."::".$tags."::".$datecreated, $id, $sender), $key, $server))
			ses_create($key, $sender, $id, $type, $list, $readonly, $tags, $datecreated);
	}
	
	// invit someone in a SeMail
	else if($action == "invit" && isset($_GET["address"]) && isset($_GET["dateinvited"]))
	{
	    $address = $_GET["address"]; // address to invit
		$dateinvited = $_GET["dateinvited"]; // date of invit

		if(ses_validate(ses_sum("invit::".$address."::".$dateinvited, $id, $sender), $key, $server))
			ses_invit($key, $sender, $id, $address, $dateinvited);
	}
	
	// get the last public SeMails of someone
	else if($action == "getfeed" && isset($_GET["address"])&& isset($_GET["from"])&& isset($_GET["limit"]))
	{
	    $address = $_GET["address"]; // address
		$from = $_GET["from"]; // from
		$limit = $_GET["limit"]; // limit
		
		echo ses_getfeed($address, $from, $limit);
	}
	
	// send a message
	else if($action == "message" && isset($_GET["message"]) && isset($_GET["datesent"]))
	{
	    $message = $_GET["message"]; // message to send
		$datesent = $_GET["datesent"]; // date and time when was sent the message

		if(ses_validate(ses_sum("message::".$message."::".$datesent, $id, $sender), $key, $server))
			ses_message($key, $sender, $id, $message, $datesent);
	}
	
	// validate a key
	else if($action == "validate")
	{
		echo ses_getsumbykey($key);
	}
	
	else
	{
	    echo "Bad query.<br />";
	}


	
	// tests
	

}

else
{
    echo "Bad command name.<br />";
}


?>
