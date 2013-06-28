
<span class='semailwin'>

<?php

if($semail)
{
	// tags 
	
	$tags = "";

	$expl = explode(";", $semail->tags);
	foreach($expl as $t)
	{
		$t = htmlentities(trim($t),0,"UTF-8");
		
		if($t != "")
		{
			$tags .= "<a href='#' title='Click to browse by tag #$t'><span class='sometag'>#$t</span></a> ";
		}
	}
	
	
	$share = "";
	
	// public id to share
	if($type == "Public")
	{
		$share = "<a href='#' class='copyid' title='Share this SeMail !' id='cop$id' name='ser".($server == "" ? $SES_SERVER : $server)."'><span class='publicid'>Share it</span></a>";
	}
	
	
	// on invite : invite link
	if($type == "On Invite" || ($type == "Private" && $isowner))
	{
		$type = "<a href='#' class='invitpeople' title='Invite people' name='inv$id'>$type - Invite people</a>";
	}
	
	// public and owner : delete link
	else if($type == "Public" && $isowner)
	{
		$type = "<a href='#' class='deletesemail' title='Delete SeMail' name='del$id'>$type - Delete</a>";
	}
	
	
	echo "<span class='typesm'>$type".($readonly?" (Read Only)":"")."</span><span class='txtbold'>Tags : </span>$tags<br /><br />$share";




	// favorite

	$tagounet = $semail->tags;
	
	$localserver = ses_getserver($SES_ADDRESS);
	$semailserver = ses_getserver($semail->owneraddress);
	$serverounet = '';
	if($type == 'Public' && $localserver != $semailserver)
		$serverounet = $semailserver; // we only set server if it's a public SeMail on a remote server
	
	echo "<a href='#'><img src='images/star.png' class='star' id='star$id' name='star$serverounet' alt='star$tagounet' title='".(ses_isfavorite($SES_ADDRESS, $id) ? "Remove from favorites" : "Add to favorites")."' /></a>";


	
	
	// participants
	
	$particip = "";
	foreach($listaddress as $a)
	{
		$a = htmlentities($a,0,"UTF-8");
		//$particip .= ($particip == "" ? "":", ") . "<a href='#'><span class='".(ses_isfollowing($SES_ADDRESS, $a) ? "followed" : "notfollowed")."'><span class='someparticipant'>$a</span></span></a>";

		
		$mail = "";
		$tab = json_decode(ses_query_getprofile($a), true);

		if(count($tab) != 0)
			$mail = $tab["mail"];
			
		$hash = md5(strtolower(trim($mail)));
		$gravatar = "http://www.gravatar.com/avatar/$hash?s=50&d=wavatar";

		$particip .= "<a href='#'><img title='Profile of $a' src='$gravatar' alt='$a' class='someparticipant' name='addr$a' /></a>";
	}
	
	//echo "<span class='someaddress'>Participants : </span>$particip<br /><br />";
	echo "$particip<br /><br />";

	
	// messages
	
	$alt = "0";

	foreach($messages as $lmsg)
	{
		//$idmsg = $lmsg->id;
		$datesent = $lmsg->datesent->format('Y-m-d H:i:s');
		$addr = htmlentities($lmsg->address,0,"UTF-8");
		$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);
		
		$bbcode = new BBCode;
		$bbcode->SetDetectURLs(true);
		$bbcode->SetSmileyDir("lib/nbbc/smileys");
		$bbcode->SetSmileyURL("lib/nbbc/smileys");
		$contbb = $bbcode->Parse(/*htmlentities(*/$lmsg->content/*,0,"UTF-8")*/);
		
		//$parser = new JBBCode\Parser();
		//$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
		//$parser->parse(htmlentities($lmsg->content,0,"UTF-8"));
		//$contbb = $parser->getAsHtml();
		
		
		$mail = "";
		$tab = json_decode(ses_query_getprofile($addr), true);

		if(count($tab) != 0)
			$mail = $tab["mail"];
			
		$hash = md5(strtolower(trim($mail)));
		$gravatar = "http://www.gravatar.com/avatar/$hash?s=50&d=wavatar";

		$gra = "<a href='#'><img title='Profile of $addr' src='$gravatar' alt='$addr' class='someparticipant, avatar, avatarblock' name='addr$addr' /></a>";
		
		

		$content = "<span class='headmsg'><a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, the <span class='somedate'>".htmlentities($datesent, 0,"UTF-8")."</span></span>";
		$content .= "<br />$gra<br /><span class='somemsg'>"./*str_replace("\n","<br />",*/$contbb/*)*/."</span>";
		
		
		echo "<div class='rowmsg row$alt'>$content</div>";

		$alt = ($alt == "0" ? "1" : "0");
	}
	
	$lastdate = $semail->dateactive->format('Y-m-d H:i:s'); // last active date
}

?>

</span>

<br />


<?php if (!$readonly) { ?>

<div class='sendbox'>

	<form method="get" action="#">

	<textarea class="msgarea" id='msg<?php echo $shortid; ?>' />
	<button class='sendbut' id='but<?php echo $shortid; ?>' type="button">Send</button>

	</form>

</div>

<br />


<script>

	$('#msg<?php echo $shortid; ?>').keyup(function(e) {

		var shortid = '<?php echo $shortid; ?>';

		// save message and caret position
		var msgarea = $("#msg" + shortid);
		arrmessages[shortid] = msgarea.val();
		arrpos[shortid] = doGetCaretPosition(document.getElementById("msg" + shortid));

	});

</script>


<?php } ?>


<script>

	$(document).ready(function(){
	
		$("button").button();
		
		var server = '<?php echo $server; ?>';
		
		if(server == '') // local
			allsemail["<?php echo $id ?>"] = "<?php echo $lastdate; ?>";
		else // remote
			allsemail["<?php echo $id ?>"] = "REMOTE_" + server + ",<?php echo $lastdate; ?>";


		var shortid = '<?php echo $shortid; ?>';
		var msgarea = $("#msg" + shortid);

		msgarea.val(arrmessages[shortid]);
		msgarea.focus();
		setCaretPosition(document.getElementById("msg" + shortid),arrpos[shortid]);

		
		// send a message
		
		$('#but<?php echo $shortid; ?>').click(function() {

			var server = '<?php echo $server; ?>';
			
			callcontroller("controllers/actionSend.php?id=<?php echo $id ?>&msg="+encodeURIComponent($("#msg<?php echo $shortid; ?>").val())+"&server="+server, function() {
				updatewin("winsemail<?php echo $shortid ?>", "controllers/viewSemail.php?id=<?php echo $id ?>&server="+server);
				refreshmain();
			});


			var shortid = '<?php echo $shortid; ?>';

			// clean message and caret position
			var msgarea = $("#msg" + shortid);
			msgarea.val("");
			arrmessages[shortid] = '';
			arrpos[shortid] = doGetCaretPosition(document.getElementById("msg" + shortid));

		});
		
		
		tiptip();
	
	});

</script>


<br />
