
<br /><br />

<div class='createbox'>

	<form method="get" action="#">
		<input type="checkbox" name="readonly" id="readonly" /><span class='blabla'> Read Only</span><br /><br />
		<span class='blabla'>Title :</span><br /><br />
		<input type='text' class="invitarea" id='createareatitle' name='createareatitle' /><br /><br />
		<span class='blabla'>Tags (separated by ';') :</span><br /><br />
		<input type='text' class="invitarea" id='createarea' name='createarea' /><br /><br />
		<button class='halfbut' id='createbut' type="button">Next</button>
		<button class='halfbut' id='createcancelbut' type="button">Cancel</button>
	</form>

</div>



<script>

	$(document).ready(function(){
	
		$("button").button();
		
		
		// next button
		
		$('#createbut').click(function() {
		
			var type = '<?php echo $type; ?>';
			var readonly = ($('#readonly').attr('checked') ? "1" : "0");

			callcontroller("controllers/actionCreate.php?readonly="+readonly+"&tags="+$('#createarea').val()+"&title="+$('#createareatitle').val()+"&type="+type, function(msg) {
				
				msg = jQuery.trim(msg);
				
				$('#wincreate').dialog("close");
				
				//alert(msg);
				
				if(type != "0")
				{
					invitpeople(msg);
				}
				
				refreshmain();
			});

			
		});
		
		
		// cancel button
		
		$('#createcancelbut').click(function() {

			$('#wincreate').dialog("close");
			
		});
		
		
		tiptip();
	
	});

</script>
