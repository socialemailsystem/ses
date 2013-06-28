

<div>
<!-- <form id='tagform' action='#'> -->

<span class='afterfield'>Display by Tag (empty for none) : </span><input type='text' id='txttags' name='txttags' value='<?php /*echo $tags;*/ ?>' />

<button type='button' id='buttags' class='formbut' style='width: 60px; height: 30px; margin-bottom: 10px;'>Go</button>

<!-- </form> -->
</div>


<div id="tabs">
	<ul>
		<li><a href="#tabs-1" title='Your last active SeMails on the server'>Last active SeMails</a></li>
		<li><a href="#tabs-2" title='Last public SeMails of people you are following'>My Feeds</a></li>
		<li><a href="#tabs-3" title='Meet new people by discovering their public SeMails'>Discover</a></li>
		<li><a href="#tabs-4" title='The SeMails you want to keep an eye on'>Favorites</a></li>
	</ul>
	<div id="tabs-1">
		<p id='lastsemails'>
		<?php
		
			//echo "tags : $tags<br />";
			
			echo "<script>mainsemail = Object();</script>";

			$alt = "0";
			
			foreach($lastsemails as $lsm)
			{
				$id = $lsm->id;
				$msg = ses_getlastmessage($id);
				
				$addr = htmlentities($lsm->owneraddress, 0,"UTF-8");
				$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);

				$typ = $lsm->type;

				if($typ == "0")
					$type = "Public";
				else if($typ == "1")
					$type = "On Invite";
				else
					$type = "Private";

				
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

					$gra = "<a href='#' title='Profile of $addrlastmsg'><img src='$gravatar' alt='$addrlastmsg' class='someparticipant, avatar' name='addr$addrlastmsg' /></a>";
				}
		
				
				$content = "$type - From <a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, created the <span class='somedate'>".htmlentities($lsm->datecreated->format('Y-m-d H:i:s'), 0,"UTF-8")."</span>";
				
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
			
		<?php
		
			// cache system (because getting feeds is ressource-consuming and contacts many servers)
			
			//$cache = cache_get();
			if($cache == "" || $forcecache)
			{
				$cache = "";
				
				$cache .=  " <a href='#' title='Refresh Feeds' id='refreshfeeds'>Refresh Feeds</a><br />";

				$alt = "0";

				foreach($feedsemails as $lsm)
				{
					$id = $lsm['id'];
					//$msg = ses_getlastmessage($id);
					$msg = $lsm['msg'];
					if($msg)
						$msg = $msg[count($msg) - 1];
					
					$addr = htmlentities($lsm['owneraddress'], 0,"UTF-8");
					$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);

					//$typ = $lsm->type;
					$type = "Public";


					
					if($msg)
					{
						$addrlastmsg = htmlentities($msg['address'], 0,"UTF-8");
						$addrlastmsgisfollowing = ses_isfollowing($SES_ADDRESS, $addrlastmsg);
					}
					
					$dateactive = $lsm['dateactive'];
					
					$tags = "";
					
					$expl = explode(";", $lsm['tags']);
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

						$gra = "<a href='#' title='Profile of $addrlastmsg'><img src='$gravatar' alt='$addrlastmsg' class='someparticipant, avatar' name='addr$addrlastmsg' /></a>";
					}
			
					
					$content = "$type - From <a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, created the <span class='somedate'>".htmlentities($lsm['datecreated'], 0,"UTF-8")."</span>";
					
					if($msg)
						$content .= "<br /><br />Last message from <a href='#' title='".($addrlastmsgisfollowing ? "Unfollow" : "Follow")." $addrlastmsg'><span class='".($addrlastmsgisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addrlastmsg'>$addrlastmsg</span></span></a> :<br />"
								 .'<br />'.$gra.'&nbsp;&nbsp;&nbsp;&nbsp;<span class="somequote">"'.htmlentities(substr($msg['content'],0,20), 0,"UTF-8").(strlen($msg['content']) > 20 ? " [...]" : "").'"</span>';
					else
						$content .= "<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;No message yet.<br /><br />";
						
					$content .= "<br /><div class='tagslist'>" . $tags . "</div><br />";
					
					$cache .=  "<div class='rowfeeds row$alt' id='row".$id."' name='serv".ses_getserver($addr)."'>$content</div>";
					
					$alt = ($alt == "0" ? "1" : "0");
					
					
					//$cache .=  "<script>mainsemail['$id'] = '$dateactive';</script>";
				}
				
				$cache .=  "<br /><br /><br /><a href='#' title='Show more Feeds' id='morefeeds'>More Feeds</a><br />";
				
				
				//echo "CACHING<br />";
				
				cache_set($cache);
			
			}
			
			echo $cache;
		
		?>
		
		</p>
	</div>
	
	
	
	
	
	
	
	<div id="tabs-3">
	
		<p id='discoversemails'>
		
		<?php
		
			//echo "<script>mainsemail = Object();</script>";

			$alt = "0";
			
			foreach($discoversemails as $lsm)
			{
				$id = $lsm->id;
				$msg = ses_getlastmessage($id);
				
				$addr = htmlentities($lsm->owneraddress, 0,"UTF-8");
				$addrisfollowing = ses_isfollowing($SES_ADDRESS, $addr);

				$typ = $lsm->type;

				if($typ == "0")
					$type = "Public";
				else if($typ == "1")
					$type = "On Invite";
				else
					$type = "Private";

				
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

					$gra = "<a href='#' title='Profile of $addrlastmsg'><img src='$gravatar' alt='$addrlastmsg' class='someparticipant, avatar' name='addr$addrlastmsg' /></a>";
				}
		
				
				$content = "$type - From <a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, created the <span class='somedate'>".htmlentities($lsm->datecreated->format('Y-m-d H:i:s'), 0,"UTF-8")."</span>";
				
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
			
			echo "<br /><br /><br /><a href='#' title='Show more Discover' id='morediscover'>More Discover</a><br />";
			
		?>
		
		</p>
	</div>
	
	
