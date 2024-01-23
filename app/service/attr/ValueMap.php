<?php 

namespace app\service\attr;
use app\service\Base;

class ValueMap extends Base
{
	public function getMapList($nameArr)
	{
		if (empty($nameArr)) {
			return false;
		}
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrv_id,name');
		if (!empty($list)) {
			$tmp = attr()->name()->getListData(['attrv_id'=>['in', array_column($list, 'attrv_id')]], 'attrv_id,name as attrv_name');
			$tmp = array_column($tmp, 'attrv_id', 'attrv_name');
			foreach($list as $key=>$value) {
				$list[$key]['attrv_name'] = $tmp[$value['attrv_id']] ?? '';
			}
		}
		return $list;
	}
}