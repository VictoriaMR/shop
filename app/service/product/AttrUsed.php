<?php

namespace app\service\product;
use app\service\Base;

class AttrUsed extends Base
{
	protected static $enNameDict = [
		'颜色'         => 'Color',
		'颜色1'        => 'Color',
		'颜色分类'     => 'Color',
		'尺寸'         => 'Size',
		'尺码'         => 'Size',
		'款式'         => 'Style',
		'规格'         => 'Specification',
		'适用床垫尺寸' => 'Mattress Size',
		'是否带储物空间' => 'Storage Option',
		'材质'         => 'Material',
		'光源'         => 'Light Source',
		'电压'         => 'Voltage',
	];

	protected static $enValueDict = [
		'白色'   => 'White',
		'黑色'   => 'Black',
		'灰色'   => 'Gray',
		'浅灰'   => 'Light Gray',
		'深灰'   => 'Dark Gray',
		'米色'   => 'Beige',
		'米白'   => 'Off-White',
		'军绿'   => 'Army Green',
		'军绿色' => 'Army Green',
		'绿色'   => 'Green',
		'蓝色'   => 'Blue',
		'浅蓝'   => 'Light Blue',
		'深蓝'   => 'Navy Blue',
		'藏青'   => 'Navy',
		'红色'   => 'Red',
		'酒红'   => 'Burgundy',
		'粉色'   => 'Pink',
		'黄色'   => 'Yellow',
		'卡其'   => 'Khaki',
		'卡其色' => 'Khaki',
		'棕色'   => 'Brown',
		'咖啡色' => 'Coffee',
		'杏色'   => 'Apricot',
		'紫色'   => 'Purple',
		'橙色'   => 'Orange',
		'金色'   => 'Gold',
		'银色'   => 'Silver',
		'暖光'   => 'Warm Light',
		'白光'   => 'Cool White',
		'三色光' => 'Tri-Color Dimming',
		'无极调光' => 'Stepless Dimming',
		'大号'   => 'Large',
		'中号'   => 'Medium',
		'小号'   => 'Small',
		'均码'   => 'One Size',
	];

	public function translateAttrLabel(string $label, bool $isName = false): string
	{
		$label = trim($label);
		if ($label === '') {
			return $isName ? 'Option' : 'Standard';
		}
		if ($isName && isset(self::$enNameDict[$label])) {
			return self::$enNameDict[$label];
		}
		if (!$isName && isset(self::$enValueDict[$label])) {
			return self::$enValueDict[$label];
		}
		// 替换常见中文词汇组合
		$replacements = self::$enNameDict + self::$enValueDict + [
			'A款' => 'Style A ',
			'B款' => 'Style B ',
			'C款' => 'Style C ',
			'D款' => 'Style D ',
			'款'  => ' Style',
			'色'  => '',
		];
		$translated = strtr($label, $replacements);
		return trim($translated) !== '' ? trim($translated) : $label;
	}

