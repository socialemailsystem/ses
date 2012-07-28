<?php
session_start();
?><!DOCTYPE html>

<?php

set_include_path("../server/" . PATH_SEPARATOR . get_include_path());

require_once("ses.php");
require_once("client_config.php");
require_once("client_functions.php");

//require_once("lib/nbbc/nbbc.php");



if(isset($_SESSION["ses_message"]) && $_SESSION["ses_message"] != "")
{
	$SES_WELCOME = $_SESSION["ses_message"];
	$_SESSION["ses_message"] = "";
}



/**
*
* About the architecture :
*
* The client is a kind of very special MVC application, since there is only one "true" page (index.php) with JQuery windows inside.
*
* -> The models are in server/models/, and are managed by the PHP Active Record ORM.
* -> The client/controllers/actionName.php do some stuff like creating SeMails, sending commands, etc, without displaying anything (called by AJAX).
* -> The client/controllers/viewName.php get data and "feed" the views (called by AJAX).
* -> The client/views/name.php are included by their controllers.
*
*/

?>

<html>

<head>
	<title><?php echo $SES_TITLE; ?></title>
	<?php
	
		// CSS files
		echo "\n";
		foreach($SES_CSS as $css) {
			echo "<link rel='stylesheet' type='text/css' href='css/$css'>\n";
		}
		
		// Javascript files
		echo "\n";
		foreach($SES_JS as $js) {
			echo "<script type='text/javascript' src='js/$js'></script>\n";
		}
	?>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
</head>

<body>


	<!-- Top notification bar -->

    <div class="notification ondemand hide">
        <p>
		
		<button id="btnlogout">Logout</button>
		
		<button id="btnnewpublic">Public SeMail</button>
		<button id="btnnewoninvit">On Invit SeMail</button>
		<button id="btnnewprivate">Private SeMail</button>
		
		<button id="btnaddcontact">Follow someone</button>
		<button id="btnsettings">Settings</button>

        </p>
        <a class="close" href="javascript:"><img src="images/icon-close.png" /></a>
    </div>
	
	<div class="show ondemand-button">
        <a href="javascript:"><img src="images/icon-arrowdown.png" /></a>
	</div>
	
	
	
	<!-- Windows -->
	
	<div id="wins">
	</div>
	
	<div id="dialog-confirm">
	</div>
	
	
	
	

<script>

	$(document).ready(function(){
		
		
		// init
		
		$("button").button();

		
		
		// welcome message
		
		shownotif("<?php echo $SES_WELCOME; ?>", 5000);
		
		
		
		

<?php

/*********************************************************/


		// *** login ***


		if(user_get() == "")
		{

		?>

		var midx = $(window).scrollLeft() + Math.floor($(window).width() / 2);
		var midy = $(window).scrollTop() + Math.floor($(window).height() / 2);
		
		
		$(".ondemand, .ondemand-button").remove();

		
		// login window
		showwin("winlogin", "Login", "controllers/viewLogin.php");
		
		$( "#winlogin" ).dialog("option", "resizable", false);
		$( "#winlogin" ).dialog("option", "width", 550);
		$( "#winlogin" ).dialog("option", "height", 205);
		$( "#winlogin" ).dialog("option", "position", [midx - 275, midy - 275]);
		
		// register window
		showwin("winregister", "Register", "controllers/viewRegister.php");
		$( "#winregister" ).dialog("option", "resizable", false);
		$( "#winregister" ).dialog("option", "width", 550);
		$( "#winregister" ).dialog("option", "height", 245);
		$( "#winregister" ).dialog("option", "position", [midx - 275, midy - 40]);



<?php


/*********************************************************/

}

else
{

?>
		
		$('.notification.ondemand').notify(({ type: 'ondemand' }));
		
		
		// main window
		showwin("winmain", "SeMails and Feeds", "controllers/viewMain.php?nbrsemails=5&nbrfeeds=5");
		
		
		// ping each X seconds for updateupdate
		
		allsemail = new Object(); // array of all open SeMails
		mainsemail = new Object(); // array of all listed SeMails in the main window
		
		freqping = <?php echo $SES_FREQPING; ?>;
		
		bigping(freqping);
		
		
		
		// follow someone
		$(".someaddress, .someparticipant, .avatar, .avatarblock").live("click", function (e) {
		
			askfollow("<?php echo $SES_ADDRESS; ?>", $(this).attr("name").substring(4));
			
		});
		
		
		// invit people to a SeMail
		$(".invitpeople").live("click", function (e) {
		
			invitpeople($(this).attr("name").substring(3));
			
		});
		
		
		// delete a Public SeMail
		$(".deletesemail").live("click", function (e) {

			var id = $(this).attr("name").substring(3);
			
			modalwin("Delete Public SeMail", "<br />Do you want to delete the SeMail ?", function() {
				deletesemail(id);
			});
			
			
		});
		
		
		// follow someone
		$('#btnaddcontact').click(function() {
			
			showwin("wininputfollow", "Follow someone", "controllers/viewInputfollow.php", function() {
				$( "#wininputfollow" ).dialog("option", "resizable", false);
				$( "#wininputfollow" ).dialog("option", "width", 400);
				$( "#wininputfollow" ).dialog("option", "height", 200);
			});
			
		});
		
		
		
		// create Public
		$('#btnnewpublic').click(function() {
			
			showwin("wincreate", "Create Public SeMail", "controllers/viewCreate.php?type=0", function() {
				$( "#wincreate" ).dialog("option", "resizable", false);
				$( "#wincreate" ).dialog("option", "width", 400);
				$( "#wincreate" ).dialog("option", "height", 250);
			});
			
		});
		
		// create On Invit
		$('#btnnewoninvit').click(function() {
			
			showwin("wincreate", "Create On Invit SeMail", "controllers/viewCreate.php?type=1", function() {
				$( "#wincreate" ).dialog("option", "resizable", false);
				$( "#wincreate" ).dialog("option", "width", 400);
				$( "#wincreate" ).dialog("option", "height", 250);
			});
			
		});

		// create Private
		$('#btnnewprivate').click(function() {
			
			showwin("wincreate", "Create Private SeMail", "controllers/viewCreate.php?type=2", function() {
				$( "#wincreate" ).dialog("option", "resizable", false);
				$( "#wincreate" ).dialog("option", "width", 400);
				$( "#wincreate" ).dialog("option", "height", 250);
			});
			
		});	
		
		
		// logout
		$('#btnlogout').click(function() {
			
			callcontroller("controllers/actionLogout.php", function() {
				window.location = ".";
			});
			
		});	
		
		
		// settings
		$('#btnsettings').click(function() {
			
			showwin("winsettings", "Settings for <?php echo user_get(); ?>", "controllers/viewSettings.php", function() {
				$( "#winsettings" ).dialog("option", "resizable", false);
				$( "#winsettings" ).dialog("option", "width", 490);
				$( "#winsettings" ).dialog("option", "height", 215);
			});
			
		});	


<?php

}

?>

		
	});

</script>




</body>

</html>
