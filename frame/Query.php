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
	private $_offset;
	private $_limit=1;

	public function database($database=null)
	{
		$this->_database = $database;
		return $this;
	}

	public function table($table = '')
	{
		$this->_table = $table;
		return $this;
	}

	public function setParam($name, $value)
	{
		$this->$name = $value;
		return $this;
	}

	public function where($columns, $operator=null, $value=null)
	{
		if (empty($columns)) return $this;
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) {
					if (!empty($value[1])) {
						$this->_where[] = [$key, $value[0], $value[1]];
					}
				} else {
					$this->_where[] = [$key, '=', $value];
				}
			}
		} else {
			if (is_null($value)) {
				$value = $operator;
				$operator = '=';
			}
			$this->_where[] = [$columns, $operator, $value];
		}
		return $this;
	}

	public function whereIn($column, $value=[])
	{
		return $this->where($column, 'IN', $value);
	}

	public function leftJoin($table, $linkForm, $linkTo)
	{
		$this->_table = sprintf('%s LEFT JOIN %s ON %s = %s', $this->_table, $table, $linkForm, $linkTo);
		return $this;
	}

	public function orderBy($columns, $operator=null)
	{
		if (empty($columns)) return $this;
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				$this->_orderBy .= $key.' '.strtoupper($value).',';
			}
		} else {
			$this->_orderBy .= $columns.' '.strtoupper($operator).',';
		}
		return $this;
	}

	public function groupBy($columns)
	{
		if (empty($columns)) return $this;
		$this->_groupBy .= trim($columns).',';
		return $this;
	}

	public function field($columns)
	{
		if (empty($columns)) return $this;
		$this->_columns .= trim($columns).',';
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

	public function value($name='')
	{
		if (!empty($name)) {
			$this->_columns = $name;
		}
		$info = $this->find();
		if (empty($info)) {
			return '';
		}
		if (empty($name)) {
			return $info;
		} else {
			return $info[$name] ?? '';
		}
	}

	public function count()
	{
		$this->_columns = 'COUNT(*) AS count';
		return $this->find()['count'] ?? 0;
	}

	public function insert(array $data=[])
	{
		if (empty($data)) return false;
		if (empty($this->_table)) {
			throw new \Exception('MySQL Error, no found table', 1);
		}
		if (!is_array(current($data))) $data = [$data];
		$insertTime = [];
		if (!empty($this->_addTime)) {
			$insertTime = explode(',', $this->_addTime);
		}
		if (!empty($this->_updateTime)) {
			$insertTime = array_merge($insertTime, explode(',', $this->_updateTime));
		}
		$fields = array_merge(array_keys(current($data)), $insertTime);
		$data = array_map(function($value) use ($insertTime){
			if (!empty($insertTime)) {
				foreach ($insertTime as $v) {
					$value[$v] = now();
				}
			}
			foreach ($value as $k => $v) {
				$value[$k] = "'".addslashes($v)."'";
			}
			return implode(',', $value);
		}, $data);
		$sql = sprintf('INSERT INTO `%s` (%s) VALUES %s', $this->_table, '`'.implode('`,`', $fields).'`', '(' . implode('), (', $data).')');
		return $this->getQuery($sql);
	}

	public function update(array $data=[])
	{
		if (empty($data)) return false;
		$tempArr = [];
		if (!empty($this->_updateTime)) {
			foreach(explode(',', $this->_updateTime) as $value) {
				$data[$value] = now();
			}
		}
		foreach ($data as $key => $value) {
			$tempArr[] = '`'.$key.'`'.'='."'".addslashes($value)."'";
		}
		$whereString = $this->analyzeWhere();
		if (!empty($whereString)){
			$sql = sprintf('UPDATE `%s` SET %s WHERE %s', $this->_table, implode(',', $tempArr), $whereString);
		} else{
			$sql = sprintf('UPDATE `%s` SET %s', $this->_table, implode(',', $tempArr));
		}
		return $this->getQuery($sql);
	}

	public function increment($value, $num=1) 
	{
		$whereString = $this->analyzeWhere();
		if (empty($whereString)) return false;
		$sql = sprintf('UPDATE `%s` SET %s WHERE %s', $this->_table, $value.'='.$value.' + '.$num, $whereString);
		return $this->getQuery($sql);
	}

	public function decrement($value, $num=1) 
	{
		$whereString = $this->analyzeWhere();
		if (empty($whereString)) return false;
		$sql = sprintf('UPDATE `%s` SET %s WHERE %s', $this->_table, $value.'='.$value.' - '.$num, $whereString);
		return $this->getQuery($sql);
	}

	public function insertGetId($data)
	{
		$result = $this->insert($data);
		if (!$result) return false;
		$result = $this->getQuery('SELECT LAST_INSERT_ID() AS last_insert_id');
		if (empty($result)) return false;
		return $result[0]['last_insert_id'] ?? false;
	}

	public function delete()
	{
		$whereString = $this->analyzeWhere();
		if (!empty($whereString)){
			$sql = sprintf('DELETE FROM %s WHERE %s', $this->_table, $whereString);
		} else{
			$sql = sprintf('TRUNCATE TABLE %s', $this->_table);
		}
		return $this->getQuery($sql);
	}

	private function getSql()
	{
		if (empty($this->_table)) {
			throw new \Exception('MySQL Error, table not exist!', 1);
		}
		$whereString = $this->analyzeWhere();
		$sql = sprintf('SELECT %s FROM `%s`', empty($this->_columns) ? '*' : rtrim($this->_columns, ','), $this->_table);
		if (!empty($whereString)) {
			$sql .= ' WHERE ' . $whereString;
		}
		if (!empty($this->_groupBy)) {
			$sql .= ' GROUP BY ' . rtrim($this->_groupBy, ',');
		}
		if (!empty($this->_orderBy)) {
			$sql .= ' ORDER BY ' . rtrim($this->_orderBy, ',');
		}
		if (!is_null($this->_offset)) {
			$sql .= ' LIMIT ' . $this->_offset;
			$sql .= ',' . $this->_limit;
		}
		return $sql;
	}

	private function analyzeWhere()
	{
		if (empty($this->_where)) return false;
		$whereString = '';
		foreach ($this->_where as $item) {
			$fields = explode(',', $item[0]);
			$operator = strtoupper($item[1]);
			$value = $item[2];
			$start = '';
			$end = '';
			if (count($fields) > 1) {
				$start = ' AND (';
				$type = ' OR';
				$end = ')';
			} else {
				$start = ' AND ';
				$type = ' AND';
			}
			$tempStr = '';
			foreach ($fields as $fk => $fv) {
				$fv = trim($fv);
				if ($operator == 'IN' || $operator == 'NOT IN') {
					$valueStr = '';
					foreach ($value as $v) {
						if (is_string($v)) {
							$valueStr .= "'".addslashes($v)."',";
						} else {
							$valueStr .= (int)$v;
							$valueStr .= ',';
						}
					}

					$tempStr .= sprintf('%s %s %s (%s)', $fk == 0 ? '' : $type, $fv, $operator, rtrim($valueStr, ','));
				} else {
					$value = is_string($value) ? "'".addslashes($value)."'" : (int) $value;
					$tempStr .= sprintf('%s %s %s %s', $fk == 0 ? '' : $type, $fv, $operator, $value);
				}
			}
			$whereString .= $start.$tempStr.$end;
		}
		if (empty($whereString)) return '';
		return ltrim(trim($whereString), 'AND ');
	}

	public function getQuery($sql)
	{
		$this->clear();
		if (env('APP_DEBUG')) {
			$GLOBALS['exec_sql'][] = $sql;
		}
		$conn = db($this->_database);
		if ($stmt = $conn->query($sql)) {
			if (is_bool($stmt)) {
				return $stmt;
			}
			while ($row = $stmt->fetch_assoc()){
			 	$returnData[] = $row;
			}
			$stmt->free();
		} else {
			throw new \Exception($sql.' '.$conn->error, 1);
		}
		return $returnData ?? null;
	}

	private function clear()
	{
		$this->_table = '';
		$this->_columns = '';
		$this->_where = [];
		$this->_groupBy = '';
		$this->_orderBy = '';
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