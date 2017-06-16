<?php

include("cpl.inc.php");

$cpl = new \Detain\Cpanel\Cpanel("", "");
$response = $cpl->registerAuth(array(
    "user" => $_SERVER['argv'][1],
    "pickup" => "__PHRASE__",
    "service" => "__SERVICE__"));

echo $response;


