<?php

/*
    Social eMail System
    Copyright (C) 2012-2013 Jeremy DAFFIX

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// main controller



$actions = array("create", "invit", "getfeed", "getprofile", "getpublic", "message", "validate");

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
	    $type = $_GET["type"]; // semail type (0 : public, 1 : on invite, 2 : private)
		$list = $_GET["list"]; // list of addresses (ex : "johndoe::server1.com ; darkvador::server2.com")
		$readonly = $_GET["readonly"]; // read only (only the owner can send messages)
		$tags = $_GET["tags"]; // tags
		$datecreated = $_GET["datecreated"]; // date of creation

		if(ses_validate(ses_sum("create::".$type."::".$list."::".$readonly."::".$tags."::".$datecreated, $id, $sender), $key, $server))
			ses_create($key, $sender, $id, $type, $list, $readonly, $tags, $datecreated);
	}
	
	// invite someone in a SeMail
	else if($action == "invit" && isset($_GET["address"]) && isset($_GET["dateinvited"]))
	{
	    $address = $_GET["address"]; // address to invite
		$dateinvited = $_GET["dateinvited"]; // date of invite

		if(ses_validate(ses_sum("invit::".$address."::".$dateinvited, $id, $sender), $key, $server))
			ses_invit($key, $sender, $id, $address, $dateinvited);
	}
	
	// get the last public SeMails of someone
	else if($action == "getfeed" && isset($_GET["address"])&& isset($_GET["from"])&& isset($_GET["limit"]) && isset($_GET["tags"]))
	{
	        $address = $_GET["address"]; // address (or list of addresses)
		$from = $_GET["from"]; // from
		$limit = $_GET["limit"]; // limit
		$tags = $_GET["tags"]; // tags
		
		echo ses_getfeed($address, $from, $limit, $tags);
	}
	
	// get a profile
	else if($action == "getprofile" && isset($_GET["address"]))
	{
	    $address = $_GET["address"]; // user address

		echo ses_getprofile($address);
	}
	
	// get a Public SeMail
	else if($action == "getpublic")
	{
		echo ses_getpublic($id);
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
