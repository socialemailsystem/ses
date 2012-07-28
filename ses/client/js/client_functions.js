

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
	if (document.selection) {
	ctrl.focus ();
		var Sel = document.selection.createRange ();
		Sel.moveStart ('character', -ctrl.value.length);
		CaretPos = Sel.text.length;
	}
	// Firefox support
	else if (ctrl.selectionStart || ctrl.selectionStart == '0')
		CaretPos = ctrl.selectionStart;
	return (CaretPos);
}

function setCaretPosition(ctrl, pos){
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



// ping each X seconds for update
function bigping(freqping)
{
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
	  data: { idlist: idlist, lastlist: lastlist }
	}).done(function( msg ) {
	
		msg = jQuery.trim(msg);
		
		var newmsg = false;
		
		if(msg != "" && msg != "[]")
		{
			//alert("'"+msg+"'");
			
			json = jQuery.parseJSON(msg);

			for(var i in json)
			{
				// open SeMail received a new message
				if(allsemail[json[i]] != null)
				{
					var shortid = json[i].substring(0,20);
					var name = "winsemail" + shortid;
					
					// save message and caret position
					var msgarea = $("#msg" + shortid);
					var msg = msgarea.val();
					var pos = doGetCaretPosition(document.getElementById("msg" + shortid));

					
					updatewin(name, "controllers/viewSemail.php?id=" + json[i], function () {
						var msgarea = $("#msg" + shortid);
						// restore message and caret position
						msgarea.val(msg);
						msgarea.focus();
						setCaretPosition(document.getElementById("msg" + shortid),pos);
					});
					
					newmsg = true;
				}
				
				// listed SeMail received a new message
				if(mainsemail[json[i]] != null)
				{
					refreshmain();
					
					newmsg = true;
				}
			}
		}
		
		if(newmsg == true)
		{
			//shownotif("New message !", 2000);
		}
		
		setTimeout('bigping('+freqping+');',freqping);
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


function askfollow(user, contact)
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

			callcontroller("controllers/action"+fl+".php?user="+user+"&contact="+contact, function() {
				refreshmain();
			});
		
		});
		
	});
	
}


function tiptip()
{
	$('a[title]').qtip({
		overwrite: false,
		
		show: {
				delay: 1000
			},
			
		style: {
			classes: 'ui-tooltip-blue ui-tooltip-shadow ui-tooltip-rounded'
		}
	});
}


function invitpeople(id)
{
	showwin("invitwin", "Invit people to a SeMail", "controllers/viewSelectpeople.php?id=" + id, function(msg) {
	});
}


function deletesemail(id)
{
	callcontroller("controllers/actionDelete.php?id=" + id, function() {
		refreshmain();
		$('#winsemail'+id.substring(0,20)).dialog("destroy");
	});
}


function refreshmain()
{

	var $tabs = $('#tabs').tabs();
	var selected = $tabs.tabs('option', 'selected');
			
	updatewin("winmain", "controllers/viewMain.php?nbrsemails="+nbrsemails+"&nbrfeeds="+nbrfeeds, function()
	{
		$tabs = $('#tabs').tabs();
		$tabs.tabs('select', selected);
	});
}

