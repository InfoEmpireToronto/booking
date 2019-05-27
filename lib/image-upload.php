<?php
require('../basicsite/init.php');
use Booking\Utilities\Crypt;
if(!$loggedIn)
{
	die();
}
$out['success'] = false;
$out['message'] = 'Failed to upload';
if($user->user_type == 1 && $_GET['dir'])
{
	if ($_FILES['file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name']))
	{
		$validFormats = [
			'jpg' => 'image/jpeg',
			'png' => 'image/png'
		];

		$fileInfo = new finfo(FILEINFO_MIME_TYPE);
		if($extension = array_search($fileInfo->file($_FILES['file']['tmp_name']), $validFormats, true))
		{
			$uploadDir = '../uploads/' . $_GET['dir'] . '/';
			do
			{
				$randomFilename =  Crypt::salt(10, Crypt::SALT_FILENAME);
				$fullFileName = $randomFilename . '.' . $extension;
				$relativePath = $newDir . $fullFileName;
				$fullPath = $uploadDir . $relativePath;
			}
			while(file_exists($fullPath));

			if(move_uploaded_file($_FILES['file']['tmp_name'], $fullPath))
			{
				$out = [
							'success' => true,
							'message' => 'Successfully uploaded',
							'image_url' => $fullFileName
					   ];
			}
		}
		
	}
}

header("Content-type: application/json");
echo json_encode($out);
?>