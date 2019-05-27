<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\Product;
use Booking\Records\Treatment;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
if($user->user_type == 1 || $user->user_type == 2)
{
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
			unset($composed);
	}
}

$products = Factory\Product::compose($core)
			->cols(['/id', '/code', '/name', '/price'])
			->get();
if($products)
{
	$composed[] = ['value' => [ 'id' => null, 'price' => null, 'tax' => null], 'text' => 'Select a product'];
	foreach ($products as $value)
	{
		$product = new Product($core, $value->id);
		$composed[] = [
			'value' => [ 'id' => $value->id, 'price' => $value->price, 'tax' => $product->getTax()],
			'text' => $value->name . ' ($' . $value->price . ')'
		];
	}
	$out['success'] = true;
	$out['products'] = $composed;
	unset($composed);
}

$treatments = Factory\Treatment::compose($core)
				->link('/duration', 'Duration')
				->cols(['/id', '/code', '/name', '/duration/duration', '/price'])
				->filter('=', '/active', 1)
				->order('/name', 'ASC')
				->get();
if($treatments)
{
	$composed[] = ['value' => [ 'id' => null, 'price' => null, 'duration' => null], 'text' => 'Select a treatment'];
	foreach ($treatments as $treatment)
		{
			// $treatment->code = ($treatment->code == null) ? '' : $treatment->code .  ' - ';
			$composed[] = [
				'value' => ['id' => $treatment->id, 'price' => $treatment->price, 'duration' => $treatment->duration],
				'text' => $treatment->name . ' (' . $treatment->duration . ' min)'
			];
			$tr = new Treatment($core, $treatment->id);
			$treatmentTax[] = [
				'id' => $treatment->id,
				'tax' => $tr->getTax()
			];
			$treatmentOptions[] = [
				'id' => $treatment->id,
				'name' => $treatment->name,
				'price' => $treatment->price,
				'duration' => $treatment->duration,
				'tax' => $tr->getTax(),
				'selected' => false
			];
		}
		$out['success'] = true;
		$out['treatments'] = $composed;
		$out['treatmentTax'] = $treatmentTax;
		$out['treatmentOptions'] = $treatmentOptions;
		unset($composed);
}

$doctors = Factory\Doctor::compose($core)
				->link('/user', 'User')
				->cols(['/id', '/firstname', '/lastname', '/title'])
				->filter('=', '/user/active', 1)
				->get();
if($doctors)
{
	$all[] = [ 'value' => 0, 'text' => 'All'];
	$search[] = [ 'value' => null, 'text' => ''];
	foreach ($doctors as $doctor)
	{
		$search[] = $all[] = $composed[] = [
			'value' => $doctor->id,
			'text' => $doctor->getFullname()
		];
		$doctorOptions[] = [
			'id' => $doctor->id,
			'name' => $doctor->getFullname(),
			'selected' => false
		];
	}
	$out['success'] = true;
	$out['doctors'] = $composed;
	$out['financialDoctors'] = $all;
	$out['searchDoctors'] = $search;
	$out['doctor'] = $composed[0]['value']; //default
	$out['doctorOptions'] = $doctorOptions;
	unset($composed);
}

$out['today'] = date('Y-m-d', time());
echo json_encode($out);
?>