<?php

include '../src/Cpanel.php';
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);


$oldip = '__SOURCEIP__';
$newip = '__DESTINATIONIP__';

$response = (array) $cpl->changeip(
    [
        'oldip' => $oldip,
        'newip' => $newip
    ]
);

var_export($response).PHP_EOL;
