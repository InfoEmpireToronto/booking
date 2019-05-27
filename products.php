<?php
$title = 'Product';
require('basicsite/init.php');
require('header.php');
switch ($user->user_type)
{
	case 1:
		require('template/product-admin.php');
		break;
	case 2:
		# code...
		break;
	case 3:
		require('template/product-patient.php');
		break;
}
require('footer.php');
?>
