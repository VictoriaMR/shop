<?php 

namespace app\service\attr;
use app\service\Base;

class NameMap extends Base
{
	public function getMapList($nameArr)
	{
		if (empty($nameArr)) {
			return false;
		}
		// 首先获取属性名中是否有相同的名称
		$list = attr()->name()->getListData(['name'=>['in', $nameArr]], 'attrn_id,name');
		$list = array_column($list, null, 'name');
		$nameArr = array_diff($nameArr, array_keys($list));
		if (empty($nameArr)) {
			return $list;
		}
		$tmp = $this->getListData(['name'=>['in', $nameArr]], 'attrn_id,name');
		if (!empty($tmp)) {
			$tmp = array_column($tmp, null, 'name');
			$tmpList = attr()->name()->getListData(['attrn_id'=>['in', array_column($tmp, 'attrn_id')]], 'attrn_id,name as attrn_name');
			$tmpList = array_column($tmpList, null, 'attrn_id');
			foreach ($tmp as $key=>$value) {
				$tmp[$key] += $tmpList[$value['attrn_id']] ?? [];
			}
			$list += $tmp;
		}
		return $list;
	}

	public function addMap($fromName, $toName)
	{
		if ($this->getCountData(['name'=>$fromName])) {
			return true;
		}
		$list = attr()->name()->addNotExist($toName);
		$data = [
			'attrn_id' => $list[$toName],
			'name' => $fromName,
		];
		return $this->insertData($data);
	}
}