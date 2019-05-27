<?php
require_once('../../../basicsite/init.php');
// $client = $_REQUEST['client'];
// if($userinfo->client_id != $client && $userinfo->user_type !== 'Staff')
// {
// 	die();
// }
$out = [
	'success' => false
];
$relativeFile = basename($_POST['image']);
$uploadDir = '/home/developer/domains/booking.infoempire.us/editor/lib/uploads/';
$clientDir = $uploadDir . $client . '/';
$filePath = $clientDir . $relativeFile;
if(file_exists($filePath) && is_file($filePath))
{
	if(unlink($filePath))
	{
		$out['success'] = true;
	}
}
header("Content-type: application/json");
echo json_encode($out);