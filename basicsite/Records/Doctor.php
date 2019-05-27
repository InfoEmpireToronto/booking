<?php
namespace Booking\Records;
Class Doctor extends AbstractObject
{
	const TABLE = 'doctors';
	const FAR_ID = 'doctor';

	function getAvailableTime($date)
	{
		$weekday = date('N', strtotime($date));
		$dayAvailablity = new DoctorAvailability($this->core, [ 'doctor' => $this->id, 'weekday' => $weekday]);
		$appointments = Factory\Appointment::get($this->core,  ['doctor' => $this->id, 'date'=> $date, 'status' => 1]);
		$start = $dayAvailablity->start;
		$end = $dayAvailablity->end;
		for($i = $start; $i < $end; $i++)
		{
			if($appointments)
			{
				foreach ($appointments as $appointment)
				{
					if($i >= $appointment->time && $i < $appointment->getEndTimeslot())
					{
						$occupied[] = $i;
					}
				}
			}
			$composed[] = $i;		
		}
		if($occupied)
		{
			foreach ($composed as $key => $value)
			{
				foreach ($occupied as $item)
				{
					if($value == $item)
						unset($composed[$key]);
				}
			}
		}
		return $composed;
	}

	function getAvailability()
	{
		return $this->getAssociatedObjectSet('DoctorAvailability');
	}

	function isAvailable($date, $time, $duration, $ignore = false) //timeID
	{
		$timeSlot = $this->core->db->getRow('time_slots', ['id' => $time]);
		$endSlot =  $time + ($duration / 15) - 1;
		$available = true;
		for($i = $time; $i <= $endSlot; $i++)
		{
			$timeslot = new TimeSlot($this->core, $i);
			$temp = $ignore ? $this->timeSlotStatus($date, $timeslot, null, true) : $this->timeSlotStatus($date, $timeslot);
			if($temp['statusDisplay'] != 'Available')
				$available = false;
		}
		return $available;
	}

	function timeSlotStatus($date, $timeslot, $patient = null, $ignore = false) //timeslot object, ignore past
	{
		$target = strtotime($date . ' '. $timeslot->time);
		$now = time();
		$past = ($target <= $now) ? true : false;
		$roundTime = ceil(time() / 900) * 900;
		$query = "SELECT `id` FROM `appointments` WHERE `doctor` = ? AND `date` = ? AND `status` != 0 AND (`time` < ? OR `time` = ?) ORDER BY `time` DESC LIMIT 1";
		$row = $this->core->db->queryRow($query, array($this->id, $date, $timeslot->id, $timeslot->id));

		if($row)
		{
			$appointment = new Appointment($this->core, $row->id);
			$endTime = $appointment->getEndTimeslot();
			if($timeslot->id == $appointment->time)
			{
				$payments = $appointment->getPayment();
				if($appointment->status == 1)
				{
					if($past)
					{
						if($payments)
							$status = 'Partially paid';
						else
							$status = 'Unpaid';
					}
					else
						$status = 'Booked';
				}
				else
				{
					$status = 'Paid';
				}
				if($patient)
				{
					if($appointment->patient != $patient)
						$status = 'Unavailable';
					else
						$status = $past ? 'Finished' : 'Booked';
				}
				$patientName = $appointment->getPatientName();
				$treatment = $appointment->getTreatment();
				$phone = $appointment->getPatientPhone();
				$tax = $appointment->getTax();
				$total = $appointment->getTotal();
				$products = $appointment->getProduct();
				$apptTime = date('g:i A', strtotime($appointment->getTime()));
				if($appointment->getDuration() == 15)
				{
					$class = ($appointment->status == 1) ? 'single' : 'complete';
					if($patient)
					{
						if($appointment->patient != $patient)
							$class = 'u-single';
						else
							$class = $past ? 'complete' : 'single' ;
					}
				}
				else
				{
					$class = ($appointment->status == 1) ? 'begin' : 'c-begin';
					if($patient)
					{
						if($appointment->patient != $patient)
							$class = 'u-begin';
						else
							$class = $past ? 'c-begin' : 'begin';
					}
				}
			}
			else if($timeslot->id < $endTime - 1)
			{
				$payments = $appointment->getPayment();
				if($appointment->status == 1)
				{
					if($past)
					{						
						if($payments)
							$status = 'Partially paid';
						else
							$status = 'Unpaid';
					}
					else
						$status = 'Booked';
				}
				else
				{
					$status = 'Paid';
				}
				if($patient)
				{
					if($appointment->patient != $patient)
						$status = 'Unavailable';
					else
						$status = $past ? 'Finished' : 'Booked';
				}
				$patientName = $appointment->getPatientName();
				$treatment = $appointment->getTreatment();
				$phone = $appointment->getPatientPhone();
				$tax = $appointment->getTax();
				$total = $appointment->getTotal();
				$products = $appointment->getProduct();
				$apptTime = date('g:i A', strtotime($appointment->getTime()));
				$class = ($appointment->status == 1) ? 'middle' : 'c-middle';
				if($patient)
				{
					if($appointment->patient != $patient)
						$class = 'u-middle';
					else
						$class = $past ? 'c-middle' : 'middle';
				}
			}
			else if($timeslot->id == $endTime -1)
			{
				$payments = $appointment->getPayment();
				if($appointment->status == 1)
				{
					if($past)
					{
						if($payments)
							$status = 'Partially paid';
						else
							$status = 'Unpaid';
					}
					else
						$status = 'Booked';
					
				}
				else
				{
					$status = 'Paid';
				}
				if($patient)
				{
					if($appointment->patient != $patient)
						$status = 'Unavailable';
					else
						$status = $past ? 'Finished' : 'Booked';
				}
				$patientName = $appointment->getPatientName();
				$treatment = $appointment->getTreatment();
				$phone = $appointment->getPatientPhone();
				$tax = $appointment->getTax();
				$total = $appointment->getTotal();
				$products = $appointment->getProduct();
				$apptTime = date('g:i A', strtotime($appointment->getTime()));
				$class = ($appointment->status == 1) ? 'end' : 'c-end';
				if($patient)
				{
					if($appointment->patient != $patient)
						$class = 'u-end';
					else
						$class = $past ? 'c-end' : 'end' ;
				}
			}
			else
			{
				$appointment = null;
				if($ignore)
				{
					$weekday = date('N', strtotime($date));
					$dayAvailablity = new DoctorAvailability($this->core, [ 'doctor' => $this->id, 'weekday' => $weekday]);
					if($dayAvailablity->active == 0)
					{
						$status = 'Unavailable';
						$class = 'unavailable';
					}
					else
					{
						if($timeslot->id >= $dayAvailablity->start && $timeslot->id < $dayAvailablity->end)
						{
							$class = 'available';
							$status = 'Available';
						}
						else
						{
							$status = 'Unavailable';
							$class = 'unavailable';
						}
					}
				}
				else
				{
					if($target < $roundTime)
					{
						$status = 'Unavailable';
						$class = 'unavailable';
					}
					else
					{
						$status = 'Available';
						$weekday = date('N', strtotime($date));
						$dayAvailablity = new DoctorAvailability($this->core, [ 'doctor' => $this->id, 'weekday' => $weekday]);
						if($dayAvailablity->active == 0)
						{
							$status = 'Unavailable';
							$class = 'unavailable';
						}
						else
						{
							if($timeslot->id >= $dayAvailablity->start && $timeslot->id < $dayAvailablity->end)
							{
								$class = 'available';
								$status = 'Available';
							}
							else
							{
								$status = 'Unavailable';
								$class = 'unavailable';
							}
						}
					}
				}
			}
			if($patient)
			{
				if($appointment->patient == $patient)
					$status = $past ? 'Finished' : 'Booked';
			}
		}
		else
		{
			if($ignore)
			{
				$weekday = date('N', strtotime($date));
				$dayAvailablity = new DoctorAvailability($this->core, [ 'doctor' => $this->id, 'weekday' => $weekday]);
				if($dayAvailablity->active == 0)
				{
					$status = 'Unavailable';
					$class = 'unavailable';
				}
				else
				{
					if($timeslot->id >= $dayAvailablity->start && $timeslot->id < $dayAvailablity->end)
					{
						$class = 'available';
						$status = 'Available';
					}
					else
					{
						$status = 'Unavailable';
						$class = 'unavailable';
					}
				}
			}
			else
			{
				if($target < $roundTime)
				{
					$status = 'Unavailable';
					$class = 'unavailable';
				}
				else
				{
					$weekday = date('N', strtotime($date));
					$dayAvailablity = new DoctorAvailability($this->core, [ 'doctor' => $this->id, 'weekday' => $weekday]);
					if($dayAvailablity->active == 0)
					{
						$status = 'Unavailable';
						$class = 'unavailable';
					}
					else
					{
						if($timeslot->id >= $dayAvailablity->start && $timeslot->id < $dayAvailablity->end)
						{
							$class = 'available';
							$status = 'Available';
						}
						else
						{
							$status = 'Unavailable';
							$class = 'unavailable';
						}
					}
				}
			}
		}
		if($treatment)
		{
			foreach ($treatment as $value)
			{
				$temp[] = [
					'id' => $value->id,
					'name' => $value->name,
					'price' => $value->price,
					'duration' => $value->getDuration()->duration
				];
			}
		}
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
		if(!$patient)
		{
			$result = [
				'appointment' => $appointment->id ?: null,
				'date' => $date,
				'timeDisplay' => $apptTime ?: null,
				'time' => $appointment->time ?: null,
				'doctor' => $this->id,
				'doctorName' => $this->getFullname(),
				'patientName' => $patientName ?: null,
				'note' => $appointment->note ?: null,
				'treatment' => $temp ?: null,
				'past' => $past,
				'tax' => $tax ?: null,
				'total' => $total ?: null,
				'payment' => $temp2 ?: null,
				'class' => $class,
				'elementID' => $ignore ? 'i' . $target : 't' . $target,
				'phone' => $phone ?: null,
				'statusDisplay' => $status,
				'product' => $temp3 ?: null,
				'status' => $appointment->status ?: null
			];
		}
		else
		{
			$result = [
				'status' => $status,
				'class' => $class,
				'elementID' => 't' . $target,
				'date' => $date,
				'doctor' => $this->getFullname(),
				'doctorID' => $this->id,
				'treatment' => $temp ?: null,
				'appointment' => $appointment->id ?: null,
				'apptstatus' => $appointment->status ?: null,
				'appointmentTime' => $appointment->time ?: null,
				'appointmentTimeDisplay' => $apptTime ?: null,
				'past' => $past,
				'tax' => $tax ?: null,
				'total' => $total ?: null,
				'product' => $temp3 ?: null
			];
		}
		return $result;
	}

	function getEmail()
	{
		$user = $this->getAssociatedObject('User');
		return $user->email;
	}

	function getFullname()
	{
		$title = $this->title ? $this->title . ' ' : '';
		return $title . $this->firstname . ' ' .$this->lastname;
	}
}