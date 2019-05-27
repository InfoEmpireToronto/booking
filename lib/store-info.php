<?php
// require('../basicsite/unauthenticated.php');
use \Booking\Records\Setting;
require('curlrequest.php');
$setting = new Setting($core, ['key' => 'api_key']);
if($setting->value != null)
{
	$url = 'https://www.wavetoget.com/embed/account/store.php';
	$post['publickey'] = $setting->value;
	$result = curlrequest($url, $post);
}
$wavetoget = false;
if($result)
{
	$result = json_decode($result);
	if($result->success)
	{
		$point_expand = $result->store->point_expand;
		$point_value = $result->store->point_value;
		$wavetoget = true;
	}
}
?>