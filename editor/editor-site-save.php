<?php
use Booking\Records\Site;
use Booking\Records\Factory;
require('../basicsite/init.php');

$out['success'] = false;
if($_POST['site'])
	$item = new Site($core, $_POST['site']);
else
	$item = new Site($core);

$item->name = $_POST['name'];
$item->faq_link = $_POST['faqLink'] ?: null;
$item->news_link = $_POST['newsLink'] ?: null;
$item->api_key = $_POST['apiKey'] ?: null;
$item->api_secret = $_POST['apiSecret'] ?: null;
$item->token = $_POST['token'] ?: null;
$item->token_secret = $_POST['tokenSecret'] ?: null;

$out['success'] = $item->save();
$newKey = $_POST['id'] ? 'siteID' : 'newSiteID';
$out[$newKey] = $item->id;

$sites = Factory\Site::get($core);
$out['sites'] = $sites;


// *****************************************

// $data = [
// 	'client_id' => $_POST['client'],
// 	'name' => $_POST['name'],
// 	'faq_link' => $_POST['faqLink'],
// 	'news_link' => $_POST['newsLink'],
// 	'api_key' => $_POST['apiKey'],
// 	'api_secret' => $_POST['apiSecret'],
// 	'token' => $_POST['token'],
// 	'token_secret' => $_POST['tokenSecret']
// ];
// if($_POST['site'])
// {
// 	$db->updateRow('sites', ['id' => $_POST['site']], $data);
// 	if($db->errorCode() === PDO::ERR_NONE)
// 	{
// 		$out['success'] = true;
// 		$out['siteID'] = $_POST['site'];
// 	}
// }
// else
// {
// 	$newSite = $db->addRow('sites', $data);
// 	if($db->errorCode() === PDO::ERR_NONE)
// 	{
// 		$out['success'] = true;
// 		$out['newSiteID'] = $newSite;
// 	}
// }
// if($out['success'])
// {
// 	$out['sites'] = $db->getSet('sites', ['client_id' => $_POST['client']]);
// }

header("Content-type: application/json");
echo json_encode($out);