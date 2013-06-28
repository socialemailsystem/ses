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


$addr = user_get();
$user = User::find(array('conditions' => array("address = ?", $addr)));

if($user == null)
	die();


$about = ($user->descr != "") ? htmlentities($user->descr,0,"UTF-8") : "

[size=6][color=red]John Doe[/color][/size]


[b]Birth date : [/b] 1969-06-26
[b]Gender : [/b]M
[b]City : [/b]MyTown (MyCountry)

[b]Hobbies : [/b]

* Trains.
* Pizzas.
* Beer.
* SeMailing.

[b]Some words about me : [/b]
[i]
I like trains.
And cats. They are so cute.
[/i]

";



// call the view
include "../views/settings.php";

?>
