<?php
require('../basicsite/init.php');
require('../lib/cardholder.php');
use \Booking\Records\Setting;
use Booking\Records\Patient;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Could not connect';
$setting = new Setting($core, ['key' => 'api_key']);
if($setting->value != null)
{
	$payload = json_decode(file_get_contents('php://input'));
	if(isset($_GET['getinfo']))
	{
		$info = getCardholderInfo($payload->cardholder);
		if($info)
		{
			$out['success'] = true;
			$out['info'] = $info;
			unset($out['message']);
		}
	}
	if(isset($_GET['link']))
	{
		$email = $payload->email ?: null;
		$card = $payload->card ?: null;
		$result = linkCardholder($email, $card, $setting->value);
		if($result->success)
		{
			if($user->user_type == 3)
				$patient = new Patient($core, ['user' => $user->id]);
			if($user->user_type == 1 || $user->user_type == 2)
				$patient = new Patient($core, $payload->patient);
			$patient->wavetoget = $result->cardholder;
			$out['success'] = $patient->save();
			if($out['success'])
			{
				$out['message'] = 'Successful';
				$out['dollars'] = $result->dollars;
				$out['points'] = $result->points;
				$out['cardholder'] = $result->cardholder;
			}
		}
		else if($result->message)
			$out['message'] = $result->message;
	}
	if(isset($_GET['unlink']))
	{
		if($user->user_type == 3)
			$patient = new Patient($core, ['user' => $user->id]);
		if($user->user_type == 1 || $user->user_type == 2)
			$patient = new Patient($core, $payload->patient);
		$patient->wavetoget = null;
		$out['success'] = $patient->save();
		if($out['success'])
			$out['message'] = 'Successful';
	}
}
echo json_encode($out);
?>