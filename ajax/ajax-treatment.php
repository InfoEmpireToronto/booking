<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;

switch ($user->user_type)
{
	case 1:
			$treatments = Factory\Treatment::compose($core)
				->link('/duration', 'Duration')
				->cols(['/id', '/code', '/name', '/price', '/description', '/duration', '/tax', '/image_url', '/duration/duration' => 'duration_time', '/active'])
				->get();	
		break;
	case 3:
			$treatments = Factory\Treatment::compose($core)
				->link('/duration', 'Duration')
				->cols(['/id', '/code', '/name', '/price', '/description', '/duration', '/tax', '/image_url', '/duration/duration' => 'duration_time', '/active'])
				->filter('=', '/active', 1)
				->get();
		break;
}

if($core->db->success())
{
	// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url = '//' . $_SERVER['SERVER_NAME'] . '/uploads/treatment/';
	foreach ($treatments as $value)
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
	$out['treatments'] = $treatments;
}
echo json_encode($out);
?>