
<?php


// Social eMail System functions


require_once("config.php");


// initialization function
function ses_init()
{
	# include the ActiveRecord library
	require_once 'php-activerecord/ActiveRecord.php';
	
		// #!ck magic quotes
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			function stripslashes_deep($value)
			{
				return is_array($value) ?
					array_map('stripslashes_deep', $value) :
					stripslashes($value);
			}
			$_POST    = array_map('stripslashes_deep', $_POST);
			$_GET     = array_map('stripslashes_deep', $_GET);
			$_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
			$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}
	
	
		ActiveRecord\Config::initialize(function($cfg)
		{
			global $SES_SQL;
			
			try {
				$cfg->set_model_directory(dirname(__FILE__).'/models/');
				$cfg->set_connections(array('development' => $SES_SQL));
				
				$test = new Semail(); // avoid displaying the connection informations if there is an error...
				
			} catch (Exception $e) {
				die("Database error");
			}

		});
	
}


// calculate a key (used for validation)
function ses_key($msg, $id, $address)
{
    return hash("sha256", $msg.$id.$address.rand()).$id.$address.rand();
}

// calculate the md5 sum of a message
function ses_sum($msg, $id, $address)
{
//echo "<br />*** calculating sum for $msg::.$id.::$address ***<br />";
    return hash("md5", $msg."::".$id."::".$address);
}

// calculate an id for a new SeMail
function ses_id($sender, $type, $list)
{
    return hash("sha256", $sender.$type.$list.rand(0,999999999)).$sender.$type.rand(0,999999999);
}


// return if an address is valid
function ses_isaddress($address)
{
    //return preg_match("#^[\w.-]+::[\w.-]+\.[a-zA-Z]{2,6}$#", $address);
	return preg_match("#^[\w.-]+::[\w.-/]+(\.[a-zA-Z]{2,6})?$#", $address);
}


// get the server name from an address
function ses_getserver($address)
{
    $ret = "";
	
	$expl = explode("::", $address);
	if(count($expl) == 2 && ses_isaddress($address))
	    $ret = $expl[1];
	
	return $ret;
}


// ask the sender's server if the sum is ok
function ses_validate($sum, $key, $server)
{
	/*if(!ses_checkserver($server))
		return false;*/
		
	$url = "http://$server/ses/server/validate?id=0&key=$key&sender=0";
	$serversum = file_get_contents($url);
	
	//echo "<br />    url : $url<br />    sum : '$sum'<br />    url sum : '$serversum'<br /><br />";
	
	return (trim($sum) == trim($serversum));
}


// return the md5 sum for validation with the key
function ses_getsumbykey($key)
{
	$ret = null;

	$o = Validate::first(array('conditions' => array("`key` = ?", $key)));
	
	if($o != null)
		$ret = $o->md5sum;
		
	return $ret;
}


// return true if the address is a participant or the owner of a SeMail 
function ses_isparticipant($address, $id)
{
	$o = Participant::first(array('conditions' => array("semail_id = ? AND address = ?", $id, $address)));
	$o2 = Semail::first(array('conditions' => array("id = ? AND owneraddress = ?", $id, $address)));
	
	return ($o != null || $o2 != null);
}


// return true if the address is the owner of a SeMail 
function ses_isowner($address, $id)
{
	$o = Semail::first(array('conditions' => array("id = ? AND owneraddress = ?", $id, $address)));
		
	return $o != null;
}


// return the type of a SeMail (0 : public, 1 : on invit, 2 : private),
// or -1 if it doesn't exist
function ses_gettype($id)
{
	$ret = -1;
	
	$o = Semail::find($id);
	
	if($o != null)
		$ret = $o->type;
	
	return $ret;
}


// return true if the SeMail is read only
function ses_getreadonly($id)
{
	$o = Semail::find($id);
	$ro = $o->readonly;
	
	return !($ro == "0" || $ro == "");
}


// return a list of all participating servers to a SeMail
function ses_listservers($id)
{
	$ret = array();
	
	$listpar = Participant::all(array('conditions' => array("semail_id = ?", $id)));

	foreach($listpar as $l)
	{
		$ret[] = ses_getserver($l->address);
	}
	
	return array_unique($ret);
}


