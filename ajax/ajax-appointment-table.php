<?php
require('../basicsite/init.php');
if(!$loggedIn)
{
	die();
}
use Booking\Records\Factory;
use Booking\Records\Doctor;
use Booking\Records\Patient;
$success['success'] = false;
$payload = json_decode(file_get_contents('php://input'));
$today = getdate();
$todayTimeStamp = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
if($payload->doctor != null && $payload->date != null)
{
	$valid = true;
	$doctor = new Doctor($core, $payload->doctor);
	$date = $payload->date;
	$dayOfWeek = date('w', $date);
	$offset = ($dayOfWeek - 1) < 0 ? 6 : ($dayOfWeek - 1);
	$mondayOfWeek = $date - ($offset * 86400);
}
else
{
	$doctors = Factory\Doctor::compose($core)
				->link('/user', 'User')
				->cols(['/id', '/firstname', '/lastname'])
				->filter('=', '/user/active', 1)
				->get();
	if($doctors)
	{
		$valid = true;
		if($user->user_type == 2)
			$doctor = new Doctor($core, ['user' => $user->id]);
		else
			$doctor = new Doctor($core, $doctors[0]->id); 
		$dayOfWeek = date('w', $today[0]);
		$offset = ($dayOfWeek - 1) < 0 ? 6 : ($dayOfWeek - 1);
		$mondayOfWeek = $todayTimeStamp - ($offset * 86400);
	}
}
if($valid)
{
	if($user->user_type == 3)
		$patient = new Patient($core, ['user' => $user->id]);
	for ($w = 1; $w <= 7; $w++)
	{
		$week[] = $mondayOfWeek;
		$class = ($todayTimeStamp == $mondayOfWeek) ? 'today' : null;
		$weekDate[] = [
			'day' => date('l', $mondayOfWeek),
			'date' => date('F d', $mondayOfWeek),
			'class' => $class
		];
		$monthOfWeek[] = date('F', $mondayOfWeek);
		$yearOfWeek[] = date('Y', $mondayOfWeek);
		$mondayOfWeek += 86400;
	}
	$timeslots = Factory\TimeSlot::get($core);
	foreach ($timeslots as $timeslot)
	{
		foreach ($week as $value)
		{
			$date = date('Y-m-d', $value);
			$data[] = $patient->id ? $doctor->timeSlotStatus($date, $timeslot, $patient->id) : $doctor->timeSlotStatus($date, $timeslot);
		}
		$row[] = [
			'time' => date('g:i A', strtotime($timeslot->time)),
			'timeid' => $timeslot->id,
			'timeslot' => $data
		];
		unset($data);
	}
	$monthOfWeek = array_values(array_unique($monthOfWeek, SORT_STRING));
	$yearOfWeek = array_values(array_unique($yearOfWeek, SORT_STRING));
	$monthDisplay = $monthOfWeek[0];
	$yearDisplay = $yearOfWeek[0];
	if(count($monthOfWeek) > 1)
		$monthDisplay = $monthOfWeek[0]. ' / ' . $monthOfWeek[1];
	if(count($yearOfWeek) > 1)
		$yearDisplay = $yearOfWeek[0] . ' / ' . $yearOfWeek[1];
	$appointmentTable = [
		'week' => $weekDate,
		'table' => $row,
		'monthdisplay' => $monthDisplay,
		'yeardisplay' => $yearDisplay,
		'nextweek' => $week[0] + 604800,
		'previousweek' => $week[0] - 604800,
		'nextmonth' => $week[0] + 2419200,
		'previousmonth' => $week[0] - 2419200,
		'today' => $todayTimeStamp
	];
	$out = [
		'success' => true,
		'appointmentable' => $appointmentTable,
		'monday' => $week[0],
		'doctor' => $doctor->id
	];
}

echo json_encode($out);

?>