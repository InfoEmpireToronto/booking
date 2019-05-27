<?php
$title = 'Appointments';
require('basicsite/init.php');
if(!$loggedIn)
	header('Location: login.php');
else
{
	require('header.php');
	switch ($user->user_type)
	{
		case 1:
			require('template/appointment-admin.php');
			break;
		case 2:
			require('template/appointment-doctor.php');
			break;
		case 3:
			require('template/appointment-patient.php');
			break;
	}
}
require('footer.php');
?>
