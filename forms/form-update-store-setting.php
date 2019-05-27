<?php
require('../basicsite/init.php');
use Booking\Records\Setting;
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Failed';
$payload = json_decode(file_get_contents('php://input'));
if($payload)
{
	foreach ($payload as $key => $value)
	{
		$setting = new Setting($core, ['key' => $key]);
		$setting->value = $value;
		$success = $setting->save();
	}
	if($success)
	{
		$out = [
			'success' => true,
			'message' => 'Updated'
		];
	}
}
else
{
	if($_POST)
	{
		foreach ($_POST as $key => $value)
		{
			$setting = new Setting($core, ['key' => $key]);
			$setting->value = $value;
			$success = $setting->save();
		}
	}
}
if($success)
{
	$out = [
		'success' => true,
		'message' => 'Updated'
	];
}

echo json_encode($out);
?>