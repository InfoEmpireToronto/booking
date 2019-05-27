<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

$treatments = Factory\Treatment::compose($core)
				->link('/duration', 'Duration')
				->cols(['/id', '/code', '/name', '/duration/duration', '/price'])
				->filter('=', '/active', 1)
				->get();
if($treatments)
{
	$composed[] = ['value' => [ 'id' => null, 'price' => null, 'duration' => null], 'text' => 'Select a treatment'];
	foreach ($treatments as $treatment)
		{
			$composed[] = [
				'value' => ['id' => $treatment->id, 'price' => $treatment->price, 'duration' => $treatment->duration],
				'text' => $treatment->name . ' (' . $treatment->duration . ' min)'
			];
		}
		$out['success'] = true;
		$out['treatments'] = $composed;
}

echo json_encode($out);
?>