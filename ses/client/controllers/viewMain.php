<?php
session_start();
?>

<?php

set_include_path("../../server/" . PATH_SEPARATOR . get_include_path());

include "ses.php";
include "../client_config.php";
include "../client_functions.php";


if(user_get() == "")
	die();

ses_init();


if(!isset($_GET["nbrsemails"]) || !isset($_GET["nbrfeeds"]) || !isset($_GET["nbrdiscover"]) || !isset($_GET["nbrfavorite"]) || !isset($_GET["tags"]))
{
	die();
}


$tags = htmlentities($_GET["tags"], 0,"UTF-8");
if($tags == 'mytag1;mytag2') {
	$tags = '';
}



$nbrsemails = intval($_GET["nbrsemails"]);
$nbrfeeds = intval($_GET["nbrfeeds"]);
$nbrdiscover = intval($_GET["nbrdiscover"]);
$nbrfavorite = intval($_GET["nbrfavorite"]);
$forcecache = isset($_GET["forcecache"]);


$cache = cache_get();


$lastsemails = ses_getlastsemails("$SES_ADDRESS", 0, $nbrsemails, $tags);
if($cache == "" || $forcecache)
	$feedsemails = ses_getuserfeeds($SES_ADDRESS, 0, $nbrfeeds, $tags);
$discoversemails = ses_getdiscover(/*"$SES_ADDRESS", */0, $nbrdiscover, $tags);
$favoritesemails = ses_getfavorites("$SES_ADDRESS", 0, $nbrfavorite, $tags);



// call the view
include "../views/main.php";

?>
