<?php

namespace frame;

final class Query
{
	private $_database;
	private $_table;
	private $_columns;
	private $_where;
	private $_groupBy='';
	private $_orderBy='';
	private $_having='';
	private $_offset;
	private $_limit=1;
	private $_sql='';
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

	public function where($columns, $operator=null, $val1=null, $val2=null)
	{
		if ($this->_withSiteId) {
			$this->_where[] = ['site_id', '=', siteId()];
		}
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) $this->_where[] = isset($value[2]) ? [$key, strtoupper($value[0]), $value[1], $value[2]] : [$key, strtoupper($value[0]), $value[1]];
				else $this->_where[] = [$key, '=', $value];
			}
		} else {
			if (is_null($value)) $this->_where[] = [$columns, '=', $operator];
			else $this->_where[] = [$columns, $operator, $value];
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
				$this->_orderBy .= $this->formatKey($key).' '.strtoupper($value).',';
			}
		} else $this->_orderBy .= $this->formatKey($columns).' '.strtoupper($operator).',';
		return $this;
	}

	public function groupBy($columns)
	{
		$this->_groupBy .= $this->formatKey($columns);
		return $this;
	}

	public function having($columns, $operator, $value)
	{
		$this->_having = $this->formatKey($columns).' '.$operator.' '.$value;
		return $this;
	}

	public function field($columns)
	{
		$this->_columns .= is_array($columns) ? implode(',', $columns) : $columns;
		return $this;
	}

	public function page($page, $size)
	{
		if ($page >= 1) {
			$this->_offset = ($page-1)*$size;
			$this->_limit = (int)$size;
		}
		return $this;
	}

	public function get()
	{
		return $this->getQuery($this->getSql()) ?? [];
	}

	public function find()
	{
		$this->_offset = 0;
		$this->_limit = 1;
		return $this->get()[0] ?? [];
	}

	public function value($name)
	{
		$this->_columns = $name;
		return $this->find();
	}

	public function count()
	{
		return $this->value('COUNT(*) AS core_count')['core_count'] + 0;
	}

	public function max($field)
	{
		return $this->value('MAX('.$this->formatKey($field).') AS core_max')['core_max']+0;
	}

	public function min($field)
	{
		return $this->value('MIN('.$this->formatKey($field).') AS core_min')['core_min']+0;
	}

	public function sum($field)
	{
		return $this->value('SUM('.$this->formatKey($field).') AS core_sum')['core_sum']+0;
	}

	public function insert(array $data)
	{
		if (empty($data)) return false;
		if (!is_array(current($data))) $data = [$data];
		$insertTime = [];
		if (!empty($this->_addTime)) {
			$insertTime = explode(',', $this->_addTime);
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
		$key = trim($key);
		if (in_array($key, $this->_specialKey)) return '`'.$key.'`';
		return $key;
	}

	protected function formatValue($key, $value)
	{
		$value = is_array($value) ? :trim($value);
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
		$whereString = $this->analyzeWhere();
		$sql = sprintf('SELECT %s FROM %s', $this->_columns ? $this->_columns : '*', $this->_table);
		if ($whereString) $sql .= ' WHERE ' . $whereString;
		if ($this->_groupBy) $sql .= ' GROUP BY ' . $this->_groupBy;
		if ($this->_orderBy) $sql .= ' ORDER BY ' . trim($this->_orderBy, ',');
		if ($this->_offset) $sql .= ' LIMIT ' . $this->_offset . ',' . $this->_limit;
		if ($this->_having) $sql .= ' HAVING ' . $this->_having;
		return $sql;
	}

	private function analyzeWhere()
	{
		if (empty($this->_where)) return '';
		$where  = '1=1';
		foreach ($this->_where as $item) {
			$where .= ' AND '.$this->formatKey($item[0]).' '.$item[1].' ';
			if ($item[1] == 'BETWEEN') {
				if (!isset($item[3])) throw new \Exception('sql error: Sql where BETWEEN value error, must have the param 2', 1);
				$where .= sprintf('%s AND %s', $this->formatValue($item[0], $item[2]), $this->formatValue($item[0], $item[3]));
			} else {
				if (is_array($item[2])) {
					$value = [];
					foreach ($item[2] as $v) {
						$value[] = $this->formatValue($item[0], $v);
					}
					$where .= '('.implode(',', $value).')';
				} else $where .= $this->formatValue($item[0], $item[2]);
			}
		}
		return $where;
	}

	public function sql()
	{
		return $this->_sql;
	}

	public function getQuery($sql)
	{
		$this->clear();
		if (isDebug()) $GLOBALS['exec_sql'][] = $sql;
		$this->_sql = $sql;
		$mysqli = make('frame/Connection')->setDb($this->_database);
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
			} else {
				$error = [];
				foreach ($mysqli->error_list as $value) {
					$error[] = 'SQL: '.$sql;
					$error[] = sprintf('errno: %s, sqlstate: %s, error: %s', $value['errno'], $value['sqlstate'], $value['error']);
				}
				throw new \Exception(implode(PHP_EOL, $error), 1);
			}
		} catch (\Exception $e){
			$error = '';
			if ($mysqli) {
				foreach ($mysqli->error_list as $value) {
					$error.= 'SQL: '.$sql.sprintf(', errno: %s, sqlstate: %s, error: %s', $value['errno'], $value['sqlstate'], $value['error']).PHP_EOL;
				}
			} else {
				$error.= 'SQL: '.$sql.sprintf(', errno: %s, error: %s', $e->getCode(), $e->getMessage()).PHP_EOL;
			}
			throw new \Exception($error, 1);
		}
	}

	private function clear()
	{
		$this->_columns = '';
		$this->_where = [];
		$this->_groupBy = '';
		$this->_orderBy = '';
		$this->_having = '';
		$this->_offset = null;
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