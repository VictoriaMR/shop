<?php

namespace frame;

final class Query
{
	private $_database;
	private $_table = '';
	private $_sql = '';
	private $_where = [];
	private $_columns = '*';
	private $_groupBy = '';
	private $_orderBy = [];
	private $_having = [];
	private $_limit;
	private $_insert_id = 0;
	public $_addTime;
	public $_updateTime;
	public $_intFields = [];
	private $_intMap;

	public function setDb($database = null)
	{
		$this->_database = $database;
		return $this;
	}

	public function table($table = '')
	{
		$this->_table = $table;
		return $this;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function setIntFields(array $fields)
	{
		$this->_intFields = $fields;
		$this->_intMap = $fields ? array_flip($fields) : [];
		return $this;
	}

	public function getIntMap(): array
	{
		if ($this->_intMap === null) {
			$this->_intMap = $this->_intFields ? array_flip($this->_intFields) : [];
		}
		return $this->_intMap;
	}

	public function where($columns, $operator = null)
	{
		if (is_array($columns)) {
			foreach ($columns as $k => $v) {
				$this->_where[$k] = $v;
			}
		} else {
			$this->_where[$columns] = $operator;
		}
		return $this;
	}

	public function leftJoin($table, $linkForm, $linkTo)
	{
		$this->_table .= ' LEFT JOIN ' . $table . ' ON ' . $linkForm . ' = ' . $linkTo;
		return $this;
	}

	public function orderBy($columns, $operator = null)
	{
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) {
					$this->_orderBy[] = "FIELD(`{$key}`, '" . implode("', '", $value) . "')";
				} else {
					$this->_orderBy[] = '`' . $key . '` ' . strtoupper($value);
				}
			}
		} elseif ($columns) {
			$this->_orderBy[] = '`' . $columns . '` ' . strtoupper($operator);
		}
		return $this;
	}

	public function groupBy($columns)
	{
		if ($columns) {
			$this->_groupBy = '`' . $columns . '`';
		}
		return $this;
	}

	public function having($columns, $operator, $value)
	{
		$this->_having[$columns] = [$operator, $value];
		return $this;
	}

	public function field($columns)
	{
		if (is_string($columns)) {
			$columns = explode(',', $columns);
		}
		$parts = [];
		foreach ($columns as $col) {
			$col = trim($col);
			if ($col === '*' || strpbrk($col, '(). *') !== false) {
				$parts[] = $col;
			} else {
				$parts[] = '`' . $col . '`';
			}
		}
		$this->_columns = implode(',', $parts);
		return $this;
	}

	public function page(int $page, int $size)
	{
		if ($size > 0) {
			$this->_limit = [$page > 0 ? ($page - 1) * $size : 0, $size];
		}
		return $this;
	}

	public function get()
	{
		$params = [];
		$paramsType = '';
		$sql = 'SELECT ' . $this->_columns . ' FROM `' . $this->_table . '`';
		$whereString = $this->analyzeWhere($paramsType, $params);
		if ($whereString !== '') {
			$sql .= ' WHERE ' . $whereString;
		}
		if ($this->_groupBy !== '') {
			$sql .= ' GROUP BY ' . $this->_groupBy;
		}
		if ($this->_having) {
			$havingParts = [];
			foreach ($this->_having as $key => $value) {
				$havingParts[] = '`' . $key . '` ' . $value[0] . ' ?';
				$paramsType .= 's';
				$params[] = $value[1];
			}
			$sql .= ' HAVING ' . implode(' AND ', $havingParts);
		}
		if ($this->_orderBy) {
			$sql .= ' ORDER BY ' . implode(',', $this->_orderBy);
		}
		if ($this->_limit !== null) {
			$sql .= ' LIMIT ' . $this->_limit[0] . ',' . $this->_limit[1];
		}
		return $this->getQuery($sql, $paramsType ? array_merge([$paramsType], $params) : []);
	}

	public function find()
	{
		$this->_limit = [0, 1];
		$res = $this->get();
		return $res[0] ?? [];
	}

	public function value($name)
	{
		return $this->field($name)->find();
	}

	public function count($where = []): int
	{
		if ($where) {
			$this->where($where);
		}
		$this->_columns = 'COUNT(*) AS core_count';
		$res = $this->find();
		return (int)($res['core_count'] ?? 0);
	}

	public function max($field): int
	{
		$this->_columns = 'MAX(`' . $field . '`) AS core_max';
		$res = $this->find();
		return (int)($res['core_max'] ?? 0);
	}

	public function min($field): int
	{
		$this->_columns = 'MIN(`' . $field . '`) AS core_min';
		$res = $this->find();
		return (int)($res['core_min'] ?? 0);
	}

	public function sum($field): int
	{
		$this->_columns = 'SUM(`' . $field . '`) AS core_sum';
		$res = $this->find();
		return (int)($res['core_sum'] ?? 0);
	}

	public function insert(array $data)
	{
		if (!is_array(current($data))) {
			$data = [$data];
		}
		$intMap = $this->getIntMap();
		$nowTime = now();
		$fields = [];
		$paramsType = '';
		$params = [];
		$paramValue = '';
		$index = 0;
		foreach ($data as $value) {
			if (isset($intMap['site_id']) && !isset($value['site_id']) && !isAdmin()) {
				$value['site_id'] = siteId();
			}
			if ($this->_addTime) {
				$value[$this->_addTime] = $nowTime;
			}
			if ($index === 0) {
				foreach ($value as $k => $v) {
					$fields[] = '`' . $k . '`';
					$paramsType .= isset($intMap[$k]) ? 'i' : 's';
					$paramValue .= '?,';
				}
				$paramValue = '(' . rtrim($paramValue, ',') . '),';
			}
			foreach ($value as $v) {
				$params[] = $v;
			}
			$index++;
		}
		$paramsType = str_repeat($paramsType, $index);
		$paramValue = str_repeat($paramValue, $index);
		return $this->getQuery("INSERT INTO `" . $this->_table . "` (" . implode(',', $fields) . ") VALUES " . rtrim($paramValue, ','), array_merge([$paramsType], $params));
	}

	public function insertGetId(array $data)
	{
		$this->insert($data);
		return $this->_insert_id;
	}

	public function update(array $data)
	{
		$intMap = $this->getIntMap();
		$fields = [];
		$params = [];
		$paramsType = '';
		foreach ($data as $key => $value) {
			$fields[] = '`' . $key . '` = ?';
			$paramsType .= isset($intMap[$key]) ? 'i' : 's';
			$params[] = $value;
		}
		if ($this->_updateTime) {
			$fields[] = '`' . $this->_updateTime . '` = ?';
			$paramsType .= 's';
			$params[] = now();
		}
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('UPDATE `' . $this->_table . '` SET ' . implode(',', $fields) . ($whereString !== '' ? ' WHERE ' . $whereString : ''), array_merge([$paramsType], $params));
	}

	public function increment($value, int $num = 1) 
	{
		return $this->crement($value, $num, '+');
	}

	public function decrement($value, int $num = 1) 
	{
		return $this->crement($value, $num, '-');
	}

	private function crement($value, int $num, $operator) 
	{
		$params = [];
		$paramsType = '';
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('UPDATE `' . $this->_table . '` SET `' . $value . '` = `' . $value . '` ' . $operator . ' ' . $num . ($whereString !== '' ? ' WHERE ' . $whereString : ''), $paramsType ? array_merge([$paramsType], $params) : []);
	}

	public function delete()
	{
		if (empty($this->_where)) {
			return false;
		}
		$params = [];
		$paramsType = '';
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('DELETE FROM `' . $this->_table . '`' . ($whereString !== '' ? ' WHERE ' . $whereString : ''), array_merge([$paramsType], $params));
	}

	private function analyzeWhere(&$paramsType = '', array &$params = [])
	{
		$intMap = $this->getIntMap();
		if (isset($intMap['site_id']) && !isset($this->_where['site_id']) && !isAdmin()) {
			$this->_where['site_id'] = siteId();
		}
		if (!$this->_where) {
			return '';
		}
		$parts = [];
		foreach ($this->_where as $key => $item) {
			if (strpos($key, ',') !== false) {
				$cols = explode(',', $key);
				$orParts = [];
				$op = is_array($item) ? $item[0] : '=';
				$val = is_array($item) ? $item[1] : $item;
				foreach ($cols as $col) {
					$col = trim($col);
					$orParts[] = '`' . $col . '` ' . $op . ' ?';
					$paramsType .= isset($intMap[$col]) ? 'i' : 's';
					$params[] = $val;
				}
				$parts[] = '(' . implode(' OR ', $orParts) . ')';
				continue;
			}
			if (is_array($item)) {
				$keyName = strtoupper(trim($item[0]));
				switch ($keyName) {
					case 'BETWEEN':
						$parts[] = '`' . $key . '` BETWEEN ? AND ?';
						$paramsType .= 'ss';
						$params[] = $item[1][0];
						$params[] = $item[1][1];
						break;
					case 'IN':
					case 'NOT IN':
						$count = count($item[1]);
						$parts[] = '`' . $key . '` ' . $keyName . ' (' . rtrim(str_repeat('?,', $count), ',') . ')';
						$paramsType .= isset($intMap[$key]) ? str_repeat('i', $count) : str_repeat('s', $count);
						foreach ($item[1] as $v) {
							$params[] = $v;
						}
						break;
					default:
						$parts[] = '`' . $key . '` ' . $item[0] . ' ?';
						$paramsType .= isset($intMap[$key]) ? 'i' : 's';
						$params[] = $item[1];
						break;
				}
			} else {
				$parts[] = '`' . $key . '` = ?';
				$paramsType .= isset($intMap[$key]) ? 'i' : 's';
				$params[] = $item;
			}
		}
		return implode(' AND ', $parts);
	}

	public function sql()
	{
		return $this->_sql;
	}

	public function getQuery($sql, array $bindParams = [])
	{
		if (isDebug()) {
			$this->_sql = $sql;
			if (!isCli()) {
				\App::append('exec_sql', $sql);
			}
		}
		$mysqli = frame('Connection')->setDb($this->_database);
		$this->clearQuery();
		if (!$mysqli) {
			return false;
		}
		try {
			if (empty($bindParams)) {
				$result = $mysqli->query($sql);
				if ($result === false) {
					throw new \Exception('SQL query failed: ' . $mysqli->error . PHP_EOL . 'SQL: ' . $sql, $mysqli->errno);
				}
				if (is_bool($result)) {
					$this->_insert_id = $mysqli->insert_id;
					return $mysqli->affected_rows;
				}
				$return = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
				return $return;
			}

			$stmt = $mysqli->prepare($sql);
			if ($stmt === false) {
				throw new \Exception('SQL prepare failed: ' . $mysqli->error . PHP_EOL . 'SQL: ' . $sql, $mysqli->errno);
			}
			$stmt->bind_param(...$bindParams);
			$stmt->execute();
			$result = $stmt->get_result();
			if (is_bool($result)) {
				$this->_insert_id = $stmt->insert_id;	
				$return = $stmt->affected_rows;
			} else {
				$return = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
			}
			$stmt->close();
			return $return;
		} catch (\Throwable $e) {
			$error = ['SQL: ' . $sql, ', errno: ' . $e->getCode() . ', error: ' . $e->getMessage()];
			throw new \Exception(implode(PHP_EOL, $error), 1);
		}
	}

	/**
	 * 仅重置查询状态，不重置 Model 层元数据 (_addTime/_updateTime/_intFields/_intMap)
	 */
	private function clearQuery()
	{
		$this->_where = [];
		$this->_columns = '*';
		$this->_groupBy = '';
		$this->_orderBy = [];
		$this->_having = [];
		$this->_limit = null;
	}

	public function start() 
	{
		$mysqli = frame('Connection')->setDb($this->_database);
		return $mysqli ? $mysqli->begin_transaction() : false;
	}

	public function rollback()
	{
		$mysqli = frame('Connection')->setDb($this->_database);
		return $mysqli ? $mysqli->rollback() : false;
	}

	public function commit()
	{
		$mysqli = frame('Connection')->setDb($this->_database);
		return $mysqli ? $mysqli->commit() : false;
	}
}