<?php
namespace Booking\Records;
Class Treatment extends AbstractObject
{
	const TABLE = 'treatments';
	const FAR_ID = 'treatment';

	function getDuration()
	{
		return $this->getAssociatedObject('Duration');
	}
	
	function getTax()
	{	
		$tax = 0;
		if($this->tax == 1)
		{
			$settings = new Setting($this->core, ['key' => 'province']);
			$province = new Province($this->core, $settings->value);
			$rate = round($province->tax_rate / 100, 2);
			$tax = round($this->price * $rate, 2);
		}
		return $tax;
	}
}