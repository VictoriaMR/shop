<?php

namespace app\model;

class Base
{
	protected $_instance;
	protected $_connect;
	protected $_table;
	protected $_primaryKey;

	private function instance()
	{
		if (is_null($this->_instance)) {
			$this->_instance = make('frame/Query');
		}
		$this->_instance->database($this->_connect);
		$this->_instance->table($this->_table);
		return $this->_instance;
	}

	public function loadData($where, $field='')
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$id];
		}
		return $this->instance()->where($where)->field($field)->find();
	}

	public function updateData($id, $data)
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$id];
		}
		return $this->instance()->where($where)->update($data);
	}

	public function deleteData($id)
	{
		if (!is_array($where)) {
			$where = [$this->_primaryKey => (int)$id];
		}
		return $this->instance()->where($this->_primaryKey, $id)->delete();
	}

	public function getCountData(array $where=[]) 
	{
		return $this->instance()->where($where)->count();
	}

	public function getListData(array $where=[], $fields=[], $page=0, $size=20)
	{
		return $this->instance()->where($where)->field($fields)->page($page, $size)->get();
	}

	public function __call($func, $arg)
	{
		return $this->instance()->$func(...$arg);
	}
}