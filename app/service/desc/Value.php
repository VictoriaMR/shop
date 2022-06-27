<?php 

namespace app\service\desc;
use app\service\Base;

class Value extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/desc/Value');
	}

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'descv_id,name');
		$list = array_column($list, 'descv_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'descv_id,name');
		return $list + array_column($tempArr, 'descv_id', 'name');
	}

	public function getListById($id, $lanId=1)
	{
		$list = $this->getListData(['descv_id'=>['in', $id]], 'descv_id,name');
		$lanArr = make('app/service/desc/ValueLanguage')->getListData(['descv_id'=>['in', $id], 'lan_id'=>$lanId], 'descv_id,name');
		$lanArr = array_column($lanArr, 'name', 'descv_id');
		foreach ($list as $key => $value) {
			if (isset($lanArr[$value['descv_id']])) {
				$list[$key]['name'] = $lanArr[$value['descv_id']];
			}
		}
		return $list;
	}
}