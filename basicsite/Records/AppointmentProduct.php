<?php
namespace Booking\Records;
Class AppointmentProduct extends AbstractObject
{
	const TABLE = 'appointment_products';
	// const FAR_ID = 'doctor';

	function getProduct()
	{
		return $this->getAssociatedObject('Product');
	}
	
}