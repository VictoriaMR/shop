<?php

namespace app\model;

class Base
{
	protected $_instance;
	protected $_connect;
	protected $_table;
	protected $_primaryKey;
	protected $_memId;
	protected $_siteId;

	private function instance()
	{
		if (is_null($this->_instance)) {
			$this->_instance = make('frame/Query');
		}
		$this->_instance->database($this->_connect);
		$this->_instance->table($this->_table);
		return $this->_instance;
	}

	public function getConst($name)
	{
		return constant(get_class($this).'::'.$name);
	}

	public function loadData($where, $field='', $orderBy=[])
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$where];
		}
		return $this->instance()->where($where)->field($field)->orderBy($orderBy)->find();
	}

	public function updateData($where, $data)
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$where];
		}
		return $this->instance()->where($where)->update($data);
	}

	public function deleteData($where)
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$where];
		}
		return $this->instance()->where($where)->delete();
	}

	public function getCountData(array $where=[]) 
	{
		return $this->instance()->where($where)->count();
	}

	public function getListData(array $where=[], $fields=[], $page=0, $size=20, $order=[])
	{
		return $this->instance()->where($where)->field($fields)->page($page, $size)->orderBy($order)->get();
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

	public function __call($func, $arg)
	{
		return $this->instance()->$func(...$arg);
	}
}