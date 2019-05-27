<?php
$out['success'] = false;
if ($_FILES['file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name']))
{
	$out['test'] = 123;
	$validFormats = [
		'jpg' => 'image/jpeg',
		'png' => 'image/png'
	];
	$fileInfo = new finfo(FILEINFO_MIME_TYPE);
	if($extension = array_search($fileInfo->file($_FILES['file']['tmp_name']), $validFormats, true))
	{
		$out['test2'] = 456;
		$uploadDir = '/home/wavetoget/domains/dev.wavetoget.com/booking/';
		// $fullPath = '/home/wavetoget/domains/dev.wavetoget.com/booking/1234.png';
		do
		{
			$randomFilename = '1234567';
			$fullFileName = $randomFilename . '.' . $extension;
			// $relativePath = $storeDir . $fullFileName;
			$fullPath = $uploadDir . $fullFileName;
		}
		while(file_exists($fullPath));
		$out['path'] = $fullPath;
		if(move_uploaded_file($_FILES['file']['tmp_name'], $fullPath))
		{
			$out['success'] = true;
		}
	}
	
}
header("Content-type: application/json");
echo json_encode($out);
?>