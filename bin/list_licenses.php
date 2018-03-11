<?php

include '../src/Cpanel.php';

$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
$cpl->format = 'json';
$licenses = json_decode($cpl->fetchLicenses(), TRUE);
echo json_encode($licenses, JSON_PRETTY_PRINT);
/*
foreach ( $licenses->licenses as $lisc ) {
	$lisc = (array)$lisc;
	print "\nLicense IP: " . $lisc['@attributes']['ip'].PHP_EOL;
	print "        ID: " . $lisc['@attributes']['name'].PHP_EOL;
	print "   groupid: " . $lisc['@attributes']['groupid'].PHP_EOL;
	print " packageid: " . $lisc['@attributes']['packageid'].PHP_EOL;
	print "   adddate: " . $lisc['@attributes']['adddate'].PHP_EOL;
}
*/

