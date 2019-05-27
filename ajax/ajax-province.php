<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;

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
	$out['items'] = $composed;
}

echo json_encode($out);
?>