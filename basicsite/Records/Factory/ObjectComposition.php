<?php
namespace Booking\Records\Factory;
Class ObjectComposition extends \Booking\Utilities\DatabaseComposition
{
	private $core;
	private $factory;
	private $mask;


	public function __construct($coreObj, $factoryClass)
	{
		$this->core = $coreObj;
		$this->factory = $factoryClass;
		$namespaceClass = $factoryClass::PRODUCT;
		$tableName = $factoryClass::TABLE ?: $namespaceClass::TABLE;
		parent::__construct($this->core->db, $tableName);
	}

	/*
		link
		Parameters:
			nearPath - path to column that matches the far column value
			farObj - AbstractObject derivative for table that you're linking
			farCol - ID column that matches value at nearPath
	*/
	public function link($nearPath, $farObj, $farCol = null)
	{
		if(is_string($farObj))
		{
			$className = '\\Booking\\Records\\' . $farObj;
			if(class_exists($className))
			{
				$farTable = $className::TABLE;
				if(!$farCol)
				{
					$farCol = $className::ID;
				}
			}
		}
		elseif(is_object($farObj))
		{
			$farTable = $farObj::TABLE;
			if(!$farCol)
			{
				$farCol = $farObj::ID;
			}
		}
		return parent::link($nearPath, $farTable, $farCol);
	}
	private function getMask()
	{
		$mask = [];
		if($this->columns)
		{
			foreach($this->columns as $name => $path)
			{
				if(substr_count($path,'/') >= 2)
				{
					$mask[] = $name;
				}
			}
		}
		return $mask;
	}
	public function get($params = [])
	{
		$idColumn = ($this->factory::PRODUCT)::ID;
		if(!$this->columns[$idColumn])
		{
			$this->col($idColumn);
		}
		$output = parent::get($params);
		return $this->factory::classify($this->core, $output, $this->getMask());
	}
}