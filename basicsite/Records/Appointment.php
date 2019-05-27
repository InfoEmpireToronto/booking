<?php
namespace Booking\Records;
Class Appointment extends AbstractObject
{
	const TABLE = 'appointments';
	const FAR_ID = 'appointment';

	function getDuration()
	{
		$treatments = $this->getAssociatedObjectSet('AppointmentTreatment');
		$duration = 0;
		foreach ($treatments as $value)
		{
			$duration += $value->getTreatment()->getDuration()->duration;
		}
		return $duration;
	}

	function getEndTimeslot()
	{
		$duration = $this->getDuration();
		$timeslot = $this->getAssociatedObjectProperty('TimeSlot', 'time', 'time');
		$end = strtotime($timeslot) + ($duration * 60);
		$roundTime = ceil($end / 900) * 900;
		$endTime = date('H:i:s', $roundTime);
		$row = $this->core->db->getRow('time_slots', ['time' => $endTime]);
		return $row->id;
	}

	function getPatientName()
	{
		$patient = $this->getAssociatedObject('Patient');
		return $patient->firstname . ' ' . $patient->lastname;
	}

	function getPatientPhone()
	{
		return $this->getAssociatedObject('Patient')->phone;
	}

	function getTreatment()
	{
		$treatments = $this->getAppointmentTreatment();
		foreach ($treatments as $treatment)
		{
			$temp[] = $treatment->getTreatment();
		}
		return $temp;
	}

	function getProduct()
	{
		$products = $this->getAppointmentProduct();
		foreach ($products as $product)
		{
			$temp[] = $product->getProduct();
		}
		return $temp;
	}

	function getAppointmentProduct()
	{
		return $this->getAssociatedObjectSet('AppointmentProduct');
	}

	function getAppointmentTreatment()
	{
		return $this->getAssociatedObjectSet('AppointmentTreatment');
	}

	function getTime()
	{
		return $this->getAssociatedObject('TimeSlot')->time;
	}

	function getSubTotal()
	{
		$treatments = $this->getTreatment();
		$products = $this->getProduct();
		$total = 0;
		if($treatments)
		{
			foreach ($treatments as $treatment)
			{
				$total += $treatment->price;
			}
		}
		if($products)
		{
			foreach ($products as $product)
			{
				$total += $product->price;
			}
		}
		
		return $total;
	}

	function getTax() //treatments
	{
		$treatments = $this->getTreatment();
		$products = $this->getProduct();
		$tax = 0;
		foreach ($treatments as $treatment)
		{
			$tax += $treatment->getTax();
		}
		if($products)
		{
			foreach ($products as $product)
			{
				$total += $product->getTax;
			}
		}
		return number_format($tax, 2);
	}

	function getTotal()
	{
		return number_format($this->getSubTotal() + $this->getTax(), 2);
	}

	function getPayment()
	{
		return $this->getAssociatedObjectSet('Payment') ?: null;
	}

	function getTotalPaid()
	{
		$payments = $this->getPayment();
		$paid = 0;
		if($payments)
		{
			foreach ($payments as $payment)
			{
				$paid += $payment->paid;
			}
		}
		return number_format($paid, 2);
	}

	function getPatient()
	{
		return $this->getAssociatedObject('Patient');
	}

	function getDoctor()
	{
		return $this->getAssociatedObject('Doctor');
	}
}