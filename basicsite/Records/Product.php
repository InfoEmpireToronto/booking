<?php
namespace Booking\Records;
Class Product extends AbstractObject
{
	const TABLE = 'products';
	const FAR_ID = 'product';
	
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
		return number_format($tax, 2);
	}
}