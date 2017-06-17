<?php
include ("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ip = '69.10.46.221';

$status = $cpl->fetchLicenseRaw(array("ip" => $ip));
print_r($status);
if (isset($status['@attributes'])) {
	print "The license id for the ip is: ".$status["@attributes"]["licenseid"]."\n";
	print "The status of the license is: ";
	if ($status["@attributes"]["valid"] > 0) {
		print "Active";
	} else {
		print "Inactive";
	}
	print "\n";
	print "The company holding the license is: ".$status["@attributes"]["company"].
		"\n";
} else {
	print "The status of the license is: Not Licensed\n";
}

