<?php

namespace app\model;

class Base
{
	protected $_instance;
	protected $_connect;
	protected $_table;
	protected $_primaryKey;
	protected $_memId;
	protected $_lanId;
	protected $_currencyId;
	protected $_addTime;
	protected $_updateTime;
	protected $_intFields = [];

	private function instance()
	{
		if (is_null($this->_instance)) {
			$this->_instance = frame('Query');
		}
		$this->_instance->setDb($this->_connect);
		$this->_instance->table($this->_table);
		$this->_instance->setParam('_addTime', $this->_addTime);
		$this->_instance->setParam('_updateTime', $this->_updateTime);
		$this->_instance->setParam('_intFields', $this->_intFields);
		return $this->_instance;
	}

	public function getConst($name)
	{
		return constant(get_class($this).'::'.$name);
	}

	public function loadData($where, $field='', $orderBy=[])
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table.'_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->field($field)->orderBy($orderBy)->find();
	}

	public function updateData($where, $data)
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table.'_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->update($data);
	}

	public function deleteData($where)
	{
		if (!is_array($where)) {
			if (!$this->_primaryKey) {
				$this->_primaryKey = $this->_table.'_id';
			}
			$where = [$this->_primaryKey => $where];
		}
		return $this->instance()->where($where)->delete();
	}

	public function insertData($data)
	{
		return $this->instance()->insert($data);
	}

	public function getCountData(array $where=[]) 
	{
		return $this->instance()->where($where)->count();
	}

	public function getListData(array $where=[], $fields=[], $page=0, $size=20, $order=[], $group='')
	{
		return $this->instance()->where($where)->field($fields)->page($page, $size)->orderBy($order)->groupBy($group)->get();
	}

	public function sql()
	{
		return $this->instance()->sql();
	}

	public function userId()
	{
		if (!$this->_memId) {
			$this->_memId = userId();
		}
		return $this->_memId;
	}

	public function siteId()
	{
		if (!$this->_siteId) {
			$this->_siteId = siteId();
		}
		return $this->_siteId;
	}

	public function lanId()
	{
		if (!$this->_lanId) {
			$this->_lanId = lanId();
		}
		return $this->_lanId;
	}

	public function currencyId()
	{
		if (!$this->_currencyId) {
			$this->_currencyId = currencyId();
		}
		return $this->_currencyId;
	}

	public function __call($func, $arg)
	{
		return $this->instance()->$func(...$arg);
	}
}