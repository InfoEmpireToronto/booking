<?php
function curlrequest($url,$post)
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl,CURLOPT_HTTPHEADER , array(
		             "cache-control: no-cache",
		             "content-type: application/x-www-form-urlencoded"
		         ));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post, '', '&'));

	$response = curl_exec($curl);

	if (curl_error($curl))
	{
		$result =  false;
	}
	elseif ($response)
	{
		$result = $response;
	}
	return $result;
}
?>