<?php
$payload = json_decode(file_get_contents('php://input'));
require('../basicsite/init.php');
use Booking\Records\Treatment;
$out['success'] = false;
$out['message'] ='Failed';

if($user->user_type != 1)
{
	$out['message'] = 'Permission denied';
}
else
{
	if(isset($_GET['add']))
	{
		$treatment = new Treatment($core);
		$treatment->name = $payload->name;
		$treatment->code = $payload->code ?: null;
		$treatment->price = $payload->price;
		$treatment->description = $payload->description ?: null;
		$treatment->duration = $payload->duration ?: null;
		$treatment->tax = $payload->tax ? 1 : 0;
		$treatment->image_url = $payload->image_url ? basename($payload->image_url) : null;
		$success = $treatment->save();
	}
	else if(isset($_GET['Enable']))
	{
		$treatment = new Treatment($core, $payload->id);
		$treatment->active = 1;
		$success1 = $treatment->save();
	}
	else if(isset($_GET['Disable']))
	{
		$treatment = new Treatment($core, $payload->id);
		$treatment->active = 0;
		$success1 = $treatment->save();
	}
	else if(isset($_GET['update']))
	{
		$treatment = new Treatment($core, $payload->id);
		$treatment->name = $payload->name;
		$treatment->code = $payload->code ?: null;
		$treatment->price = $payload->price;
		$treatment->description = $payload->description ?: null;
		$treatment->duration = $payload->duration ?: null;
		$treatment->tax = $payload->tax;
		$treatment->image_url = $payload->image_url ? basename($payload->image_url) : null;
		$success1 = $treatment->save();
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