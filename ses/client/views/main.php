

<div id="tabs">
	<ul>
		<li><a href="#tabs-1" title='Your last active SeMails'>Last active SeMails</a></li>
		<li><a href="#tabs-2" title='Last public SeMails of people you are following'>My Feeds</a></li>
		<li><a href="#tabs-3" title='Meet new people by discovering their SeMails'>Discover</a></li>
	</ul>
	<div id="tabs-1">
		<p id='lastsemails'>
		<?php
		
			echo "<script>mainsemail = Object();</script>";

			$alt = "0";
			
			foreach($lastsemails as $lsm)
			{
				$id = $lsm->id;
				$msg = ses_getlastmessage($id);
				
				$addr = htmlentities($lsm->owneraddress, 0,"UTF-8");
				$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);
				
				if($msg)
				{
					$addrlastmsg = htmlentities($msg->address, 0,"UTF-8");
					$addrlastmsgisfollowing = ses_isfollowing($SES_ADDRESS, $addrlastmsg);
				}
				
				$dateactive = $lsm->dateactive->format('Y-m-d H:i:s');
				
				$tags = "";
				
				$expl = explode(";", $lsm->tags);
				foreach($expl as $t)
				{
					$t = htmlentities(trim($t),0,"UTF-8");
					
					if($t != "")
					{
						$tags .= "<a href='#' title='Click to browse by tag #$t'><span class='sometag'>#$t</span></a> ";
					}
				}
				
				
				if($msg)
				{
					$mail = "";
					$tab = json_decode(ses_query_getprofile($addrlastmsg), true);

					if(count($tab) != 0)
						$mail = $tab["mail"];
						
					$hash = md5(strtolower(trim($mail)));
					$gravatar = "http://www.gravatar.com/avatar/$hash?s=50&d=wavatar";

					$gra = "<a href='#' title='".(ses_isfollowing($SES_ADDRESS, $addrlastmsg) ? "Unfollow" : "Follow")." $addrlastmsg'><img src='$gravatar' alt='$addrlastmsg' class='someparticipant, avatar' name='addr$addrlastmsg' /></a>";
				}
		
				
				$content = "From <a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, created the <span class='somedate'>".htmlentities($lsm->datecreated->format('Y-m-d H:i:s'), 0,"UTF-8")."</span>";
				
				if($msg)
					$content .= "<br /><br />Last message from <a href='#' title='".($addrlastmsgisfollowing ? "Unfollow" : "Follow")." $addrlastmsg'><span class='".($addrlastmsgisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addrlastmsg'>$addrlastmsg</span></span></a> :<br />"
							 .'<br />'.$gra.'&nbsp;&nbsp;&nbsp;&nbsp;<span class="somequote">"'.htmlentities(substr($msg->content,0,20), 0,"UTF-8").(strlen($msg->content) > 20 ? " [...]" : "").'"</span>';
				else
					$content .= "<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;No message yet.<br /><br />";
					
				$content .= "<br /><div class='tagslist'>" . $tags . "</div><br />";
				
				echo "<div class='rowmain row$alt' id='row".$id."' name='addr".$addr."'>$content</div>";
				
				$alt = ($alt == "0" ? "1" : "0");
				
				
				echo "<script>mainsemail['$id'] = '$dateactive';</script>";
			}
			
			echo "<br /><br /><br /><a href='#' title='Show more SeMails' id='moresemails'>More SeMails</a><br />";
			
		?>

		</p>
	</div>
	
	<div id="tabs-2">
		<p>
		</p>
	</div>
	
	<div id="tabs-3">
		<p>
		</p>
	</div>
	
</div>


<script>

	$(document).ready(function(){

		nbrsemails = <?php echo $nbrsemails; ?>;

		$("#tabs").tabs();
		
		$(".rowmain").click(function(e) {

			var id = $(this).attr('id').substring(3);
			var addr = $(this).attr('name').substring(4);
			
			showwin("winsemail"+id.substring(0,20), "SeMail by "+addr, "controllers/viewSemail.php?id="+id);
		
		});
		
		
		$("#moresemails").click(function() {

			nbrsemails += 5;
			updatewin("winmain", "controllers/viewMain.php?nbrsemails="+nbrsemails);

		});
		
		tiptip();
	
	});
	
	
</script>

<br />