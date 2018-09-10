<?php

include '../src/Cpanel.php';

$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
$response = $cpl->addPickupPass(['pickup' => '__PHRASE__']);

echo $response;