// return a list of all participating addresses to a SeMail
function ses_listaddresses($id)
{
	$ret = array();
	
	$listpar = Participant::all(array('conditions' => array("semail_id = ?", $id)));

	foreach($listpar as $l)
	{
		$ret[] = $l->address;
	}
	
	return array_unique($ret);
}


function ses_query_all_semail($server, $id)
{
	// send all the creates, invits and messages for a SeMail to a new participant
	// (must be called ONLY if the SeMail doesn't exist on the receiving server)
	
	
	$o = Semail::find($id);
	
	if($o->type == "1")
	{
		// send the create

		ses_query_create($server, $o->commandkey, $o->owneraddress, $id, $o->type, $o->list, $o->readonly, $o->tags, $o->datecreated->format('Y-m-d H:i:s'));
		
		
		// send the invits
		
		$listpar = Participant::all(array('conditions' => array("semail_id = ? AND commandkey != ''", $id)));
		
		foreach($listpar as $l)
		{
			ses_query_invit($server, $l->commandkey, $l->commandsender, $id, $l->address, $l->dateinvited->format('Y-m-d H:i:s'));
		}
		
		
		// send the messages
		
		$listmsg = Message::all(array('conditions' => array("semail_id = ?", $id)));
		
		foreach($listmsg as $l)
		{
			ses_query_message($server, $l->commandkey, $l->address, $id, $l->content, $l->datesent->format('Y-m-d H:i:s'));
			
		}
	}
}


// return the last active semails of an address
function ses_getlastsemails($address, $from, $limit)
{
	$address = Semail::connection()->escape($address);
	$from = intval($from);
	$limit = intval($limit);
	
	// first block : SeMails whose the address is a participant
	// second block : Public SeMails whose the address is the owner
	// third block : Public SeMails whose the address is NOT the owner, but there is some messages sent by it (only on local server)
	
	$listsemail = Semail::find_by_sql(
	"select s.* from ses_semail s, ses_participant p
	where (p.semail_id = s.id AND p.address = $address)
	
	UNION
	
	select * from ses_semail
	where type = '0' and owneraddress = $address
	
	UNION
	
	select distinct s.* from ses_semail s, ses_message m
	where s.type = '0' and s.owneraddress != $address and s.id = m.semail_id and m.address = $address
	
	order by dateactive desc, datecreated desc
	limit $from, $limit"
);

	return $listsemail;
}


// return the most recent message
function ses_getlastmessage($id)
{
	$o = Message::first(array('conditions' => array("semail_id = ?", $id), 'order' => 'datesent desc'));
	
	return $o;
}


// return the date of the most recent message or the date of creation (used for AJAX)
function ses_getdateactive($id)
{
	$ret = null;
	
	$o = Semail::find($id);
	
	if($o)
		$ret = $o->dateactive->format('Y-m-d H:i:s');
	
	return $ret;
}


// return a list of changed SeMails
function ses_bigping($idlist, $lastlist)
{
	$ret = array();
	
	$s = count($idlist);
	
	for($i = 0 ; $i < $s ; $i++)
	{
		$id = $idlist[$i];
		$last = $lastlist[$i];

		// remote
		if(substr($last,0,7) == "REMOTE_")
		{
			$end = substr($last, 7);

			$expl = explode(",", $end);
			if(count($expl) == 2)
			{
				$server = $expl[0];
				$last = $expl[1];
				
				// get the SeMail in json format
				$r = trim(ses_query_getpublic($server, $id));
				
				if($r != "" && $r != "[]")
				{
					$r = json_decode($r, true);
					
					$dateactive = $r["dateactive"];
					
					if($dateactive > $last)
						$ret[] = $id;
				}
			}
		}
		
		// local
		else
		{
			$sm = Semail::find(array('conditions' => array("id = ? and dateactive > ?", $id, $last)));

			if($sm != null)
			{
				$ret[] = $id;
			}
		}
	}
	
	
	// search for new SeMails
	
	$maxdate = "1900-01-01 00:00:00";
	
	if(count($idlist) != 0)
	{
		// get the most recent dateactive of idlist
		$max = Semail::find(array('conditions' => array("id in (?)", $idlist), 'order' => 'dateactive desc'));
		if($max != null)
			$maxdate = $max->dateactive->format('Y-m-d H:i:s');
	}
				
	$listnew = Semail::find(array('conditions' => array("dateactive > ?", $maxdate)));

	if($listnew != null)
		$ret[] = "4242";
		

	return $ret;
}



