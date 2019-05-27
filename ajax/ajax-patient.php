<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\Patient;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
if($user->user_type == 1 || $user->user_type == 2)
{
	$patients = Factory\Patient::compose($core)
				->link('/marital_status', 'MaritalStatus')
				->link('/user', 'User')
				->link('/province', 'Province')
				->link('/country', 'Country')
				->cols(['/id' => 'id', '/firstname', '/lastname', '/birthday', '/gender', '/marital_status', '/phone',
						'/address', '/city', '/province', '/country', '/postalcode', '/marital_status/name' => 'marital_status_name',
						'/user/email', '/province/province_code', 'country/code' => 'country_code', '/wavetoget', '/email_notification', '/sms_notification'])
				->get();
	if($core->db->success())
	{
		$out['success'] = true;
		$out['patients'] = $patients;
	}
	require('../lib/store-info.php');
	$out['wavetoget'] = $wavetoget;
}
if($user->user_type == 3)
{
	$patient = new Patient($core, ['user' => $user->id]);
	if($patient->exists())
	{
		if(isset($_GET['email']))
		{
			$out['email'] = $user->email;
			$out['success'] = true;
		}
		else
			$out = [
				'success' => true,
				'firstname' => $patient->firstname,
				'lastname' => $patient->lastname,
				'phone' => $patient->phone,
				'gender' => $patient->gender,
				'marital' => $patient->marital_status,
				'address' => $patient->address,
				'city' => $patient->city,
				'province' => $patient->province,
				'country' => $patient->country,
				'postalcode' => $patient->postalcode,
				'email' => $user->email,
				'birthday' => $patient->birthday,
				'wavetoget' => $patient->wavetoget,
				'email_notification' => $patient->email_notification,
				'sms_notification' => $patient->sms_notification
			];

	}
}
echo json_encode($out);
?>