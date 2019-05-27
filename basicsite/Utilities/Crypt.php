<?php
namespace Booking\Utilities;
Class Crypt
{
	const SALT_BCRYPT = './1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const SALT_FILENAME = '_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const SALT_TOKEN = '~!*()_-1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const SALT_NUMERIC = '1234567890';
	const SALT_CODE = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	static function _compare($stored, $checked)
	{
		$ret = true;
		$out = '';

		$loopk = strlen($stored);
		
		$ka = str_split($stored);
		$ua = str_split($checked);
		
		if(!isset($ua[$loopk-1]) || isset($ua[$loopk]))
		{
			$ret = false;
		}
		$ub = array();
		$uc = array();
		for($i = 0; $i != $loopk; $i++)
		{
			$uc[$i] = '0';
		}
		for($i = 0; $i != $loopk; $i++)
		{
			if(isset($ua[$i]))
			{
				$ub[$i] = $ua[$i];
			}
			else
			{
				$ub[$i] = $uc[$i];
				$ret = false;
			}
		}	
		for($i = 0; $i != $loopk; $i++)
		{
			if($ka[$i] !== $ub[$i])
			{
				$ret = false;
			}
		}
		return $ret;
	}

	static function random($min, $max)
	{
		$diff = $max - $min;
		if ($diff <= 0) return $min; // not so random...
		$range = $diff + 1; // because $max is inclusive
		$bits = ceil(log(($range),2));
		$bytes = ceil($bits/8.0);
		$bits_max = 1 << $bits;
		// e.g. if $range = 3000 (bin: 101110111000)
		//  +--------+--------+
		//  |....1011|10111000|
		//  +--------+--------+
		//  bits=12, bytes=2, bits_max=2^12=4096
		$num = 0;
		do {
			$num = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes))) % $bits_max;
			if ($num >= $range) {
				continue; // start over instead of accepting bias
			}
			break;
		} while (True);
		return $num + $min;
	}
	static function salt($num, $validChars = self::SALT_BCRYPT)
	{
		$valid = str_split($validChars);
		$salt = '';
		for($i = 0; $i != $num; $i++)
		{
			$salt .= $valid[self::random(0, count($valid) - 1)];
		}
		return $salt;
	}
	static function hash($str, $salt = null)
	{
		if(is_null($salt))
		{
			$salt = '$2y$' . '12$' . self::salt(22) . '$';
		}
		$bcrypt = crypt($str, $salt);
		return $bcrypt;
	}
	static function compare($encrypted, $plaintext)
	{
		$compare = self::hash($plaintext, $encrypted);
		return self::_compare($encrypted, $compare);
	}
}