

// Client Javascript functions



// show a notification at the bottom of the screen
function shownotif(msg, to)
{

	noty({layout : 'bottom', theme : 'noty_theme_mitgux', type : 'warning', text: msg, timeout : to/*, closable: true,
		buttons: [
			{type: 'btn', text: 'New Public SeMail', click: function($noty) {		
				}
			},
				]*/
	});
}


// show a new window
function showwin(name, title, urlview, fct)
{
	$("#"+name).remove();
	
	$("#wins").append("<div id='"+name+"' title=\""+title+"\"></div>");
	
	$("#"+name).dialog({
			width: '500',
			height: '400',
			minWidth: '350',
			minHeight: '200',
			closeOnEscape: true
		});
	
	$.ajax({
	  type: "GET",
	  url: urlview
	}).done(function( msg ) {

	
	  $("#"+name).html(msg);
	  
	  //alert(msg);
	  
	  if(fct)
		fct();
	});
}


// update the content of an existing window
function updatewin(name, urlview, fct)
{
	$.ajax({
	  type: "GET",
	  url: urlview
	}).done(function( msg ) {

	  $("#"+name).html(msg);
		//alert(msg);
	  
	  if(fct)
		fct();
	});
}


// call an AJAX controller
function callcontroller(urlcont, fct)
{
	$.ajax({
	  type: "GET",
	  url: urlcont
	  
	}).done(function( msg ) {

	  if(fct)
		fct(msg);
	  
	});
}



function doGetCaretPosition (ctrl) {

	var CaretPos = 0;	// IE Support

	try {

		if(ctrl != null) {

			if (document.selection) {
			ctrl.focus ();
				var Sel = document.selection.createRange ();
				Sel.moveStart ('character', -ctrl.value.length);
				CaretPos = Sel.text.length;
			}

			// Firefox support
			else if (ctrl.selectionStart || ctrl.selectionStart == '0')
				CaretPos = ctrl.selectionStart;
		}

	} catch(err) {
	}

	return (CaretPos);
}

function setCaretPosition(ctrl, pos){

	try {

		if(ctrl != null) {

			if(ctrl.setSelectionRange)
			{
				ctrl.focus();
				ctrl.setSelectionRange(pos,pos);
			}

			else if (ctrl.createTextRange) {
				var range = ctrl.createTextRange();
				range.collapse(true);
				range.moveEnd('character', pos);
				range.moveStart('character', pos);
				range.select();
			}
		}

	} catch(err) {
	}
}



// ping each X seconds for update
function bigping(freqping)
{

	// reposition qtip

	if($('#mytipid').length != 0) {
		
		$('#mytipid').qtip('reposition');
	}


	// one array to rule them all

	var all = Object();
	
	for(var i in allsemail) // open SeMails
	{
		all[i] = allsemail[i];
	}
	
	for(var i in mainsemail) // listed SeMails
	{
		all[i] = mainsemail[i];
	}
	

	
	var idlist = '';
	var lastlist = '';
	
	for(var i in all)
	{
		idlist += (idlist == '' ? '' : ";") + i;
		lastlist += (lastlist == '' ? '' : ";") + all[i];
	}


	// ping !
	$.ajax({
	  type: "GET",
	  url: "controllers/actionPing.php",
	  data: { idlist: idlist, lastlist: lastlist, tags: tags }
	}).done(function( msg ) {
	
		msg = jQuery.trim(msg);
		
		//var newmsg = false;
		//alert('ping : ' + msg);
		if(msg != "" && msg != "[]")
		{
			//alert("'"+msg+"'");
			
			json = jQuery.parseJSON(msg);
		
			// code for "new SeMail"
			var ind = json.indexOf("4242");
			if(ind != -1)
			{
				json[ind] = '';
				refreshmain();
			}

			for(var i in json)
			{	
				// open SeMail received a new message
				if(allsemail[json[i]] != null)
				{
					var shortid = json[i].substring(0,20);
					var name = "winsemail" + shortid;
					
					
					var server = '';
					
					var last = allsemail[json[i]];
					
					// remote
					if(last.substring(0,7) == "REMOTE_")
					{
						last = last.substring(7);
						var spl = last.split(",");
						server = spl[0];
						
						//alert(server);
					}

					
					updatewin(name, "controllers/viewSemail.php?id=" + json[i] + "&server=" + server, function () {

						/*alert("js : " + arrmessages[shortid]);
						msgarea.val(arrmessages[shortid]);
						msgarea.focus();
						setCaretPosition(document.getElementById("msg" + shortid),arrpos[shortid]);*/
					});
					
					//newmsg = true;
				}
				
				// listed SeMail received a new message
				if(mainsemail[json[i]] != null)
				{
					refreshmain();
					
					//newmsg = true;
				}
			}
		}
		
		/*if(newmsg == true)
		{
			//shownotif("New message !", 2000);
		}*/
		
		
		setTimeout('bigping('+freqping+');',freqping);
		
	}).fail(function(jqXHR, textStatus, errorThrown) {
	
		//alert(textStatus + " " + errorThrown);
		
		setTimeout('bigping('+(freqping * 4)+');',(freqping * 4));
	});
}


