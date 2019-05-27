<?php
namespace Booking\Utilities;
use \PDO;
Class Database
{
	private $pdo;
	private $pdoCfg;
	private $statements;
	private $lastHash;
	private $lastParameters;
	private $lastQuery;

	const MYSQL_DATETIME = 'Y-m-d H:i:s';

/* TOC

	Construction
	Utility
	Queries
	Fetches
	Metadata
	CRUD

*/

/* Construction */
	public function __construct($dbName, $dbUser, $dbPassword, $dbHost = 'localhost', $options = array())
	{
		$dsn = 'mysql:host=' . $dbHost . ';dbname=' . $dbName;
		$this->pdoCfg = array(
			'dsn' => $dsn,
			'user' => $dbUser,
			'pass' => $dbPassword,
			'opts' => $options
		);
	}
	public function setAttribute($attribute, $value)
	{
		$this->pdo->setAttribute($attribute, $value);
	}

/* Utility */
	public function generateSpecifiers($values, $delimiter = ' AND ')
	{
		$qryAr = array();
		$parAr = array();
		$usedParamNames = [];
		foreach($values as $col => $val)
		{
			$safeCol = str_replace('`', '``', $col);
			$sqlCol = '`' . $safeCol . '`';
			if(is_array($val))
			{
				//Complex types need transforming
				if(isset($val['lat']) && isset($val['long']))
				{
					//Point Coordinates
					$paramLat = self::makeParamName($safeCol . 'Lat', $usedParamNames);
					$paramLon = self::makeParamName($safeCol . 'Lon', $usedParamNames);
					$qryAr[] = "{$sqlCol} = POINT({$paramLon}, {$paramLat}";
					$parAr[$paramLat] = $val['lat'];
					$parAr[$paramLon] = $val['long'];
				}
			}
			else
			{
				$paramName = self::makeParamName($safeCol, $usedParamNames);
				$qryAr[] = $sqlCol . ' = ' . $paramName;
				$parAr[$paramName] = $val;
			}
		}
		$qry = implode($delimiter, $qryAr);
		return array(
			'q' => $qry,
			'p' => $parAr
		);
	}
	private static function quoteName($name)
	{
		$safeName = str_replace('`', '``', $name);
		return '`' . $safeName . '`';
	}
	private static function makeParamName($name, &$used)
	{
		$paramName = ':' . $name;
		$num = 1;
		while(in_array($paramName, $used, true))
		{
			$paramName = ':' . $name . $num;
			$num++;
		}
		$used[] = $paramName;
		return $paramName;
	}
	static function mysqlDateTime($timestamp = null, $accuracy = null)
	{
		$format = self::MYSQL_DATETIME;
		if(is_null($timestamp))
		{
			$timestamp = time();
		}
		if(is_string($accuracy) && $accuracy !== 'seconds')
		{
			$search = [];
			$replace = [];
			switch($accuracy)
			{
				case 'years':
					$search[] = 'm';
					$replace[] = '01';
				case 'months':
					$search[] = 'd';
					$replace[] = '01';
				case 'days':
					$search[] = 'H';
					$replace[] = '00';
				case 'hours':
					$search[] = 'i';
					$replace[] = '00';
				case 'minutes':
					$search[] = 's';
					$replace[] = '00';
			}
			$format = str_replace($search, $replace, $format);
		}
		if(is_int($timestamp))
		{
			return date($format, $timestamp);
		}
		elseif(is_object($timestamp))
		{
			return $timestamp->format($format);
		}
	}

/* Queries */
	public function query($query, $parameters = array())
	{
		if(!$this->pdo)
		{
			$this->pdo = new PDO($this->pdoCfg['dsn'], $this->pdoCfg['user'],
										$this->pdoCfg['pass'], $this->pdoCfg['opts']);
		}
		$hash = crc32($query);
		if(!isset($this->statements[$hash]))
		{
			$this->statements[$hash] = $this->pdo->prepare($query);
			
		}
		if($this->statements[$hash] === false)
		{
			error_log("Following query failed to prepare:\n" . $query . "\nParameters:\n" . print_r($parameters, true));
			error_log(print_r($this->pdo->errorInfo(), true));
		}
		else
		{
			$this->statements[$hash]->execute($parameters);
			if($this->statements[$hash]->errorCode() != PDO::ERR_NONE)
			{
				error_log("Following query failed to execute:\n" . $query . "\nParameters:\n" . print_r($parameters, true));
				error_log(print_r($this->statements[$hash]->errorInfo(), true));
			}
		}
		$this->lastHash = $hash;
		$this->lastParameters = $parameters;
		$this->lastQuery = $query;
		return $hash;
	}
	public function queryRow($query, $parameters = array(), $fetchStyle = PDO::FETCH_OBJ)
	{
		$res = $this->query($query, $parameters);
		$out = $this->fetch($fetchStyle, $res);
		$this->close($res);
		return $out;
	}
	public function querySet($query, $parameters = array(), $fetchStyle = PDO::FETCH_OBJ)
	{
		$res = $this->query($query, $parameters);
		$out = $this->fetchAll($fetchStyle, $res);
		$this->close($res);
		return $out;
	}
	public function queryColumn($query, $parameters = array(), $columnNumber = 0)
	{
		$res = $this->query($query, $parameters);
		$out = $this->fetchColumn($columnNumber, $res);
		$this->close($res);
		return $out;
	}
	private function close($resource)
	{
		$this->statements[$resource]->closeCursor();
	}

/* Fetches */
	public function fetch($fetchStyle = PDO::FETCH_OBJ, $resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		return $resource ? $this->statements[$resource]->fetch($fetchStyle) : false;
	}
	public function fetchAll($fetchStyle = PDO::FETCH_OBJ, $resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		return $resource ? $this->statements[$resource]->fetchAll($fetchStyle) : false;
	}
	public function fetchColumn($columnNumber = 0, $resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		return $resource ? $this->statements[$resource]->fetchColumn($columnNumber) : false;
	}

/* Metadata */
	public function rowCount($resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		return $this->statements[$resource]->rowCount();
	}
	public function lastInsertId()
	{
		if($this->pdo)
		{
			return $this->pdo->lastInsertId();
		}
	}
	public function errorCode($resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		if($resource)
		{
			return $this->statements[$resource]->errorCode();
		}
		elseif($this->pdo)
		{
			return $this->pdo->errorCode();
		}
	}
	public function errorInfo($resource = null)
	{
		if(is_null($resource))
		{
			$resource = $this->lastHash;
		}
		if($resource)
		{
			return $this->statements[$resource]->errorInfo();
		}
		elseif($this->pdo)
		{
			return $this->pdo->errorInfo();
		}
	}
	public function success($resource = null)
	{
		return $this->errorCode($resource) === PDO::ERR_NONE;
	}
	public function debugInfo()
	{
		return [
			'errorInfo' => $this->errorInfo(),
			'query' => $this->lastQuery,
			'parameters' => $this->lastParameters
		];
	}
/* Compose */
	public function composeSet($table)
	{
		return new DatabaseComposition($this, $table);
	}

/* CRUD */

	public function addRow($table, $values)
	{
		$sets = $this->generateSpecifiers($values, ', ');
		$sqlTable = self::quoteName($table);
		$res = $this->query(
			"INSERT INTO {$sqlTable}
			SET {$sets['q']}", $sets['p']
		);
		if($this->success($res))
		{
			return $this->lastInsertId();
		}
		return false;

	}
	public function getColumn($table, $identifiers, $column = 0)
	{
		if($identifiers)
		{
			$specs = $this->generateSpecifiers($identifiers);
			$where = ' WHERE ' . $specs['q'];
			$params = $specs['p'];
		}
		else
		{
			$where = '';
			$params = array();
		}
		if(is_numeric($column))
		{
			$columnNum = $column;
			$sqlColumn = '*';
		}
		elseif(is_string($column))
		{
			$columnNum = 0;
			$sqlColumn = self::quoteName($column);
		}
		$sqlTable = self::quoteName($table);
		return $this->queryColumn(
					'SELECT ' . $sqlColumn . ' FROM ' . $sqlTable . $where,
					$params, $columnNum
				);
	}
	public function getRow($table, $identifiers, $fetchStyle = PDO::FETCH_OBJ)
	{
		if($identifiers)
		{
			$specs = $this->generateSpecifiers($identifiers);
			$where = ' WHERE ' . $specs['q'];
			$params = $specs['p'];
		}
		else
		{
			$where = '';
			$params = array();
		}
		$sqlTable = self::quoteName($table);
		return $this->queryRow(
					'SELECT * FROM ' . $sqlTable . $where,
					$params, $fetchStyle
				);
	}
	public function getSet($table, $identifiers = array(), $fetchStyle = PDO::FETCH_OBJ)
	{
		if($identifiers)
		{
			$specs = $this->generateSpecifiers($identifiers);
			$where = ' WHERE ' . $specs['q'];
			$params = $specs['p'];
		}
		else
		{
			$where = '';
			$params = array();
		}
		$sqlTable = self::quoteName($table);
		return $this->querySet(
					'SELECT * FROM ' . $sqlTable . $where,
					$params, $fetchStyle
				);
	}
	public function updateRow($table, $identifiers, $values)
	{
		$sets = $this->generateSpecifiers($values, ', ');
		$params = $sets['p'];
		if($identifiers)
		{
			$specify = $this->generateSpecifiers($identifiers);
			$where = 'WHERE ' . $specify['q'];
			$params = array_merge($params, $specify['p']);
		}
		else
		{
			$where = '';
		}
		$sqlTable = self::quoteName($table);
		return $this->query(
			"UPDATE {$sqlTable} SET {$sets['q']} {$where}",
			$params);
	}
	public function deleteRow($table, $identifiers)
	{
		if($identifiers)
		{
			$specs = $this->generateSpecifiers($identifiers);
			$where = 'WHERE ' . $specs['q'];
			$params = $specs['p'];
		}
		else
		{
			$where = '';
			$params = array();
		}
		$sqlTable = self::quoteName($table);
		return $this->query(
			"DELETE FROM {$sqlTable} {$where}",
			$params
		);
	}
}