

<form method="get" action="#">

	<br />
	<span class="blabla">You can share this public id with your friends : </span><br /><br />
	<input type='text' class="invitarea" id='sharearea' name='sharearea' value='<?php echo $id; ?>' readonly=1 /><br /><br />
	<button id='sharecancelbut' type="button">Close</button>

</form>




<script>

	$(document).ready(function(){
	
		$("button").button();

		
		// cancel button
		
		$('#sharecancelbut').click(function() {

			$('#winshare').dialog("close");
			
		});
		
		
		// select on focus
		$("#sharearea").focus(function(){
			this.select();
		});

		
		
		tiptip();
	
	});

</script>
