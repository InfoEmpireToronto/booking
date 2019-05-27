<?php
use Booking\Records\Factory;
$payload = json_decode(file_get_contents('php://input'));
if(isset($_GET['logout']))
{
	session_start();
	unset($_SESSION['booking']);
	setcookie('booking', '', time() + 3600, '/');
	session_write_close();
	$out['success'] = false;
}
else
{
	require('../basicsite/init.php');
	$out['success'] = $loggedIn;
	$out['message'] = (!$loggedIn) ? 'Email or password incorrect.' : '';
	if($loggedIn)
	{
		if(isset($_GET['currentPage']))
		{
			$menu = Factory\Menu::compose($core)
							->cols(['/name', '/url'])
							->filter('=', '/user_type', $user->user_type)
							->filter('=', '/active', 1)
							->get();
			if($core->db->success())
			{
				foreach ($menu as $value)
				{
					$composed[] = [
						'name' => $value->name,
						'url' => $value->url,
						'active' => ($value->name == $_GET['currentPage']) ? true : ''
					];
				}
				$out['items'] = $composed;
			}
		}
		$out['user'] = $user->id;
		$out['user_type'] = $user->user_type;
	}
}
echo json_encode($out);
?>