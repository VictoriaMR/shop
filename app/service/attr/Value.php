<?php 

namespace app\service\attr;
use app\service\Base;

class Value extends Base
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

	public function getListById($attvId, $lanId=1)
	{
		$list = $this->getListData(['attv_id'=>['in', $attvId]], 'attv_id,name', 0, 0, ['sort'=>'asc']);
		$lanArr = make('app/service/attr/ValueLanguage')->getListData(['attv_id'=>['in', $attvId], 'lan_id'=>$lanId], 'attv_id,name');
		$lanArr = array_column($lanArr, 'name', 'attv_id');
		foreach ($list as $key => $value) {
			if (!empty($lanArr[$value['attv_id']])) {
				$list[$key]['name'] = $lanArr[$value['attv_id']];
			}
		}
		return $list;
	}
}