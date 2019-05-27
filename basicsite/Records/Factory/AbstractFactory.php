<?php
namespace Booking\Records\Factory;
/*
	Note: AbstractFactory produces objects derived from AbstractObject only
*/
Class AbstractFactory
{
	const PRODUCT = null;
	const TABLE = null;
	// TABLE if provided overrides the product's table in order to use views in place of the base tables

	public static function get($coreObj, $filters = [])
	{
		$namespaceClass = static::PRODUCT;
		$tableName = static::TABLE ?: $namespaceClass::TABLE;

		$resultSet = $coreObj->db->getSet($tableName, $filters);

		return static::classify($coreObj, $resultSet);
	}
	public static function compose($coreObj)
	{
		$factoryClass = get_called_class();
		return new ObjectComposition($coreObj, $factoryClass);
	}
	public static function classify($coreObj, $rows, $mask = [])
	{
		$namespaceClass = static::PRODUCT;
		$out = [];
		foreach($rows as $row)
		{
			$temp = new $namespaceClass($coreObj, $row);
			$temp->mask($mask);
			$out[] = $temp;
		}
		return $out;
	}

}