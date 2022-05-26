<?php 

namespace app\service\desc;
use app\service\Base;

class Name extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/desc/Name');
	}

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'descn_id,name');
		$list = array_column($list, 'descn_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'descn_id,name');
		return array_merge($list, array_column($tempArr, 'descn_id', 'name'));
	}
}