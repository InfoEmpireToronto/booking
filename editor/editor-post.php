<?php
require('../basicsite/init.php');
use Booking\Records\Blog;
use Booking\Records\Faq;
use Booking\Records\Factory;
function cleanHTML($content)
{
	$content = str_replace(['<div><br></div>', '<div></div>'], '<br>', $content);
	return strip_tags($content, '<b><i><a><strong><em><br><p><ul><li><ol><img><u><span><blockquote><h1><h2><h3><h4><h5><h6><code><pre>');
}
function hasKeyValues($array, $keys)
{
	foreach($keys as $key)
	{
		if(!$array[$key])
		{
			return false;
		}
	}
	return true;
}

$requiredKeys = [
	'site',
	'type',
	'title',
	'content'
];
if(!hasKeyValues($_POST, $requiredKeys))
{
	http_response_code(422);
	die();
}

if($_POST['type'] === 'article')
{
	if($_POST['id'])
		$newPost = new Blog($core, $_POST['id']);
	else
		$newPost = new Blog($core);

	$newPost->site = $_POST['site'];
	$newPost->title = $_POST['title'];
	$newPost->content = cleanHTML($_POST['content']);

	if($_POST['category'])
		$newPost->category = $_POST['category'];
	if($_POST['metatitle'])
		$newPost->meta_title = $_POST['metatitle'];
	if($_POST['metadescription'])
		$newPost->meta_description = $_POST['metadescription'];
	if($_POST['date'])
		$newPost->date_display = $_POST['date'] . ' ' . $_POST['time'];
	if($_POST['status'])
		$newPost->status = $_POST['status'];

	// $remap = [
	// 	'site' => 'site_id',
	// 	'category' => 'category',
	// 	'title' => 'title',
	// 	'content' => 'content',
	// 	'metatitle' => 'meta_title',
	// 	'metadescription' => 'meta_description',
	// 	'date' => 'date_display',
	// 	'time' => 'time_display',
	// 	'status' => 'status'
	// ];
}
else
{
	if($_POST['id'])
		$newPost = new Faq($core, $_POST['id']);
	else
		$newPost = new Faq($core);

	$newPost->site = $_POST['site'];
	$newPost->question = $_POST['title'];
	$newPost->answer = cleanHTML($_POST['content']);

	if($_POST['category'])
		$newPost->category = $_POST['category'];
	if($_POST['date'])
		$newPost->date_display = $_POST['date'] . ' ' . $_POST['time'];
	if($_POST['status'])
		$newPost->status = $_POST['status'];

	// $remap = [
	// 	'site' => 'site_id',
	// 	'category' => 'category',
	// 	'title' => 'question',
	// 	'content' => 'answer',
	// 	'date' => 'date_display',
	// 	'time' => 'time_display',
	// 	'status' => 'status'
	// ];
}
$newPost->save();
$newKey = $_POST['id'] ? 'postID' : 'newPostID';
$out = [];
$out[$newKey] = $newPost->id;
// if($_POST['social'])
// {
// 	if($twitterInfo = $db->getRow('sites', ['id' => $data['site_id']]))
// 	{
// 		$id = $_POST['id'] ?: $_POST['newPostID'];
// 		if($_POST['type'] === 'article')
// 		{
// 			$link = $twitterInfo->news_link . urlencode($data['title']) . '-' . $id;
// 		}
// 		else
// 		{
// 			$link = $twitterInfo->faq_link;
// 		}
// 		require('updatetwitter.php');
// 		$apiAccess = [
// 			'CONSUMER_KEY' => $twitterInfo->api_key,
// 			'CONSUMER_SECRET' => $twitterInfo->api_secret,
// 			'OAUTH_TOKEN' => $twitterInfo->token,
// 			'OAUTH_SECRET' => $twitterInfo->token_secret
// 		];
// 		$twitter = new twitter_post($title,$link,$apiAccess);
// 	}
// }

/* Get new posts list */

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
	$out['posts'] = $compose;
}

header("Content-type: application/json");
echo json_encode($out);