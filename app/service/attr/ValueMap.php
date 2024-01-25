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
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrv_id,name,ext');
		if (!empty($list)) {
			$attvId = array_column($list, 'attrv_id');
			$attnId = [];
			foreach ($list as $value) {
				if (!empty($value['ext'])) {
					$tmp = json_decode($value['ext'], true);
					$attnId = array_merge($attnId, array_keys($tmp));
					$attvId = array_merge($attvId, array_values($tmp));
				}
			}
			if (!empty($attvId)) {
				$attnId = attr()->name()->getListData(['attrn_id'=>['in', $attnId]], 'attrn_id,name');
				$attnId = array_column($attnId, 'name', 'attrn_id');
			}
			$attvId = attr()->value()->getListData(['attrv_id'=>['in', $attvId]], 'attrv_id,name');
			$attvId = array_column($attvId, 'name', 'attrv_id');
			foreach($list as $key=>$value) {
				$value['attrv_name'] = $attvId[$value['attrv_id']];
				$tmp = $value['ext'] ? json_decode($value['ext'], true) : [];
				$value['ext'] = [];
				foreach ($tmp as $k=>$v) {
					if (isset($attnId[$k]) && isset($attvId[$v])) {
						$value['ext'][$attnId[$k]] = $attvId[$v];
					}
				}
				$list[$key] = $value;
			}
		}
		return array_column($list, null, 'name');
	}

	public function addMap($fromName, $toName, $attr=[])
	{
		$info = $this->loadData(['name'=>$fromName]);
		if (!$info) {
			$list = attr()->value()->addNotExist($toName);
			$data = [
				'attrv_id' => $list[$toName],
				'name' => $fromName,
			];
			$id = $this->insertGetId($data);
		} else {
			$id = $info['item_id'];
		}
		// 额外映射
		$tmp = [];
		$nameArr = attr()->name()->addNotExist(array_unique($attr['name']));
		$valueArr = attr()->value()->addNotExist(array_unique($attr['value']));
		foreach ($attr['name'] as $key => $value) {
			$tmp[$nameArr[$value]] = $valueArr[$attr['value'][$key]];
		}
		$tmp = $tmp ? json_encode($tmp) : '';
		return $this->updateData($id, ['ext'=>$tmp]);
	}
}