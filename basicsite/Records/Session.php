<?php
namespace Booking\Records;
Class Session extends AbstractObject
{
	const TABLE = 'sessions';
	const FAR_ID = 'session';
	function getUser()
	{
		return $this->getAssociatedObject('User');
	}
	function grantAccess($input)
	{
		return true;
	}
}