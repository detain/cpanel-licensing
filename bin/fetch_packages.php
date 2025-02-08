<?php

include __DIR__.'/../../../../include/functions.inc.php';

$cpl = new \Detain\Cpanel\Cpanel(CPANEL_LICENSING_USERNAME, CPANEL_LICENSING_PASSWORD);
$cpl->format = 'json';
$return = json_decode($cpl->fetchPackages(),true);
print_r($return);
//echo json_encode($licenses, JSON_PRETTY_PRINT);
