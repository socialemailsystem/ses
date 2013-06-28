

	<?php

		$bbcode = new BBCode;
		$bbcode->SetDetectURLs(true);
		$bbcode->SetSmileyDir("lib/nbbc/smileys");
		$bbcode->SetSmileyURL("lib/nbbc/smileys");
		$contbb = $bbcode->Parse($about);

		echo "<br /><br />$contbb<br />";
		
		//echo "<a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."' style='float: left; margin-top: 15px;'><span class='someaddress' name='addr$addr'>$addr</span></span></a>";
		
		echo "<a href='http://$server/ses/client/controllers/actionRss.php?user=$addr' target='_blank'><img src='images/rss.png' alt='RSS' class='rssimg' title='RSS Feed' /></a>";

	?>


	
<script>

	$(document).ready(function(){
	
		$("button").button();

		
		tiptip();
	
	});

</script>
