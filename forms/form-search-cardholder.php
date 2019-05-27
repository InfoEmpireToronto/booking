<?php
require('../basicsite/unauthenticated.php');
use \Booking\Records\Setting;
$setting = new Setting($core, ['key' => 'api_key']);
if($setting->value != null)
{
	
	require('../lib/curlrequest.php');
	$url = 'https://www.wavetoget.com/embed/booking/search-cardholder.php';
	$payload = json_decode(file_get_contents('php://input'));
	if($payload)
	{
		$post['email'] = $payload->email ?: ''; //'blahtest@example.com';
		$post['card'] = $payload->card ?: ''; //'04FDF8EA453682';
	}
	else
	{
		$post['email'] = $_POST['email'] ?: ''; //'blahtest@example.com';
		$post['card'] = $_POST['card'] ?: ''; //'04FDF8EA453682';
	}
	$post['api_key'] = $setting->value;
	$result = curlrequest($url, $post);
	echo $result;
}

?>