<?php
namespace Booking\System;
use \Booking\Utilities\Database;
use \Booking\Utilities\Crypt;
use \Booking\Records\Session;
use \Booking\Records\User;

class Core
{
/*
	Components
*/
	public $db;

/*
	Properties
*/
	private $accessUser;

/*
------------------------------------------------------------------------------------------------------------------------------------
METHODS
*/

/*
	__construct
	Sets up required components
*/
public function __construct()
{
	$this->db = new Database(Config::db, Config::user, Config::pass, Config::host,
				[
					\PDO::MYSQL_ATTR_FOUND_ROWS => true,
					\PDO::ATTR_PERSISTENT => true,
					\PDO::ATTR_EMULATE_PREPARES => false,
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
				]);
}

/*
	useToken
	Attempts to use existing session token
*/
	public function useToken($token)
	{
		$session = new Session($this, ['token' => $token]);
		if($session->user)
		{
			$user = $session->getUser();
			if($user->id)
			{
				$this->accessUser = $user;
				return true;
			}
		}
		return false;
	}

/*
	getToken
	Creates a new session token from a login
*/
	public function getToken($username, $password)
	{
		if($userRow = $this->db->getRow('users', ['email' => $username]))
		{
			$user = new User($this, $userRow);
			if($user->exists() && $user->isPassword($password))
			{				
				$session = new Session($this);
				$session->user = $user->id;
				$session->token = Crypt::salt(30, Crypt::SALT_TOKEN);
				$session->save();
				if($session->id)
				{
					$this->accessUser = $user;
					return $session->token;
				}
			}
		}
		return false;
	}
	function getAccessUser()
	{
		return $this->accessUser;
	}
	function loggedIn()
	{
		return isset($this->accessUser);
	}
}