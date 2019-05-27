<?php
require('../basicsite/init.php');
require('../lib/mail.php');
use \Booking\Records\Factory;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Could not connect';
$payload = json_decode(file_get_contents('php://input'));
if($payload->email && $payload->message)
{
	$settings = Factory\Setting::compose($core)
			->cols(['/value'])
			->filter('=', '/key', 'store_name')
			->get();
	$store = $settings[0]->value;
	$from = EMAIL_NOREPLY;
	$title = 'Contact form via ' . $store;
	$msg = 'Name: ' . $user->displayname . '<br/><br/>';
	$msg .= 'Email: ' . $payload->email . '<br/><br/>';
	$msg .= 'Message: ' . $payload->message . '<br/><br/>';
	$out['success'] = sendMail($from, EMAIL_TO, $title, $msg);
	if($out['success'])
		$out['message'] = "Successfully sent";
}
echo json_encode($out);
?>