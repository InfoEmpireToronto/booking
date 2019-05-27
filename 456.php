<?php
require('basicsite/init.php');
use Booking\Records\Appointment;
use Booking\Records\Doctor;
use Booking\Records\Payment;
use Booking\Records\Treatment;
use Booking\Records\Factory;
if($user->user_type != 1)
{
	die();
}
$out['success'] = false;

// $doctor = new Doctor($core, 13);
// for($i = 59; $i <= 61; $i++)
// {
	// $app = new Appointment($core, 67);
	// $product = $app->getProduct();
	// $app->price = $app->getSubTotal();
	// $app->tax = $app->getTax();
	// $app->total = $app->getTotal();
	// $app->save();

// }

// $app->test = 123;
// $app->kk = 123;
// // $timetable = $doctor->isConflict('2018-02-10');
// $test = $app->getEndTimeslot();
// $test = $app->getDuration();
// $test = $app->getPaymentMethod();
// $timetable = $doctor->getAvailableTime('2018-02-10');
// $test = validateBookingTime('2018-10-11', '11:45:00');
// $trea = new Treatment($core, 8528);
// $payment = new Payment($core);


// $payment->paid = 456;

// $payment->appointment = 52;
// 		$payment->price = 99;
// 		$payment->tax = 0;
// 		$payment->total = 99;
// 		$paymemt->test = 20;
// 		$payment->method = 1;

// 		$success = $payment->save();
// $test = $pay->getAppointment();
// $test = $trea->getTax();
// echo json_encode($payment);
// $query = "SELECT `id`, `appointment`, SUM(`paid`) FROM `payments` GROUP BY(`appointment`);";
// $financial = $core->db->querySet($query);
// $today = date('Y-m-d', time());
// $today = '2018-10-09';
// $financial = Factory\Payment::compose($core)
// 				->link('/appointment', 'Appointment')
// 				->link('/appointment/doctor', 'Doctor')
// 				->link('/appointment/patient', 'Patient')
// 				->cols(['/id', '/appointment', '/paid', '/appointment/date', '/appointment/time', 'appointment/doctor/firstname' => 'd_firstname', 
// 						'appointment/doctor/lastname' => 'd_lastname', '/appointment/patient/firstname' => 'p_firstname', '/appointment/patient/lastname' => 'p_lastname'])
// 				->transform('/paid', 'sum')
// 				->filter('=', '/appointment/date', $today)
// 				->group('/appointment')
// 				->get();
// $financial = date('Y-m-d', 1489161927);
echo json_encode($product);
function validateBookingTime($date, $time)
{
	$dateTime = strtotime($date. ' '. $time);
	$now = time();
	$nextTimeSlot = ceil($now / 900) * 900;
	echo 'now:'.date('Y-m-d H:i:s', $now).'<br>';
	echo 'next:'.date('Y-m-d H:i:s', $nextTimeSlot);
	return ($dateTime > $nextTimeSlot);
}
?>