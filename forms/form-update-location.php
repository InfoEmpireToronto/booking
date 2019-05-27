<?php
$payload = json_decode(file_get_contents('php://input'));
require('../basicsite/init.php');
use Booking\Records\Location;
$out['success'] = false;
$out['message'] ='Failed';
if($user->user_type != 1)
{
	$out['message'] = 'Permission denied';
}
else
{
	if(isset($_GET['addlocation']))
	{
		$location = new Location($core);
		$location->name = $payload->name;
		$location->phone = $payload->phone ?: null;
		$location->address = $payload->address;
		$location->city = $payload->city;
		$location->province = $payload->province;
		$location->country = $payload->country;
		$location->postalcode = $payload->postalcode;
		$location->email = $payload->email ?: null;

		$success = $location->save();
	}
	else if(isset($_GET['update']))
	{
		$location = new Location($core, $payload->id);
		$location->name = $payload->name;
		$location->phone = $payload->phone ?: null;
		$location->address = $payload->address;
		$location->city = $payload->city;
		$location->province = $payload->province;
		$location->country = $payload->country;
		$location->postalcode = $payload->postalcode;
		$location->email = $payload->email ?: null;

		$success2 = $location->save();
	}
	else if(isset($_GET['Activate']))
	{
		$location = new Location($core, $payload->id);
		$location->active = 1;
		$success2 = $location->save();
	}
	else if(isset($_GET['Deactivate']))
	{
		$location = new Location($core, $payload->id);
		$location->active = 0;
		$success2 = $location->save();	
	}

	if($success)
	{
		$out['message'] = 'Successful';
		$out['success'] = true;
	}

	if($success2)
	{
		$out['success'] = true;
		$out['message'] = 'Updated';
	}
}

echo json_encode($out);
?>