<?php

namespace app\service;

class SystemStaticFile extends Base
{
	protected function getModel()
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
		if ($this->getCountData($where)) {
			return $this->where($where)->update(['status'=>0]);
		} else {
			return $this->insert($where);
		}
	}
}