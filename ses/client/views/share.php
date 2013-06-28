

<form method="get" action="#">

	<br />
	<span class="blabla">You can share this public id with your friends : </span><br /><br />
	<input type='text' class="invitarea" id='sharearea' name='sharearea' value='<?php echo $id; ?>' readonly=1 /><br /><br /><br />
	<span class="blabla">You can also use this RSS Feed : </span><br /><br />
	<?php 
		$id2 = explode(";",$id);
		$id2 = $id2[1];
		$addr = "http://$server/ses/client/controllers/actionRss.php?id=$id2&server=$server";
		echo "<a href='$addr' target='_blank'>Retrieve the last messages with a Feed aggregator !</a>";
	?> 
	<br /><br /><br />
	<span class="blabla">Or you can share this public link : </span><br /><br />
	<?php 
		//$id2 = explode(";",$id);
		//$id2 = $id2[1];
		$addr = "http://$server/ses/client/index.php?displaypublic=$id2&displayserver=$server";
		echo "<a href='$addr' target='_blank'>You don't need to be logged in to display me !</a>";
	?> <br /><br />
	
	<br />
	
	<!-- AddThis Button BEGIN -->
 <div id='addtb' class="addthis_toolbox addthis_default_style addthis_32x32_style"
		addthis:url="<?php echo $addr; ?>"
        addthis:title="<?php echo "Public SeMail on $server : "; ?>"
        addthis:description="Social eMail System - Beyond social network and mail">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
 <!-- <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f25720603bd8fc5"></script>  -->
<!-- AddThis Button END -->


	
	<br /><br /><br />

	<button type='button' id='sharecancelbut'>Close</button>

</form>



<script>

	$(document).ready(function(){
	
		$("button").button();
		
		//addthis.button('#addt', {}, {url: "<?php echo $addr; ?>", title: "<?php echo "Public SeMail on $server : "; ?>"});
		addthis.toolbox('#addtb');
		
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
