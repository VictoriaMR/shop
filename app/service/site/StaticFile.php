<?php

namespace app\service\site;
use app\service\Base;

class StaticFile extends Base
{
	public function addNotExist($name, $type)
	{
		$name = explode('.', $name)[0];
		$where = ['name'=>$name, 'type'=>$type];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['status'=>0]);
		} else {
			return $this->insert($where);
		}
	}
}