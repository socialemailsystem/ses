
<?php

function jsdbg($msg)
{
	echo "<script>alert('".addslashes($msg)."');</script>";
}



function user_get()
{
	return $_SESSION["ses_user"];
}

function user_login($u)
{
	global $SES_ADDRESS;
	$_SESSION["ses_user"] = $u;
	$SES_ADDRESS = $u;
}

function user_logout()
{
	global $SES_ADDRESS;
	$_SESSION["ses_user"] = "";
	$SES_ADDRESS = "";
}





if(!isset($_SESSION["ses_user"]))
		$_SESSION["ses_user"] = "";
else
	user_login($_SESSION["ses_user"]);


?>
