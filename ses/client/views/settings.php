
	<br /><br />

	<form method='get' action='#' id='settingsform'>

		<!-- password -->
		<label for=settingspwd >Password : </label>
		<input id=settingspwd name=settingspwd type=password autofocus value="" /><span class='afterfield'>Nothing for unchanged.</span>

		<br /><br />
		
		<!-- mail -->
		<label for=settingsmail>Mail: </label>
		<input id=settingsmail name=settingsmail type=text autofocus value="<?php echo htmlentities($user->mail,0,"UTF-8"); ?>" /><span class='afterfield'>Used for Gravatars.</span>

		<br /><br />	

		<!-- settings  -->
		<button type=button id='settingsbut' class='formbut'>Update</button>

	</form>
	
	
	
	
<script>

	$(document).ready(function(){
	
		$("button").button();

		

        $("#settingsform").validate({

			// some rules
          	rules: {

					settingspwd: {
						minlength: 6,
						maxlength: 42
					},
					  
					settingsmail: {
                	      maxlength: 128/*,
                	      email: true*/
                    }
          	  },

          	  messages: {

          	  },

          	  // errors
          	  errorPlacement: function(error, element) {

              	  if(error.html()!='')
              	  {
					  var name = element.attr("name");
					  var n = "#" + name;

					  $(n).css("background-color", "#ff0000");
              	  }
              },

              // validation success
          	  success: function(lab) {

          		  // on nettoie

              	  var name = lab.attr('for');
              	  var n = "#" + name;

                  $(n).css("background-color", "");
          	  },

          	  // submit form
          	  submitHandler: function(form) {
			  
				//form.submit();

				callcontroller("controllers/actionSettings.php?settingsaddr=<?php echo $user->address; ?>&settingsmail="+$("#settingsmail").val()+"&settingspwd="+$("#settingspwd").val(), function(msg) {
				
					msg = jQuery.trim(msg);

					shownotif(msg, 5000);
					$('#winsettings').dialog("close");
				
				});

          	  }

              });
			  
		
		$('#settingsbut').click(function() {
			$('#settingsform').submit();
		});
		
		
		tiptip();
	
	});

</script>
