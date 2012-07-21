<?php
session_start();
?><!DOCTYPE html>

<?php

set_include_path("../server/" . PATH_SEPARATOR . get_include_path());

require_once("ses.php");
require_once("client_config.php");
require_once("client_functions.php");

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
		
		$('.notification.ondemand').notify(({ type: 'ondemand' }));
		

		$("button").button();

		
		
		// welcome message
		shownotif("<?php echo $SES_WELCOME; ?>", 3000);
		
		// main window
		showwin("winmain", "SeMails and Feeds", "controllers/viewMain.php?nbrsemails=5");
		
		
		// ping each X seconds for update
		
		allsemail = new Object(); // array of all open SeMails
		mainsemail = new Object(); // array of all listed SeMails in the main window
		
		freqping = <?php echo $SES_FREQPING; ?>;
		
		bigping(freqping);
		
		
		
		
		$(".someaddress, .someparticipant").live("click", function (e) {
		
			askfollow("<?php echo $SES_ADDRESS; ?>", $(this).html());
			
		});
		
		
	});

</script>

</body>

</html>
