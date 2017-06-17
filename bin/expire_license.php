<?php
include("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ip = "__IP__";

$lisc = (array) $cpl->fetchLicenseId(array("ip" => $ip));
$liscid = $lisc["@attributes"]["licenseid"];

if ($liscid > 0) {
    $expire = (array) $cpl->expireLicense(array(
        liscid => $liscid,
        reason => "Automagic Expiration",
        expcode => "normal"
        )
    );
    print $expire["@attributes"]["result"]."\n";
} else {
    print "There is no valid license for $ip\n";
}


