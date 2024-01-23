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
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrn_id,name');
		if (!empty($list)) {
			$tmp = attr()->name()->getListData(['attrn_id'=>['in', array_column($list, 'attrn_id')]], 'attrn_id,name as attrn_name');
			$tmp = array_column($tmp, 'attrn_id', 'attrn_name');
			foreach($list as $key=>$value) {
				$list[$key]['attrn_name'] = $tmp[$value['attrn_id']] ?? '';
			}
		}
		return $list;
	}
}