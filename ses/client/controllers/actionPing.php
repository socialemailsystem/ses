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



if(!isset($_GET["idlist"]) || !isset($_GET["lastlist"]) || !isset($_GET["tags"]))
{
	die();
}


$idlist = $_GET["idlist"];
$lastlist = $_GET["lastlist"];
$tags = $_GET["tags"];



$arrid = array();
$arrlast = array();


// array with all ids
$expl = explode(";", $idlist);
foreach($expl as $l)
{
	$id = trim($l);
	if($id != "")
		$arrid[] = $id;
}

// array with all last active dates
$expl = explode(";", $lastlist);
foreach($expl as $l)
{
	$la = trim($l);
	if($la != "")
		$arrlast[] = $la;
}


$list = ses_bigping($arrid, $arrlast, $tags);

echo json_encode($list);


?>
