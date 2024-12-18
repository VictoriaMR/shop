<?php

namespace frame;

final class Query
{
	private $_database;
	private $_table;
	private $_where=[];
	private $_columns='*';
	private $_groupBy='';
	private $_orderBy='';
	private $_having='';
	private $_offset=0;
	private $_limit=1;
	private $_sql='';
	private $_withSite = true;
	private $_specialKey = ['status', 'name', 'order', 'system', 'type', 'rank', 'show', 'commit'];

	public function setDb($database=null)
	{
		$this->_database = $database;
		return $this;
	}

	public function table($table = '')
	{
		$this->_table = $this->formatKey($table);
		return $this;
	}

	public function setParam($name, $value)
	{
		$this->$name = $value;
		return $this;
	}

	public function where($columns, $operator=null)
	{
		if ($operator) {
			$this->_where[$columns] = $operator;
		} else {
			$this->_where = array_merge($this->_where, $columns);
		}
		return $this;
	}

	public function leftJoin($table, $linkForm, $linkTo)
	{
		$this->_table = sprintf('%s LEFT JOIN %s ON %s = %s', $this->_table, $this->formatKey($table), $linkForm, $linkTo);
		return $this;
	}

	public function orderBy($columns, $operator=null)
	{
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) {
					$this->_orderBy .= 'FIELD('.$this->formatKey($key).', '.implode(',', $value).'),';
				} else {
					$this->_orderBy .= $this->formatKey($key).' '.strtoupper($value).',';
				}
			}
		} else {
			$this->_orderBy .= $this->formatKey($columns).' '.strtoupper($operator).',';
		}
		return $this;
	}

	public function groupBy($columns)
	{
		$columns && $this->_groupBy .= $this->formatKey($columns);
		return $this;
	}

	public function having($columns, $operator, $value)
	{
		$this->_having = $this->formatKey($columns).' '.$operator.' '.$value;
		return $this;
	}

	public function field($columns)
	{
		$columns && $this->_columns = is_array($columns) ? implode(',', $columns) : $columns;
		return $this;
	}

	public function page($page, $size)
	{
		$this->_offset = $page > 0 ? ($page-1) * $size : 0;
		$this->_limit = (int)$size;
		return $this;
	}

	public function get()
	{
		return $this->getQuery($this->getSql()) ?? [];
	}

	public function find()
	{
		$this->page(0, 1);
		return $this->get()[0] ?? [];
	}

	public function value($name)
	{
		return $this->field($name)->find();
	}

	public function count($where=[]):int
	{
		return $this->where($where)->value('COUNT(*) AS core_count')['core_count'];
	}

	public function max($field):int
	{
		return $this->value('MAX('.$this->formatKey($field).') AS core_max')['core_max'];
	}

	public function min($field):int
	{
		return $this->value('MIN('.$this->formatKey($field).') AS core_min')['core_min'];
	}

	public function sum($field):int
	{
		return $this->value('SUM('.$this->formatKey($field).') AS core_sum')['core_sum'];
	}

	public function insert(array $data)
	{
		if (empty($data)) return false;
		if (!is_array(current($data))) $data = [$data];
		$insertTime = [];
		if (!empty($this->_addTime)) {
			$insertTime = explode(',', $this->_addTime);
		}
		foreach ($data as $key=>$value) {
			if (in_array('site_id', $this->_intFields) && !isset($value['site_id'])) {
				$data[$key]['site_id'] = siteId();
			}
		}
		$fields = array_merge(array_keys(current($data)), $insertTime);
		foreach ($fields as $key => $value) {
			$fields[$key] = $this->formatKey($value);
		}
		$data = array_map(function($value) use ($insertTime){
			foreach ($value as $k => $v) {
				$value[$k] = $this->formatValue($k, $v);
			}
			foreach ($insertTime as $v) {
				$value[$v] = $this->formatValue($v, now());
			}
			return implode(',', $value);
		}, $data);
		$sql = sprintf('INSERT INTO %s (%s) VALUES %s', $this->_table, implode(',', $fields), '(' . implode('), (', $data).')');
		return $this->getQuery($sql);
	}

	public function update(array $data, $returnSql=false)
	{
		if (empty($data)) return false;
		$tempArr = [];
		foreach ($data as $key => $value) {
			$tempArr[] = $this->formatKey($key).' = '.$this->formatValue($key, $value);
		}
		if (!empty($this->_updateTime)) {
			foreach(explode(',', $this->_updateTime) as $value) {
				$tempArr[] = $this->formatKey($value).' = '.$this->formatValue($value, now());
			}
		}
		$whereString = $this->analyzeWhere();
		$sql = sprintf('UPDATE %s SET %s WHERE %s', $this->_table, implode(',', $tempArr), $whereString);
		if ($returnSql) return $sql;
		return $this->getQuery($sql);
	}

	protected function formatKey($key)
	{
		return '`'.trim($key).'`';
	}

	protected function formatValue($key, $value)
	{
		if (in_array($key, $this->_intFields)) return (int)$value;
		return "'".addslashes($value)."'";
	}

	public function increment($value, $num=1) 
	{
		$whereString = $this->analyzeWhere();
		if (empty($whereString)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE %s', $this->_table, $this->formatKey($value).' = '.$this->formatKey($value).' + '.$num, $whereString);
		return $this->getQuery($sql);
	}

	public function decrement($value, $num=1) 
	{
		$whereString = $this->analyzeWhere();
		if (empty($whereString)) return false;
		$sql = sprintf('UPDATE %s SET %s WHERE %s', $this->_table, $this->formatKey($value).' = '.$this->formatKey($value).' - '.$num, $whereString);
		return $this->getQuery($sql);
	}

	public function insertGetId($data)
	{
		if (!$this->insert($data)) return false;
		$result = $this->getQuery('SELECT LAST_INSERT_ID() AS last_insert_id');
		if (empty($result)) return false;
		return $result[0]['last_insert_id'] ?? false;
	}

	public function delete()
	{
		$sql = sprintf('DELETE FROM %s WHERE %s', $this->_table, $this->analyzeWhere());
		return $this->getQuery($sql);
	}

	private function getSql()
	{
		$sql = 'SELECT '.$this->_columns.' FROM '.$this->_table;
		$whereString = $this->analyzeWhere();
		$whereString && $sql .= ' WHERE ' . $whereString;
		$this->_groupBy && $sql .= ' GROUP BY ' . $this->_groupBy;
		$this->_orderBy && $sql .= ' ORDER BY ' . trim($this->_orderBy, ',');
		$this->_limit && $sql .= ' LIMIT ' . $this->_offset . ',' . $this->_limit;
		$this->_having && $sql .= ' HAVING ' . $this->_having;
		return $sql;
	}

	private function analyzeWhere()
	{
		if (in_array('site_id', $this->_intFields) && !isset($this->_where['site_id']) && $this->_withSite) {
			$this->_where['site_id'] = config('domain', 'site_id');
		}
		$where = '';
		foreach ($this->_where as $key => $item) {
			$where .= ' AND '.$this->formatKey($key).' ';
			if (is_array($item)) {
				$keyName = strtoupper(trim($item[0]));
				switch ($keyName) {
					case 'BETWEEN':
						if (empty($item[1]) || count($item[1]) != 2) {
							throw new \Exception('SQL WHERE '.$key.' BETWEEN VALUE ERROR', 1);
						}
						$where .= sprintf('BETWEEN %s AND %s', $this->formatValue($key, $item[1][0]), $this->formatValue($key, $item[1][1]));
						break;
					case 'IN':
					case 'NOT IN':
						if (empty($item[1])) {
							throw new \Exception('SQL WHERE '.$key.' '.$keyName.' EMPTY VALUE', 1);
						}
						$value = [];
						foreach ($item[1] as $v) {
							$value[] = $this->formatValue($key, $v);
						}
						$where .= strtoupper($item[0]).' ('.implode(',', $value).')';
						break;
					default:
						$where .= $item[0].' '.$this->formatValue($key, $item[1]);
						break;
				}
			} else {
				$where .= '= '.$this->formatValue($key, $item);
			}
		}
		return trim($where, ' AND ');
	}

	public function sql()
	{
		return $this->_sql;
	}

	public function getQuery($sql)
	{
		if (isDebug()) $GLOBALS['exec_sql'][] = $sql;
		$this->_sql = $sql;
		$mysqli = frame('Connection')->setDb($this->_database);
		$this->clear();
		if (!$mysqli) {
			return false;
		}
		try {
			$result = $mysqli->query($sql);
			if ($mysqli->errno==0) {
				if (is_bool($result)) return $result;
				$returnData = [];
				while ($row = $result->fetch_assoc()){
				 	$returnData[] = $row;
				}
				$result->free();
				return $returnData;
			}
			$error[] = 'SQL: '.$sql;
		} catch (\Exception $e){
			$error[] = 'SQL: '.$sql;
			$error[] = sprintf(', errno: %s, error: %s', $e->getCode(), $e->getMessage());
		}
		foreach ($mysqli->error_list as $value) {
			$error[] = sprintf('errno: %s, sqlstate: %s, error: %s', $value['errno'], $value['sqlstate'], $value['error']);
		}
		throw new \Exception(implode(PHP_EOL, $error), 1);
	}

	private function clear()
	{
		$this->_where = [];
		$this->_columns = '*';
		$this->_groupBy = '';
		$this->_orderBy = '';
		$this->_having = '';
		$this->_offset = 0;
		$this->_limit = 1;
	}

	public function start() 
	{
		return $this->getQuery('START TRANSACTION');
	}

	public function rollback()
	{
		return $this->getQuery('ROLLBACK');
	}

	public function commit()
	{
		return $this->getQuery('commit');
	}
}