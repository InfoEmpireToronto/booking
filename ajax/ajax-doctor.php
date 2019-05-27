<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;

$doctors = Factory\Doctor::compose($core)
				->link('/user', 'User')
				->cols(['/id', '/firstname', '/lastname'])
				->filter('=', '/user/active', 1)
				->get();
if($doctors)
{
	foreach ($doctors as $doctor)
	{
		$composed[] = [
			'value' => $doctor->id,
			'text' => $doctor->firstname . ' ' . $doctor->lastname
		];
	}
	$out['success'] = true;
	$out['doctors'] = $composed;
	$out['doctor'] = $composed[0]['value']; //default
	
}
echo json_encode($out);
?>