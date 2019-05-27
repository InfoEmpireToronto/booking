<?php
use Booking\Utilities\Crypt;
require_once('../../../basicsite/init.php');
// $client = $_REQUEST['client'];
// if($userinfo->client_id != $client && $userinfo->user_type !== 'Staff')
// {
// 	die();
// }
$out = [
	'success' => false,
	'image' => ''
];

error_log($_FILES['image']['error']);
if ($_FILES['image']['error'] === UPLOAD_ERR_OK               //checks for errors
&& is_uploaded_file($_FILES['image']['tmp_name'])) //checks that file is uploaded
{
	$validFormats = [
		'jpg' => 'image/jpeg',
		'png' => 'image/png'
	];
	$fileInfo = new finfo(FILEINFO_MIME_TYPE);
	if($extension = array_search($fileInfo->file($_FILES['image']['tmp_name']), $validFormats, true))
	{
		$uploadDir = '/home/developer/domains/booking.infoempire.us/editor/lib/uploads/';
		$clientDir = $client . '/';
		do
		{
			$randomFilename = Crypt::salt(10, Crypt::SALT_FILENAME);
			$fullFileName = $randomFilename . '.' . $extension;
			$relativePath = $clientDir . $fullFileName;
			$fullPath = $uploadDir . $relativePath;
			if(!is_dir($uploadDir . $clientDir))
			{
				mkdir($uploadDir . $clientDir, 0755, true);
			}
		}
		while(file_exists($fullPath));
		if(move_uploaded_file($_FILES['image']['tmp_name'], 
			$fullPath))
		{
			$out['success'] = true;
			$out['image'] = $relativePath;
		}
	}
}
else
{
	switch($_FILES['image']['error'])
	{
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			$out['message'] = 'Image is too large';
			break;
		case UPLOAD_ERR_PARTIAL:
		case UPLOAD_ERR_NO_FILE:
			$out['message'] = 'Upload was not completed';
			break;
	}
}

header("Content-type: application/json");
echo json_encode($out);