<?php
use Booking\Records\Factory;
require('../basicsite/init.php');
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

if($countries = Factory\Country::get($core))
{
	$temp[] = ['value' => null, 'text' => 'Select conutry'];
	$out['success'] = true;
	foreach ($countries as $value)
	{
		$temp[] = [
			'value' => $value->id,
			'text' => $value->name
		];
	}
	$out['countries'] = $temp;
}
echo json_encode($out);
?>