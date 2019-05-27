<?php
require('basicsite/init.php');
require('lib/mail.php');
use Booking\Records\Factory;
use Booking\Records\Setting;
use Booking\Records\Appointment;
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
$tomorrow = date('Y-m-d', time() + 86400 );
$appointments = Factory\Appointment::compose($core)
				->link('/patient', 'Patient')
				->link('/patient/user', 'User')
				->link('/time', 'TimeSlot')
				->link('/doctor', 'Doctor')
				->link('/doctor/user', 'User')
				->cols(['/id', '/patient/firstname' => 'patientFname', '/patient/lastname' => 'patientLname', '/date', '/patient',
					'/time/time', '/doctor/firstname' => 'doctorFname', '/doctor/lastname' => 'doctorLname', '/patient/phone' => 'patientPhone',
					'/patient/user/email' => 'patientEmail', '/doctor/user/email' => 'doctorEmail', '/patient/email_notification' => 'patientEmailNotification', 
					'/patient/sms_notification' => 'patientSMSNotification', '/doctor/email_notification' => 'doctorNotification'])
				->filter('=', '/date', $tomorrow)
				->filter('=', '/notification', 0)
				->get();

if($appointments)
{	
	foreach ($appointments as $appointment)
	{
		$day = date('l', strtotime($appointment->date));
		$time = date('g:i A', strtotime($appointment->time));
		$date = date('M j, Y', strtotime($appointment->date));
		$subject = 'Appointment reminder ' . $appointment->date;
		$newTreatments = $appointment->getTreatment();
		foreach ($newTreatments as $key => $newService)
		{
			$services .= $key == 0 ? $newService->name : ', ' . $newService->name;
		}
		$fields = ['[patientFname]', '[patientLname]', '[doctorFname]', '[doctorLname]', '[day]', '[date]', '[time]', '[store]', '[treatment]'];
		$text = [$appointment->patientFname, $appointment->patientLname, $appointment->doctorFname, $appointment->doctorLname, $day, $date, $time, $setting->store_name, $services];
		$msg = str_ireplace($fields, $text, $setting->reminder_body);
		$subject = str_ireplace($fields, $text, $setting->reminder_subject);
		if($appointment->patientEmailNotification)
		{
			sendMail(EMAIL_NOREPLY, $appointment->patientEmail, $subject, nl2br($msg));
		}
		if($appointment->patientSMSNotification)
		{
			$appointment->to = str_replace('-', '', $appointment->patientPhone);
			$appointment->identifier = "patient={$appointment->patient}&appointment={$appointment->id}";
			$appointment->body = $msg;
			
		}
		if($appointment->patientEmailNotification || $patientSMSNotification)
		{
			$temp = new Appointment($core, $appointment->id);
			$temp->notification = 1;
			$temp->save();
		}
		$response = sendSMS($appointments);
		sms_callback($response);
	}
}

?>