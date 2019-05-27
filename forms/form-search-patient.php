<?php
require('../basicsite/init.php');
use \Booking\Records\Factory;
use \Booking\Records\Patient;
$out['success'] = false;
$out['message'] ='Failed';
if($user->user_type != 1 && $user->user_type != 2)
{
	$out['message'] = 'Permission denied';
}
else
{
	$payload = json_decode(file_get_contents('php://input'));
	$email = $payload->email ?: '';
	$name = $payload->value ?: '';
	if(isset($_GET['phone']))
	{
		$patients = Factory\Patient::compose($core)
			->link('/user', 'User')
			->cols(['/id', '/firstname', '/lastname', '/birthday', 'user/email', '/phone'])
			->filter('LIKE', '/phone', $name)
			->get();
		if($patients)
			$out = [
				'success' => true,
				'patients' => $patients
			];
		else
			$out['message'] = 'No results found';
	}
	else
	{
		if(isset($_GET['email']))
			$email = $name;
		if($email)
		{
			$row = $core->db->getRow('users', ['email' => $email, 'user_type' => 3]);
			if($row)
			{
				$patient = $core->db->getRow('patients', ['user' => $row->id]);
				$out = [
					'success' => true,
					'patient' => $patient->id,
					'name' => $patient->firstname . ' ' . $patient->lastname
				];
				if(isset($_GET['email']))
				{
					$patient->email = $email;
					$patients[] = $patient;
					$out = [
						'success' => true,
						'patients' => $patients
					];
				}
			}
		}
		else if($name)
		{
			$name = explode(' ', $name);
			if(count($name) > 1 && $name[1])
			{
				$patientFirstname = Factory\Patient::compose($core)
							->link('/user', 'User')
							->cols(['/id', '/firstname', '/lastname', '/birthday', 'user/email', '/phone'])
							->filter('LIKE', '/firstname', '%'.$name[0].'%')
							->filter('LIKE', '/lastname', '%'.$name[1].'%')
							->get();
				// $patientLastname = Factory\Patient::compose($core)
				// 			->link('/user', 'User')
				// 			->cols(['/id', '/firstname', '/lastname', '/birthday', 'user/email'])
				// 			->filter('LIKE', '/lastname', '%'.$name[1].'%')
				// 			->get();
			}
			else
			{
				$patientFirstname = Factory\Patient::compose($core)
							->link('/user', 'User')
							->cols(['/id', '/firstname', '/lastname', '/birthday', 'user/email', '/phone'])
							->filter('LIKE', '/firstname', '%'.$name[0].'%')
							->get();
				$patientLastname = Factory\Patient::compose($core)
							->link('/user', 'User')
							->cols(['/id', '/firstname', '/lastname', '/birthday', 'user/email', '/phone'])
							->filter('LIKE', '/lastname', '%'.$name[0].'%')
							->get();
			}
			if($patientFirstname && $patientLastname)
			{
				foreach ($patientFirstname as $key1 => $first)
				{
					foreach ($patientLastname as $key2 => $second)
					{
						if($first->id == $second->id)
						{
							$keys[] = $key2;
						}
					}
				}
				if($keys)
				{
					foreach ($keys as $key)
					{
						unset($patientLastname[$key]);
					}
				}
				$patients = array_merge($patientFirstname, $patientLastname);
			}
			else if($patientFirstname)
				$patients = $patientFirstname;
			else if($patientLastname)
				$patients = $patientLastname;
			
			if($patients)
				$out = [
					'success' => true,
					'patients' => $patients
				];
			else
				$out['message'] = 'No results found';
		}
	}
	
}
echo json_encode($out);
?>