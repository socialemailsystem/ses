
<?php

	foreach($listcontact as $l)
	{
		$mail = "";
		$tab = json_decode(ses_query_getprofile($l), true);
		
		if(count($tab) != 0)
			$mail = $tab["mail"];
			
		$hash = md5(strtolower(trim($mail)));
		$gravatar = "http://www.gravatar.com/avatar/$hash?s=50&d=wavatar";

		$gra = "<a href='#' title='Invite $l'><img src='$gravatar' alt='$l' class='gra' name='gra$l' /></a>";
		
		echo $gra;
		
	}

?>

<br /><br /><br /><br />
<br /><br /><br /><br />

<div class='invitbox'>

	<form method="get" action="#">

	<span class='blabla'>Other addresses (separated by ';') :</span><br /><br />
	<input type='text' class="invitarea" id='invitarea' name='invitarea' /><br /><br />
	<button class='invitbut' id='invitbut' type="button">Invite</button>

	</form>

</div>



<script>

	$(document).ready(function(){
	
		$("button").button();
		
		
		// select
		
		$('.gra').click(function() {
		
			//var id = $(this).attr("name").substring(3);
			
			$(this).toggleClass('graselected');
		});
		
		
		// invite button
		
		$('#invitbut').click(function() {
		
			var id = '<?php echo $id; ?>';
			var shortid = '<?php echo $shortid; ?>';
			
			
			// make a list
			
			var list = jQuery.trim($('#invitarea').val());
			
			$(".graselected").each(function (i) {
			
				var idgra = jQuery.trim(this.alt);
				
				if(idgra != "")
				{
					if(list != "")
						list += ";";
						
					list += idgra;
				}
			});
			
			
			
			// send invites

			callcontroller("controllers/actionInvit.php?id="+id+"&list="+list, function() {
			
				$("#invitwin").dialog("destroy");
			
				// refresh the SeMail
				
				if($("#winsemail" + shortid).length)
				{
					// save message and caret position
					var msgarea = $("#msg" + shortid);
					var msg = msgarea.val();
					var pos = doGetCaretPosition(document.getElementById("msg" + shortid));
					
					updatewin("winsemail" + shortid, "controllers/viewSemail.php?id=" + id, function () {
						var msgarea = $("#msg" + shortid);
						// restore message and caret position
						msgarea.val(msg);
						msgarea.focus();
						setCaretPosition(document.getElementById("msg" + shortid),pos);
					});

				}

			});
			
		});
		
		
		tiptip();
	
	});

</script>


