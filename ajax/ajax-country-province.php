<?php
use Booking\Records\Factory;
require('../basicsite/init.php');
$out['success'] = false;

if($countries = Factory\Country::get($core))
{
	
	$success = true;
	$temp[] = ['value' => null, 'text' => 'Select conutry'];
	foreach ($countries as $value)
	{
		$temp[] = [
			'value' => $value->id,
			'text' => $value->name
		];
	}
	$out['countries'] = $temp;
}

$provinces = Factory\Province::compose($core)
				->cols(['/id', '/province'])
				->get();

if($core->db->success())
{
	$composed[] = ['value' => null, 'text' => 'Select province'];
	foreach ($provinces as  $province) {
		$composed[] = [
			'value' => $province->id,
			'text' => $province->province
		];
	}
	$success2 = true;
	$out['provinces'] = $composed;
}

if($success && $success2)
	$out['success'] = true;

echo json_encode($out);
?>