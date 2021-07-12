<?php

namespace app\service;

class SystemStaticFile extends Base
{
	public function __construct()
	{
		$this->baseModel = make('app/model/SystemStaticFile');
	}

	public function addNotExist($name, $type)
	{
		if (empty($name) || empty($type)) {
			return false;
		}
		$name = explode('.', $name)[0];
		$where = ['name'=>$name, 'type'=>$type];
		if ($this->baseModel->getCountData($where)) {
			return $this->baseModel->where($where)->update(['status'=>0]);
		} else {
			return $this->baseModel->insert($where);
		}
	}
}