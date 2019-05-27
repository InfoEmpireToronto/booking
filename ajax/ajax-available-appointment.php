<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\Doctor;
use Booking\Records\TimeSlot;
use Booking\Records\Appointment;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = '';
$payload = json_decode(file_get_contents('php://input'));
if(isset($_GET['date']))
{
	$day = date('N', strtotime($payload->date));
	$row = $core->db->getRow('week_days', ['day' => getDayText($day)]);
	$doctors = Factory\DoctorAvailability::compose($core)
					->link('/doctor', 'Doctor')
					->link('/doctor/user', 'User')
					->cols(['/doctor', '/doctor/firstname', '/doctor/lastname', '/start', '/end'])
					->filter('=', '/weekday', $row->id)
					->filter('=', '/active', 1)
					->filter('=', '/doctor/user/active', 1)
					->get();
	if($doctors)
	{
		$composed[] = ['value' => null, 'text' => 'Select a doctor'];
		foreach ($doctors as $doctor)
		{
			$composed[] = [
				'value' => $doctor->doctor,
				'text' => $doctor->firstname . ' ' . $doctor->lastname
			];
		}
		$out['success'] = true;
		$out['doctors'] = $composed;
	}
	else
	{
		$out['message'] = 'No available doctors, try another date';
	}
}

if(isset($_GET['time']))
{
	if($payload->appointment)
	{
		$doctor = new Doctor($core, $payload->doctor);
		$timetable = $doctor->getAvailableTime($payload->date);
		$appointment = new Appointment($core, $payload->appointment);
		for($i = $appointment->time; $i < $appointment->getEndTimeslot();  $i++)
		{
			$timetable[] = $i;
		}
		asort($timetable);
		foreach ($timetable as $value)
		{
			$time = new TimeSlot($core, $value);
			$composed[] = [
				'value' => $time->id,
				'text' => date('g:i A', strtotime($time->time))
			];
		}
		$out['success'] = true;
		$out['time'] = $composed;

	}
	else
	{
		$doctor = new Doctor($core, $payload->doctor);
		$timetable = $doctor->getAvailableTime($payload->date);
		if($timetable)
		{
			$composed[] = ['value' => null, 'text' => 'Select time'];
			foreach ($timetable as $value)
			{
				$time = new TimeSlot($core, $value);
				$composed[] = [
					'value' => $time->id,
					'text' => date('g:i A', strtotime($time->time))
				];
			}
			$out['success'] = true;
			$out['time'] = $composed;
		}
		else
		{
			$out['message'] = 'No available time';
		}
	}
	
}

echo json_encode($out);

function getDayText($day)
{
	switch ($day) {
		case 1:
			$result = 'MON';
			break;
		case 2:
			$result = 'TUE';
			break;
		case 3:
			$result = 'WED';
			break;
		case 4:
			$result = 'THU';
			break;
		case 5:
			$result = 'FRI';
			break;
		case 6:
			$result = 'SAT';
			break;
		case 7:
			$result = 'SUN';
			break;
	}
	return $result;
}
?>