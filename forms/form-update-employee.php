<?php
$payload = json_decode(file_get_contents('php://input'));
require('../basicsite/init.php');
use Booking\Records\User;
use Booking\Records\Doctor;
use Booking\Records\DoctorAvailability;

$out['success'] = false;
$out['message'] = 'Failed';

if($user->user_type != 1 && $user->user_type != 2)
{
	$out['message'] = 'Permission denied';
}
else
{
	if(isset($_GET['Activate']))
	{
		$doctor = new User($core, $payload->user);
		$doctor->active = 1;
		$success = $doctor->save();
	}
	else if(isset($_GET['Deactivate']))
	{
		$doctor = new User($core, $payload->user);
		$doctor->active = 0;
		$success = $doctor->save();	
	}
	else if(isset($_GET['update']))
	{
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$d_user = new User($core, $user->id);
		}			
		else
		{
			$doctor = new Doctor($core, $payload->doctor);
			$d_user = new User($core, $payload->user);
		}
		$doctor->firstname = $payload->firstname;
		$doctor->lastname = $payload->lastname;
		$doctor->title = $payload->title ?: null;
		//$doctor->location = $payload->location ?: null;
		$doctor->email_notification = $payload->email_notification;
		$doctor->sms_notification = $payload->sms_notification;
		$doctor->description = $payload->description ?: null;
		$success = $doctor->save();
		
		$d_user->email = $payload->email;
		$d_user->displayname = $payload->firstname . ' ' . $payload->lastname;
		$success1 = $d_user->save();

		$success = ($success && $success1);
	}
	else if(isset($_GET['adduser']))
	{
		$new = new User($core);
		if($new->emailAvailable($payload->email))
		{
			$new->user_type = 2;
			$new->email = $payload->email;
			$new->password = $payload->password;
			$new->displayname = $payload->firstname . ' ' . $payload->lastname;
			$success1 = $new->save();
			if($success1)
			{
				$doctor = new Doctor($core);
				$doctor->user = $new->id;
				$doctor->firstname = $payload->firstname;
				$doctor->lastname = $payload->lastname;
				$doctor->title = $payload->title;
				// if($payload->location)
				// 	$doctor->location = $payload->location;
				$doctor->description = $payload->description ?: null;
				$doctor->email_notification = $payload->email_notification;
				$doctor->sms_notification = $payload->sms_notification;
				$success2 = $doctor->save();

				foreach ($payload->availability as $value)
				{
					$doctorTimetable = new DoctorAvailability($core);
					$doctorTimetable->doctor = $doctor->id;
					$doctorTimetable->weekday = $value->id;
					$doctorTimetable->start = $value->start;
					$doctorTimetable->end = $value->end;
					$doctorTimetable->active = $value->availability ? 1 : 0;
					$success3 = $doctorTimetable->save();
				}
				
			}
			else
			{
				$out['message'] = 'Could not connect';
			}
		}
		else
		{
			$out['message'] = 'Username has been used, try another.';
		}
		$success2 = ($success1 && $success2 && $success3);
	}
	else if(isset($_GET['timetable']))
	{
		foreach ($payload->availability as $value)
		{
			$timetable = new DoctorAvailability($core, $value->id);
			$timetable->weekday = $value->weekday;
			$timetable->start = $value->start;
			$timetable->end = $value->end;
			$timetable->active = $value->active ? 1 : 0;
			$success = $timetable->save();
		}
	}

	if($success)
	{
		$out['success'] = true;
		$out['message'] = 'Updated';
	}

	if($success2)
	{
		$out['success'] = true;
		$out['message'] = 'Successful';
	}
}

echo json_encode($out);
?>