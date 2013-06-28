<?php

/*
    Social eMail System
    Copyright (C) 2012-2013 Jeremy DAFFIX

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


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



cache_create();



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

    <div class="notification ondemand" id='topnotif'> <!-- ondemand hide ? -->
        <p>
		
		<button id="btnlogout">Logout</button>
		
		<button id="btnnewpublic">Public SeMail</button>
		<button id="btnnewoninvit">On Invite SeMail</button>
		<button id="btnnewprivate">Private SeMail</button>
		
		<button id="btnopenid">Open with id</button>
		
		<button id="btnaddcontact">Follow someone</button>
		<button id="btnsettings">Settings</button>

        </p>
        <a class="close" href="javascript:"><img src="images/icon-close.png" /></a>
    </div>
	
	<div class="show ondemand-button" id='notifbut'>
        <a href="javascript:"><img src="images/icon-arrowdown.png" /></a>
	</div>
	
	
	
	<!-- Windows -->
	
	<div id="wins">
	</div>
	
	<div id="dialog-confirm">
	</div>
	
	<span id='dbg'></span>


	<?php

		if(user_get() != "" && !(isset($_GET['displaypublic']) && isset($_GET['displayserver'])))
			echo "<img src='images/ses.png' />\n";

	?>
	
	
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f25720603bd8fc5"></script>


<script>

	$(document).ready(function(){
		
		
		// init
		
		$("button").button();

		
		
		// welcome message
		
		shownotif("<?php echo $SES_WELCOME; ?>", 5000);
		
		
		
		

<?php

/*********************************************************/




		// *** displaying a public SeMail ***

		if(isset($_GET['displaypublic']) && isset($_GET['displayserver'])) {

			$dp = $_GET['displaypublic'];
			$ds = $_GET['displayserver'];

		?>
		
		
		allsemail = new Object(); // array of all open SeMails
		mainsemail = new Object(); // array of all listed SeMails in the main window
		
		arrmessages = new Object(); // array of messages (to keep them while refreshing a SeMail...)
		arrpos = new Object();
		

		var id = '<?php echo $dp; ?>';
		var s = '<?php echo $ds; ?>';
				
		showwin("winsemail"+id.substring(0,20), "Public SeMail", "controllers/viewSemail.php?id="+id+"&server="+s, function() {

			// display a Public id
			$(".copyid").live("click", function (e) {
			
				showwin("winshare", "Share Public SeMail", "controllers/viewShare.php?id="+s+';'+id+"&server="+s, function() {
					$( "#winshare" ).dialog("option", "resizable", false);
					$( "#winshare" ).dialog("option", "width", 600);
					$( "#winshare" ).dialog("option", "height", 430);
				});
				
			
			});


			// details on someone
			var gra = null;
			//$(".someparticipant, .avatar, .avatarblock").live("click", function (e) {
			$(document).on('click', ".someparticipant, .avatar, .avatarblock", function (e) {

				gra = $(e.target);

				if($('#mytipid').length != 0)
					$('#mytipid').attr('id', '');

				gra.attr('id', 'mytipid');


				var x = gra.offset().left;
				var y = gra.offset().top;


				var addr = $(this).attr("name").substring(4);

				$.ajax({
				  type: "GET",
				  url: "controllers/viewProfile.php?addr=" + addr
				}).done(function( msg ) {

					$('.qtip').qtip("destroy");


					var my ='';
					var at = '';

					if(x < 350) {
						my = 'left center';
						at = 'right center'
					}

					else {
						my = 'right center';
						at = 'left center'
					}

				
					addr = "<a href='#' title='(Un)follow'><span class='someaddress' style='color: #70b0d0;' name='addr"+addr+"'>"+addr+"</span></a>";


					gra
					.qtip({
						content: {
							text: msg, 
							title: {
								text: "Profile of " + addr,
								button: true
							}
						},
						position: {
							my: my, // Use the corner...
							at: at, // ...and opposite corner

							viewport: $(window),

							adjust: {
								screen: 'flip'
							},

							target: jQuery('#mytipid')
						},
						show: {
							event: false, // Don't specify a show event...
							ready: true // ... but show the tooltip when ready
						},
						hide: false, // Don't specify a hide event either!
						style: {
							classes: 'profileqtip ui-tooltip-blue qtip-shadow qtip-blue'
						},
			    		
				    		events: {
					    		
					    		render: function(event, api) {

					    			//gra.qtip('option', { 'position.target' : 'jQuery("#mytipid")' });;
					    			
					    		}
				    		}
					});

				});
			});

			

			$('textarea, button').attr('disabled', 'disabled');
			$('.star').css('display', 'none');
			
		
		});

		$(".ondemand, .ondemand-button").remove();


		//return;


		<?php

			//user_logout();
			//return;

		}


		else {




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
		$( "#winlogin" ).dialog("option", "height", 215);
		$( "#winlogin" ).dialog("option", "position", [midx - 275, midy - 275]);
		
		// register window
		showwin("winregister", "Register", "controllers/viewRegister.php");
		$( "#winregister" ).dialog("option", "resizable", false);
		$( "#winregister" ).dialog("option", "width", 550);
		$( "#winregister" ).dialog("option", "height", 260);
		$( "#winregister" ).dialog("option", "position", [midx - 275, midy - 40]);



<?php



/*********************************************************/

}

