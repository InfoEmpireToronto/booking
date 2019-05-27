<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use \Booking\Records\Setting;
$defaultStartTime = new Setting($core, ['key' => 'start_time']);
$defaultEndTime = new Setting($core, ['key' => 'end_time']);
if($user->user_type != 1 && $user->user_type != 2)
{
	die();
}
$out['success'] = false;

$time = Factory\TimeSlot::compose($core)
			->cols(['/id' => 'value', '/time' => 'text'])
			->get();
if($time)
{
	foreach ($time as $value)
	{
		$value->text = date('g:i A', strtotime($value->text));
	}
	$out = [
		'success' => true,
		'time' => $time,
		'start' => $defaultStartTime->value,
		'end' => $defaultEndTime->value
	];
}
echo json_encode($out);
?>