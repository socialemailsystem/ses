
	<br /><br />

	<form method='get' action='controllers/actionLogin.php' id='loginform'>

		<!-- address -->
		<label for=loginaddr>Address: </label>
		<input id=loginaddr name=loginaddr type=text autofocus value="" /><span class='afterfield'>::<?php echo $SES_SERVER; ?></span>

		<br /><br />	

		<!-- password -->
		<label for=loginpwd >Password : </label>
		<input id=loginpwd name=loginpwd type=password autofocus value="" />

		<br /><br />

		<!-- login  -->
		<button type=submit id='loginbut' class='formbut'>Login</button>

	</form>
	


	
<script>

	$(document).ready(function(){
	
		$("button").button();
		
		
		tiptip();
	
	});

</script>
