<?php
use Booking\Records\User;
use Booking\Records\UserReset;
$out['success'] = false;
$out['message'] = 'Your password could not be reset.';
$payload = json_decode(file_get_contents('php://input'));
require('../basicsite/init.php');
if($payload->reset)
{
	if($payload->newpassword == $payload->confirmpassword)
	{
		$reset = new UserReset($core, ['token' => $payload->token]);
		if($reset->exists() && $reset->completed == 0)
		{
			$user = new User($core, $reset->user);
			$out['test'] = $user;
			if($user->exists() && $user->active == 1)
			{
				
				$user->password = $payload->newpassword;
				$success = $user->save();
				if($success)
				{
					$reset->completed = 1;
					$reset->save();
				}
			}
			if($success)
			{
				$out['success'] = true;
				$out['message'] = 'Password has been reset. You may now Log In with your new password, redirecting...';
			}
			// else
			// 	$out['message'] = 'Your password could not be reset. Please <a href="https://www.zalea.us/company#contact">contact us</a> for assistance.';
		} 
	}
	else
	{
		$out['message'] = 'Passwords do not match, please try again';
	}
}
echo json_encode($out);
?>