<?php
include '../src/Cpanel.php';
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = $_SERVER['argv'][3];
$cpl->format = 'json';
$lisc = json_decode($cpl->fetchLicenseId(['ip' => $ipAddress]), TRUE);
print_r($lisc);
$liscid = $lisc['licenseid'][0];

if ($liscid > 0) {
	$expire = json_decode($cpl->expireLicense(
		[
			liscid => $liscid,
			reason => 'Automagic Expiration',
			expcode => 'normal'
		]
	), TRUE);
	print $expire['result'].PHP_EOL;
} else {
	print "There is no valid license for $ipAddress\n";
}


