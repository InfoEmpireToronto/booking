<?php
function has_keys($array, $keys)
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
use Booking\Records\Factory;
$request = $_REQUEST;
$request = array_map('urldecode', $request);

$out = [];
if(has_keys($request, ['type','site_id']))
{
	require('../../basicsite/init.php');
	$tableName = $request['type'];
	$site_id = $request['site_id'];
	$tableName = str_replace('`', '``', $tableName);

	$isSearch = has_keys($request, ['needle', 'haystack']);

	if(isset($request['count']))
	{
		$query = "SELECT COUNT(*)
					FROM `{$tableName}`";
		$wheres = [
			'`site_id` = :siteid'
		];
		$params = [
			':siteid' => $site_id
		];
		if($request['type'] !== 'gallery')
		{
			$wheres[] = '`status` = :status';
			$params[':status'] = 1;
		}
		if($isSearch)
		{
			$matchQuery = 'MATCH(';
			$columns = [];
			$haystacks = explode(',', $request['haystack']);
			foreach($haystacks as $col)
			{
				$columns[] = '`' . $col . '`';
			}

			$needle = ':needle';
			$params[$needle] = $request['needle'];
			$matchQuery .= implode(',', $columns) .') AGAINST (' .
				$needle. ' IN BOOLEAN MODE)';
			$wheres[] = $matchQuery;
		}
		if($wheres)
		{
			$query .= 'WHERE ' . implode(' AND ', $wheres);
		}
		$count = $db->queryColumn($query, $params);
		$out['count'] = $count;
	}
	elseif(isset($request['id']))
	{
		$out['post'] = $db->getRow($tableName, ['id' => $request['id']]);
		$out['post']->date_utc = strtotime($out['post']->date_display);
		$next = $db->composeSet($tableName)
			->cols('/title', '/id')
			->filter('=', '/status', 1)
			->filter('>', '/date_display', $out['post']->date)
			->order('/date_display', 'ASC')
			->range(0, 1)
			->get();
		if($next[0])
		{
			$out['post']->next = $next[0];
		}
		
		$previous = $db->composeSet($tableName)
			->cols('/title', '/id')
			->filter('=', '/status', 1)
			->filter('<', '/date_display', $out['post']->date)
			->order('/date_display', 'DESC')
			->range(0, 1)
			->get();
		if($previous[0])
		{
			$out['post']->previous = $previous[0];
		}
	}
	else
	{
		$set = $db->composeSet($tableName)
			->filter('=', '/site_id', $site_id);
		if($request['type'] === 'news')
		{
			$set->cols(['/id', '/category', '/title', '/content', '/meta_title', '/meta_description', '/date_display' => 'date_utc', '/status'])
					->transform('/date_display', 'UNIX_TIMESTAMP')
					->filter('=', '/status', 1);
		}
		elseif($request['type'] === 'faq')
		{
			$set->cols('/id', '/category', '/question', '/answer', '/date_created', '/date_display', '/status')
					->filter('=', '/status', 1);
		}
		elseif($request['type'] === 'gallery')
		{
			$set->cols('/id', '/filename', '/create', '/description');
		}
		if(!$isSearch)
		{
			if($request['type'] === 'news' || $request['type'] === 'faq')
			{
				$set->order('/date_display', 'DESC');
			}
			elseif($request['type'] === 'gallery')
			{
				$set->order('/create', 'DESC');
			}
		}

		if($request['limit'])
		{
			$limits = explode(',', $request['limit']);
			if($limits[1])
			{
				$start = (int)$limits[0];
				$num = (int)$limits[1];
			}
			elseif($limits[0])
			{
				$start = 0;
				$num = (int)$limits[0];
			}
			$set->range($start, $num);
		}

		if($request['group'])
		{
			$groups = explode(',', $request['group']);
			$set->group($groups);
		}

		if($isSearch)
		{
			$cols = explode(',', $request['haystack']);
			$set->search($request['needle'], $cols);
		}
		$dbg = $set->preview();
		error_log(print_r($dbg, true));
		$setList = $set->get();
		$out['results'] = $setList;
	}
}
header("Content-type: application/json");
echo json_encode($out);