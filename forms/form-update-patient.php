<?php
require('../basicsite/init.php');
use Booking\Records\Patient;
use Booking\Records\User;
use Booking\Records\Factory;
use Booking\Utilities\Crypt;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] ='Failed';
$payload = json_decode(file_get_contents('php://input'));
if($user->user_type == 1 || $user->user_type == 2)
{
	if(isset($_GET['add']))
	{
		$p_user = new User($core);
		if($p_user->emailAvailable($payload->email))
		{
			$p_user->user_type = 3;
			$p_user->email = $payload->email;
			$p_user->displayname = sentenceCase($payload->firstname) . ' ' . sentenceCase($payload->lastname);
			$password = Crypt::salt(15, Crypt::SALT_CODE);
			$p_user->password = $password;
			$success = $p_user->save();

			if($success)
			{
				$patient = new Patient($core);
				$patient->user = $p_user->id;
				$patient->firstname = sentenceCase($payload->firstname);
				$patient->lastname = sentenceCase($payload->lastname);
				$patient->birthday = $payload->birthday ?: null;
				$patient->gender = $payload->gender;
				$patient->marital_status = $payload->marital_status ?: null;
				$patient->phone = $payload->phone;
				$patient->address = $payload->address ?: null;
				$patient->city = $payload->city ?: null;
				$patient->province = $payload->province ?: null;
				$patient->country = $payload->country ?: null;
				$patient->postalcode = $payload->postalcode;
				$patient->wavetoget = $payload->wavetoget ?: null;
				$patient->email_notification = $payload->email_notification;
				$patient->sms_notification = $payload->sms_notification;
				$success = $patient->save();

				if($success)
				{
					require('../lib/mail.php');
					$settings = Factory\Setting::compose($core)
							->cols(['/value'])
							->filter('=', '/key', 'store_name')
							->get();
					$store = $settings[0]->value;
					$from = EMAIL_NOREPLY;
					$title = $store . ' online booking';
					$url = BASE_URL . 'login.php';
					$msg = 'Your password: ' . $password . '<br/><br/>';
					$msg.= "Login at <a href={$url} target=\"_blank\">{$store}</a> to book online.";
					$sendSuccess = sendMail($from, $payload->email, $title, $msg);
					// $sendSuccess = sendMail($from, $payload->email, $title, $msg);
				}
			}
		}
		else
		{
			$out['message'] = 'Email has been used, try another.';
		}
	}
	else if(isset($_GET['update']))
	{
		$patient = new Patient($core, $payload->id);
		$p_user = new User($core, $patient->user);
		if($payload->email != $p_user->email && !$p_user->emailAvailable($payload->email))
			$out['message'] = 'Email has been used, try another.';
		else
		{
			$p_user->email = $payload->email;
			$p_user->displayname = sentenceCase($payload->firstname) . ' ' . sentenceCase($payload->lastname);
			$success1 = $p_user->save();

			$patient->firstname = sentenceCase($payload->firstname);
			$patient->lastname = sentenceCase($payload->lastname);
			$patient->birthday = $payload->birthday ?: null;
			$patient->gender = $payload->gender;
			$patient->marital_status = $payload->marital_status;
			$patient->phone = $payload->phone;
			$patient->address = $payload->address ?: null;
			$patient->city = $payload->city ?: null;
			$patient->province = $payload->province ?: null;
			$patient->country = $payload->country ?: null;
			$patient->postalcode = $payload->postalcode ?: null;
			$patient->email_notification = $payload->email_notification;
			$patient->sms_notification = $payload->sms_notification;

			$success2 = $patient->save();
			$success1 = $success1 && $success2;
		}
		
	}
}
if($user->user_type == 3)
{
	$patient = new Patient($core, ['user' => $user->id]);
	if($payload->email != $user->email && !$user->emailAvailable($payload->email))
		$out['message'] = 'Email has been used, try another.';
	else
	{
		$user->email = $payload->email;
		$user->displayname = sentenceCase($payload->firstname) . ' ' . sentenceCase.($payload->lastname);
		$success1 = $user->save();
		$patient->firstname = sentenceCase($payload->firstname);
		$patient->lastname = sentenceCase($payload->lastname);
		$patient->birthday = $payload->birthday ?: null;
		$patient->gender = $payload->gender;
		$patient->marital_status = $payload->marital_status;
		$patient->phone = $payload->phone;
		$patient->address = $payload->address ?: null;
		$patient->city = $payload->city ?: null;
		$patient->province = $payload->province ?: null;
		$patient->country = $payload->country ?: null;
		$patient->postalcode = $payload->postalcode ?: null;
		$patient->email_notification = $payload->email_notification;
		$patient->sms_notification = $payload->sms_notification;
		$success2 = $patient->save();
		$success1 = $success1 && $success2;
	}
}
if($success)
{
	$out['success'] = true;
	$out['message'] = 'Successful';
}

if($success1)
{
	$out['success'] = true;
	$out['message'] = 'Updated';
}
echo json_encode($out);

function sentenceCase($str)
{
	$str = strtolower($str);
	return ucwords($str);
}
?>