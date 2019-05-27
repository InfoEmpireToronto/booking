<?php
use Booking\Records\Factory;
require('../basicsite/init.php');
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

if(isset($_GET['options']))
{
	$locations = Factory\Location::compose($core)
				->cols(['/id', '/name'])
				->get();

	if($core->db->success())
	{
		$composed[] = ['value' => null, 'text' => 'Select a location'];
		foreach ($locations as  $location)
		{
			$composed[] = [
				'value' => $location->id,
				'text' => $location->name
			];
		}
		$out['success'] = true;
		$out['locations'] = $composed;
	}
}
else
{
	$locations = Factory\Location::compose($core)
				->link('/province', 'Province')
				->link('/country', 'Country')
				->cols(['/id', '/name', '/phone', '/address', '/city', '/province/id' => 'province', '/country/id' => 'country',
						'/province/province_code' => 'province_name', '/country/name' => 'country_name', '/postalcode', '/email', '/active'])
				->get();

	if($core->db->success())
	{
		$out['success'] = true;
		$out['locations'] = $locations;
	}
}


echo json_encode($out);
?>