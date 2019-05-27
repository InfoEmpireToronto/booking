<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use \Booking\Records\Setting;

$defaultStartTime = new Setting($core, ['key' => 'start_time']);
$defaultEndTime = new Setting($core, ['key' => 'end_time']);

if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

$time = Factory\TimeSlot::compose($core)
			->cols(['/id' => 'value', '/time' => 'text'])
			->get();

if($core->db->success())
{
	$success1 = true;
	foreach ($time as $value)
	{
		$value->text = date('g:i A', strtotime($value->text));
	}
}

if($weekdays = Factory\Weekday::get($core))
{
	$success = true;
	foreach ($weekdays as $value)
	{
		$value->elementID = 'weekday' . $value->id;
		$value->availability = true;
		$value->start = $defaultStartTime->value;
		$value->end = $defaultEndTime->value;
	}
}

if($success && $success1)
{
	$out = [
		'success' => true,
		'weekdays' => $weekdays,
		'time' => $time
	];
}
echo json_encode($out);
?>