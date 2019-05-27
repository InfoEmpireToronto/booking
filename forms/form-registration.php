<?php
require('../basicsite/init.php');
use Booking\Records\User;
use Booking\Records\Patient;
use Booking\Records\Factory;
require('../lib/mail.php');
$out['success'] = false;
$out['message'] = 'Failed';
$payload = json_decode(file_get_contents('php://input'));
if($payload->register)
{
	$user = new User($core);
	if($user->emailAvailable($payload->email))
	{
		$user->user_type = 3;
		$user->email = $payload->email;
		$user->password = $payload->password;
		$user->displayname = $payload->firstname . ' ' . $payload->lastname;
		$user->email = $payload->email;
		$success = $user->save();
		if($success)
		{
			$patient = new Patient($core);
			$patient->user = $user->id;
			$patient->firstname = sentenceCase($payload->firstname);
			$patient->lastname = sentenceCase($payload->lastname);
			$patient->birthday = $payload->birthday;
			$patient->phone = $payload->phone;
			$patient->gender = $payload->gender ?: null;
			$patient->marital_status = $payload->marital_status ?: null;
			$patient->address = $payload->address ?: null;
			$patient->city = $payload->city ?: null;
			$patient->province = $payload->province ?: null;
			$patient->country = $payload->country ?: null;
			$patient->postalcode = $payload->postalcode;
			$patient->wavetoget = $payload->wavetoget ?: null;
			$patient->email_notification = $payload->email_notification;
			$patient->sms_notification = $payload->sms_notification;
			$success2 = $patient->save();
		}
		else
		{
			$out['message'] = 'Could not connect';
		}
	}
	else
	{
		$out['message'] = 'Email has been used, try another.';
	}
	
	if($success && $success2)
	{
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
		$fields = ['[patientFname]', '[patientLname]', '[store]'];
		$text = [$patient->firstname, $patient->lastname, $setting->store_name];
		$msg = str_ireplace($fields, $text, $setting->registration_body);
		$subject = str_ireplace($fields, $text, $setting->registration_subject);
		sendMail(EMAIL_NOREPLY, $payload->email, $subject, nl2br($msg));
		// $user->to = str_replace('-', '', $payload->phone);
		// $user->identifier = "patient={$patient->id}";
		// $user->body = $msg;
		// if($payload->sms_notification)
		// {
		// 	$response = sendSMS($user);
		// 	sms_callback($response);
		// }
		$out['message'] = 'Successful, redirecting...';
		$out['success'] = true;
	}	
}
echo json_encode($out);

function sentenceCase($str)
{
	$str = strtolower($str);
	return ucwords($str);
}

?>