<?php
use Booking\Records\SMS;
function sendMail($from, $to, $subject, $msg, $replyTo  = null)
{
	$from = 'From: ' . $from;
	if($replyTo)
	{
		$reply = 'Reply-To: ' . $replyTo;
		$headers = $from . "\r\n" . $reply . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n";
	}
	else
	{
		$headers = $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n";
	}
	
	$success = mail($to, $subject, $msg, $headers);
	return $success;
}

function sendSMS($messages)
{
	$curl = curl_init();
	$headers = array();
	$headers[] = "Authorization: Basic " . base64_encode('InfoEmpire:206F6E08-3270-7DEC-2AFE-92080AE9F693');
	$headers[] = "Content-Type: application/json";
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$url = "https://rest.clicksend.com/v3/sms/send";
	if(is_array($messages))
	{
		foreach ($messages as $message)
		{
			$data['messages'][] = array(
				'source' => 'php',
				'to' => $message->to,
				'body' => $message->body,
				'custom_string' => base64_encode($message->identifier)
			);
		}
	}
	else
	{
		$data['messages'][] = array(
				'source' => 'php',
				'to' => $messages->to,
				'body' => $messages->body,
				'custom_string' => base64_encode($messages->identifier)
			);
	}
	
	$dataJSON = json_encode($data);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl,CURLOPT_POSTFIELDS, $dataJSON);
	$statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	$responseJSON = curl_exec($curl); // Send request
	$response = json_decode($responseJSON);
	if($statusCode >= 300)
	{
		error_log(
			"ERROR in api_request:\n" .
			print_r(array('Path' => $path, 'Request Type' => $requestType, 'data' => $data,
						'Response' => $response), true));
	}
	return (object)array('status' => $statusCode, 'response' => $response);
}

function sms_callback($response)
{
	global $core;
	foreach ($response->response->data->messages as $value)
	{
		$ids = array();
		parse_str(base64_decode($value->custom_string), $ids);
		$sms = new SMS($core);
		$sms->appointment = $ids['appointment'];
		$sms->patient = $ids['patient'];
		$sms->message_id = $value->message_id;
		$sms->body = $value->body;
		$sms->price = $value->message_price ?: null;
		$sms->message_status = $value->status;
		$sms->packets = $value->message_parts ?: null;
		$sms->save();
	}
}
?>