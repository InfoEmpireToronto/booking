<?php
namespace Booking\Utilities;
Class DatabaseComposition
{
	private $relationships = [];
	private $columns = [];
	private $tableIds = [];
	private $baseTable;
	private $sort;
	private $transforms = [];
	private $filters = [];
	private $range = [];
	private $groups = [];
	private $searches = [];
	private $db;
	private $storedSql;
	private $fetchStyle = \PDO::FETCH_OBJ;
	public function __construct($dbObj, $tableName)
	{
		$this->db = $dbObj;
		$this->baseTable = $tableName;
		$this->tableIds['']['.'] = $this->nextLetter();
	}
	
	private function nextLetter()
	{
		static $letter = 'a';
		return $letter++;
	}
	const TRANSFORM_PHP = 1;
	const TRANSFORM_SQL = 2;
	const TRANSFORM_AUTO = 3;

/*
	Chained inputs
*/

	/*
		link
		Parameters:
			nearPath - path to column that matches the far column value
			farObj - AbstractObject derivative for table that you're linking
			farCol - ID column that matches value at nearPath
	*/
	public function link($nearPath, $farTable, $farCol = 'id')
	{
		$nearPath = static::cleanPath($nearPath);
		$this->relationships[$nearPath] = 
		[
			'fTable' => $farTable,
			'fCol' => $farCol
		];
		if(!$this->tableIds[$farTable][$nearPath])
		{
			$this->tableIds[$farTable][$nearPath] = $this->nextLetter();
		}
		return $this;
	}
	/*
		cols
		Parameters:
			colList - array of column paths to use in composition
				Either value is the column path, or 
				Key is column path and value is output name
	*/
	public function cols($colList)
	{
		if(func_num_args() !== 1 && !is_array($colList))
		{
			$colList = func_get_args();
		}
		unset($this->storedSql);
		foreach($colList as $key => $val)
		{
			if(is_numeric($key))
			{
				$val = static::cleanPath($val);
				$this->columns[basename($val)] = $val;
			}
			else
			{
				$key = static::cleanPath($key);
				$this->columns[$val] = $key;
			}
		}
		return $this;
	}
	/*
		col
		Parameters:
			path - array of column path to use in composition
			name - output name for the pathed column
	*/
	public function col($path, $name = null)
	{
		unset($this->storedSql);
		$path = static::cleanPath($path);
		if($name)
		{
			$this->columns[$name] = $path;
		}
		else
		{
			$this->columns[basename($path)] = $path;
		}
		return $this;
	}
	/*
		transform
		Parameters:
			path - path of transformed column
			func - function that performs transformation of column
				Either an SQL function or PHP function
			sql - boolean value, whether to perform transform with an SQL function
	*/
	public function transform($path, $func, $transType = self::TRANSFORM_AUTO)
	{
		$path = static::cleanPath($path);
		if($transType === self::TRANSFORM_SQL)
		{
			$type = 'sql';
			unset($this->storedSql);
		}
		elseif($transType === self::TRANSFORM_PHP)
		{
			$type = 'php';
		}
		elseif($transType === self::TRANSFORM_AUTO)
		{
			$type = is_callable($func) ? 'php' : 'sql';
		}
		$this->transforms[$type][$path][] = $func;
		return $this;
	}
	/*
		order
		Parameters:
			path - path of column to sort by
			order - SQL order ('asc', 'desc') of column, or
				PHP function that accepts two values and returns a comparison number (-1, 0, 1)
	*/
	public function order($path, $order = 'ASC')
	{
		$path = static::cleanPath($path);
		$dirOrder = strtoupper($order);
		if($dirOrder === 'ASC' || $dirOrder === 'DESC')
		{
			unset($this->storedSql);
			$this->sort = [
				'type' => 'sql',
				'order' => $dirOrder,
				'path' => $path
			];
		}
		else
		{
			$this->sort = [
				'type' => 'php',
				'order' => $order,
				'path' => $path
			];
		}
		return $this;
	}
	/*
		filter
		Parameters:
			cmp - SQL comparison operator, or
				PHP function that accepts arguments and returns boolean
				where true will keep the row, false will discard it
			args - column paths or values to use in comparison, either
				two arguments for typical (a < b) type, or
				three arguments for (a between b and c) type, or
				any number of arguments for php functions
	*/
	public function filter($cmp, ...$args)
	{
		static $sqlCmps = [
			'=', '<=>', '>', '>=', '<', '<=', '!=', '<>',
			'LIKE', 'NOT LIKE', 'IS', 'IS NOT', 'BETWEEN', 'NOT BETWEEN'
		];
		$icmp = strtoupper($cmp);
		//$args = array_map([$this, 'cleanPath'], $args);
		if(in_array($icmp, $sqlCmps, true))
		{
			$thisFilter = [
				'cmp' => $icmp,
				'a' => $args[0]
			];
			if($cmp === 'BETWEEN' || $cmp === 'NOT BETWEEN')
			{
				$thisFilter['b'] = $args[1];
				$thisFilter['c'] = $args[2];
			}
			else
			{
				$thisFilter['b'] = $args[1];
			}
			$this->filters['sql'][] = $thisFilter;
		}
		else
		{
			$this->filters['php'][] = [
				'cmp' => $cmp,
				'args' => $args
			];
		}
		return $this;
	}
	/*
		range
		Parameters:
			first - number of results to include or
						number of results to skip if $second is set
			second - number of results to include
	*/
	public function range($first, $second = null)
	{
		$this->range = [];
		if($second)
		{
			$this->range[0] = $first;
			$this->range[1] = $second;
		}
		else
		{
			$this->range[0] = $first;
		}
		return $this;
	}

	/*
		group
		Parameters:
			column - columnName to group together
	*/
	public function group($column)
	{
		if(func_num_args() > 1)
		{
			$column = func_get_args();
		}
		elseif(is_scalar($column))
		{
			$column = [$column];
		}
		if(is_array($column))
		{
			foreach($column as $col)
			{
				$col = static::cleanPath($col);
				if(!in_array($col, $this->groups))
				{
					$this->groups[] = $col;
				}
			}
		}
		return $this;
	}
	/*
		search
		Parameters:
			term - text to find
			columns - columnPaths to search
	*/
	public function search($term, $paths)
	{
		if(is_scalar($paths))
		{
			$paths = [$paths];
		}
		$this->searches[] = [
			'term' => $term,
			'paths' => $paths
		];
	}

	/*
		apply
		Parameters:
			chain - ChainStorage to apply to composition
	*/
	public function apply($chain)
	{
		return $chain->apply($this);
	}

/*
	Settings
*/
	public function fetchStyle($style)
	{
		$this->fetchStyle = $style;
		return $this;
	}

/*
	Outputs
*/
	public function get($params = [])
	{
		$out = [];
		if($this->colsValid())
		{
			if(!$this->storedSql)
			{
				$this->storedSql = $this->buildSql($params);
			}
			$sql = $this->storedSql;
			$sqlParams = array_filter($params, function($key) use($sql){return strpos($sql, $key) !== false;}, ARRAY_FILTER_USE_KEY);
			$out = $this->db->querySet($sql, $sqlParams, $this->fetchStyle);
			if($this->filters['php'])
			{
				$out = $this->filterSet($out, $params);
			}
			if($this->transforms['php'])
			{
				$out = $this->transformSet($out);
			}
			if($this->sort['type'] === 'php')
			{
				$out = $this->orderSet($out);
			}
			if($this->range)
			{
				$out = $this->limitSet($out);
			}
			if($this->groups)
			{
				// $out = $this->groupSet($out);
			}
		}
		return $out;
	}
	public function preview($params = [])
	{
		if($this->colsValid())
		{
			$sql = $this->buildSql($params);

			return ['query' => $sql, 'parameters' => $params];
		}
	}

/*
	SQL Helpers
*/
	private function buildSql(&$params = [])
	{
		$select = $this->buildSqlSelect();
		$fromTable = static::wrapTerm($this->baseTable);
		$from = "\nFROM " . $fromTable . ' AS ' . static::wrapTerm($this->tableIds['']['.']);
		$join = $this->buildSqlJoins();
		$order = '';
		if($this->sort['type'] === 'sql')
		{
			$order = "\nORDER BY " . $this->pathSql($this->sort['path']) . ' ' . $this->sort['order'];
		}
		$where = !empty($this->filters['sql']) ? $this->buildSqlWhere($params) : '';
		if($this->groups)
		{
			if(count($this->groups) > 1)
			{
				$group = "\nGROUP BY ( ";
				foreach ($this->groups as $key => $value)
				{
					if($key == 0)
						$group .= static::wrapTerm($this->tableIds['']['.']) . '.' . static::wrapTerm($value);
					else
						$group .= ', ' . static::wrapTerm($this->tableIds['']['.']) . '.'. static::wrapTerm($value);
				}
			}
			else
			{
				$group = "\nGROUP BY " .  static::wrapTerm($this->tableIds['']['.']) . '.' . static::wrapTerm($this->groups[0]);
			}
		}
		$limit = $this->buildSqlLimit();
		return $select . $from . $join . $where . $group . $order . $limit;
	}
	private function buildSqlSelect()
	{
		$selCols = [];
		foreach($this->columns as $colOut => $colPath)
		{
			$as = '';
			if($colOut !== basename($colPath) || $this->transforms['sql'][$colPath])
			{
				$as = ' AS ' . static::wrapTerm($colOut);
			}
			$selCols[] = $this->pathSql($colPath, !empty($this->transforms['sql'][$colPath])) . $as;
		}
		return 'SELECT ' . implode(', ', $selCols);
	}
	private function buildSqlJoins()
	{
		$joins = [];
		foreach($this->relationships as $path => $rel)
		{
			$tableName = $rel['fTable'];
			$colName = $rel['fCol'];
			$tableId = static::wrapTerm($this->tableIds[$tableName][$path]);
			$tableName = static::wrapTerm($tableName);
			$tableKeyPath = $path . '/' . $colName;
			$tableKeySql = $this->pathSql($tableKeyPath);
			$tablePrevSql = $this->pathSql($path);
			$thisJoin =  "\nLEFT JOIN " . $tableName . ' AS ' . $tableId
						. ' ON ' . $tableKeySql . ' = ' . $tablePrevSql;
			$joins[] = $thisJoin;
		}
		return implode('', $joins);
	}
	private function buildSqlWhere(&$params)
	{
		$wheres = [];
		foreach($this->filters['sql'] as $filter)
		{
			$a = $this->autoFilterColumn($filter['a'], $params);
			$b = $this->autoFilterColumn($filter['b'], $params);

			$thisWhere = $a . ' ' . $filter['cmp'] . ' ' . $b;
			if($filter['c'])
			{
				$c = $this->autoFilterColumn($filter['c'], $params);
				$thisWhere .= ' AND ' . $c;
			}
			$wheres[] = '(' . $thisWhere . ')';
		}
		return "\nWHERE " . implode(' AND ', $wheres);
	}
	private function buildSqlLimit()
	{
		$out = '';
		if(!$this->filters['php'])
		{
			$limCount = count($this->range);
			if($limCount === 1)
			{
				$out = "\nLIMIT " . $this->range[0];
			}
			elseif($limCount === 2)
			{
				$out = "\nLIMIT " . $this->range[0] . ',' . $this->range[1];
			}
		}
		return $out;
	}
	private function autoFilterColumn($path, &$params)
	{
		if(substr($path, 0, 1) === '/')
		{
			$isPath = true;
			$path = substr($path, 1);
		}
		static $paramName = 'a';
		if($isPath || in_array($path, $this->columns, true))
		{
			return $this->pathSql($path);
		}
		elseif(substr($path, 0, 1) === ':')
		{
			return $path;
		}
		elseif(is_null($path))
		{
			return 'NULL';
		}
		else
		{
			$params[':' . $paramName] = $path;
			$out =  ':' . $paramName;
			++$paramName;
			return $out;
		}
	}
	private function pathSql($path, $transform = false)
	{

		$relPath = dirname($path);
		$rel = $this->relationships[$relPath];
		$tableId = $this->tableIds[$rel['fTable']][$relPath];
		$termSql = static::wrapTerm($tableId) . '.' . static::wrapTerm(basename($path));
		if($transform)
		{
			$termSql = $this->wrapTransforms($termSql, $path);
		}
		return $termSql;
	}
	private function wrapTransforms($termSql, $path)
	{
		$trans = $this->transforms['sql'][$path];
		foreach($trans as $funcName)
		{
			$termSql = $funcName . '(' . $termSql . ')';
		}
		return $termSql;
	}
	private static function wrapTerm($term)
	{
		str_replace('`', '``', $term);
		return '`' . $term . '`';
	}

/*
	PHP Helpers
*/
	private function filterSet($set, $params)
	{
		$out = [];
		$filters = $this->filters['php'];
		foreach($this->filters['php'] as $filter)
		{
			$args = array_map([$this, 'cleanPath'], $filter['args']);
			$args = array_map(function($val) use($params){
				$col = array_search($val, $this->columns, true);
				if($col !== false)
				{
					return ['type' => 'ref', 'val' => $col];
				}
				elseif(substr($val, 0, 1) === ':' && isset($params[$val]))
				{
					return ['type' => 'lit', 'val' => $params[$val]];
				}
				else
				{
					return ['type' => 'lit', 'val' => $val];
				}
			}, $args);
			foreach($set as $key => $item)
			{
				$rowArgs = [];
				foreach($args as $arg)
				{
					if($arg['type'] === 'ref')
					{
						$argCol = $arg['val'];
						$rowArgs[] = $item->$argCol;
					}
					else
					{
						$rowArgs[] = $arg['val'];
					}
				}
				if($filter['cmp'](...$rowArgs))
				{
					$out[] = $item;
				}
			}
		}
		return $out;
	}
	private function transformSet($set)
	{
		$trans = $this->transforms['php'];
		return array_map(function($item) use($trans){
			foreach($trans as $col => $transList)
			{
				foreach($transList as $transFunc)
				{
					$item->$col = $transFunc($item->$col);
				}
			}
			return $item;
		},$set);
	}
	private function orderSet($set)
	{
		$col = array_search($this->sort['path'], $this->columns, true);
		$sort = $this->sort['order'];
		usort($set, function($a, $b) use($col, $sort){
			return $sort($a->$col, $b->$col);
		});
		return $set;
	}
	private function limitSet($set)
	{
		if($this->filters['php'])
		{
			$countRange = count($this->range);
			if($countRange === 1)
			{
				$set = array_slice($set, 0, $this->range[0]);
			}
			elseif($countRange === 2)
			{
				$set = array_slice($set, $this->range[0], $this->range[1]);
			}
		}
		return $set;
	}
	private function groupSet($set)
	{
		$groups = $this->groups;
		if($currentGroup = reset($groups))
		{
			$set = static::buildGroupLayer($set, $groups);
		}
		return $set;
	}
	private static function buildGroupLayer($set, $groups)
	{
		 $groupedSet = static::makeIndexGroup($set, reset($groups));
		 
		 if($groupsNext = array_slice($groups, 1))
		 {
			  foreach($groupedSet as $key => $gs)
			  {
					$groupedSet[$key] = static::buildGroupLayer($gs, $groupsNext);
			  }
		 }
		 return $groupedSet;
	}
	private static function makeIndexGroup($set, $column)
	{
		$out = [];
		foreach($set as $item)
		{
			$out[$item->$column][] = $item;
		}
		return $out;
	}

/*
	Validity checking
*/
	private function colsValid()
	{
		$valid = true;
		foreach($this->columns as $col)
		{
			if(strpos($col, '/') !== false)
			{
				$valid = $valid && $this->isValidCol($col);
			}
		}
		return $valid;
	}
	private function isValidCol($colPath)
	{
		$valid = true;
		$currentPath = dirname($colPath);
		while($valid && $currentPath !== '' && $currentPath !== '/' && $currentPath !== '.')
		{
			$valid = $valid && isset($this->relationships[$currentPath]);
			$currentPath = dirname($currentPath);
		}
		return $valid;
	}
	private static function cleanPath($path)
	{
		return (substr($path, 0, 1) === '/') ? substr($path, 1) : $path;
	}

}