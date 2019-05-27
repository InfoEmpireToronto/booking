<?php
$title = 'Treatment';
require('basicsite/init.php');
if(!$loggedIn)
	header('Location: login.php');
else
{
	require('header.php');
	switch ($user->user_type)
	{
		case 1:
			require('template/treatment-admin.php');
			break;
		case 2:
			# code...
			break;
		case 3:
			require('template/treatment-patient.php');
			break;
	}
}
require('footer.php');
?>