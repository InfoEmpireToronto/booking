<?php
require('../basicsite/init.php');
use Booking\Records\Product;
$out['success'] = false;
$out['message'] ='Failed';

if($user->user_type != 1)
{
	$out['message'] = 'Permission denied';
}
else
{
	$payload = json_decode(file_get_contents('php://input'));
	if(isset($_GET['addproduct']))
	{
		$product = new Product($core);
		$product->name = $payload->name;
		$product->code = $payload->code ?: null;
		$product->price = $payload->price;
		$product->description = $payload->description ?: null;
		$product->tax = $payload->tax ? 1 : 0;
		$product->image_url = $payload->image_url ? basename($payload->image_url) : null;
		$success = $product->save();
	}
	else if(isset($_GET['Enable']))
	{
		$product = new Product($core, $payload->id);
		$product->active = 1;
		$success1 = $product->save();
	}
	else if(isset($_GET['Disable']))
	{
		$product = new Product($core, $payload->id);
		$product->active = 0;
		$success1 = $product->save();
	}
	else if(isset($_GET['update']))
	{
		$product = new Product($core, $payload->id);
		$product->name = $payload->name;
		$product->code = $payload->code ?: null;
		$product->price = $payload->price;
		$product->description = $payload->description ?: null;
		$product->tax = $payload->tax;
		$product->image_url = $payload->image_url ? basename($payload->image_url) : null;
		$success1 = $product->save();
	}

	if($success)
	{
		$out['success'] = true;
		$out['message'] = 'Successful';
	}

	if($success1)
	{
		$out['success'] = true;
		$out['message'] = 'Updated';
	}
}
echo json_encode($out);
?>