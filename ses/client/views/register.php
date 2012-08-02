
	<br /><br />

	<form method='get' action='controllers/actionRegister.php' id='registerform'>

		<!-- address -->
		<label for=registeraddr>Address: </label>
		<input id=registeraddr name=registeraddr type=text autofocus value="" /><span class='afterfield'>::<?php echo $SES_SERVER; ?></span>

		<br /><br />	

		<!-- password -->
		<label for=registerpwd >Password : </label>
		<input id=registerpwd name=registerpwd type=password autofocus value="" /><span class='afterfield'>At least 6 characters !</span>

		<br /><br />
		
		<!-- mail -->
		<label for=registermail>Mail: </label>
		<input id=registermail name=registermail type=text autofocus value="" /><span class='afterfield'>Used for Gravatars.</span>

		<br /><br />	

		<!-- register  -->
		<button type=submit id='registerbut' class='formbut'>Register</button>

	</form>
	
	
	
	
<script>

	$(document).ready(function(){
	
		$("button").button();

		

        $("#registerform").validate({

			// some rules
          	rules: {
			
					registeraddr: {
						required: true,
						minlength: 4,
						maxlength: 42
					},

					registerpwd: {
						required: true,
						minlength: 6,
						maxlength: 42
					},
					  
					registermail: {
                	      maxlength: 128,
                	      email: true
                    },
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
			  
				form.submit();

          	  }

              });
		
		
		tiptip();
	
	});

</script>
