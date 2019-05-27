<?php
require('../basicsite/init.php');
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Failed';

$payload = json_decode(file_get_contents('php://input'));

if($user->isPassword($payload->password))
{
	$user->password = $payload->newpassword;
	$success = $user->save();
	if($success)
	{
		$out = [
			'success' => true,
			'message' => 'Updated'
		];
	}
}
else
{
	$out['message'] = 'Current password incorrect';
}
echo json_encode($out);
?>