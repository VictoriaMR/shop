<?php 

namespace app\service\attr;
use app\service\Base;

class ButeService extends Base
{
	const CACHE_KEY = 'product-attr:bute_cache:';

	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/Bute');
	}

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
		$name = trim($name);
		$info = $this->loadData(['name'=>$name], 'attr_id');
		if (!empty($info)) {
				return $info['attr_id'];
		}
		return $this->insertGetId(['name'=>$name]);
	}
}