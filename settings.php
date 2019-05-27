<?php
$title = 'Setting';
require('basicsite/init.php');
if(!$loggedIn)
	header('Location: login.php');
else
{
	require('header.php');
	switch ($user->user_type)
	{
		case 1:
			require('template/setting-admin.php');
			break;
		case 2:
			require('template/setting-doctor.php');
			break;
		case 3:
			require('template/setting-patient.php');
			break;
	}
}
require('footer.php');
?>
