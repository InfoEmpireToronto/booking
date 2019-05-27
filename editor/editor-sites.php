<?php
use Booking\Records\Factory;
require('../basicsite/init.php');

$out = ['success' => false];

$sites = Factory\Site::get($core);

if($sites)
{
	$out = ['success' => true, 'sites' => $sites];
}

header("Content-type: application/json");
echo json_encode($out);