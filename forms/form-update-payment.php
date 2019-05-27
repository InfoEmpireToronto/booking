<?php
require('../basicsite/init.php');
use Booking\Records\Payment;
use Booking\Records\AppointmentProduct;
use Booking\Records\Appointment;
$out['success'] = false;
$out['message'] = 'Failed';
if($user->user_type != 1 && $user->user_type != 2)
{
	$out['message'] = 'Permission denied';
}
else
{
	if(isset($_GET['add']))
	{
		$payload = json_decode(file_get_contents('php://input'));
		$payment = new Payment($core);
		$payment->appointment = $payload->appointment;
		$payment->paid = $payload->amount;
		$payment->method = $payload->method;
		$success = $payment->save();
		$success = true;
		if($success)
		{
			$payoff = false;
			$appointment = new Appointment($core, $payload->appointment);
			$appointment->price = $payload->price;
			$appointment->tax = $payload->tax;
			$appointment->total = $payload->total;
			$appointment->note = $payload->note ?: null;
			$appointment->save();
			if($payload->products)
			{
				foreach ($payload->products as $product)
				{
					if($product->product->id != null && $product->disable == false)
					{
						$newproduct = new AppointmentProduct($core);
						$newproduct->appointment = $payload->appointment;
						$newproduct->product = $product->product->id;
						$newproduct->save();
					}
				}
			}
			if($appointment->getTotalPaid() == $appointment->total)
			{
				$appointment->status = 2;
				$success = $appointment->save();
				$payoff = true;
			}
			if($success)
				$out['message'] = 'Successful';
			$payments = $appointment->getPayment();
			foreach ($payments as $payment)
			{
				$temp[] = [
					'paid' => $payment->paid,
					'method' => $payment->getPaymentMethod()
				];
			}
			$cardholder = $appointment->getPatient()->wavetoget;
			if($cardholder)
			{
				require('../lib/store-info.php');
				$url = 'https://www.wavetoget.com/embed/booking/cardholder.php';
				$post['cardholder'] = $cardholder;
				$post['dollar'] = $payload->amount;
				$post['point'] = $payload->amount * $point_expand;
				$result = curlrequest($url, $post);
				$result = json_decode($result);
				if($result->success)
					$out['message'] = 'Successful, ' . $post['point'] . ' points added';
			}
					
			if($success)
			{
				$out['success'] = true;
				$out['payoff'] = $payoff;
				$out['payment'] = $temp;
			}
		}
	}
}

echo json_encode($out);
?>