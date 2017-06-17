<?php

include("../cpl.inc.php");

$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$licenses = $cpl->fetchExpiredLicenses();

foreach ($licenses->licenses as $lisc) {
	$lisc = (array) $lisc;
	print "\nLicense IP: ".$lisc['@attributes']['ip']."\n";
	print "        ID: ".$lisc['@attributes']['name']."\n";
	print "   groupid: ".$lisc['@attributes']['groupid']."\n";
	print " packageid: ".$lisc['@attributes']['packageid']."\n";
	print "   adddate: ".$lisc['@attributes']['adddate']."\n";
	print "expired on: ".$lisc['@attributes']['expiredon']."\n";
	print "    reason: ".$lisc['@attributes']['expirereason']."\n";
}


