<?php

namespace frame;

final class Query
{
	private $_database;
	private $_table;
	private $_sql='';
	private $_where=[];
	private $_columns='*';
	private $_groupBy='';
	private $_orderBy='';
	private $_having=[];
	private $_offset=0;
	private $_limit=1;
	private $_withSite=true;
	private $_insert_id=0;
	public $_addTime;
	public $_updateTime;
	public $_intFields=[];

	public function setDb($database=null)
	{
		$this->_database = $database;
		return $this;
	}

	public function table($table='')
	{
		$this->_table = $table;
		return $this;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function where($columns, $operator=null)
	{
		if (is_array($columns)) $this->_where += $columns;
		else $this->_where[$columns] = $operator;
		return $this;
	}

	public function leftJoin($table, $linkForm, $linkTo)
	{
		$this->_table .= ' LEFT JOIN '.$table.' ON '.$linkForm.' = '.$linkTo;
		return $this;
	}

	public function orderBy($columns, $operator=null)
	{
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) {
					$this->_orderBy .= "FIELD(`{$key}`, '".implode('", "', $value)."'),";
				} else {
					$this->_orderBy .= '`'.$key.'` '.strtoupper($value).',';
				}
			}
		} else {
			$this->_orderBy .= '`'.$columns.'` '.strtoupper($operator).',';
		}
		return $this;
	}

	public function groupBy($columns)
	{
		if ($columns) {
			$this->_groupBy .= '`'.$columns.'`';
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
		if (is_string($columns)) $columns = explode(',', $columns);
		$parts = [];
		foreach ($columns as $col) {
			$col = trim($col);
			if ($col === '*' || preg_match('/[\(\)\s\.\*]/', $col)) {
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
		$this->_offset = $page > 0 ? ($page-1) * $size : 0;
		$this->_limit = $size;
		return $this;
	}

	public function get()
	{
		$params = [];
		$paramsType = '';
		$sql = 'SELECT '.$this->_columns.' FROM `'.$this->_table.'`';
		$whereString = $this->analyzeWhere($paramsType, $params);
		$whereString && $sql .= ' WHERE ' . $whereString;
		$this->_groupBy && $sql .= ' GROUP BY ' . $this->_groupBy;
		$this->_orderBy && $sql .= ' ORDER BY ' . trim($this->_orderBy, ',');
		if ($this->_having) {
			$sql .= ' HAVING ';
			foreach ($this->_having as $key => $value) {
				$this->_having[$key] = ' `'.$key.'` '.$value[0].' ?';
				$paramsType .= 's';
				$params[] = $value[1];
			}
			$sql .= implode(' AND ', $this->_having);
		}
		$this->_limit && $sql .= ' LIMIT ' . $this->_offset . ',' . $this->_limit;
		return $this->getQuery($sql, $paramsType ? array_merge([$paramsType], $params) : []);
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
		return $this->where($where)->value('COUNT(*) AS core_count')['core_count'] ?? 0;
	}

	public function max($field):int
	{
		return $this->value('MAX(`'.$field.'`) AS core_max')['core_max'] ?? 0;
	}

	public function min($field):int
	{
		return $this->value('MIN(`'.$field.'`) AS core_min')['core_min'] ?? 0;
	}

	public function sum($field):int
	{
		return $this->value('SUM(`'.$field.'`) AS core_sum')['core_sum'] ?? 0;
	}

	public function insert(array $data)
	{
		if (!is_array(current($data))) $data = [$data];
		$intMap = $this->_intFields ? array_flip($this->_intFields) : [];
		$nowTime = now();
		$fields = [];
		$paramsType = '';
		$params = [];
		$paramValue = '';
		$index = 0;
		foreach ($data as $value) {
			if ($this->_withSite && isset($intMap['site_id'])) {
				$value['site_id'] = siteId();
			}
			$this->_addTime && $value[$this->_addTime] = $nowTime;
			if ($index == 0) {
				foreach ($value as $k=>$v) {
					$fields[] = '`'.$k.'`';
					$paramsType .= isset($intMap[$k]) ? 'i' : 's';
					$paramValue .= '?,';
				}
				$paramValue = '('.trim($paramValue, ',').'),';
			}
			$params = array_merge($params, array_values($value));
			$index++;
		}
		$paramsType = str_repeat($paramsType, $index);
		$paramValue = str_repeat($paramValue, $index);
		return $this->getQuery("INSERT INTO `".$this->_table."` (".implode(',', $fields).") VALUES ".trim($paramValue, ','), array_merge([$paramsType], $params));
	}

	public function insertGetId(array $data)
	{
		$this->insert($data);
		return $this->_insert_id;
	}

	public function update(array $data)
	{
		$intMap = $this->_intFields ? array_flip($this->_intFields) : [];
		$fields = [];
		$params = [];
		$paramsType = '';
		foreach ($data as $key => $value) {
			$fields[] = '`'.$key.'` = ?';
			$paramsType .= isset($intMap[$key]) ? 'i' : 's';
			$params[] = $value;
		}
		if ($this->_updateTime) {
			$fields[] = '`'.$this->_updateTime.'` = ?';
			$paramsType .= 's';
			$params[] = now();
		}
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('UPDATE `'.$this->_table.'` SET '.implode(',', $fields).' WHERE '.$whereString, array_merge([$paramsType], $params));
	}

	public function increment($value, int $num=1) 
	{
		return $this->crement($value, $num, '+');
	}

	public function decrement($value, int $num=1) 
	{
		return $this->crement($value, $num, '-');
	}

	private function crement($value, int $num, $operator) 
	{
		$params = [];
		$paramsType = '';
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('UPDATE `'.$this->_table.'` SET `'.$value.'` = `'.$value.'` '.$operator.' '.$num.' WHERE '.$whereString, $paramsType ? array_merge([$paramsType], $params) : []);
	}

	public function delete()
	{
		if (empty($this->_where)) {
			return false;
		}
		$params = [];
		$paramsType = '';
		$whereString = $this->analyzeWhere($paramsType, $params);
		return $this->getQuery('DELETE FROM `'.$this->_table.'` WHERE '.$whereString, array_merge([$paramsType], $params));
	}

	private function analyzeWhere(&$paramsType='', &$params=[])
	{
		$intMap = $this->_intFields ? array_flip($this->_intFields) : [];
		$this->_withSite && isset($intMap['site_id']) && $this->_where['site_id'] = siteId();
		$where = '';
		foreach ($this->_where as $key => $item) {
			$where .= ' AND `'.$key.'` ';
			if (is_array($item)) {
				$keyName = strtoupper(trim($item[0]));
				switch ($keyName) {
					case 'BETWEEN':
						$where .= 'BETWEEN ? AND ?';
						$paramsType .= 'ss';
						$params[] = $item[1][0];
						$params[] = $item[1][1];
						break;
					case 'IN':
					case 'NOT IN':
						$index = count($item[1]);
						$where .= $keyName.' ('.trim(str_repeat('?,', $index), ',').')';
						$paramsType .= isset($intMap[$key]) ? str_repeat('i', $index) : str_repeat('s', $index);
						$params = array_merge($params, $item[1]);
						break;
					default:
						$where .= $item[0].' ?';
						$paramsType .= isset($intMap[$key]) ? 'i' : 's';
						$params[] = $item[1];
						break;
				}
			} else {
				$where .= ' = ?';
				$paramsType .= isset($intMap[$key]) ? 'i' : 's';
				$params[] = $item;
			}
		}
		return ltrim($where, ' AND ') ? substr($where, 5) : '';
	}

	public function sql()
	{
		return $this->_sql;
	}

	public function getQuery($sql, $bindParams=[])
	{
		isDebug() && \App::append('exec_sql', $sql);
		$this->_sql = $sql;
		$mysqli = frame('Connection')->setDb($this->_database);
		$this->clearQuery();
		if (!$mysqli) {
			return false;
		}
		try {
			$stmt = $mysqli->prepare($sql);
			if ($stmt === false) {
				throw new \Exception('SQL prepare failed: ' . $mysqli->error . PHP_EOL . 'SQL: ' . $sql, $mysqli->errno);
			}
			if ($bindParams) {
				$stmt->bind_param(...$bindParams);
			}
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
		} catch (\Exception $e){
			$error[] = 'SQL: '.$sql;
			$error[] = ', errno: '.$e->getCode().', error: '.$e->getMessage();
		}
		throw new \Exception(implode(PHP_EOL, $error), 1);
	}

	/**
	 * 仅重置查询状态，不重置 Model 层元数据 (_addTime/_updateTime/_intFields)
	 */
	private function clearQuery()
	{
		$this->_sql='';
		$this->_where=[];
		$this->_columns='*';
		$this->_groupBy='';
		$this->_orderBy='';
		$this->_having=[];
		$this->_offset=0;
		$this->_limit=1;
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
		return $this->getQuery('COMMIT');
	}
}