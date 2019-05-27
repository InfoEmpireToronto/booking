<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\Doctor;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
if($user->user_type == 1)
{
	$employees = Factory\Doctor::compose($core)
				->link('/location', 'Location')
				->link('/user', 'User')
				->cols(['/id' => 'doctor', '/firstname', '/lastname', '/location/id' => 'location' ,
						'/location/name' =>'location_name' , '/description', '/user/active', '/user/id' => 'user', '/user/email', '/email_notification', '/sms_notification', '/title'])
				//->filter('=', '/user/active' , 1)
				->get();

	$timetable = Factory\DoctorAvailability::compose($core)
				->link('/weekday', 'Weekday')
				->link('/start', 'TimeSlot')
				->link('/end', 'TimeSlot')
				->cols(['/doctor', '/weekday/day', '/start/time' => 'start_time', '/end/time' => 'end_time', 'active'])
				->get();
	foreach ($timetable as $value)
	{
		$value->start_time = date('g:i A', strtotime($value->start_time));
		$value->end_time = date('g:i A', strtotime($value->end_time));
		$value->active = $value->active ? true : false;
	}
	if($core->db->success())
	{
		$out['success'] = true;
		foreach ($employees as $employee)
		{
			foreach ($timetable as $time)
			{
				if($time->doctor == $employee->doctor)
					$temp[] = $time;
			}
			if(!empty($temp))
			{
				$employee->availability = $temp;
				unset($temp);
			}
		}
		$out['items'] = $employees;
	}
}
if($user->user_type == 2)
{
	$doctor = new Doctor($core, ['user' => $user->id]);
	$doctorAvailability = $doctor->getAvailability();
	$time = Factory\TimeSlot::compose($core)
			->cols(['/id' => 'value', '/time' => 'text'])
			->get();

	if($core->db->success())
	{
		foreach ($time as $value)
		{
			$value->text = date('g:i A', strtotime($value->text));
		}
	}
	foreach ($doctorAvailability as $value)
	{
		$value->start_time = date('g:i A', strtotime($value->start_time));
		$value->end_time = date('g:i A', strtotime($value->end_time));
		$value->active = $value->active ? true : false;
		$value->day = $value->weekday;
		$value->dayDisplay = $value->getDay()->day;
		$value->elementID = 'weekday' . $value->id;
	}
	if($doctor->exists())
	{
		$out = [
			'success' => true,
			'firstname' => $doctor->firstname,
			'lastname' => $doctor->lastname,
			'description' => $doctor->description,
			'email' => $user->email,
			'title' => $doctor->title,
			'email_notification' => $doctor->email_notification,
			'sms_notification' => $doctor->sms_notification,
			'timetable' => $doctorAvailability,
			'timeOptions' => $time
		];
	}
}
echo json_encode($out);
?>