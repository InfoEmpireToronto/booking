<?php
namespace Booking\Records;
Class DoctorAvailability extends AbstractObject
{
	const TABLE = 'doctor_availabilities';
	// const FAR_ID = 'doctor';

	function getDay()
	{
		return $this->getAssociatedObject('Weekday');
	}
}