<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\Treatment;
use Booking\Records\Doctor;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
if($user->user_type == 1 || $user->user_type == 2)
{
	if(isset($_GET['id']))
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('time', 'TimeSlot')
							->cols(['/id', '/doctor/firstname', '/doctor/lastname', '/date', '/time/time', '/note', '/doctor/title'])
							->filter('=', '/status', 1)
							->filter('=', '/patient', $_GET['id'])
							->get();

		if($core->db->success())
		{
			if($appointments)
			{
				foreach ($appointments as $appointment)
				{
					$treatments = Factory\AppointmentTreatment::compose($core)
									->link('/treatment', 'Treatment')
									->cols(['/treatment', '/treatment/name'])
									->filter('=', '/appointment', $appointment->id)
									->get();
					foreach ($treatments as $value)
					{					
						$treatment = new Treatment($core, $value->treatment);
						$duration = $treatment->getDuration()->duration;
						$temp[] = [
							'name' => $value->name . ' (' . $duration . 'min)'
						];
					}
					$title = $appointment->title ? $appointment->title . ' ' : '';
					$composed[] = [
						'date' => $appointment->date,
						'time' => $appointment->time,
						'note' => $appointment->note,
						'doctor' => $title . $appointment->firstname . ' ' . $appointment->lastname,
						'treatments' => $temp
					];
				}
				$out['appointments'] = $composed;
				$out['success'] = true;
			}
		}
	}
	if($_GET['status'] == 5) // All 
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('/patient', 'Patient')
							->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', 
								'/doctor', '/patient', '/note', '/doctor/title' => 'd_title', '/created']);
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$appointments->filter('=', '/doctor', $doctor->id);
		}
		$appointments = $appointments->get();
		if($appointments)
		{
			$now = time();	
			foreach ($appointments as $appointment)
			{
				$target =  strtotime($appointment->date . ' '. $appointment->getTime());
				$past = ($target <= $now) ? true : false;
				$treatments = $appointment->getTreatment();
				if($treatments)
				{
					foreach ($treatments as $value)
					{
						$temp[] = [
							'id' => $value->id,
							'name' => $value->name,
							'price' => $value->price,
							'duration' => $value->getDuration()->duration
						];
					}
				}
				$payments = $appointment->getPayment();
				if($payments)
				{
					foreach ($payments as $payment)
					{
						$temp2[] = [
							'paid' => $payment->paid,
							'method' => $payment->getPaymentMethod()
						];
					}
				}
				$products = $appointment->getProduct();
				if($products)
				{
					foreach ($products as $product)
					{
						$temp3[] = [
							'id' => $product->id,
							'price' => $product->price,
							'tax' => $product->getTax(),
						];
					}
				}
				if($appointment->status == 1)
				{
					$status = 'Booked';
					if($past)
					{
						if($payments)
							$status = 'Partially paid'; // partial paid
						else
							$status = 'Unpaid'; //unpaid
					}
				}
				else if($appointment->status == 2)
				{
					$status = 'Paid'; // paid
				}
				else
					$status = 'Canceled';
				
				$title = $appointment->d_title ? $appointment->d_title . ' ' : '';
				$composed[] = [
					'appointment' => $appointment->id,
					'date' => $appointment->date,
					'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
					'time' => $appointment->time,
					'doctor' => $appointment->doctor,
					'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
					'patientName' => $appointment->getPatientName(),
					'note' => $appointment->note,
					'treatment' => $temp,
					'past' => $past,
					'tax' => $appointment->getTax(),
					'total' => $appointment->getTotal(),
					'payment' => $temp2 ?: null,
					'status' => $appointment->status,
					'statusDisplay' => $status,
					'product' => $temp3 ?: null,
					'created' => date('Y-m-d', strtotime($appointment->created))
				];
				unset($temp);
				unset($temp2);
				unset($temp3);
			}
			$out['success'] = true;
			$out['appointments'] = $composed;
		}
	}
	if($_GET['status'] == 1) // Booked
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('/patient', 'Patient')
							->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', 
								'/patient', '/note', '/doctor/title' => 'd_title', '/created'])
							->filter('=', '/status', 1);
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$appointments->filter('=', '/doctor', $doctor->id);
		}
		$appointments = $appointments->get();
		if($appointments)
		{
			$now = time();	
			foreach ($appointments as $appointment)
			{
				$target =  strtotime($appointment->date . ' '. $appointment->getTime());
				$past = ($target <= $now) ? true : false;
				if(!$past)
				{
					$treatments = $appointment->getTreatment();
					if($treatments)
					{
						foreach ($treatments as $value)
						{
							$temp[] = [
								'id' => $value->id,
								'name' => $value->name,
								'price' => $value->price,
								'duration' => $value->getDuration()->duration
							];
						}
					}
					$products = $appointment->getProduct();
					if($products)
					{
						foreach ($products as $product)
						{
							$temp3[] = [
								'id' => $product->id,
								'price' => $product->price,
								'tax' => $product->getTax(),
							];
						}
					}
					$payments = $appointment->getPayment();
					if($payments)
					{
						foreach ($payments as $payment)
						{
							$temp2[] = [
								'paid' => $payment->paid,
								'method' => $payment->getPaymentMethod()
							];
						}
					}
					$title = $appointment->d_title ? $appointment->d_title . ' ' : '';
					$composed[] = [
						'appointment' => $appointment->id,
						'date' => $appointment->date,
						'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
						'time' => $appointment->time,
						'doctor' => $appointment->doctor,
						'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
						'patientName' => $appointment->getPatientName(),
						'note' => $appointment->note,
						'status' => $appointment->status,
						'treatment' => $temp,
						'past' => $past,
						'tax' => $appointment->getTax(),
						'total' => $appointment->getTotal(),
						'payment' => $temp2 ?: null,
						'statusDisplay' => 'Booked',
						'product' => $temp3 ?: null,
						'created' => date('Y-m-d', strtotime($appointment->created))
					];
				}				
				unset($temp);
				unset($temp2);
				unset($temp3);
			}
			$out['success'] = true;
			$out['appointments'] = $composed;
		}
	}
	if($_GET['status'] == 2) // Unpaid
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('/patient', 'Patient')
							->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', 
								'/patient', '/note', '/doctor/title' => 'd_title', '/created'])
							->filter('=', '/status', 1);
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$appointments->filter('=', '/doctor', $doctor->id);
		}
		$appointments = $appointments->get();
		if($appointments)
		{
			$now = time();	
			foreach ($appointments as $appointment)
			{
				$target =  strtotime($appointment->date . ' '. $appointment->getTime());
				$past = ($target <= $now) ? true : false;
				$treatments = $appointment->getTreatment();
				if($past)
				{
					$payments = $appointment->getPayment();
					if(!$payments)
					{
						if($treatments)
						{
							foreach ($treatments as $value)
							{
								$temp[] = [
									'id' => $value->id,
									'name' => $value->name,
									'price' => $value->price,
									'duration' => $value->getDuration()->duration
								];
							}
						}
						$products = $appointment->getProduct();
						if($products)
						{
							foreach ($products as $product)
							{
								$temp3[] = [
									'id' => $product->id,
									'price' => $product->price,
									'tax' => $product->getTax(),
								];
							}
						}
						$title = $appointment->d_title ? $appointment->d_title . ' ' : '';
						$composed[] = [
							'appointment' => $appointment->id,
							'date' => $appointment->date,
							'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
							'time' => $appointment->time,
							'doctor' => $appointment->doctor,
							'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
							'patientName' => $appointment->getPatientName(),
							'note' => $appointment->note,
							'status' => 2,
							'treatment' => $temp,
							'past' => $past,
							'tax' => $appointment->getTax(),
							'total' => $appointment->getTotal(),
							'payment' => null,
							'statusDisplay' => 'Unpaid',
							'product' => $temp3 ?: null,
							'created' => date('Y-m-d', strtotime($appointment->created))
						];
					}
				}				
				unset($temp);
				unset($temp3);
			}
			$out['success'] = true;
			$out['appointments'] = $composed;
		}
	}
	if($_GET['status'] == 3) // Partial paid
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('/patient', 'Patient')
							->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', 
								'/patient', '/note', '/doctor/title' => 'd_title', '/created'])
							->filter('=', '/status', 1);
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$appointments->filter('=', '/doctor', $doctor->id);
		}
		$appointments = $appointments->get();
		if($appointments)
		{
			$now = time();	
			foreach ($appointments as $appointment)
			{
				$target =  strtotime($appointment->date . ' '. $appointment->getTime());
				$past = ($target <= $now) ? true : false;
				$products = $appointment->getProduct();
				if($products)
				{
					foreach ($products as $product)
					{
						$temp3[] = [
							'id' => $product->id,
							'price' => $product->price,
							'tax' => $product->getTax(),
						];
					}
				}
				$treatments = $appointment->getTreatment();
				if($past)
				{
					$payments = $appointment->getPayment();
					if($payments)
					{
						if($treatments)
						{
							foreach ($treatments as $value)
							{
								$temp[] = [
									'id' => $value->id,
									'name' => $value->name,
									'price' => $value->price,
									'duration' => $value->getDuration()->duration
								];
							}
						}
						foreach ($payments as $payment)
						{
							$temp2[] = [
								'paid' => $payment->paid,
								'method' => $payment->getPaymentMethod()
							];
						}
						$title = $appointment->d_title ? $appointment->d_title . ' ' : '';	
						$composed[] = [
							'appointment' => $appointment->id,
							'date' => $appointment->date,
							'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
							'time' => $appointment->time,
							'doctor' => $appointment->doctor,
							'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
							'patientName' => $appointment->getPatientName(),
							'note' => $appointment->note,
							'status' => 3,
							'treatment' => $temp,
							'past' => $past,
							'tax' => $appointment->getTax(),
							'total' => $appointment->getTotal(),
							'payment' => $temp2,
							'statusDisplay' => 'Partially paid',
							'product' => $temp3 ?: null,
							'product' => $temp3 ?: null,
							'created' => date('Y-m-d', strtotime($appointment->created))
						];
					}
				}				
				unset($temp);
				unset($temp2);
				unset($temp3);
			}
			$out['success'] = true;
			$out['appointments'] = $composed;
		}
	}
	if($_GET['status'] == 4) // Paid
	{
		$appointments = Factory\Appointment::compose($core)
							->link('/doctor', 'Doctor')
							->link('/patient', 'Patient')
							->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', 
								'/patient', '/note', '/doctor/title' => 'd_title', '/created'])
							->filter('=', '/status', 2);
		if($user->user_type == 2)
		{
			$doctor = new Doctor($core, ['user' => $user->id]);
			$appointments->filter('=', '/doctor', $doctor->id);
		}
		$appointments = $appointments->get();
		if($appointments)
		{
			$now = time();	
			foreach ($appointments as $appointment)
			{
				$target =  strtotime($appointment->date . ' '. $appointment->getTime());
				$past = ($target <= $now) ? true : false;
				$treatments = $appointment->getTreatment();
				$payments = $appointment->getPayment();
				if($treatments)
				{
					foreach ($treatments as $value)
					{
						$temp[] = [
							'id' => $value->id,
							'name' => $value->name,
							'price' => $value->price,
							'duration' => $value->getDuration()->duration
						];
					}
				}
				$products = $appointment->getProduct();
				if($products)
				{
					foreach ($products as $product)
					{
						$temp3[] = [
							'id' => $product->id,
							'price' => $product->price,
							'tax' => $product->getTax(),
						];
					}
				}
				foreach ($payments as $payment)
				{
					$temp2[] = [
						'paid' => $payment->paid,
						'method' => $payment->getPaymentMethod()
					];
				}
				$title = $appointment->d_title ? $appointment->d_title . ' ' : '';
				$composed[] = [
					'appointment' => $appointment->id,
					'date' => $appointment->date,
					'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
					'time' => $appointment->time,
					'doctor' => $appointment->doctor,
					'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
					'patientName' => $appointment->getPatientName(),
					'note' => $appointment->note,
					'status' => 4,
					'treatment' => $temp,
					'past' => $past,
					'tax' => $appointment->getTax(),
					'total' => $appointment->getTotal(),
					'payment' => $temp2,
					'statusDisplay' => 'Paid',
					'product' => $temp3 ?: null,
					'created' => date('Y-m-d', strtotime($appointment->created)),
					'invoice' => BASE_URL . 'invoice.php?i=' . $appointment->id
				];
				unset($temp);
				unset($temp2);
				unset($temp3);
			}
			$out['success'] = true;
			$out['appointments'] = $composed;
		}
	}
	if($_GET['status'] == 6) // Financial activity
	{
	}
}
if($user->user_type == 3)
{
	$appointments = Factory\Appointment::compose($core)
					->link('/doctor', 'Doctor')
					->link('/patient', 'Patient')
					->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/doctor/title' => 'd_title', '/date', '/time', '/status', '/doctor', '/patient'])
					->filter('=', '/patient/user', $user->id)
					->filter('!=', '/status', 0)
					->get();
	if($appointments)
	{
		$now = time();	
		foreach ($appointments as $appointment)
		{
			$target =  strtotime($appointment->date . ' '. $appointment->getTime());
			$past = ($target <= $now) ? true : false;
			$treatments = $appointment->getTreatment();
			if($treatments)
			{
				foreach ($treatments as $value)
				{
					$temp[] = [
						'id' => $value->id,
						'name' => $value->name,
						'price' => $value->price,
						'duration' => $value->getDuration()->duration
					];
				}
			}
			if($appointment->status == 1)
			{
				if($past)
				{
					$appointment->status = 2; // complete
				}
			}
			$title = $appointment->d_title ? $appointment->d_title . ' ' : '';
			$composed[] = [
				'id' => $appointment->id,
				'date' => $appointment->date,
				'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
				'time' => $appointment->time,
				'doctor' => $appointment->doctor,
				'doctorName' => $title . $appointment->d_firstname . ' ' . $appointment->d_lastname,
				'patientName' => $appointment->getPatientName(),
				'status' => $appointment->status,
				'treatment' => $temp,
				'past' => $past,
				'tax' => $appointment->getTax(),
				'total' => $appointment->getTotal()
			];
			unset($temp);
		}
		$out['success'] = true;
		$out['appointments'] = $composed;
	}
}
echo json_encode($out);
?>