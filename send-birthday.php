<?php
require('basicsite/init.php');
require('lib/mail.php');
use Booking\Records\Factory;

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
if($setting->birthday_notification)
{
	$month = date('m', time() + 86400 );
	$day = date('d', time() + 86400 );
	$date = "%-$month-$day";
	$list = Factory\Patient::compose($core)
		->link('/user', 'User')
		->cols(['/id', '/user/email', '/firstname', '/lastname'])
		->filter('LIKE', '/birthday', $date)
		->filter('=', '/user/active', 1)
		->get();
	if($list)
	{
		foreach ($list as $item)
		{
			$fields = ['[patientFname]', '[patientLname]', '[store]'];
			$text = [$item->firstname, $item->lastname, $setting->store_name];
			$msg = str_ireplace($fields, $text, $setting->birthday_body);
			$subject = str_ireplace($fields, $text, $setting->birthday_subject);
			sendMail(EMAIL_NOREPLY, $item->email, $subject, nl2br($msg));
		}
	}
}
?>