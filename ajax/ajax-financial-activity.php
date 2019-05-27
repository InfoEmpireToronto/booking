<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\TimeSlot;
if($user->user_type != 1)
{
	die();
}

$payload = json_decode(file_get_contents('php://input'));
$from = $payload->from;
$to = $payload->to;
$doctor = $payload->doctor;
if($from != null && $to != null)
{
	if($doctor == 0 || $doctor == null)
	{
		$financial = Factory\Payment::compose($core)
				->link('/appointment', 'Appointment')
				->link('/appointment/doctor', 'Doctor')
				->link('/appointment/patient', 'Patient')
				->cols(['/id', '/appointment', '/paid', '/appointment/date', '/appointment/time', 'appointment/doctor/firstname' => 'd_firstname', 'appointment/doctor/title' => 'd_title', 
						'appointment/doctor/lastname' => 'd_lastname', '/appointment/patient/firstname' => 'p_firstname', '/appointment/patient/lastname' => 'p_lastname'])
				->transform('/paid', 'sum')
				->filter('>=', '/appointment/date', $from)
				->filter('<=', '/appointment/date', $to)
				->group('/appointment')
				->get();
	}
	else
	{
		$financial = Factory\Payment::compose($core)
				->link('/appointment', 'Appointment')
				->link('/appointment/doctor', 'Doctor')
				->link('/appointment/patient', 'Patient')
				->cols(['/id', '/appointment', '/paid', '/appointment/date', '/appointment/time', 'appointment/doctor/firstname' => 'd_firstname', 'appointment/doctor/title' => 'd_title', 
						'appointment/doctor/lastname' => 'd_lastname', '/appointment/patient/firstname' => 'p_firstname', '/appointment/patient/lastname' => 'p_lastname'])
				->transform('/paid', 'sum')
				->filter('>=', '/appointment/date', $from)
				->filter('<=', '/appointment/date', $to)
				->filter('=', '/appointment/doctor/id', $doctor)
				->group('/appointment')
				->get();
	}
}
else
{
	$today = date('Y-m-d', time());
	if($doctor == 0 || $doctor == null)
	{
		$financial = Factory\Payment::compose($core)
				->link('/appointment', 'Appointment')
				->link('/appointment/doctor', 'Doctor')
				->link('/appointment/patient', 'Patient')
				->cols(['/id', '/appointment', '/paid', '/appointment/date', '/appointment/time', 'appointment/doctor/firstname' => 'd_firstname', 'appointment/doctor/title' => 'd_title', 
						'appointment/doctor/lastname' => 'd_lastname', '/appointment/patient/firstname' => 'p_firstname', '/appointment/patient/lastname' => 'p_lastname'])
				->transform('/paid', 'sum')
				->filter('=', '/appointment/date', $today)
				->group('/appointment')
				->get();
	}
	else
	{
		$financial = Factory\Payment::compose($core)
				->link('/appointment', 'Appointment')
				->link('/appointment/doctor', 'Doctor')
				->link('/appointment/patient', 'Patient')
				->cols(['/id', '/appointment', '/paid', '/appointment/date', '/appointment/time', 'appointment/doctor/firstname' => 'd_firstname', 'appointment/doctor/title' => 'd_title', 
						'appointment/doctor/lastname' => 'd_lastname', '/appointment/patient/firstname' => 'p_firstname', '/appointment/patient/lastname' => 'p_lastname'])
				->transform('/paid', 'sum')
				->filter('=', '/appointment/date', $today)
				->filter('=', '/appointment/doctor/id', $doctor)
				->group('/appointment')
				->get();
	}
	
}
$total = 0;
if($financial)
{
	foreach ($financial as $value)
	{
		$timeslot = new TimeSlot($core, $value->time);
		$title = $value->d_title ? $value->d_title . ' ' : '';
		$composed[] = [
			'date' => $value->date,
			'time' => date('g:i A', strtotime($timeslot->time)),
			'doctor' => $title . $value->d_firstname . ' ' . $value->d_lastname,
			'patient' => $value->p_firstname . ' ' .$value->p_lastname,
			'payment' => '$' . $value->paid
		];
		$total += $value->paid;
	}
	$out['financial'] = $composed;
}
$out['today'] = $today ?: null;
$out['total'] = number_format($total, 2);
echo json_encode($out);
?>