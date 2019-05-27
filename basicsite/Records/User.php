<?php
namespace Booking\Records;
use \Booking\Utilities\Crypt;
Class User extends AbstractObject
{
	const TABLE = 'users';
	const FAR_ID = 'user';

	public function __get($key)
	{
		if($key !== 'password')
		{
			return $this->data[$key];
		}
	}
	public function __set($key, $value)
	{
		if($key === 'password')
		{
			$value = Crypt::hash($value);
		}
		$this->data[$key] = $value;
	}
	public function read()
	{
		$output = $this->data;
		unset($output['password']);
		return (object)$output;
	}
	public function isPassword($password)
	{
		return Crypt::compare($this->data['password'], $password);
	}
	public function emailAvailable($email)
	{
		if($userRow = $this->core->db->getRow('users', ['email' => $email]))
			return false;
		else
			return true;
	}

}