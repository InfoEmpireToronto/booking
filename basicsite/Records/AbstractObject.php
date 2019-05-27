<?php
namespace Booking\Records;
Class AbstractObject
	implements \JsonSerializable, \Iterator
{
	const TABLE = null;
	const ID = 'id';
	// ID is the column name this table identifies its rows with
	const FAR_ID = null;
	// FAR_ID is the column name other tables use to refer to rows in this table
	protected $core;
	protected $data;
	protected $originalData;
	protected $mask;
	private $getObjectCache;
	private $getPropertyCache;
	private $getSetCache;
	private $getBridgeCache;
	protected $saveActions = [];
	/*
		AbstractObject Constructor
		Requires a core object and a tablename
		Optionally accepts a third input which can be any of:
			Integer: should contain value of 'id' column in the table that identifies the correct row
				Constructor loads the corresponding row into the data array
			Object: should contain data from corresponding row, including id field
				Constructor copies object properties into data array
			Array: should contain filters for selecting correct row from the table
				Constructor gets the row from the table according to the array and loads it into data array
		Usage:
			new AbstractObject($core, 'table_name', 13);
			new AbstractObject($core, 'table_name', (object)['id' => 13, 'name' => 'example']);
			new AbstractObject($core, 'table_name', ['name' => 'example']);

		Naming Convention
			AbstractObjects and derived classes should use this naming convention for retrieving data:
			GetSomething
				Returns an AbstractObject or derived object
			ViewSomething
				Returns a ConstantObject with expected properties
	*/
	public function __construct($core, $input = null)
	{
		$this->core = $core;

		$this->mask = [
			static::ID,
			'created',
			'updated'
		];
		
		if(is_object($input))
		{
			$this->data = (array)$input;
		}
		elseif($this->grantAccess($input))
		{
			if(is_scalar($input))
			{
				$this->data = $this->core->db->getRow(static::TABLE, [static::ID => $input], \PDO::FETCH_ASSOC);
			}
			elseif(is_array($input))
			{
				$this->data = $this->core->db->getRow(static::TABLE, $input, \PDO::FETCH_ASSOC);
			}
		}
		$this->originalData = $this->data;
	}
	public function __get($key)
	{
		return $this->data[$key];
	}
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}
	public function __unset($key)
	{
		unset($this->data[$key]);
	}
	public function __debugInfo()
	{
		return ['data' => $this->data,
		'core' => $this->core];
	}
	public function text($prop)
	{
		return htmlspecialchars($this->data[$prop]);
	}
	public function exists()
	{
		return isset($this->data[static::ID]);
	}
	public function mask($key)
	{
		if(is_array($key))
		{
			$this->mask += $key;
		}
		else
		{
			$this->mask[] = $key;
		}
	}
	public function save()
	{
		if(!$this->changed())
		{
			return true;
		}
		foreach($this->saveActions as $act)
		{
			$act();
		}
		$saveSuccess = false;
		if($this->exists())
		{
			//Selective include
			if(is_string($include))
			{
				$include = [$include];
			}
			if(is_array($include))
			{
				$masked = [];
				foreach($include as $key)
				{
					if(array_key_exists($key, $this->data))
					{
						$masked[$key] = $this->data[$key];
					}
				}
			}
			else
			{
				$masked = $this->data;
			}
			//Selective exclude
			foreach($this->mask as $dataMask)
			{
				unset($masked[$dataMask]);
			}
			$this->core->db->updateRow(static::TABLE, [static::ID => $this->data[static::ID]], $masked);
			$saveSuccess = $this->core->db->success();
		}
		else
		{
			if($id = $this->core->db->addRow(static::TABLE, $this->data))
			{
				$this->data[static::ID] = $id;
				$saveSuccess = true;
			}
		}
		if($saveSuccess && $this->cacheKey)
		{
			$this->cache($this->cacheKey);
		}
		return $saveSuccess;
	}
	public function delete()
	{
		if($this->exists())
		{
			$this->core->db->deleteRow(static::TABLE, [static::ID => $this->data[static::ID]]);
			if($this->core->db->success())
			{
				$this->data = [];
				return true;
			}
		}
		return false;
	}
	public function changed()
	{
		return ($this->data !== $this->originalData) ||
					(count($this->saveActions) !== 0) ||
					(!$this->exists());
	}
	public function read()
	{
		return (object)$this->data;
	}
	public function viewComposed()
	{
		return $this->read();
	}
	public function jsonSerialize()
	{
		return $this->data;
	}

	/*
		getCreated()
		getUpdated()
		are implementations of common operations across most rows
	*/
	public function getCreated()
	{
		if($this->data['created'])
		{
			return new \DateTime($this->data['created']);
		}
	}
	public function getUpdated()
	{
		if($this->data['updated'])
		{
			return new \DateTime($this->data['updated']);
		}
	}

	/* Iterator Implementation */
	public function rewind() { return reset($this->data); }
	public function current() { return current($this->data); }
	public function key() { return key($this->data); }
	public function next() { return next($this->data); }
	public function valid()
	{
		 $key = key($this->data);
		 return ($key !== NULL && $key !== FALSE);
	}

	/*
		grantAccess
		Verifies the current user has permission to create the object before allowing DB access
		Derived functions should override this if they want better than the most simplistic access control
	*/
	protected function grantAccess($input)
	{
		return true;
	}

	protected function getAssociatedObject($className, $assocColumn = null)
	{
		$namespaceClass = __NAMESPACE__ . '\\' . $className;
		if(\is_null($assocColumn))
		{
			$assocColumn = $namespaceClass::FAR_ID;
		}
		return new $namespaceClass($this->core, $this->data[$assocColumn]);
	}
	protected function getAssociatedObjectProperty($className, $assocColumn, $property)
	{
		if($this->data[$assocColumn])
		{
			if($this->getObjectCache[$assocColumn])
			{
				return $this->getObjectCache[$assocColumn]->$property;
			}
			elseif(!$this->getPropertyCache[$assocColumn][$property])
			{
				$namespaceClass = __NAMESPACE__ . '\\' . $className;
				$tableName = $namespaceClass::TABLE;
				$farId = $namespaceClass::ID;
				$this->getPropertyCache[$assocColumn][$property] = $this->core->db->getColumn($tableName, [$farId => $this->data[$assocColumn]], $property);
			}
		}
		return $this->getPropertyCache[$assocColumn][$property];
	}
	/*
		getAssociatedObjectSet
		
		accepts
			className:		Construct this class out of discovered rows
			farColumn:		Name of the column in tableName that points to this object
			assocColumn:	Name of the column in this object that is used for identity in tableName

		returns
			array(className...)
	*/
	protected function getAssociatedObjectSet($className, $farColumn = null, $assocColumn = null)
	{
		$namespaceClass = __NAMESPACE__ . '\\' . $className;
		$tableName = $namespaceClass::TABLE;
		if(\is_null($assocColumn))
		{
			$assocColumn = static::ID;
		}
		if(\is_null($farColumn))
		{
			$farColumn = static::FAR_ID;
		}
		if(!$this->getSetCache[$tableName] && $this->data[$assocColumn])
		{
			$out = [];
			if($items = $this->core->db->getSet($tableName, [$farColumn => $this->data[$assocColumn]]))
			{
				foreach($items as $item)
				{
					$out[] = new $namespaceClass($this->core, $item);
				}
			}
			$this->getSetCache[$tableName] = $out;
		}
		return $this->getSetCache[$tableName];
	}
	/*
		getBridgedObjectSet
		
		accepts
			className:			Construct this class out of discovered rows
			bridgeTable:		Name of the table that bridges the two tables for a many to many relationship
			bridgeOwnColumn:	Name of the column in bridgeTable that contains this object's ID
			bridgeFarColumn:	Name of the column in bridgeTable that contains the far object's IDs
			ownId:				Name of the column in this table that bridgeTable identifies rows with, usually 'id'
			farId:				Name of the column in the far table that bridgeTable identifies rows with, usually 'id'

		returns
			array(className...)
	*/
	protected function getBridgedObjectSet($className, $bridgeTable, $bridgeOwnColumn = null, $bridgeFarColumn = null, $ownId = null, $farId = null)
	{
		$farClass = __NAMESPACE__ . '\\' . $className;
		$farTable = $farClass::TABLE;
		if(\is_null($ownId))
		{
			$ownId = static::ID;
		}
		if(\is_null($farId))
		{
			$farId = $farClass::ID;
		}
		if(\is_null($bridgeOwnColumn))
		{
			$bridgeOwnColumn = static::FAR_ID;
		}
		if(\is_null($bridgeFarColumn))
		{
			$bridgeFarColumn =  $farClass::FAR_ID;
		}
		if(!$this->getBridgeCache[$bridgeTable][$farTable] && $this->data[$ownId])
		{
			$out = [];
			$items = $this->core->db->querySet("
				SELECT `f`.* FROM `{$bridgeTable}` AS `b`
					LEFT JOIN `{$farTable}` AS `f`
					ON `f`.`{$farId}` = `b`.`{$bridgeFarColumn}`
				WHERE `b`.`{$bridgeOwnColumn}` = :id
			", [':id' => $this->data[$ownId]]);
			if($items)
			{
				foreach($items as $item)
				{
					$out[] = new $farClass($this->core, $item);
				}
			}
			$this->getBridgeCache[$bridgeTable][$farTable] = $out;
		}
		return $this->getBridgeCache[$bridgeTable][$farTable];
	}
}