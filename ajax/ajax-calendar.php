<?php
require('../basicsite/init.php');
if(!$loggedIn)
{
	die();
}
$out = [
	'success' => false,
	'message' => 'Failed to load calendar'
];
$today = getdate(); //mktime(0, 0, 0, 10, 1, 2018)
$currentYear = $today['year'];
$currentMonth = $today['mon'];

if($currentMonth == 1)
{
	$firstYear = $currentYear - 1;
	$firstMonth = 12;
	$last = $currentMonth + 2;
	$calendars[] = makeCalender($firstMonth, $firstYear);
	do{
		$calendars[] = makeCalender($currentMonth, $currentYear);
		$currentMonth++;
	}while($currentMonth <= $last);
}
else if($currentMonth > 10)
{
	$year = $currentYear;
	$month = $currentMonth - 1;
	for($i = 0; $i != 4 ; $i++)
	{
		if($month > 12)
		{
			$month -= 12;
			$year += 1;
		}		
		$calendars[] = makeCalender($month, $year);
		$month++;		
	}
}
else
{
	$last = $currentMonth + 2;
	$month = $currentMonth - 1;
	do{
		$calendars[] = makeCalender($month, $currentYear);
		$month++;
	}while($month <= $last);
}

if($calendars)
{
	$out = [
		'calendar' => $calendars,
		'success' => true
	];
}

echo json_encode($out);

function makeCalender($month, $year)
{
	$today = getdate();
	$todayTimeStamp = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
	$firstday = mktime(0, 0, 0, $month, 1, $year);
	$total = date('t', $firstday);
	$lastday = mktime(0, 0, 0, $month, $total, $year);
	for($i = 1; $i <= $total ; $i++)
	{
		$eachDay = mktime(0, 0, 0, $month, $i, $year);
		$class = ($eachDay == $todayTimeStamp) ? 'today' : null;
		$index = date('w', $eachDay);
		$week[$index] = [ 
				'day' => date('d', $eachDay),
				'date' => $eachDay,
				'class' => $class
			];
		if($index == 6)
		{
			$weeks[] = $week;
			$week = [];
		}
		
		if($i == $total && $index != 6)
		{
			$weeks[] = $week;
			$week = [];
		}
	}
	
	if(!$weeks[0][0])
	{
		$dayOfWeek = date('w', $firstday);
		$mondayOfWeek = $firstday - ($dayOfWeek * 86400);
		for ($w = 0; $w < 7; $w++)
		{
			$class = ($mondayOfWeek == $todayTimeStamp) ? 'today' : null;
			if(date('n', $mondayOfWeek) != $month)
			{
				$class = 'grey';
			}
			$weeks[0][$w] = [
				'day' => date('d', $mondayOfWeek),
				'date' => $mondayOfWeek,
				'class' => $class
			];
			$mondayOfWeek += 86400;
		}
		ksort($weeks[0]);
	}
	
	$lastRow = count($weeks) - 1;
	if(!$weeks[$lastRow][6])
	{
		$dayOfWeek = date('w', $lastday);
		$mondayOfWeek = $lastday - ($dayOfWeek * 86400);
		for ($w = 0; $w < 7; $w++)
		{
			$class = ($mondayOfWeek == $todayTimeStamp) ? 'today' : null;
			if(date('n', $mondayOfWeek) != $month)
			{
				$class = 'grey';
			}
			$weeks[$lastRow][$w] = [
				'day' => date('d', $mondayOfWeek),
				'date' => $mondayOfWeek,
				'class' => $class
			];
			$mondayOfWeek += 86400;
		}
		ksort($weeks[$lastRow]);
	}
	$monthName = date('F', $firstday);
	$calendar = [
		'name' => $monthName,
		'weeks' => $weeks
	];
	// foreach ($weeks as $key => $week)
	// {
	// 	foreach ($week as $key2 => $value) {
	// 		echo $value['day']. ' ';
	// 	if($key2 == 6)
	// 		echo '<br>';
	// 	}
		
	// }
	// echo '<br>';
	return $calendar;
}
?>