
<br /><br />

<div class='inputidbox'>

	<form method="get" action="#">

	<span class='blabla'>Public SeMail to open :</span><br /><br />
	<input type='text' class="invitarea" id='inputidarea' name='inputidarea' /><br /><br />
	<button class='halfbut' id='inputidbut' type="button">Open</button>
	<button class='halfbut' id='inputidcancelbut' type="button">Cancel</button>

	</form>

</div>



<script>

	$(document).ready(function(){
	
		$("button").button();
		
		
		// open button
		
		$('#inputidbut').click(function() {
		
			var tab = $('#inputidarea').val().split(";");
			if(tab.length == 2)
			{
				var s = tab[0];
				var id = tab[1]

				showwin("winsemail"+id.substring(0,20), "Public SeMail", "controllers/viewSemail.php?id="+id+"&server="+s);
				
				$('#wininputid').dialog("close");
			}
			
		});
		
		
		// cancel button
		
		$('#inputidcancelbut').click(function() {

			$('#wininputid').dialog("close");
			
		});
		
		
		tiptip();
	
	});

</script>
