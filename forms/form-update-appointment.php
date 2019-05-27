<?php
require('../basicsite/init.php');
require('../lib/mail.php');
use Booking\Records\Appointment;
use Booking\Records\AppointmentTreatment;
use Booking\Records\Treatment;
use Booking\Records\Doctor;
use Booking\Records\TimeSlot;
use Booking\Records\Patient;
use Booking\Records\Factory;
$out['success'] = false;
$out['message'] ='Failed';
if(!$loggedIn)
{
	die();
}
else
{
	$payload = json_decode(file_get_contents('php://input'));
	$settings = Factory\Setting::get($core);
	if($settings)
	{
		$setting = (object)[];
		foreach ($settings as $value)
		{
			$key = $value->key;
			$setting->$key = $value->value;
		}
	}
	if(isset($_GET['add']))
	{
		$timeSlot = new TimeSlot($core, $payload->time);
		$valid = $payload->ignore ? true : validateBookingTime($payload->date, $timeSlot->time);
		if($valid)
		{
			$doctor = new Doctor($core, $payload->doctor);
			$available = $payload->ignore ? $doctor->isAvailable($payload->date, $payload->time, $payload->duration, true) : $doctor->isAvailable($payload->date, $payload->time, $payload->duration);
			if(!$available)
			{
				$out['message'] = 'Conflict time, adjust duration or time';
			}
			else
			{
				$appointment = new Appointment($core);
				if($user->user_type == 3)
				{
					$patient = new Patient($core, ['user' => $user->id]);
					$appointment->patient = $patient->id;
				}
				else
					$appointment->patient = $payload->patient;
				$appointment->doctor = $payload->doctor;
				$appointment->date = $payload->date;
				$appointment->time = $payload->time;
				$appointment->note = $payload->note ?: null;
				$success = $appointment->save();
				if($success)
				{
					$firstTreatment = new AppointmentTreatment($core);
					$firstTreatment->appointment = $appointment->id;
					$firstTreatment->treatment = $payload->firsttreatment->id;
					$firstTreatment->save();
					foreach ($payload->treatments as $value)
					{
						if($value->treatment->id)
						{
							$treatment = new AppointmentTreatment($core);
							$treatment->appointment = $appointment->id;
							$treatment->treatment = $value->treatment->id;
							$treatment->save();
						}
					}
					$appointment->price = $appointment->getSubTotal();
					$appointment->tax = $appointment->getTax();
					$appointment->total = $appointment->getTotal();
					if($out['success'] = $appointment->save())
					{
						$newTreatments = $appointment->getTreatment();
						foreach ($newTreatments as $key => $newService)
						{
							$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
						}
						$out['message'] = 'Successful';
						$patient = $appointment->getPatient();
						$date = date('F j, Y', strtotime($appointment->date));
						$day = date('l', strtotime($appointment->date));
						$time = date('g:i A', strtotime($appointment->getTime()));
						$doctorFname = $doctor->title ? $doctor->title . ' ' . $doctor->firstname : $doctor->firstname;
						$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[treatment]'];
						$text = [$patient->firstname, $patient->lastname, $doctorFname, $doctor->lastname, $day, $date, $time, $setting->store_name, $services];
						$msg = str_ireplace($fields, $text, $setting->confirmation_body);
						$subject = str_ireplace($fields, $text, $setting->confirmation_subject);
						sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
						if($doctor->email_notification)
						{
							$title = $doctor->title ? $doctor->title . ' ' : '';
							$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>You have a new appointment on <b>[day], [date] at [time]</b>.<br/>";
							$body .= 'Patient: [patientFname] [patientLname]<br/>';
							$body .= 'Treatment: [treatment]';
							$subject = 'New appointment';
							$body = str_ireplace($fields, $text, $body);
							sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
						}
					}
				}
			}
		}
		else
		{
			$out['message'] = 'Invalid time, cannot book a time in the past';
		}
	}
	if(isset($_GET['update']))
	{
		$doctor = new Doctor($core, $payload->doctor);
		$appointment = new Appointment($core, $payload->appointment);
		$oldDay = date('l', strtotime($appointment->date));
		$oldTime = date('g:i A', strtotime($appointment->getTime()));
		$oldDate = date('F j, Y', strtotime($appointment->date));
		$oldAppointmentTime = $oldDay . ', ' . $oldDate . ' at ' . $oldTime;
		$date = $payload->date;
		$newStart = $payload->time;
		$newEnd = $newStart + ($payload->duration / 15) - 1;
		$update = false;
		if($date == $appointment->date && $payload->doctor == $appointment->doctor && $newStart >= $appointment->time && $newStart < $appointment->getEndTimeslot())
		{
			if($newEnd < $appointment->getEndTimeslot())
			{
				$update = true;
			}
			else
			{
				$available = true;
				for($i = $appointment->getEndTimeslot(); $i <= $newEnd; $i++)
				{
					$timeslot = new TimeSlot($core, $i);
					$result = $doctor->timeSlotStatus($date, $timeslot);
					if($result['statusDisplay'] != 'Available')
						$available = false;
				}
				if($available)
				{
					$update = true;
				}
				else
				{
					$out['message'] = 'Conflict time, adjust duration or time';
				}
			}
			if($update)
			{
				$appointment->time = $newStart;
				$appointment->note = $payload->note ?: null;
				$appointment->save();
				$treatments = $appointment->getAppointmentTreatment();
				foreach ($treatments as $treatment)
				{
					$treatment->delete();
				}
				$firstTreatment = new AppointmentTreatment($core);
				$firstTreatment->appointment = $appointment->id;
				$firstTreatment->treatment = $payload->firsttreatment->id;
				$firstTreatment->save();
				if($payload->treatments)
				{
					foreach ($payload->treatments as $value)
					{
						if($value->treatment->id)
						{
							$newTreatment = new AppointmentTreatment($core);
							$newTreatment->appointment = $appointment->id;
							$newTreatment->treatment = $value->treatment->id;
							$newTreatment->save();
						}
					}
				}
				$appointment = new Appointment($core, $payload->appointment);
				$appointment->price = $appointment->getSubTotal();
				$appointment->tax = $appointment->getTax();
				$appointment->total = $appointment->getTotal();
				if($out['success'] = $appointment->save())
				{
					$newTreatments = $appointment->getTreatment();
					foreach ($newTreatments as $key => $newService)
					{
						$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
					}
					$out['message'] = 'Updated';
					$patient = $appointment->getPatient();
					$date = date('M j, Y', strtotime($appointment->date));
					$day = date('l', strtotime($appointment->date));
					$time = date('g:i A', strtotime($appointment->getTime()));
					$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[oldApointmentDate]', '[treatment]'];
					$text = [$patient->firstname, $patient->lastname, $doctor->firstname, $doctor->lastname, $day, $date, $time, $setting->store_name, $oldAppointmentTime, $services];
					$msg = str_ireplace($fields, $text, $setting->adjustment_body);
					$subject = str_ireplace($fields, $text, $setting->adjustment_subject);
					sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
					if($doctor->email_notification)
					{
						$title = $doctor->title ? $doctor->title . ' ' : '';
						$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>Your appointment on [oldApointmentDate] has been changed.<br/>";
						$body .= 'New appointment: <b>[day], [date] at [time]</b><br/>';
						$body .= 'Patient: [patientFname] [patientLname]<br/>';
						$body .= 'Treatment: [treatment]';
						$subject = 'Appointment change';
						$body = str_ireplace($fields, $text, $body);
						sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
					}
				}
			}
		}
		else if($date == $appointment->date && $payload->doctor == $appointment->doctor && $newEnd >= $appointment->time && $newEnd < $appointment->getEndTimeslot())
		{
			if($newStart > $appointment->time)
			{
				$update = true;
			}
			else
			{
				$available = true;
				for($i = $newStart; $i != $appointment->time; $i++)
				{
					$timeslot = new TimeSlot($core, $i);
					$result = $doctor->timeSlotStatus($date, $timeslot);
					if($result['statusDisplay'] != 'Available')
						$available = false;
				}
				$out['avai2'] = $available;
				if($available)
				{
					$update = true;
				}
				else
				{
					$out['message'] = 'Conflict time, adjust duration or time';
				}
			}
			if($update)
			{
				$appointment->time = $newStart;
				$appointment->save();
				$treatments = $appointment->getAppointmentTreatment();
				foreach ($treatments as $treatment)
				{
					$treatment->delete();
				}
				$firstTreatment = new AppointmentTreatment($core);
				$firstTreatment->appointment = $appointment->id;
				$firstTreatment->treatment = $payload->firsttreatment->id;
				$firstTreatment->save();
				if($payload->treatments)
				{
					foreach ($payload->treatments as $value)
					{
						if($value->treatment->id)
						{
							$newTreatment = new AppointmentTreatment($core);
							$newTreatment->appointment = $appointment->id;
							$newTreatment->treatment = $value->treatment->id;
							$newTreatment->save();
						}
					}
				}
				$appointment = new Appointment($core, $payload->appointment);
				$appointment->price = $appointment->getSubTotal();
				$appointment->tax = $appointment->getTax();
				$appointment->total = $appointment->getTotal();
				
				if($out['success'] = $appointment->save())
				{
					$out['message'] = 'Updated';
					$newTreatments = $appointment->getTreatment();
					foreach ($newTreatments as $key => $newService)
					{
						$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
					}
					$patient = $appointment->getPatient();
					$date = date('M j, Y', strtotime($appointment->date));
					$day = date('l', strtotime($appointment->date));
					$time = date('g:i A', strtotime($appointment->getTime()));
					$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[oldApointmentDate]', '[treatment]'];
					$text = [$patient->firstname, $patient->lastname, $doctor->firstname, $doctor->lastname, $day, $date, $time, $setting->store_name, $oldAppointmentTime, $services];
					$msg = str_ireplace($fields, $text, $setting->adjustment_body);
					$subject = str_ireplace($fields, $text, $setting->adjustment_subject);
					sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
					if($doctor->email_notification)
					{
						$title = $doctor->title ? $doctor->title . ' ' : '';
						$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>Your appointment on [oldApointmentDate] has been changed.<br/>";
						$body .= 'New appointment: <b>[day], [date] at [time]</b><br/>';
						$body .= 'Patient: [patientFname] [patientLname]<br/>';
						$body .= 'Treatment: [treatment]';
						$subject = 'Appointment change';
						$body = str_ireplace($fields, $text, $body);
						sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
					}
				}
			}
		}
		else
		{
			$available = true;
			for($i = $newStart; $i <= $newEnd; $i++)
			{
				$timeslot = new TimeSlot($core, $i);
				$result = $doctor->timeSlotStatus($date, $timeslot);
				if($result['statusDisplay'] != 'Available')
					$available = false;
			}
			if($available)
			{
				$oldDoctor = $appointment->getDoctor();
				$appointment->date = $date;
				$appointment->time = $newStart;
				$appointment->doctor = $payload->doctor;
				$appointment->save();
				$treatments = $appointment->getAppointmentTreatment();
				foreach ($treatments as $treatment)
				{
					$treatment->delete();
				}
				$firstTreatment = new AppointmentTreatment($core);
				$firstTreatment->appointment = $appointment->id;
				$firstTreatment->treatment = $payload->firsttreatment->id;
				$firstTreatment->save();
				if($payload->treatments)
				{
					foreach ($payload->treatments as $value)
					{
						if($value->treatment->id)
						{
							$newTreatment = new AppointmentTreatment($core);
							$newTreatment->appointment = $appointment->id;
							$newTreatment->treatment = $value->treatment->id;
							$newTreatment->save();
						}
					}
				}
				$appointment = new Appointment($core, $payload->appointment);
				$appointment->price = $appointment->getSubTotal();
				$appointment->tax = $appointment->getTax();
				$appointment->total = $appointment->getTotal();
				if($out['success'] = $appointment->save())
				{
					$out['message'] = 'Updated';
					$newTreatments = $appointment->getTreatment();
					foreach ($newTreatments as $key => $newService)
					{
						$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
					}
					$patient = $appointment->getPatient();
					$date = date('F j, Y', strtotime($appointment->date));
					$day = date('l', strtotime($appointment->date));
					$time = date('g:i A', strtotime($appointment->getTime()));
					$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[oldApointmentDate]', '[treatment]'];
					$text = [$patient->firstname, $patient->lastname, $doctor->firstname, $doctor->lastname, $day, $date, $time, $setting->store_name, $oldAppointmentTime, $services];
					$msg = str_ireplace($fields, $text, $setting->adjustment_body);
					$subject = str_ireplace($fields, $text, $setting->adjustment_subject);
					sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
					if($oldDoctor->id != $doctor->id)
					{
						if($oldDoctor->email_notification)
						{
							$title = $oldDoctor->title ? $oldDoctor->title . ' ' : '';
							$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>Your appointment on [oldApointmentDate] has been canceled.<br/>";
							$subject = 'Appointment cancelation';
							$body = str_ireplace($fields, $text, $body);
							sendMail(EMAIL_NOREPLY, $oldDoctor->getEmail(), $subject, $body);
						}
						if($doctor->email_notification)
						{
							$title = $doctor->title ? $doctor->title . ' ' : '';
							$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>You have a new appointment on <b>[day], [date] at [time]</b>.<br/>";
							$body .= 'Patient: [patientFname] [patientLname]<br/>';
							$body .= 'Treatment: [treatment]';
							$subject = 'New appointment';
							$body = str_ireplace($fields, $text, $body);
							sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
						}

					}
					else if($doctor->email_notification)
					{
						$title = $doctor->title ? $doctor->title . ' ' : '';
						$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>Your appointment on [oldApointmentDate] has been changed.<br/>";
						$body .= 'New appointment: <b>[day], [date] at [time]</b><br/>';
						$body .= 'Patient: [patientFname] [patientLname]<br/>';
						$body .= 'Treatment: [treatment]';
						$subject = 'Appointment change';
						$body = str_ireplace($fields, $text, $body);
						sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
					}
				}
			}
		}
	}
	if(isset($_GET['cancel']))
	{
		$appointment = new Appointment($core, $payload->appointment);
		$appointment->status = 0;
		if($out['success'] = $appointment->save())
		{
			$out['message'] = 'Appointment canceled';
			$patient = $appointment->getPatient();
			$doctor = $appointment->getDoctor();
			$date = date('F j, Y', strtotime($appointment->date));
			$day = date('l', strtotime($appointment->date));
			$time = date('g:i A', strtotime($appointment->getTime()));
			$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[treatment]'];
			$text = [$patient->firstname, $patient->lastname, $doctor->firstname, $doctor->lastname, $day, $date, $time, $setting->store_name, $services];
			$msg = str_ireplace($fields, $text, $setting->cancelation_body);
			$subject = str_ireplace($fields, $text, $setting->cancelation_subject);
			sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
			if($doctor->email_notification)
			{
				$title = $doctor->title ? $doctor->title . ' ' : '';
				$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>Your appointment on [day], [date] at [time] has been canceled.<br/>";
				$subject = 'Appointment cancelation';
				$body = str_ireplace($fields, $text, $body);
				sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
			}
		}
	}
	if(isset($_GET['create']))
	{
		$timeSlot = new TimeSlot($core, $payload->time);
		$valid = $payload->ignore ? true : validateBookingTime($payload->date, $timeSlot->time);
		if($valid)
		{
			if($user->user_type == 2)
				$doctor = new Doctor($core, ['user' => $user->id]);
			else
				$doctor = new Doctor($core, $payload->doctor);
			$available = $payload->ignore ? $doctor->isAvailable($payload->date, $payload->time, $payload->duration, true) : $doctor->isAvailable($payload->date, $payload->time, $payload->duration);
			if(!$available)
			{
				$out['message'] = 'Conflict time, adjust duration or time';
			}
			else
			{
				$appointment = new Appointment($core);
				if($user->user_type == 3)
				{
					$patient = new Patient($core, ['user' => $user->id]);
					$appointment->patient = $patient->id;
				}
				else
					$appointment->patient = $payload->patient;
				if($user->user_type == 2)
					$appointment->doctor = $doctor->id;
				else
					$appointment->doctor = $payload->doctor;
				$appointment->date = $payload->date;
				$appointment->time = $payload->time;
				$appointment->note = $payload->note ?: null;
				$success = $appointment->save();
				if($success)
				{
					foreach ($payload->treatments as $value)
					{
						$treatment = new AppointmentTreatment($core);
						$treatment->appointment = $appointment->id;
						$treatment->treatment = $value->id;
						$treatment->save();
					}
					$appointment->price = $appointment->getSubTotal();
					$appointment->tax = $appointment->getTax();
					$appointment->total = $appointment->getTotal();
					if($out['success'] = $appointment->save())
					{
						$newTreatments = $appointment->getTreatment();
						foreach ($newTreatments as $key => $newService)
						{
							$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
						}
						$out['message'] = 'Successful';
						$patient = $appointment->getPatient();
						$date = date('F j, Y', strtotime($appointment->date));
						$day = date('l', strtotime($appointment->date));
						$time = date('g:i A', strtotime($appointment->getTime()));
						$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[treatment]'];
						$text = [$patient->firstname, $patient->lastname, $doctor->firstname, $doctor->lastname, $day, $date, $time, $setting->store_name, $services];
						$msg = str_ireplace($fields, $text, $setting->confirmation_body);
						$subject = str_ireplace($fields, $text, $setting->confirmation_subject);
						sendMail(EMAIL_NOREPLY, $patient->getEmail(), $subject, nl2br($msg));
						if($doctor->email_notification)
						{
							$title = $doctor->title ? $doctor->title . ' ' : '';
							$body = "Hi $title [doctorFname] [doctorLname],<br/><br/>You have a new appointment on <b>[day], [date] at [time]</b>.<br/>";
							$body .= 'Patient: [patientFname] [patientLname]<br/>';
							$body .= 'Treatment: [treatment]';
							$subject = 'New appointment';
							$body = str_ireplace($fields, $text, $body);
							sendMail(EMAIL_NOREPLY, $doctor->getEmail(), $subject, $body);
						}
					}
				}
			}
		}
		else
		{
			$out['message'] = 'Invalid time, cannot book a time in the past';
		}
	}
}

echo json_encode($out);

function validateBookingTime($date, $time)
{
	$dateTime = strtotime($date. ' '. $time);
	$now = time();
	$nextTimeSlot = ceil($now / 900) * 900;
	return ($dateTime >= $nextTimeSlot);
}
?>