<?php

namespace app\model;

class Base
{
	protected ?\frame\Query $_instance = null;
	protected $_connect;
	protected $_table;
	protected $_primaryKey;
	protected $_memId;
	protected $_siteId;
	protected $_lanId;
	protected $_currencyId;
	protected $_addTime;
	protected $_updateTime;
	protected $_intFields = [];

	private function instance(): \frame\Query
	{
		if ($this->_instance === null) {
			$this->_instance = new \frame\Query();
			$this->_instance->setDb($this->_connect);
			$this->_instance->table($this->_table);
			$this->_instance->_addTime = $this->_addTime;
			$this->_instance->_updateTime = $this->_updateTime;
			$this->_instance->setIntFields($this->_intFields);
		}
		return $this->_instance;
	}

	public function getConst(string $name)
	{
		return constant(static::class . '::' . $name);
	}

	public function loadData($where, $field = '*', $orderBy = [])
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table . '_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->field($field)->orderBy($orderBy)->find();
	}

	public function updateData($where, $data)
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table . '_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->update($data);
	}

	public function deleteData($where)
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table . '_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->delete();
	}

	public function insertData($data, $returnId = false)
	{
		return $returnId ? $this->instance()->insertGetId($data) : $this->instance()->insert($data);
	}

	public function getCountData(array $where = []): int
	{
		return $this->instance()->where($where)->count();
	}

	public function getListData(array $where = [], $field = '*', $page = 0, $size = 0, $order = [], $group = '')
	{
		return $this->instance()->where($where)->field($field)->page((int)$page, (int)$size)->orderBy($order)->groupBy($group)->get();
	}

	public function sql()
	{
		return $this->instance()->sql();
	}

	public function userId()
	{
		return $this->_memId ??= userId();
	}

	public function siteId()
	{
		return $this->_siteId ??= siteId();
	}

	public function lanId()
	{
		return $this->_lanId ??= lanId();
	}

	public function currencyId()
	{
		return $this->_currencyId ??= currencyId();
	}

	public function __call($func, $arg)
	{
		return $this->instance()->$func(...$arg);
	}
}
