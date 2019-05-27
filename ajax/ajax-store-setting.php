<?php
require('../basicsite/init.php');
use Booking\Records\Setting;
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}
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
$provinceOption = Factory\Province::compose($core)
					->cols(['/id' => 'value', '/province' => 'text'])
					->get();
$countryOption = Factory\Country::compose($core)
					->cols(['/id' => 'value', '/name' => 'text'])
					->get();
$time = Factory\TimeSlot::compose($core)
			->cols(['/id' => 'value', '/time' => 'text'])
			->get();
if($time)
{
	foreach ($time as $value)
	{
		$value->text = date('g:i A', strtotime($value->text));
	}
}
$out = [
	'success' => true,
	'storename' => $setting->store_name,
	'apikey' => $setting->api_key,
	'address' => $setting->address,
	'city' => $setting->city,
	'province' => $setting->province,
	'country' => $setting->country,
	'postalcode' => $setting->postalcode,
	'email' => $setting->email,
	'phone' => $setting->phone,
	'provinceOptions' => $provinceOption,
	'countryOptions' => $countryOption,
	'time' => $time,
	'start' => $setting->start_time,
	'end' => $setting->end_time,
	'notification' => $setting->receive_notification,
	'registration_subject' => $setting->registration_subject,
	'registration_body' => $setting->registration_body,
	'confirmation_subject' => $setting->confirmation_subject,
	'confirmation_body' => $setting->confirmation_body,
	'cancelation_subject' => $setting->cancelation_subject,
	'cancelation_body' => $setting->cancelation_body,
	'reminder_subject' => $setting->reminder_subject,
	'reminder_body' => $setting->reminder_body,
	'adjustment_subject' => $setting->adjustment_subject,
	'adjustment_body' => $setting->adjustment_body,
	'registration_notification' => $setting->registration_notification,
	'confirmation_notification' => $setting->confirmation_notification,
	'adjustment_notification' => $setting->adjustment_notification,
	'cancelation_notification' => $setting->cancelation_notification,
	'birthday_notification' => $setting->birthday_notification,
	'reminder_notification' => $setting->reminder_notification,
	'birthday_subject' => $setting->birthday_subject,
	'birthday_body' => $setting->birthday_body
];
echo json_encode($out);
?>