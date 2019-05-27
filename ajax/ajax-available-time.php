<?php
require('../basicsite/init.php');
use Booking\Records\TimeSlot;
use Booking\Records\Doctor;
use Booking\Records\DoctorAvailability;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$payload = json_decode(file_get_contents('php://input'));
$day = date('N', strtotime($payload->date));
$row = $core->db->getRow('week_days', ['day' => getDayText($day)]);
if($user->user_type == 2)
{
	$doctor = new Doctor($core, ['user' => $user->id]);
	$dayAvailability = new DoctorAvailability($core, ['weekday' => $row->id, 'doctor' => $doctor->id, 'active' => 1]);
}
else
	$dayAvailability = new DoctorAvailability($core, ['weekday' => $row->id, 'doctor' => $payload->doctor, 'active' => 1]);
if($dayAvailability->exists())
{
	if($user->user_type == 3 || $user->user_type == 1)
		$doctor = new Doctor($core, $payload->doctor);
	$result = array();
	for($i = $dayAvailability->start; $i <= $dayAvailability->end; $i++)
	{
		if($doctor->isAvailable($payload->date, $i, $payload->duration))
		{
			$time = new TimeSlot($core, $i);
			$result[] = [
				'id' => $time->id,
				'time' => date('g:i A', strtotime($time->time)),
				'selected' => false
			];
		}
	}
	$out['success'] = count($result) > 0 ? true : false;
	$out['message'] = count($result) > 0 ? '' : 'No avaiable time, try another date';
}
else
	$out['message'] = 'No avaiable time, try another date';

$out['timeOptions'] = $result;
echo json_encode($out);
function getDayText($day)
{
	switch ($day) {
		case 1:
			$result = 'MON';
			break;
		case 2:
			$result = 'TUE';
			break;
		case 3:
			$result = 'WED';
			break;
		case 4:
			$result = 'THU';
			break;
		case 5:
			$result = 'FRI';
			break;
		case 6:
			$result = 'SAT';
			break;
		case 7:
			$result = 'SUN';
			break;
	}
	return $result;
}
?>