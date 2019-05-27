<?php
require_once('../basicsite/init.php');
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Could not connect';
$payload = json_decode(file_get_contents('php://input'));
if($user->user_type == 1 && $_GET['dir'] && $payload->image)
{
	$relativeFile = basename($payload->image);
	$uploadDir = '../uploads/' . $_GET['dir'] . '/';
	$filePath = $uploadDir . $relativeFile;
	if(file_exists($filePath) && is_file($filePath))
	{
		if(unlink($filePath))
		{
			$out['success'] = true;
		}
	}
}
header("Content-type: application/json");
echo json_encode($out);
?>