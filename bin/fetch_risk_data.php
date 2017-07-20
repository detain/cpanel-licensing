<?php
include('../cpl.inc.php');
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = '__IP__';

// This is only useful to cPanel Distributors
$status = (array) $cpl->fetchLicenseRiskData(
	[
		'ip' => $ipAddress
	]
);

if ($status['@attributes']['status'] == 1) {
   print "Risk Scores for $ipAddress:\n";
   print 'aggregate : ' . $status['@attributes']['riskscore.aggregate.score'].PHP_EOL;
   print 'directorder : ' . $status['@attributes']['riskscore.directorder.score'].PHP_EOL;
   print 'main : ' . $status['@attributes']['riskscore.main.score'].PHP_EOL;
} else {
   print "Failed to fetch risk data\n";
}


