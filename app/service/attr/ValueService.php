<?php 

namespace app\service\attr;
use app\service\Base;

class ValueService extends Base
{
	const CACHE_KEY = 'product-attr:value_cache:';

	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/Value');
	}

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
		$name = trim($name);
		$info = $this->loadData(['name'=>$name], 'attv_id');
		if (!empty($info)) {
			return $info['attv_id'];
		}
		return $this->insertGetId(['name'=>$name]);
	}
}