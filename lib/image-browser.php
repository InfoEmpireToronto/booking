<?php
require('../basicsite/init.php');
if(!$loggedIn)
{
	die();
}
if($user->user_type == 1 && $_GET['dir'])
{
	$uploadDir = '../uploads/' . $_GET['dir'] . '/';
	$images = [];
	if($dirHandle = opendir($uploadDir))
	{
		while($dirItem = readdir($dirHandle))
		{
			$extension = strtolower(substr($dirItem, strrpos($dirItem, '.') + 1));
			if(is_file($uploadDir . '/' . $dirItem) && ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg'))
			{
				$src = BASE_URL . 'uploads/' . $_GET['dir'] . '/' . $dirItem;
				$imageSize = getimagesize($uploadDir . $dirItem);
				$size = [
					'width' => $imageSize[0],
					'height' => $imageSize[1]
				];
				$images[] = [
					'src' => $src,
					'size' => $size
				];
			}
		}
	}
}
$out['images'] = $images;
echo json_encode($out);
?>