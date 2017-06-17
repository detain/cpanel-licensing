<?php

include("../cpl.inc.php");

$cpl = new \Detain\Cpanel\Cpanel( $_SERVER['argv'][1], $_SERVER['argv'][2] );

$ip = "__IP__";

$groupid = $cpl->findKey("__GROUPNAME__", $cpl->fetchGroups() );
$packageid = $cpl->findKey("__PACKAGENAME__", $cpl->fetchPackages() );

$lisc = (array)$cpl->activateLicense(array(
		"ip" => $ip,
		"groupid" => $groupid,
		"packageid" => $packageid,
		"force" => 1,
// If 0 the license will not be activated if you would be billed a reactivation fee
// If 1 the license will be activated if a fee is required (at the time of this writing, licenses reactivated within 72 hours of billing)
		"reactivateok" => 1
	)
);

if ($lisc['@attributes']['status'] > 0) {
	print "license added with id: ". $lisc['@attributes']['licenseid'] . "\n";
} else {
	print "License add failed: " . $lisc['@attributes']['reason'] . "\n";
}