// make an user follow an other user
function ses_follow($useraddress, $contactaddress, $name = "")
{
	$user = User::find(array('conditions' => array("address = ?", $useraddress)));
	
	if(($user != null) && (ses_isaddress($contactaddress)) && (!ses_isfollowing($useraddress, $contactaddress)))
	{
		if(!ses_checkserver(ses_getserver($contactaddress)))
			return false;
			
		$id = $user->id;
		
		$o = Contact::create(array("address" => $contactaddress, "name" => $name, "user_id" => $id));
		$o->save();
	}
}


// make an user unfollow an other user
function ses_unfollow($useraddress, $contactaddress)
{
	$user = User::find(array('conditions' => array("address = ?", $useraddress)));
	
	if(($user != null) && (ses_isaddress($contactaddress)))
	{
		$id = $user->id;
		
		$o = Contact::find(array('conditions' => array("address = ? and user_id = ?", $contactaddress, $id)));
		
		if($o != null)
			$o->delete();
	}
}


// return if an user is following an other user
function ses_isfollowing($useraddress, $contactaddress)
{
	$ret = false;
	
	$user = User::find(array('conditions' => array("address = ?", $useraddress)));
	
	if(($user != null) && (ses_isaddress($contactaddress)))
	{
		$id = $user->id;
		
		$o = Contact::find(array('conditions' => array("address = ? and user_id = ?", $contactaddress, $id)));
		
		$ret = ($o != null);
	}

	return $ret;
}


// return all contacts of an user
function ses_getcontacts($useraddress)
{
	$ret = array();
	
	$user = User::find(array('conditions' => array("address = ?", $useraddress)));
	
	if($user != null)
	{
		$list = Contact::all(array('conditions' => array("user_id = ?", $user->id)));

		if($list != null)
		{
			foreach($list as $l)
			{
				$ret[] = $l->address;
			}
		}
	}
	
	return $ret;
}


// return if a server exist
function ses_checkserver($server)
{
	return (fopen ("http://$server/ses/", "r") != null);
	
	/*$expl = explode("/", $server);
	$server = $expl[0];
	return (fsockopen($server, 80, $errno, $errstr, 2) != false);*/
	
	//return checkdnsrr($server);
}



// return the feeds for an user
function ses_getuserfeeds($addr, $from = "0", $to = "0")
{
	$ret = array();
	
	$user = User::find(array('conditions' => array("address = ?", $addr)));
	
	if($user != null)
	{
		$id = $user->id;
		
		$listcontacts = Contact::all(array('conditions' => array("user_id = ?", $id)));
		
		
		// get the list of servers with their corresponding contacts
		// (because we only send ONE command per server, for optimization)
		
		$listservers = array();
		
		foreach($listcontacts as $l)
		{
			$a = $l->address;
			$s = ses_getserver($a);
			
			if(!isset($listservers[$s]))
				$listservers[$s] = $a;
			else
				$listservers[$s] .= ";$a";
		}
	
		

		foreach($listservers as $s => $listaddr)
		{
			$js = ses_query_getfeed($s, $listaddr, $from, $to);
			$tab = json_decode($js, true);

			foreach($tab as $sem)
			{
				$ret[] = $sem;
			}
		}
	}
	
	uasort($ret, 'ses_cmpfeeds'); // sort by dateactive desc
	
	$ret=array_slice($ret, $from, $to);
	
	return $ret;
}


// comparison function for getuserfeeds
function ses_cmpfeeds($a, $b)
{

    if ($a['dateactive'] == $b['dateactive']) {
        return 0;
    }

    return ($a['dateactive'] > $b['dateactive']) ? -1 : 1;
}


// log a message
function ses_dbg($msg)
{
	file_put_contents("dbg.log", "\r\n$msg\r\n", FILE_APPEND);
}



/*
* For each command :
* 
*   ses_commandname() apply the command to the local server
*
*   ses_query_commandname() send the command to an other server
*
*   ses_prepare_commandname() calculate the key, the md5 sum
*   and do all the necessary thing for validation on the local server
*/



// create


