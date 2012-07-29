
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



function cache_set($v)
{
	if(user_get() != "")
	{
		$cache_file = $_SESSION["ses_cache"];
		file_put_contents($cache_file, $v);
	}
}

function cache_get()
{
	if(user_get() != "")
	{
		$cache_file = $_SESSION["ses_cache"];
		return file_get_contents($cache_file);
	}
	
	else
		return "";
}



function cache_create()
{
	if(user_get() != "")
	{
		$cache_file = dirname(__FILE__)."/tmp/cache_".md5(user_get());
		$_SESSION["ses_cache"] = $cache_file;
		
		if(!file_exists($cache_file))
		{
			file_put_contents($cache_file, "");
			//touch($cache_file);
		}
	}
	
	else
	{
		$_SESSION["ses_cache"] = "";
	}
}



if(!isset($_SESSION["ses_user"]))
		$_SESSION["ses_user"] = "";
else
	user_login($_SESSION["ses_user"]);


?>
