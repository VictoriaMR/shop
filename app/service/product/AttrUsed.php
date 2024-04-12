<?php 

namespace app\service\product;
use app\service\Base;

class AttrUsed extends Base
{
	public function getListBySkuIds($skuId, $lanId=1, $simple=false)
	{
		$list = $this->getListData(['sku_id'=>['in', $skuId]], 'sku_id,attrn_id,attrv_id,attach_id');
		$data = [];
		//$attr属性
		$tempArr = array_unique(array_column($list, 'attrn_id'));
		$lanArr = array_unique([1, $lanId]);
		$tempArr = service('attr/NameLanguage')->getListData(['attrn_id'=>['in', $tempArr], 'lan_id'=>['in', $lanArr]], 'attrn_id,name');
		$data['attr'] = $data['attrMap'] = [];
		foreach ($tempArr as $value) {
			$data['attr'][$value['attrn_id']] = $value['name'];
			$data['attrMap'][$value['attrn_id']] = [];
		}
		$attrArr = array_keys($data['attr']);
		//$attv属性
		$tempArr = array_unique(array_column($list, 'attrv_id'));
		$tempArr = service('attr/ValueLanguage')->getListData(['attrv_id'=>['in', $tempArr], 'lan_id'=>['in', $lanArr]], 'attrv_id,name');
		$data['attv'] = $attvArr = [];
		foreach ($tempArr as $key => $value) {
			$data['attv'][$value['attrv_id']] = $value['name'];
			$attvArr[$value['attrv_id']] = $key;
		}
		$data['skuAttv'] = [];
		$data['skuMap'] = [];
		$data['attvImage'] = [];
		foreach ($list as $key => $value) {
			$data['attrMap'][$value['attrn_id']][$attvArr[$value['attrv_id']]] = $value['attrv_id'];
			$data['skuMap'][$value['sku_id']][$value['attrn_id']] = $value['attrv_id'];
			$data['attvImage'][$value['attrv_id']] = $value['attach_id'];
		}

		$data['attrMap'] = array_map(function($value) {
			ksort($value);
			return array_values($value);
		}, $data['attrMap']);
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
						$data['filterMap'][$key] = array_values(array_merge(array_diff($value, $vv), $data['filterMap'][$key]));
					} else {
						$diff = array_diff($value, $vv);
						if ($diff) {
							$data['filterMap'][$key] = array_values($diff);
						}
					}
				}
			}
		}
		return $data;
	}

	public function getListById($skuId, $lanId=1)
	{
		$list = $this->getListData(['sku_id'=>$skuId], 'attrn_id,attrv_id,attach_id');
		//$attr属性
		$lanArr = array_unique([1, $lanId]);
		$tempArr = array_column($list, 'attrn_id');
		$tempArr = service('attr/NameLanguage')->getListData(['attrn_id'=>['in', $tempArr], 'lan_id'=>['in', $lanArr]], 'attrn_id,name');
		$attrn = array_column($tempArr, 'name', 'attrn_id');
		$tempArr = array_column($list, 'attrv_id');
		$tempArr = service('attr/ValueLanguage')->getListData(['attrv_id'=>['in', $tempArr], 'lan_id'=>['in', $lanArr]], 'attrv_id,name');
		$attrv = array_column($tempArr, 'name', 'attrv_id');
		foreach ($list as $key=>$value) {
			$value['attrn_name'] = $attrn[$value['attrn_id']];
			$value['attrv_name'] = $attrv[$value['attrv_id']];
			$list[$key] = $value;
		}
		return $list;
	}

	protected function getArraySubSet($arr)
	{
		$len = count($arr);
		$subsets = pow(2, $len);
		$result = [];
		for ($i=0; $i<$subsets; $i++) {
			$bits = sprintf('%0'.$len.'b', $i);
			$item = [];
			for ($j=0;$j<$len;$j++) {
				if ($bits[$j] == '1') {
					$item[] = $arr[$j];
				}
			}
			if (!empty($item)) $result[] = $item;
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

	public function getSiteAttr()
	{
		$cacheKey = 'spu:attr:'.siteId();
		$rst = redis()->get($cacheKey);
		if (!$rst) {
			$sql = 'SELECT b.`attrn_id`,b.`attrv_id` FROM `product_sku` a LEFT JOIN `product_attr_used` b ON a.`sku_id`=b.`sku_id` WHERE a.`site_id`='.siteId().' GROUP BY b.`attrn_id`, b.`attrv_id` ORDER BY b.`attrn_id` ASC, b.`attrv_id` ASC';
			$rst = $this->getQuery($sql);
			$tempArr = [];
			foreach ($rst as $value) {
				if (!isset($tempArr[$value['attrn_id']])) {
					$tempArr[$value['attrn_id']] = [];
				}
				$tempArr[$value['attrn_id']][] = $value['attrv_id'];
			}
			redis()->set($cacheKey, $tempArr, 24*3600);
			$rst = $tempArr;
		}
		$lanArr = array_unique([1, lanId()]);
		$attrLanguage = service('attr/NameLanguage')->getListData(['attrn_id'=>['in', array_keys($rst)], 'lan_id'=>['in', $lanArr]], 'attrn_id,name', 0, 0, ['lan_id'=>'asc']);
		$attrLanguage = array_column($attrLanguage, 'name', 'attrn_id');
		$attvIdArr = [];
		foreach ($rst as $value) {
			$attvIdArr = array_merge($attvIdArr, $value);
		}
		$attvLanguage = service('attr/ValueLanguage')->getListData(['attrv_id'=>['in', array_unique($attvIdArr)], 'lan_id'=>['in', $lanArr]], 'attrv_id,name', 0, 0, ['lan_id'=>'asc']);
		$attvLanguage = array_column($attvLanguage, 'name', 'attrv_id');
		$result = [];
		foreach ($rst as $key=>$value) {
			if (isset($attrLanguage[$key])) {
				$tempArr = [
					'attrn_id' => $key,
					'name' => $attrLanguage[$key],
					'attv_list' => [],
				];
				foreach ($value as $attv) {
					if (isset($attvLanguage[$attv])) {
						$tempArr['attv_list'][] = [
							'attrv_id' => $attv,
							'name' => $attvLanguage[$attv],
						];
					}
				}
				$result[] = $tempArr;
			}
		}
		return $result;
	}

	public function getSpuId($vidArr)
	{
		$skuId = $this->getListData(['attrv_id'=>['in', $vidArr]], 'sku_id');
		if ($skuId) {
			$spuId = service('product/Sku')->getListData(['sku_id'=>['in', array_column($skuId, 'sku_id')]], 'spu_id');
			return array_column($spuId, 'spu_id');
		}
		return [];
	}
}