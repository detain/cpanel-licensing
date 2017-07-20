<?php
include ("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = '69.10.46.221';

$status = $cpl->fetchLicenseRaw(["ip" => $ipAddress]);
var_export($status);
if (isset($status['@attributes'])) {
	print "The license id for the ip is: ".$status["@attributes"]["licenseid"].PHP_EOL;
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

