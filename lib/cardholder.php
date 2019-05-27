<?php
require('curlrequest.php');
function getCardholderInfo($cardholder)
{
	if($cardholder != null)
	{
		$url = 'https://www.wavetoget.com/embed/booking/cardholder.php';
		$post['cardholder'] = $cardholder;
		$result = curlrequest($url, $post);
	}
	if($result)
	{
		$result = json_decode($result);
		if($result->success)
		{
			$out['points'] = $result->points;
			$out['dollars'] = number_format($result->dollars, 2);
		}
	}
	return $result->success ? (object)$out : null;
}

function linkCardholder($email, $card, $key)
{
	$url = 'https://www.wavetoget.com/embed/booking/cardholder.php';
	$post['email'] = $email;
	$post['card'] = $card;
	$post['key'] = $key;
	$result = curlrequest($url, $post);
	if($result)
		$result = json_decode($result);
	return $result;
}
?>