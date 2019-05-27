<?php
require('../basicsite/init.php');
use Booking\Records\Factory;
use Booking\Records\TimeSlot;
use Booking\Records\Doctor;
if($user->user_type != 1)
{
	die();
}
$payload = json_decode(file_get_contents('php://input'));
$out['success'] = false;
$out['message'] = 'No results found';
if($payload->doctor || $payload->patient || $payload->date)
{
	$out['format'] = 'table';
	if($payload->patient)
	{
		$out['displayDateCol'] = true;
		$appointments = Factory\Appointment::compose($core)
						->link('/patient', 'Patient')
						->link('/doctor', 'Doctor')
						->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', '/patient'])
						->filter('=', '/patient', $payload->patient);
		if($payload->doctor)
			$appointments->filter('=', '/doctor', $payload->doctor);
		if($payload->date)
		{
			$appointments->filter('=', '/date', $payload->date);
			$out['displayDateCol'] = false;
		}
		$appointments = $appointments->get();
	}
	else if($payload->doctor)
	{
		if($payload->date)
		{
			$out['format'] = 'graphic';
			$out['currentDoctor'] = $payload->doctor;
			$doctor = new Doctor($core, $payload->doctor);
			$dateUnix = strtotime($payload->date);
			$firstDay = $dateUnix - (3 * 86400);
			for ($w = 1; $w <= 7; $w++)
			{
				$week[] = $firstDay;
				$class = ($dateUnix == $firstDay) ? 'today' : null;
				$weekDate[] = [
					'day' => date('D', $firstDay),
					'date' => date('M d', $firstDay),
					'class' => $class
				];
				$firstDay += 86400;
			}
			$timeslots = Factory\TimeSlot::get($core);
			foreach ($timeslots as $timeslot)
			{
				foreach ($week as $value)
				{
					$date = date('Y-m-d', $value);
					$data[] = $doctor->timeSlotStatus($date, $timeslot, null, true);
				}
				$row[] = [
					'time' => date('g:i A', strtotime($timeslot->time)),
					'timeid' => $timeslot->id,
					'timeslot' => $data
				];
				unset($data);
			}
			$appointmentTable = [
				'week' => $weekDate,
				'table' => $row
			];
			$out['success'] = true;
			$out['message'] = '';
			$out['appointmentable'] = $appointmentTable;
		}
		else
		{
			$out['displayDateCol'] = true;
			$appointments = Factory\Appointment::compose($core)
						->link('/patient', 'Patient')
						->link('/doctor', 'Doctor')
						->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', '/patient'])
						->filter('=', '/doctor', $payload->doctor)
						->get();
		}
	}
	else
	{
		$out['displayDateCol'] = false;
		$appointments = Factory\Appointment::compose($core)
				->link('/patient', 'Patient')
				->link('/doctor', 'Doctor')
				->cols(['/id', '/doctor/firstname' => 'd_firstname', '/doctor/lastname' => 'd_lastname', '/date', '/time', '/status', '/doctor', '/patient'])
				->filter('=', '/date', $payload->date)
				->get();
	}
	if($out['format'] == 'table')
	{
		if($appointments)
		{
			$out['success'] = true;
			$out['message'] = count($appointments) . ' results found';
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
							'method' => $payment->method
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
					if($past)
					{
						if($payments)
							$appointment->status = 3; // partial paid
						else
							$appointment->status = 2; //unpaid
					}
				}
				else if($appointment->status == 2)
				{
					$appointment->status = 4; // paid
				}
				
				
				$composed[] = [
					'id' => $appointment->id,
					'date' => $appointment->date,
					'timeDisplay' => date('g:i A', strtotime($appointment->getTime())),
					'time' => $appointment->time,
					'doctor' => $appointment->doctor,
					'doctorName' => $appointment->d_firstname . ' ' . $appointment->d_lastname,
					'patientName' => $appointment->getPatientName(),
					'status' => $appointment->status,
					'treatment' => $temp,
					'past' => $past,
					'tax' => $appointment->getTax(),
					'total' => $appointment->getTotal(),
					'payment' => $temp2 ?: null,
					'product' => $temp3 ?: null
				];
				unset($temp);
				unset($temp2);
				unset($temp3);
			}
			$out['appointments'] = $composed;
		}
	}
}
else
{
	$out['message'] = 'Minimum 1 field is reuqired to search';
}
echo json_encode($out);
?>