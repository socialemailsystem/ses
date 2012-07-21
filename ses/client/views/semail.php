
<span class='semailwin'>

<?php

if($semail)
{
	// tags 
	
	$tags = "";

	$expl = explode(";", $semail->tags);
	foreach($expl as $t)
	{
		$pa = trim($t);
		
		if($t != "")
		{
			$tags .= "<a href='#'><span class='sometag'>#$t</span></a> ";
		}
	}
	
	echo "<span class='typesm'>$type".($readonly?" (Read Only)":"")."</span><span class='someaddress'>Tags : </span>$tags<br /><br />";

	
	// participants
	
	$particip = "";
	foreach($listaddress as $a)
	{
		$particip .= ($particip == "" ? "":", ") . "<a href='#'><span class='".(ses_isfollowing($SES_ADDRESS, $a) ? "followed" : "notfollowed")."'><span class='someparticipant'>$a</span></span></a>";
	}
	
	echo "<span class='someaddress'>Participants : </span>$particip<br /><br />";

	
	// messages
	
	$alt = "0";

	foreach($messages as $lmsg)
	{
		//$idmsg = $lmsg->id;
		$datesent = $lmsg->datesent->format('Y-m-d H:i:s');

		$content = "<span class='headmsg'><a href='#'><span class='".(ses_isfollowing($SES_ADDRESS, $lmsg->address) ? "followed" : "notfollowed")."'><span class='someaddress'>".htmlentities($lmsg->address,0,"UTF-8")."</span></span></a>, the <span class='somedate'>".htmlentities($datesent, 0,"UTF-8")."</span></span>";
		$content .= "<br /><div class='somecontent'>".str_replace("\n","<br />", htmlentities($lmsg->content,0,"UTF-8"))."</div>";
		
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
	<button class='sendbut' id='but<?php echo $shortid; ?>'>Send</button>

	</form>

</div>

<?php } ?>


<script>

	$(document).ready(function(){
	
		$("button").button();
		

		allsemail["<?php echo $id ?>"] = "<?php echo $lastdate; ?>";

		
		// send a message
		
		$('#but<?php echo $shortid; ?>').click(function() {

			callcontroller("controllers/actionSend.php?id=<?php echo $id ?>&msg="+encodeURIComponent($("#msg<?php echo $shortid; ?>").val()), function() {
				updatewin("winmain", "controllers/viewMain.php?nbrsemails="+nbrsemails);
				updatewin("winsemail<?php echo $shortid ?>", "controllers/viewSemail.php?id=<?php echo $id ?>");
			});
			
			$("#msg<?php echo $shortid; ?>").val("");

		});
	
	});

</script>
