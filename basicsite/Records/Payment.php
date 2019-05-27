<?php
namespace Booking\Records;
Class Payment extends AbstractObject
{
	const TABLE = 'payments';
	const FAR_ID = 'payment';

	function getPaymentMethod()
	{
		return $this->getAssociatedObject('PaymentMethod')->name;
	}

}