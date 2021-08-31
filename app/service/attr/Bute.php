<?php 

namespace app\service\attr;
use app\service\Base;

class Bute extends Base
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

	public function getListById($attrId, $lanId=1)
	{
		$list = $this->getListData(['attr_id'=>['in', $attrId]], 'attr_id,name', 0, 0, ['sort'=>'asc']);
		$lanArr = make('app/service/attr/ButeLanguage')->getListData(['attr_id'=>['in', $attrId], 'lan_id'=>$lanId], 'attr_id,name');
		$lanArr = array_column($lanArr, 'name', 'attr_id');
		foreach ($list as $key => $value) {
			if (!empty($lanArr[$value['attr_id']])) {
				$list[$key]['name'] = $lanArr[$value['attr_id']];
			}
		}
		return $list;
	}
}