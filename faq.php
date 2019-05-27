<?php
$title = 'FAQ';
require('basicsite/init.php');
if(!$loggedIn)
	header('Location: login.php');
else
{
	require('header.php');
	switch ($user->user_type)
	{
		case 1:
			require('template/faq-admin.php');
			break;
		case 2:
			require('template/faq-doctor.php');
			break;
		case 3:
			require('template/faq-patient.php');
			break;
	}
}
require('footer.php');
?>