function ses_create($key, $sender, $id, $type, $list, $readonly, $tags, $datecreated)
{
	// create the semail

	$o = Semail::create(array("id" => $id, "type" => $type, "owneraddress" => $sender, "commandkey" => $key, "readonly" => $readonly, "tags" => $tags, "list" => $list, "datecreated" => $datecreated, "dateactive" => $datecreated));
	$o->save();

	// create the participants from the list
	
	ses_invit("", $sender, $id, $sender, $datecreated);

	$expl = explode(";", $list);
	foreach($expl as $p)
	{
		$pa = trim($p);
		
		if($pa != "" && ses_isaddress($pa))
		{
			ses_invit("", $sender, $id, $pa, $datecreated);
		}
	}
}

function ses_query_create($server, $key, $sender, $id, $type, $list, $readonly, $tags, $datecreated)
{
    //$server = ses_getserver($dest);
	
	/*if(!ses_checkserver($server))
		return false;*/
	
	$datecreated = urlencode($datecreated);
	
	$url = "http://$server/ses/server/create?id=$id&key=$key&sender=$sender&type=$type&list=$list&readonly=$readonly&tags=$tags&datecreated=$datecreated";

	return file_get_contents($url);
}

function ses_prepare_create($sender, $type, $list, $readonly, $tags, $datecreated)
{
    $id = ses_id($sender, $type, $list);
	$key = ses_key("create::".$type."::".$list."::".$readonly."::".$tags."::".$datecreated, $id, $sender);
	$sum = ses_sum("create::".$type."::".$list."::".$readonly."::".$tags."::".$datecreated, $id, $sender);
	
	$o = Validate::create(array("key" => $key, "md5sum" => $sum));
	$o->save();
	
	return array($key, $id);
}



// invit


function ses_invit($key, $sender, $id, $address, $dateinvited)
{
	if(!ses_checkserver(ses_getserver($address)))
		return false;
		
	// if the sender is a participant and On invit SeMail
	// or Private SeMail and the sender is the owner
	$ip = ses_isparticipant($sender, $id);
	$io = ses_isowner($sender, $id);
	$type = ses_gettype($id);
	$alreadyin = Participant::all(array('conditions' => array("semail_id = ? AND address = ?", $id, $address))); // don't invit if already in !

	if(($alreadyin == null) && (($ip && $type == 1) ||($io && $type == 2)))
	{
		$sm = Semail::find($id);
		$sm->dateactive = $dateinvited;
		$sm->save();
		
		$o = Participant::create(array("address" => $address, "semail_id" => $id, "commandkey" => $key, "commandsender" => $sender, "dateinvited" => $dateinvited));
		$o->save();
	}
}	

function ses_query_invit($server, $key, $sender, $id, $address, $dateinvited)
{
    //$server = ses_getserver($dest);
	
	/*if(!ses_checkserver($server))
		return false;*/
	
	$dateinvited = urlencode($dateinvited);
	
	$url = "http://$server/ses/server/invit?id=$id&key=$key&sender=$sender&address=$address&dateinvited=$dateinvited";

	return file_get_contents($url);
}

function ses_prepare_invit($sender, $id, $address, $dateinvited)
{
	$key = ses_key("invit::".$address."::".$dateinvited, $id, $sender);
	$sum = ses_sum("invit::".$address."::".$dateinvited, $id, $sender);
	
	$o = Validate::create(array("key" => $key, "md5sum" => $sum));
	$o->save();
	
	return $key;
}




// update

/*
function ses_update()
{
}*/



// delete


function ses_delete($sender, $id)
{
	$o = Semail::find($id);
	
	
	if($o != null && $o->owneraddress == $sender && $o->type == "0")
	{
		// delete all messages
		
		$listmsg = Message::all(array('conditions' => array("semail_id = ?", $id)));
		
		foreach($listmsg as $l)
		{
			$l->delete();
		}
		
		
		// delete the public SeMail
		
		$o->delete();
	}
}



// getfeed


