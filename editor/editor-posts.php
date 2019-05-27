<?php
use Booking\Records\Factory;
require('../basicsite/init.php');
$out['success'] = false;
if($_POST['type'] == 'faq')
{
	$posts = Factory\Faq::get($core, ['site' => $_POST['site']]);
}
else
{
	$posts = Factory\Blog::get($core, ['site' => $_POST['site']]);
}

$compose = [];
if($posts)
{
	foreach($posts as $post)
	{
		// $rmPost = remap($post, $remap);
		$dateTime = $post->date_display;
		if($_POST['type'] == 'faq')
		{
			$compose[] = [
				'id' => $post->id,
				'category' => stripslashes($post->category),
				'title' => stripslashes($post->question),
				'content' => stripslashes($post->answer),
				'metatitle' => stripslashes($post->meta_title),
				'metadescription' => stripslashes($post->meta_description),
				'date' => substr($dateTime,0,10),
				'time' => substr($dateTime,11,8),
				'utc' => strtotime($dateTime),
				'status' => $post->status
			];
		}
		else
		{
			$compose[] = [
				'id' => $post->id,
				'category' => stripslashes($post->category),
				'title' => stripslashes($post->title),
				'content' => stripslashes($post->content),
				'metatitle' => stripslashes($post->meta_title),
				'metadescription' => stripslashes($post->meta_description),
				'date' => substr($dateTime,0,10),
				'time' => substr($dateTime,11,8),
				'utc' => strtotime($dateTime),
				'status' => $post->status
			];
		}
	}
}
if($core->db->success())
{
	$out['success'] = true;
	$out['posts'] = $compose;
}

header("Content-type: application/json");
echo json_encode($out);