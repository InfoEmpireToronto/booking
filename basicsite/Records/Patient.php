<?php
namespace Booking\Records;
Class Patient extends AbstractObject
{
	const TABLE = 'patients';
	const FAR_ID = 'patient';

	function getEmail()
	{
		$user = $this->getAssociatedObject('User');
		return $user->email;
	}
	
}