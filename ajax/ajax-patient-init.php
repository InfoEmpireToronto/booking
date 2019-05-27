<?php
use Booking\Records\Factory;
require('../basicsite/init.php');
if($user->user_type != 1 && $user->user_type !=2)
{
	die();
}
$out['success'] = false;
if($countries = Factory\Country::get($core))
{
	$composed[] = ['value' => null, 'text' => 'Select conutry'];
	foreach ($countries as $value)
	{
		$composed[] = [
			'value' => $value->id,
			'text' => $value->name
		];
	}
	$out['success'] = true;
	$out['countries'] = $composed;
	unset($composed);
}

$provinces = Factory\Province::compose($core)
				->cols(['/id', '/province'])
				->get();

if($core->db->success())
{
	$composed[] = ['value' => null, 'text' => 'Select province'];
	foreach ($provinces as  $province) {
		$composed[] = [
			'value' => $province->id,
			'text' => $province->province
		];
	}
	$out['success'] = true;
	$out['provinces'] = $composed;
	unset($composed);
}

$treatments = Factory\Treatment::compose($core)
				->link('/duration', 'Duration')
				->cols(['/id', '/code', '/name', '/duration/duration', '/price'])
				->filter('=', '/active', 1)
				->get();
if($treatments)
{
	$composed[] = ['value' => [ 'id' => null, 'price' => null, 'duration' => null], 'text' => 'Select a treatment'];
	foreach ($treatments as $treatment)
		{
			$composed[] = [
				'value' => ['id' => $treatment->id, 'price' => $treatment->price, 'duration' => $treatment->duration],
				'text' => $treatment->name . ' (' . $treatment->duration . ' min)'
			];
		}
		$out['success'] = true;
		$out['treatments'] = $composed;
		unset($composed);
}

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

echo json_encode($out);
?>