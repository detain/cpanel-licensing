<?php
include("../cpl.inc.php");
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
$ip = "__IP__";
$lisc = (array) $cpl->fetchLicenseId(array("ip" => $ip));
print_r($lisc);
$id = $lisc['licenseid'];
$id = is_array($id) ? $id[0] : $id;
if ($id) {
    print "The license id for $ip is $id\n";
} else {
    print "No valid license exists for $ip\n";
}
?>

