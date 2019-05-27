<?php
/*
	On every secure page
*/
if(!$core->loggedIn())
{
	session_start();
	$authToken = $_SESSION['token'] ?: $_COOKIE['token'];
	session_write_close();
	if($authToken)
	{
		$core->useToken($authToken);
	}
}
if($core->loggedIn())
{
	// Secure code/page here
}
else
{
	// Redirect to login here
}


/*
	On login
*/
if($authToken = $core->getToken($_POST['username'], $_POST['password']))
{
	session_start();
	$_SESSION['token'] = $authToken;
	setcookie('token', $authToken, time() + 48 * 3600, '/');
	session_write_close();

	$message = 'Logged in';
}
else
{
	$message = 'Username or password incorrect';
}
header("Content-type: application/json");
echo json_encode([
	'success' => $authToken !== false,
	'message' => $message
]);