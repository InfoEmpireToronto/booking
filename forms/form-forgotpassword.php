<?php
use Booking\Records\User;
use Booking\Records\UserReset;
use Booking\Utilities\Crypt;
use Booking\Records\Factory;
$out['success'] = false;
$payload = json_decode(file_get_contents('php://input'));
require('../basicsite/init.php');
if($payload->forgot && $payload->email)
{
	$user = new User($core, ['email' => $payload->email]);
	if($user->exists())
	{
		$reset = new UserReset($core);
		$reset->user = $user->id;
		$resetToken = Crypt::salt(20, Crypt::SALT_TOKEN);
		$reset->token = $resetToken;
		$success = $reset->save();
		if($reset->exists())
		{
			$out['success'] = true;
			require('../lib/mail.php');
			$settings = Factory\Setting::compose($core)
							->cols(['/value'])
							->filter('=', '/key', 'store_name')
							->get();
			$store = $settings[0]->value;
			$from = EMAIL_NOREPLY;
			$title = $store . ' Password Reset Requested';
			ob_start();
?>
			<p>Hi, <?=$user->displayname;?></p>
			<p>
				Your <strong><?=$store;?></strong> password can be reset at the following link:<br>
				<a href="<?=BASE_URL;?>resetpassword.php?t=<?=$resetToken;?>">Reset Password</a>
			</p>
			<p>
				If that link does not appear correctly, please copy and paste this into your browser to set your new password:<br>
				<?=BASE_URL;?>resetpassword.php?t=<?=$resetToken;?>
			</p>
			<p>
				Best Regards,<br>
				<span>The <strong><?=$store;?></strong> Team</span>
			</p>
<?php
			$msg = ob_get_clean();
			$sendSuccess = sendMail($from, EMAIL_TO, $title, $msg);
		}
	}
	else
	{
		$out['message'] = 'Email does not exist.';
	}

	if($success)
		$out['message'] = 'Please check your email in the next few minutes for a link to set a new password';
	// else
	// 	$out['message'] = 'Your password could not be reset. Please <a href="https://www.zalea.us/company#contact">contact us</a> for assistance.';
}
echo json_encode($out);
?>