<?php
include("cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = "__IP_TO_TRANSFER__";

$group = $cpl->findKey("__GROUP_NAME__", $cpl->fetchGroups());
$package = $cpl->findKey("__PACKAGE_NAME__", $cpl->fetchPackages());

$result = (array) $cpl->requestTransfer(
	[
	"ip" => $ipAddress,
	"groupid" => $group,
	"packageid" => $package
	]
);

print $result["@attributes"]['reason'].PHP_EOL;