	public function getListBySkuIds($skuId, $lanId = 1, $simple = false)
	{
		$skuIds = is_array($skuId) ? array_values(array_unique(array_filter(array_map('intval', $skuId)))) : [(int)$skuId];
		$emptyResult = [
			'attr'      => [],
			'attrMap'   => [],
			'attv'      => [],
			'skuAttv'   => [],
			'skuMap'    => [],
			'attvImage' => [],
			'filterMap' => [],
		];
		if (empty($skuIds)) {
			return $emptyResult;
		}

		$list = $this->getListData(['sku_id' => ['in', $skuIds]], 'item_id,sku_id,attrn_id,attrv_id,attach_id', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
		if (empty($list)) {
			return $emptyResult;
		}

		$attrnIds = array_values(array_unique(array_column($list, 'attrn_id')));
		$attrvIds = array_values(array_unique(array_column($list, 'attrv_id')));
		$lanArr = array_values(array_unique([1, (int)$lanId]));

		$data = $emptyResult;

		// 1. 优先从 product_sku_attr 按当前 SKU 列表精确匹配真实属性名与属性值
		$rawSkuAttrs = service('product/SkuAttr')->getListData(['sku_id' => ['in', $skuIds]], 'item_id,sku_id,attr_name,attr_value,attach_id', 0, 0, ['item_id' => 'asc']);
		if (!empty($rawSkuAttrs)) {
			$rawBySku = [];
			foreach ($rawSkuAttrs as $rRow) {
				$rawBySku[(int)$rRow['sku_id']][] = $rRow;
			}
			$usedBySku = [];
			foreach ($list as $uRow) {
				$usedBySku[(int)$uRow['sku_id']][] = $uRow;
			}
			foreach ($usedBySku as $sId => $uRows) {
				$rRows = $rawBySku[$sId] ?? [];
				foreach ($uRows as $idx => $uRow) {
					$nid = (int)$uRow['attrn_id'];
					$vid = (int)$uRow['attrv_id'];
					$matchedRaw = $rRows[$idx] ?? null;
					if ($matchedRaw) {
						if (!isset($data['attr'][$nid]) && !empty($matchedRaw['attr_name'])) {
							$data['attr'][$nid] = $this->translateAttrLabel($matchedRaw['attr_name'], true);
							$data['attrMap'][$nid] = [];
						}
						if (!isset($data['attv'][$vid]) && !empty($matchedRaw['attr_value'])) {
							$data['attv'][$vid] = $this->translateAttrLabel($matchedRaw['attr_value'], false);
						}
					}
				}
			}
		}

		// 2. 补充从 attr_name / attr_name_language 获取属性名
		if (count($data['attr']) < count($attrnIds)) {
			$baseNames = !empty($attrnIds) ? service('attr/Name')->getListData(['attrn_id' => ['in', $attrnIds]], 'attrn_id,name') : [];
			foreach ($baseNames as $row) {
				$nid = (int)$row['attrn_id'];
				if (!isset($data['attr'][$nid])) {
					$data['attr'][$nid] = $this->translateAttrLabel($row['name'], true);
					$data['attrMap'][$nid] = [];
				}
			}
		}
		$transNames = !empty($attrnIds) ? service('attr/NameLanguage')->getListData(['attrn_id' => ['in', $attrnIds], 'lan_id' => ['in', $lanArr]], 'attrn_id,name') : [];
		foreach ($transNames as $row) {
			if (!empty($row['name'])) {
				$data['attr'][(int)$row['attrn_id']] = $row['name'];
				if (!isset($data['attrMap'][(int)$row['attrn_id']])) {
					$data['attrMap'][(int)$row['attrn_id']] = [];
				}
			}
		}

		// 3. 补充从 attr_value / attr_value_language 获取属性值
		if (count($data['attv']) < count($attrvIds)) {
			$baseValues = !empty($attrvIds) ? service('attr/Value')->getListData(['attrv_id' => ['in', $attrvIds]], 'attrv_id,name') : [];
			foreach ($baseValues as $row) {
				$vid = (int)$row['attrv_id'];
				if (!isset($data['attv'][$vid])) {
					$data['attv'][$vid] = $this->translateAttrLabel($row['name'], false);
				}
			}
		}
		$transValues = !empty($attrvIds) ? service('attr/ValueLanguage')->getListData(['attrv_id' => ['in', $attrvIds], 'lan_id' => ['in', $lanArr]], 'attrv_id,name') : [];
		foreach ($transValues as $row) {
			if (!empty($row['name'])) {
				$data['attv'][(int)$row['attrv_id']] = $row['name'];
			}
		}

		// 保底填充，彻底杜绝 Undefined offset
		foreach ($attrnIds as $nid) {
			$nid = (int)$nid;
			if (!isset($data['attr'][$nid])) {
				$data['attr'][$nid] = 'Option';
			}
			if (!isset($data['attrMap'][$nid])) {
				$data['attrMap'][$nid] = [];
			}
		}
		foreach ($attrvIds as $vid) {
			$vid = (int)$vid;
			if (!isset($data['attv'][$vid])) {
				$data['attv'][$vid] = 'Option #' . $vid;
			}
		}

		$attrOrder = array_keys($data['attr']);
		$attvOrderMap = [];
		$orderIdx = 0;
		foreach ($data['attv'] as $vid => $vName) {
			$attvOrderMap[(int)$vid] = $orderIdx++;
		}

		foreach ($list as $value) {
			$nid = (int)$value['attrn_id'];
			$vid = (int)$value['attrv_id'];
			$sid = (int)$value['sku_id'];
			$sortKey = $attvOrderMap[$vid] ?? $vid;
			$data['attrMap'][$nid][$sortKey] = $vid;
			$data['skuMap'][$sid][$nid] = $vid;
			if (!empty($value['attach_id']) || !isset($data['attvImage'][$vid])) {
				$data['attvImage'][$vid] = (int)$value['attach_id'];
			}
		}

		foreach ($data['attrMap'] as $nid => $valMap) {
			ksort($valMap);
			$data['attrMap'][$nid] = array_values(array_unique($valMap));
		}

		// 按 $attrOrder 顺序排列每个 SKU 的属性值数组（替代易报错的 array_multisort）
		foreach ($data['skuMap'] as $sid => $nidMap) {
			$orderedAttvs = [];
			foreach ($attrOrder as $nid) {
				if (isset($nidMap[$nid])) {
					$orderedAttvs[] = $nidMap[$nid];
				}
			}
			$data['skuAttv'][$sid] = $orderedAttvs;
		}

		if ($simple) {
			return $data;
		}

		$tempArr = [];
		foreach ($data['attrMap'] as $nid => $value) {
			foreach ($data['skuMap'] as $sid => $v) {
				if (!isset($v[$nid])) {
					continue;
				}
				if (!isset($tempArr[$sid])) {
					$tempArr[$sid] = '';
				}
				$tempArr[$sid] .= $nid . ':' . $v[$nid] . ';';
			}
		}
		$data['skuMap'] = array_flip($tempArr);

		// SKU 属性联动过滤映射
		$data['filterMap'] = [];
		if (count($data['attr']) > 1) {
			foreach ($data['skuAttv'] as $value) {
				foreach ($this->getArraySubSet($value) as $vv) {
					$key = implode(':', $vv);
					$diff = array_diff($value, $vv);
					if ($diff) {
						if (isset($data['filterMap'][$key])) {
							$data['filterMap'][$key] = array_values(array_unique(array_merge($diff, $data['filterMap'][$key])));
						} else {
							$data['filterMap'][$key] = array_values($diff);
						}
					}
				}
			}
		}
		return $data;
	}

	public function getListById($skuId, $lanId = 1)
	{
		$skuId = (int)$skuId;
		if ($skuId <= 0) {
			return [];
		}
		$list = $this->getListData(['sku_id' => $skuId], 'item_id,attrn_id,attrv_id,attach_id', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
		if (empty($list)) {
			return [];
		}
		$bundle = $this->getListBySkuIds([$skuId], $lanId, true);
		foreach ($list as $key => $value) {
			$nid = (int)$value['attrn_id'];
			$vid = (int)$value['attrv_id'];
			$value['attrn_name'] = $bundle['attr'][$nid] ?? 'Option';
			$value['attrv_name'] = $bundle['attv'][$vid] ?? ('#' . $vid);
			$list[$key] = $value;
		}
		return $list;
	}

	protected function getArraySubSet($arr)
	{
		$arr = array_values($arr);
		$len = count($arr);
		if ($len === 0 || $len > 8) {
			return [];
		}
		$subsets = pow(2, $len);
		$result = [];
		for ($i = 1; $i < $subsets; $i++) {
			$bits = sprintf('%0' . $len . 'b', $i);
			$item = [];
			for ($j = 0; $j < $len; $j++) {
				if ($bits[$j] === '1') {
					$item[] = $arr[$j];
				}
			}
			if (!empty($item)) {
				$result[] = $item;
			}
		}
		return $result;
	}

	public function addAttrUsed($skuId, array $insert)
	{
		if (empty($insert)) return false;
		$list = $this->getListData(['sku_id' => $skuId], 'attrn_id,attrv_id');
		if (!empty($list)) {
			$tempData = [];
			foreach ($list as $value) {
				$tempData[$value['attrn_id'] . '-' . $value['attrv_id']] = true;
			}
			if (!is_array(current($insert))) $insert = [$insert];
			foreach ($insert as $key => $value) {
				if (isset($tempData[$value['attrn_id'] . '-' . $value['attrv_id']])) {
					unset($insert[$key]);
				}
			}
			$insert = array_values($insert);
			if (empty($insert)) {
				return true;
			}
		}
		return $this->insert($insert);
	}

	public function getSiteAttr()
	{
		$cacheKey = 'spu:attr:' . siteId();
		$rst = redis()->get($cacheKey);
		if (!$rst) {
			$siteCateIds = service('category/Category')->getSiteCateIds();
			$cateWhere = !empty($siteCateIds) ? ' AND s.`cate_id` IN (' . implode(',', $siteCateIds) . ')' : '';
			$sql = 'SELECT b.`attrn_id`,b.`attrv_id` FROM `product_sku` a INNER JOIN `product_spu` s ON a.`spu_id`=s.`spu_id` INNER JOIN `product_attr_used` b ON a.`sku_id`=b.`sku_id` WHERE s.`status`=1 AND a.`status`=1' . $cateWhere . ' GROUP BY b.`attrn_id`, b.`attrv_id` ORDER BY b.`attrn_id` ASC, b.`attrv_id` ASC';
			$rst = $this->getQuery($sql);
			$tempArr = [];
			foreach ($rst as $value) {
				if (!isset($tempArr[$value['attrn_id']])) {
					$tempArr[$value['attrn_id']] = [];
				}
				$tempArr[$value['attrn_id']][] = $value['attrv_id'];
			}
			redis()->set($cacheKey, $tempArr, 24 * 3600);
			$rst = $tempArr;
		}
		if (empty($rst)) {
			return [];
		}
		$lanArr = array_unique([1, lanId()]);
		$attrLanguage = service('attr/NameLanguage')->getListData(['attrn_id' => ['in', array_keys($rst)], 'lan_id' => ['in', $lanArr]], 'attrn_id,name', 0, 0, ['lan_id' => 'asc']);
		$attrLanguage = array_column($attrLanguage, 'name', 'attrn_id');
		$attvIdArr = [];
		foreach ($rst as $value) {
			$attvIdArr = array_merge($attvIdArr, $value);
		}
		$attvLanguage = service('attr/ValueLanguage')->getListData(['attrv_id' => ['in', array_unique($attvIdArr)], 'lan_id' => ['in', $lanArr]], 'attrv_id,name', 0, 0, ['lan_id' => 'asc']);
		$attvLanguage = array_column($attvLanguage, 'name', 'attrv_id');
		$result = [];
		foreach ($rst as $key => $value) {
			if (isset($attrLanguage[$key])) {
				$tempArr = [
					'attrn_id'  => $key,
					'name'      => $attrLanguage[$key],
					'attv_list' => [],
				];
				foreach ($value as $attv) {
					if (isset($attvLanguage[$attv])) {
						$tempArr['attv_list'][] = [
							'attrv_id' => $attv,
							'name'     => $attvLanguage[$attv],
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
		$skuId = $this->getListData(['attrv_id' => ['in', $vidArr]], 'sku_id');
		if ($skuId) {
			$spuId = service('product/Sku')->getListData(['sku_id' => ['in', array_column($skuId, 'sku_id')]], 'spu_id');
			return array_column($spuId, 'spu_id');
		}
		return [];
	}
}