<div id="tabs-4">
	
		<p id='favoritesemails'>
		
		<?php
		
			//echo "<script>mainsemail = Object();</script>";

			$alt = "0";
			
			foreach($favoritesemails as $lsm)
			{
				$id = $lsm->id;
				

				$typ = $lsm->type;

				if(!isset($lsm->msg)) {
					$msg = ses_getlastmessage($id);
				}
				else {

					$msg = $lsm->msg;

					if($msg && count($msg >= 1))
						$msg = $msg[count($msg) - 1];

				}

				if($typ == "0")
					$type = "Public";
				else if($typ == "1")
					$type = "On Invite";
				else
					$type = "Private";

				
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

					$gra = "<a href='#' title='Profile of $addrlastmsg'><img src='$gravatar' alt='$addrlastmsg' class='someparticipant, avatar' name='addr$addrlastmsg' /></a>";
				}
		
				
				$content = "$type - From <a href='#' title='".($addrisfollowing ? "Unfollow" : "Follow")." $addr'><span class='".($addrisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addr'>$addr</span></span></a>, created the <span class='somedate'>".htmlentities($lsm->datecreated->format('Y-m-d H:i:s'), 0,"UTF-8")."</span>";
				
				if($msg)
					$content .= "<br /><br />Last message from <a href='#' title='".($addrlastmsgisfollowing ? "Unfollow" : "Follow")." $addrlastmsg'><span class='".($addrlastmsgisfollowing ? "followed" : "notfollowed")."'><span class='someaddress' name='addr$addrlastmsg'>$addrlastmsg</span></span></a> :<br />"
							 .'<br />'.$gra.'&nbsp;&nbsp;&nbsp;&nbsp;<span class="somequote">"'.htmlentities(substr($msg->content,0,20), 0,"UTF-8").(strlen($msg->content) > 20 ? " [...]" : "").'"</span>';
				else
					$content .= "<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;No message yet.<br /><br />";
					
				$content .= "<br /><div class='tagslist'>" . $tags . "</div><br />";


				if($typ == '0') { // public

					echo "<div class='rowfeeds row$alt' id='row".$id."' name='serv".ses_getserver($addr)."'>$content</div>";
				} else {

					echo "<div class='rowmain row$alt' id='row".$id."' name='addr".$addr."'>$content</div>";
				}

			
				
				$alt = ($alt == "0" ? "1" : "0");
				
				
				echo "<script>mainsemail['$id'] = '$dateactive';</script>";	
			}
			
			echo "<br /><br /><br /><a href='#' title='Show more Favorites' id='morefavorite'>More Favorites</a><br />";
				
		?>
		
		</p>
	</div>
	
</div>


<script>

	$(document).ready(function(){
	
		$("button").button();

		nbrsemails = <?php echo $nbrsemails; ?>;
		nbrfeeds = <?php echo $nbrfeeds; ?>;
		nbrdiscover = <?php echo $nbrdiscover; ?>;
		nbrfavorite = <?php echo $nbrfavorite; ?>;
		//tabselected = '1';

		$("#tabs").tabs(
			{
                select: function(event,ui){
                    tabselected = ui.panel.id.substring(5);
                }
			}
		);
		
		$("#tabs").tabs("select", tabselected); // default : tab 1 selected

		
		$(".rowmain").click(function(e) {

			var elem = e.target;
			var typ = elem.tagName;
			
			if(typ == 'DIV') {
		
				var id = $(this).attr('id').substring(3);
				var addr = $(this).attr('name').substring(4);
				
				showwin("winsemail"+id.substring(0,20), "SeMail by "+addr, "controllers/viewSemail.php?id="+id);
			
			}
		
		});
		
		$(".rowfeeds").click(function(e) {

			//var elem = window.event.srcElement;			
			var elem = e.target;
			var typ = elem.tagName;
			
			if(typ == 'DIV') {
			
				var id = $(this).attr('id').substring(3);
				var s = $(this).attr('name').substring(4);
				
				showwin("winsemail"+id.substring(0,20), "Public SeMail", "controllers/viewSemail.php?id="+id+"&server="+s);
			
			}
		
		});
		
		
		$("#moresemails").click(function() {

			nbrsemails += 5;
			refreshmain();

		});
		
		
		$("#morefeeds").click(function() {

			nbrfeeds += 5;
			refreshmain(true);

		});
		
		
		$("#morediscover").click(function() {

			nbrdiscover += 5;
			refreshmain();

		});
		
		$("#morefavorite").click(function() {

			nbrfavorite += 5;
			refreshmain();

		});
		
		
		$("#refreshfeeds").click(function() {

			refreshmain(true);

		});
		
		
		$("#txttags").val(tags);
		
		$("#txttags").click(function() {
			if($("#txttags").val() == 'mytag1;mytag2') {
				$("#txttags").val('');
				tags = '';
			}
		});
		

		
		$("#txttags").keyup(function() {
		
			tags = $("#txttags").val();
		});
		
		
		$('#buttags').click(function() {
		
			refreshmain('1');
		});


       	 	$("#txttags").keyup(function(event) {
			
            		if(event.keyCode == 13){
                		$("#buttags").click();
            		}
        	});
		
		
		tiptip();
	
	});
	
	
</script>

<br />
