

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Last active SeMails</a></li>
		<li><a href="#tabs-2">My Feeds</a></li>
		<li><a href="#tabs-3">Discover</a></li>
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
				$addr = $lsm->owneraddress;
				$dateactive = $lsm->dateactive->format('Y-m-d H:i:s');
				
				$tags = "";
				
				$expl = explode(";", $lsm->tags);
				foreach($expl as $t)
				{
					$pa = trim($t);
					
					if($t != "")
					{
						$tags .= "<a href='#'><span class='sometag'>#$t</span></a> ";
					}
				}
				
				
				$content = "From <a href='#'><span class='".(ses_isfollowing($SES_ADDRESS, $addr) ? "followed" : "notfollowed")."'><span class='someaddress'>".htmlentities($addr, 0,"UTF-8")."</span></span></a>, created the <span class='somedate'>".htmlentities($lsm->datecreated->format('Y-m-d H:i:s'), 0,"UTF-8")."</span>";
				
				if($msg)
					$content .= "<br /><br />Last message from <a href='#'><span class='".(ses_isfollowing($SES_ADDRESS, $msg->address) ? "followed" : "notfollowed")."'><span class='someaddress'>".htmlentities($msg->address, 0,"UTF-8")."</span></span></a> :<br />"
							 .'<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class="somequote">"'.htmlentities(substr($msg->content,0,20), 0,"UTF-8").(strlen($msg->content) > 20 ? " [...]" : "").'"</span>';
				else
					$content .= "<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;No message yet.<br /><br />";
					
				$content .= "<div class='tagslist'>" . $tags . "</div><br />";
				
				echo "<div class='rowmain row$alt' id='row".$id."' name='addr".$addr."'>$content</div>";
				
				$alt = ($alt == "0" ? "1" : "0");
				
				
				echo "<script>mainsemail['$id'] = '$dateactive';</script>";
			}
			
			echo "<br /><br /><br /><a href='#' id='moresemails'>More SeMails</a><br />";
			
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
	
	});
	
	
</script>
