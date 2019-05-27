<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}

$out['success'] = false;
if(isset($_GET['id']))
{
	$timetable = Factory\DoctorAvailability::compose($core)
				->link('/weekday', 'Weekday')
				->link('/start', 'TimeSlot')
				->link('/end', 'TimeSlot')
				->cols(['/id', 'weekday', '/weekday/day', '/start', '/end', 'active'])
				->filter('=', '/doctor', $_GET['id'])
				->get();

	if($core->db->success())
	{
		$out['success'] = true;
		foreach ($timetable as $value)
		{
			$value->active = $value->active ? true : false;
			$value->elementID = 'w' . $value->id;
		}
		$out['availability'] = $timetable;
	}	
}

echo json_encode($out);
?>