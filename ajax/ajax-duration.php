<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

$durations = Factory\Duration::compose($core)
				->cols(['/id', '/duration'])
				->filter('=', '/active', 1)
				->get();

if($core->db->success())
{
	$composed[] = ['value' => null, 'text' => 'Select duration (min)'];
	foreach ($durations as  $duration) {
		$composed[] = [
			'value' => $duration->id,
			'text' => $duration->duration
		];
	}
	$out['success'] = true;
	$out['durations'] = $composed;
}

echo json_encode($out);
?>