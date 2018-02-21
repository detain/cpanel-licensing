<?php
include '../src/Cpanel.php';
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = '__IP__';

$lisc = (array) $cpl->fetchLicenseId(['ip' => $ipAddress]);

$id = $lisc['@attributes']['licenseid'];

if ($id > 0) {
	$result = (array) $cpl->reactivateLicense(['liscid' => $id, reactivateok => 1]);
} else {
	print "no expired license exists for $ipAddress\n";
}

if ($result['@attributes']['status']) {
	print "the liscense for $ipAddress has been reactivated\n";
} else {
	print "Failed to reactivate license!\n";
}


