<?php
include("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);


$oldip = "__SOURCEIP__";
$newip = "__DESTINATIONIP__";

$response = (array) $cpl->changeip(array(
	"oldip" => $oldip,
	"newip" => $newip
));

print_r($response)."\n";


