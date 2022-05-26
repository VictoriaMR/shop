<?php 

namespace app\service\product;
use app\service\Base;

class AttrUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/AttrUsed');
	}

	public function getListById($skuId, $lanId=1, $simple=false)
	{
		if (!is_array($skuId)) $skuId = [$skuId];
		$list = $this->getListData(['sku_id'=>['in', $skuId]], 'sku_id,attr_id,attv_id,attach_id');
		$data = [];
		//$attr属性
		$tempArr = array_unique(array_column($list, 'attr_id'));
		$tempArr = make('app/service/attr/Bute')->getListById($tempArr, $lanId);
		$data['attr'] = $data['attrMap'] = [];
		foreach ($tempArr as $value) {
			$data['attr'][$value['attr_id']] = $value['name'];
			$data['attrMap'][$value['attr_id']] = [];
		}
		$data['attrArr'] = array_keys($data['attr']);
		//$attv属性
		$tempArr = array_unique(array_column($list, 'attv_id'));
		$tempArr = make('app/service/attr/Value')->getListById($tempArr, $lanId);
		$data['attv'] = $attvArr = [];
		foreach ($tempArr as $key => $value) {
			$data['attv'][$value['attv_id']] = $value['name'];
			$attvArr[$value['attv_id']] = $key;
		}
		$data['skuAttv'] = [];
		$data['skuMap'] = [];
		$data['attvImage'] = [];
		foreach ($list as $key => $value) {
			$data['attrMap'][$value['attr_id']][$attvArr[$value['attv_id']]] = $value['attv_id'];
			$data['skuMap'][$value['sku_id']][$value['attr_id']] = $value['attv_id'];
			$data['attvImage'][$value['attv_id']] = $value['attach_id'];
		}

		$data['attrMap'] = array_map(function($value) {
			ksort($value);
			return array_values($value);
		}, $data['attrMap']);
		$attrArr = $data['attrArr'];
		$data['skuAttv'] = array_map(function($value) use($attrArr){
			array_multisort($attrArr, $value);
			return array_values($value);
		}, $data['skuMap']);
		if ($simple) {
			return $data;
		}
		$tempArr = [];
		foreach ($data['attrMap'] as $key => $value) {
			foreach ($data['skuMap'] as $k => $v) {
				if (!isset($v[$key])) continue;
				if (!isset($tempArr[$k])) $tempArr[$k] = '';
				$tempArr[$k] .= $key.':'.$v[$key].';';
			}
		}
		$data['skuMap'] = array_flip($tempArr);

		//sku属性选择
		$data['filterMap'] = [];
		if (count($data['attr']) > 1) {
			foreach ($data['skuAttv'] as $value) {
				foreach ($this->getArraySubSet($value) as $vv) {
					$key = implode(':', $vv);
					if (isset($data['filterMap'][$key])) {
						$data['filterMap'][$key] = array_merge(array_diff($value, $vv), $data['filterMap'][$key]);
					} else {
						$diff = array_diff($value, $vv);
						if ($diff) {
							$data['filterMap'][$key]=$diff;
						}
					}
				}
			}
		}
		return $data;
	}

	protected function getArraySubSet($arr)
	{
		$len = count($arr);
		$subsets = pow(2, $len);
		$result = [];
		for ($i=0; $i<$subsets; $i++) {
			$bits = sprintf("%0".$len."b", $i);
			$item = [];
			for ($j=0;$j<$len;$j++) {
				if ($bits[$j] == '1') {
					$item[] = $arr[$j];
				}
			}
			if (!empty($item))
			$result[] = $item;
		}
		return $result;
	}

	public function addAttrUsed($skuId, array $insert)
	{
		if (empty($insert)) return false;
		//获取已有的列表
		$list = $this->getListData(['sku_id'=>$skuId], 'attrn_id,attrv_id');
		if (!empty($list)) {
			$tempData = [];
			foreach ($list as $value) {
				$tempData[$value['attrn_id'].'-'.$value['attrv_id']] = true;
			}
			if (!is_array(current($insert))) $insert = [$insert];
			foreach ($insert as $key => $value) {
				if (isset($tempData[$value['attrn_id'].'-'.$value['attrv_id']])) {
					unset($insert[$key]);
				}
			}
		}
		return $this->insert($insert);
	}
}