function ses_getfeed($address, $from, $limit)
{
	// get an array with all addresses
	
	$listaddr = array();
	
	$expl = explode(";", $address);
	foreach($expl as $a)
	{
		$a = trim($a);
		
		if($a != "" && ses_isaddress($a))
		{
			$listaddr[] = $a;
		}
	}
	
	
	
	$tab = array();
	$listsemail = Semail::all(array('conditions' => array("owneraddress in (?) AND type = '0'", $listaddr), 'order' => 'dateactive desc','offset' => $from, 'limit' => $limit));
	
	foreach($listsemail as $lsm)
	{
		$tabmsg = array();
		$listmsg = Message::all(array('conditions' => array("semail_id = ?", $lsm->id)));
		
		foreach($listmsg as $lmsg)
		{
			$tabmsg[] = array("id" => $lmsg->id, "content" => $lmsg->content, "address" => $lmsg->address, "datesent" => $lmsg->datesent->format('Y-m-d H:i:s'), "semail_id" => $lmsg->semail_id);
		}
		

		$tab[] = array("id" => $lsm->id, "type" => $lsm->type, "readonly" => $lsm->readonly, "owneraddress" => $lsm->owneraddress, "list" => $lsm->list, "tags" => $lsm->tags,
						"datecreated" => $lsm->datecreated->format('Y-m-d H:i:s'), "dateactive" => $lsm->dateactive->format('Y-m-d H:i:s'), "msg" => $tabmsg);
	}
	
	return json_encode($tab);
}

function ses_query_getfeed($server, $address, $from, $limit)
{
    //$server = ses_getserver($address);
	
	/*if(!ses_checkserver($server))
		return false;*/

	$url = "http://$server/ses/server/getfeed?id=0&key=0&sender=0&address=$address&from=$from&limit=$limit";

	return file_get_contents($url);
}



// getprofile


function ses_getprofile($address)
{
	$tab = array();

	$o = User::find(array('conditions' => array("address = ? ", $address)));

	if($o != null)
	{
		$tab = array("mail" => $o->mail);
	}
	
	return json_encode($tab);
}

function ses_query_getprofile($address)
{
    $server = ses_getserver($address);
	
	/*if(!ses_checkserver($server))
		return false;*/

	$url = "http://$server/ses/server/getprofile?id=0&key=0&sender=0&address=$address";

	return file_get_contents($url);
}



// getpublic


function ses_getpublic($id)
{
	$ret = "";

	$lsm = Semail::find($id);

	if($lsm != null && $lsm->type == "0")
	{
		$tabmsg = array();
		$listmsg = Message::all(array('conditions' => array("semail_id = ?", $id)));
		
		foreach($listmsg as $lmsg)
		{
			$tabmsg[] = array("id" => $lmsg->id, "content" => $lmsg->content, "address" => $lmsg->address, "datesent" => $lmsg->datesent->format('Y-m-d H:i:s'), "semail_id" => $lmsg->semail_id);
		}
		

		$ret = array("id" => $lsm->id, "type" => $lsm->type, "readonly" => $lsm->readonly, "owneraddress" => $lsm->owneraddress, "list" => $lsm->list, "tags" => $lsm->tags,
						"datecreated" => $lsm->datecreated->format('Y-m-d H:i:s'), "dateactive" => $lsm->dateactive->format('Y-m-d H:i:s'), "msg" => $tabmsg);
	}
	
	return json_encode($ret);
}

function ses_query_getpublic($server, $id)
{
	/*if(!ses_checkserver($server))
		return false;*/

	$url = "http://$server/ses/server/getpublic?id=$id&key=0&sender=0";

	return file_get_contents($url);
}



// message


function ses_message($key, $sender, $id, $message, $datesent)
{
	// if the sender is a participant or public SeMail
	$ip = ses_isparticipant($sender, $id);
	$io = ses_isowner($sender, $id);
	$type = ses_gettype($id);
	$ro = ses_getreadonly($id);

	if((!$ro || $io) && ($ip || $type == 0))
	{
		$sm = Semail::find($id);
		$sm->dateactive = $datesent;
		$sm->save();
		
		$o = Message::create(array("content" => $message, "datesent" => $datesent, "address" => $sender, "semail_id" => $id, "commandkey" => $key));
		$o->save();
	}
}

function ses_query_message($server, $key, $sender, $id, $message, $datesent)
{
    //$server = ses_getserver($dest);
	
	/*if(!ses_checkserver($server))
		return false;*/
		
	$message = urlencode($message);
	$datesent = urlencode($datesent);

	$url = "http://$server/ses/server/message?id=$id&key=$key&sender=$sender&message=$message&datesent=$datesent";

	return file_get_contents($url);
}

function ses_prepare_message($sender, $id, $message, $datesent)
{
	$key = ses_key("message::".$message."::".$datesent, $id, $sender);
	$sum = ses_sum("message::".$message."::".$datesent, $id, $sender);
	
	$o = Validate::create(array("key" => $key, "md5sum" => $sum));
	$o->save();
	
	return $key;
}





?>
