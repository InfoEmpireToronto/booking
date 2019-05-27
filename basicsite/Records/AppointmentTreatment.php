<?php
namespace Booking\Records;
Class AppointmentTreatment extends AbstractObject
{
	const TABLE = 'appointment_treatments';
	// const FAR_ID = 'doctor';

	function getTreatment()
	{
		return $this->getAssociatedObject('Treatment');
	}
	
}