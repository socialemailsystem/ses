
<?


// tests


require_once("ses.php");


ses_init();


echo "<h2>Tests</h2><br /><br />";

$sender = "tests::$SES_SERVER";
$type = "1";
$list = "lol::lilol42.com;lol242::lilol.net;kikoo::$SES_SERVER";
$readonly = "1";
$tags = "one;two;three";
$idk = ses_prepare_create($sender, $type, $list, $readonly, $tags);

echo "key : $idk[0]<br />";
echo "id : $idk[1]<br /><br />";

echo '"<br />';
echo ses_query_create($SES_SERVER, $idk[0], $sender, $idk[1], $type, $list, $readonly, $tags);
echo '<br />"<br /><br />';


$message = "kikoo kom sa va & lol";
$datesent = date("Y-m-d H:i:s");
//$sender = "kikoo::$SES_SERVER";
$keymsg = ses_prepare_message($sender, $idk[1], $message, $datesent);

echo '"<br />';
echo ses_query_message($SES_SERVER, $keymsg, $sender, $idk[1], $message, $datesent);
echo '<br />"<br /><br />';



$address = "someonetoinvit::$SES_SERVER";
//$sender = "tests::$SES_SERVER";
$sender = "kikoo::$SES_SERVER";
$keyinv = ses_prepare_invit($sender, $idk[1], $address);

echo '"<br />';
echo ses_query_invit($SES_SERVER, $keyinv, $sender, $idk[1], $address);
echo '<br />"<br /><br />';

echo "participating servers : <br/>";
var_dump(ses_listservers($idk[1]));
echo '<br />"<br /><br />';

echo "participating addresses : <br/>";
var_dump(ses_listaddresses($idk[1]));
echo '<br />"<br /><br />';


//ses_query_all_semail("localhost/projets/ses/server2", $idk[1]);

//$sender = "tests::$SES_SERVER";
//ses_delete($sender, $idk[1]);


$address = "tests::$SES_SERVER";
$from = "0";
$limit = "10";
echo "<br /> Feed : <br />";
echo ses_query_getfeed($address, $from, $limit);
echo '<br />"<br /><br />';


?>
