<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

$methods = Factory\PaymentMethod::get($core);

if($methods)
{
	$composed[] = ['value' => null, 'text' => 'Select a payment method'];
	foreach ($methods as $method)
		{
			$composed[] = [
				'value' => $method->id,
				'text' => $method->name
			];
		}
		$out['success'] = true;
		$out['methods'] = $composed;
}

echo json_encode($out);
?>