<?php
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
function remap($array, $mapping)
{
	if(is_object($array))
	{
		$array = (array)$array;
	}
	$out = [];
	foreach($mapping as $origin => $destination)
	{
		$out[$destination] = $array[$origin];
	}
	return $out;
}