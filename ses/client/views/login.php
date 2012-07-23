
	<br /><br />

	<form method='get' action=''>

			<div class="myformcontent">

				<!-- address -->
				<div>
				<label for=loginaddr>Address: </label>
				<input id=loginaddr name=loginaddr type=text autofocus value="" />
				<span id="errAddr" class="formerror"></span>
				</div>

				<br />

				<!-- password -->
				<div>
				<label for=loginpwd >Password : </label>
				<input id=loginpwd name=loginpwd type=password autofocus value="" />
				<span id="errPassword" class="formerror"></span>
				</div>

				<br />
				
				<!-- mail -->
				<div>
				<label for=loginmail>Mail: </label>
				<input id=loginmail name=loginmail type=text autofocus value="" />
				<span id="errMail" class="formerror"></span>
				</div>

				<br />

				<!-- login  -->
				<button type=button id='mysub'>Login</button>

			</div>

	</form>
	
	
	
	
<script>

	$(document).ready(function(){
	
		$("button").button();
		
		/*
		// next button
		
		$('#createbut').click(function() {
		
			var type = '<?php echo $type; ?>';
			var readonly = ($('#readonly').attr('checked') ? "1" : "0");

			callcontroller("controllers/actionCreate.php?readonly="+readonly+"&tags="+$('#createarea').val()+"&type="+type, function(msg) {
				
				msg = jQuery.trim(msg);
				
				$('#wincreate').dialog("close");
				
				//alert(msg);
				
				if(type != "0")
				{
					invitpeople(msg);
				}
				
				updatewin("winmain", "controllers/viewMain.php?nbrsemails="+nbrsemails);
			});

			
		});
		
		
		// cancel button
		
		$('#createcancelbut').click(function() {

			$('#wincreate').dialog("close");
			
		});*/
		
		
		tiptip();
	
	});

</script>