else
{

		

?>
		
		$('.notification.ondemand').notify(({ type: 'ondemand' }));
		$('#notifbut').click();

		
		
		tags = '';
		
		// main window
		showwin("winmain", "SeMails and Feeds", "controllers/viewMain.php?nbrsemails=5&nbrfeeds=5&nbrdiscover=5&nbrfavorite=5&tags=&forcecache=1");
		
		// click on a tag
		$(document).on('click', '.sometag', function(e) {
	
        //e.stopPropagation();
        //e.preventDefault();
			
			tags = $(this).text().substr(1);
			$('#txttags').val(tags);
			
			//$('#txttags').keydown();
			$("#buttags").click();
		});

		
		
		// ping each X seconds for updateupdate
		
		allsemail = new Object(); // array of all open SeMails
		mainsemail = new Object(); // array of all listed SeMails in the main window
		
		nbrsemails = 5;
		nbrfeeds = 5;
		nbrdiscover = 5;
		nbrfavorite = 5;
		tabselected = '1';

		arrmessages = new Object(); // array of messages (to keep them while refreshing a SeMail...)
		arrpos = new Object();
		

		freqping = <?php echo $SES_FREQPING; ?>;
		
		bigping(freqping);
		
		
		
		// follow someone
		$(".someaddress").live("click", function (e) {
			
			askfollow("<?php echo $SES_ADDRESS; ?>", $(this).attr("name").substring(4), $(this).parent());
						
		});

		
		// details on someone
		var gra = null;
		//$(".someparticipant, .avatar, .avatarblock").live("click", function (e) {
		$(document).on('click', ".someparticipant, .avatar, .avatarblock", function (e) {

			gra = $(e.target);

			if($('#mytipid').length != 0)
				$('#mytipid').attr('id', '');

			gra.attr('id', 'mytipid');


			var x = gra.offset().left;
			var y = gra.offset().top;


			var addr = $(this).attr("name").substring(4);

			$.ajax({
			  type: "GET",
			  url: "controllers/viewProfile.php?addr=" + addr
			}).done(function( msg ) {

				$('.qtip').qtip("destroy");


				var my ='';
				var at = '';

				if(x < 350) {
					my = 'left center';
					at = 'right center'
				}

				else {
					my = 'right center';
					at = 'left center'
				}

				
				addr = "<a href='#' title='(Un)follow'><span class='someaddress' style='color: #70b0d0;' name='addr"+addr+"'>"+addr+"</span></a>";


				gra
				.qtip({
					content: {
						text: msg, 
						title: {
							text: "Profile of " + addr,
							button: true
						}
					},
					position: {
						my: my, // Use the corner...
						at: at, // ...and opposite corner

						viewport: $(window),

						adjust: {
							screen: 'flip'
						},

						target: jQuery('#mytipid')
					},
					show: {
						event: false, // Don't specify a show event...
						ready: true // ... but show the tooltip when ready
					},
					hide: false, // Don't specify a hide event either!
					style: {
						classes: 'profileqtip ui-tooltip-blue qtip-shadow qtip-blue'
					},
	            		
			    		events: {
				    		
				    		render: function(event, api) {

				    			//gra.qtip('option', { 'position.target' : 'jQuery("#mytipid")' });;
				    			
				    		}
			    		}
				});

			});
		});
		
		
		// favorite
		$(".star").live("click", function (e) {
		
			var user = '<?php echo $SES_ADDRESS; ?>';
			var semailfav = $(this).attr("id").substring(4);
			var serverfav = $(this).attr("name").substring(4);
			var tagsfav = $(this).attr("alt").substring(4);
			
			if($(this)/*.parent()*/.attr('title') == 'Add to favorites')
				$(this)/*.parent()*/.attr('title', 'Remove from favorites');
			else
				$(this)/*.parent()*/.attr('title', 'Add to favorites');
			
			callcontroller("controllers/actionFavorite.php?user="+user+"&semail="+semailfav+"&server="+serverfav+"&tags="+tagsfav, function() {
				refreshmain();
			});
			
		});
		
		
		// invite people to a SeMail
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
		
		
		// display a Public id
		$(".copyid").live("click", function (e) {

			var serv = $(this).attr("name").substring(3);
			var id = serv + ";" + $(this).attr("id").substring(3);

			showwin("winshare", "Share Public SeMail", "controllers/viewShare.php?id="+id+"&server="+serv, function() {
				$( "#winshare" ).dialog("option", "resizable", false);
				$( "#winshare" ).dialog("option", "width", 600);
				$( "#winshare" ).dialog("option", "height", 430);
			});
			
			
		});
		
		
		
		// open with id
		$('#btnopenid').click(function() {
			
			showwin("wininputid", "Open Public SeMail", "controllers/viewInputid.php", function() {
				$( "#wininputid" ).dialog("option", "resizable", false);
				$( "#wininputid" ).dialog("option", "width", 300);
				$( "#wininputid" ).dialog("option", "height", 200);
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
				$( "#wincreate" ).dialog("option", "height", 300);
			});
			
		});
		
		// create On Invite
		$('#btnnewoninvit').click(function() {
			
			showwin("wincreate", "Create On Invite SeMail", "controllers/viewCreate.php?type=1", function() {
				$( "#wincreate" ).dialog("option", "resizable", false);
				$( "#wincreate" ).dialog("option", "width", 400);
				$( "#wincreate" ).dialog("option", "height", 300);
			});
			
		});

		// create Private
		$('#btnnewprivate').click(function() {
			
			showwin("wincreate", "Create Private SeMail", "controllers/viewCreate.php?type=2", function() {
				$( "#wincreate" ).dialog("option", "resizable", false);
				$( "#wincreate" ).dialog("option", "width", 400);
				$( "#wincreate" ).dialog("option", "height", 300);
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
				$( "#winsettings" ).dialog("option", "width", 550);
				$( "#winsettings" ).dialog("option", "height", 430);
			});
			
		});	


<?php

}

}

?>

		
	});

</script>




</body>

</html>
