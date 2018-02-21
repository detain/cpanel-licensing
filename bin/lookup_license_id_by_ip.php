<?php
include '../src/Cpanel.php';
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
$ipAddress = '__IP__';
$lisc = (array) $cpl->fetchLicenseId(['ip' => $ipAddress]);
var_export($lisc);
$id = $lisc['licenseid'];
$id = is_array($id) ? $id[0] : $id;
if ($id) {
	print "The license id for $ipAddress is $id\n";
} else {
	print "No valid license exists for $ipAddress\n";
}