function modalwin(title, content, fctyes, fctno)
{
	$("#dialog-confirm").attr("title", title);
	$("#dialog-confirm").html(content);
	
	$("#dialog-confirm").dialog({
		resizable: false,
		//height:140,
		modal: true,
		buttons: {
		
			"Yes": function() {

				$(this).dialog("destroy");
				
				if(fctyes)
					fctyes();
			},
			
			"No": function() {
			
				$(this).dialog("destroy");
				
				if(fctno)
					fctno();
			}
		}
	});
}


function askfollow(user, contact, t)
{
	callcontroller("controllers/actionIsfollowing.php?user="+user+"&contact="+contact, function(msg) {
	
		msg = jQuery.trim(msg);
		
		var title = "";
		var content = "";
		var fl = "";

		if(msg == "0")
		{
			title = "Follow " + contact;
			content = "You are not following " + contact + ".<br /><br />Do you want to follow it ?";
			fl = "Follow";
		}
		
		else if(msg == "1")
		{
			title = "Unfollow " + contact;
			content = "You are following " + contact + ".<br /><br />Do you want to unfollow it ?";
			fl = "Unfollow";
		}
		
		modalwin(title, content, function() {
		
			/*if(f1 == 'Follow')
				t.attr('label', 'Unfollow ' + contact);
			else
				t.attr('label', 'Follow ' + contact);
				
			tiptip();*/
				
			callcontroller("controllers/action"+fl+".php?user="+user+"&contact="+contact, function() {
				refreshmain();
			});
		
		});
		
	});
	
}


function tiptip()
{
	$('a[title], img[title]').qtip({
		overwrite: false,
		
		show: {
				delay: 50
			},
			
		style: {
			classes: 'ui-tooltip-blue ui-tooltip-shadow ui-tooltip-rounded qtip-blue'
		}
	});
}


function invitpeople(id)
{
	showwin("invitwin", "Invite people to a SeMail", "controllers/viewSelectpeople.php?id=" + id, function(msg) {
	});
}


function deletesemail(id)
{
	callcontroller("controllers/actionDelete.php?id=" + id, function() {
		refreshmain();
		$('#winsemail'+id.substring(0,20)).dialog("destroy");
	});
}


function refreshmain(forcecache)
{

	var $tabs = $('#tabs').tabs();
	//var selected = $tabs.tabs('option', 'selected');
	
	var fc = "";
	
	if(forcecache)
		fc = "&forcecache=1";
		
	//var tags = $('#txttags').val();
	//alert("controllers/viewMain.php?nbrsemails="+nbrsemails+"&nbrfeeds="+nbrfeeds+"&nbrdiscover="+nbrdiscover+"&tags="+tags+fc);
	
	updatewin("winmain", "controllers/viewMain.php?nbrsemails="+nbrsemails+"&nbrfeeds="+nbrfeeds+"&nbrdiscover="+nbrdiscover+"&nbrfavorite="+nbrfavorite+"&tags="+tags+fc, function()
	{
		$tabs = $('#tabs').tabs();
		//$tabs.tabs('select', selected);
		
		$("#tabs").tabs("select", tabselected);
		
		$("#txttags").val(tags);
		
		//$('#txttags').focus();
		//dbg('refresh');
	});
}


function dbg(txt)
{
	$('#dbg').text($('#dbg').text() + '\n' + txt);
}
