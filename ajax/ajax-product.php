<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;

$products = Factory\Product::compose($core)
				->cols(['/id', '/code', '/name', '/price', '/description', '/tax', '/image_url', '/active'])
				->get();

if($core->db->success())
{
	// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url = '//' . $_SERVER['SERVER_NAME'] . '/uploads/product/';
	foreach ($products as $value)
	{
		if($value->image_url)
			$value->imagePath = $url . $value->image_url;
		else
		{
			if($user->user_type == 3)
				$value->imagePath = '//' . $_SERVER['SERVER_NAME'] . '/images/default.png';
		}
	}
	$out['success'] = true;
	$out['products'] = $products;
}

echo json_encode($out);
?>