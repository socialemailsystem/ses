
<br /><br />

<div class='inputfollowbox'>

	<form method="get" action="#">

	<span class='blabla'>Address to follow :</span><br /><br />
	<input type='text' class="invitarea" id='inputfollowarea' name='inputfollowarea' /><br /><br />
	<button class='halfbut' id='inputfollowbut' type="button">Invit</button>
	<button class='halfbut' id='inputfollowcancelbut' type="button">Cancel</button>

	</form>

</div>



<script>

	$(document).ready(function(){
	
		$("button").button();
		
		
		// follow button
		
		$('#inputfollowbut').click(function() {
		
			user = '<?php echo $user; ?>';
			callcontroller("controllers/actionFollow.php?user="+user+"&contact="+$('#inputfollowarea').val(), function() {
				$('#wininputfollow').dialog("close");
			});
			
		});
		
		
		// cancel button
		
		$('#inputfollowcancelbut').click(function() {

			$('#wininputfollow').dialog("close");
			
		});
		
		
		tiptip();
	
	});

</script>
