<?php
include("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = "__IP__";

// This is only useful to cPanel Distributors
$status = (array) $cpl->fetchLicenseRiskData(array(
	"ip" => $ipAddress
	)
);

if ($status["@attributes"]["status"] == 1) {
   print "Risk Scores for $ipAddress:\n";
   print "aggregate : ".$status["@attributes"]["riskscore.aggregate.score"]."\n";
   print "directorder : ".$status["@attributes"]["riskscore.directorder.score"]."\n";
   print "main : ".$status["@attributes"]["riskscore.main.score"]."\n";
} else {
   print "Failed to fetch risk data\n";
}


