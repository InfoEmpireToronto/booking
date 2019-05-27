<?php
/*
	exports $core
*/
date_default_timezone_set('America/Toronto');
require(__DIR__ . '/autoload.php');
require(__DIR__ . '/config.php');
use Booking\System\Core;
use Booking\Records\User;
$core = new Core();

$loggedIn = false;

if($payload->login)
{	
	if($token = $core->getToken($payload->email, $payload->password))
	{		
		session_start();
		$_SESSION['booking'] = $token;
		session_write_close();
		$loggedIn = true;
	}	
}
else
{
	session_start();
	if($_SESSION['booking'])
	{
		$token = $_SESSION['booking'];
		$loggedIn = $core->useToken($token);
	}
	else if($_COOKIE['booking'])
	{
		$token = $_COOKIE['booking'];
		$loggedIn = $core->useToken($token);
		setcookie('booking', $token, time() + 3600, '/');
		$_SESSION['booking'] = $token;
	}
	session_write_close();
}

if($loggedIn)
{
	$user = $core->getAccessUser();
}
?>