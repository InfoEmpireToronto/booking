<?php
// require_once('../init.php');
// $client = $_REQUEST['client'];
// if($userinfo->client_id != $client && $userinfo->user_type !== 'Staff')
// {
// 	error_log("no auth");
// 	die();
// }
$uploadDir = '/home/developer/domains/booking.infoempire.us/editor/lib/uploads/';
$galleryDir = $uploadDir . $client;
$images = [];
if($dirHandle = opendir($uploadDir))
{
	while($dirItem = readdir($dirHandle))
	{
		$extension = strtolower(substr($dirItem, strrpos($dirItem, '.') + 1));
		if(is_file($uploadDir . '/' . $dirItem) &&
			($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg'))
		{
			$images[] = $client . '/' . $dirItem;
		}
	}
}
?>
<div class="row">
<?php
$imgNum = 1;
foreach($images as $img)
{
	$imageSize = getimagesize($uploadDir . $img);
?>
<div class="col-md-4 gallery-select quit" data-iu-image="<?=$img;?>">
	<img class="img-responsive"
		src="/editor/lib/uploads/<?=$img;?>">
	<small><?=$imageSize[0] . 'x' . $imageSize[1];?></small>
	<button type="button" class="image-remove btn btn-sm btn-danger bs">
		Del
	</button>
</div>
<?php
	if($imgNum++ % 3 === 0)
	{
		echo '</div><div class="row">';
	}
}
?>
</div>