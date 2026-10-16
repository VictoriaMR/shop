<?php 

namespace app\service\attr;
use app\service\Base;

class ValueMap extends Base
{
	public function getMapList($nameArr)
	{
		if (empty($nameArr)) {
			return [];
		}
		$nameArr = array_values(array_unique(array_filter($nameArr)));
		if (empty($nameArr)) {
			return [];
		}
		// 先查映射表（优先保留带有 ext 扩展属性的映射配置）
		$result = [];
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrv_id,name,ext');
		if (!empty($list)) {
			$attvId = array_column($list, 'attrv_id');
			$attnId = [];
			foreach ($list as $value) {
				if (!empty($value['ext'])) {
					$tmp = json_decode($value['ext'], true);
					if (is_array($tmp)) {
						$attnId = array_merge($attnId, array_keys($tmp));
						$attvId = array_merge($attvId, array_values($tmp));
					}
				}
			}
			if (!empty($attnId)) {
				$attnId = attr()->name()->getListData(['attrn_id'=>['in', array_unique($attnId)]], 'attrn_id,name');
				$attnId = array_column($attnId, 'name', 'attrn_id');
			}
			$attvId = attr()->value()->getListData(['attrv_id'=>['in', array_unique($attvId)]], 'attrv_id,name');
			$attvId = array_column($attvId, 'name', 'attrv_id');
			foreach ($list as $key => $value) {
				$value['attrv_name'] = $attvId[$value['attrv_id']] ?? $value['name'];
				$tmp = $value['ext'] ? json_decode($value['ext'], true) : [];
				$value['ext'] = [];
				if (is_array($tmp)) {
					foreach ($tmp as $k => $v) {
						if (isset($attnId[$k]) && isset($attvId[$v])) {
							$value['ext'][$attnId[$k]] = $attvId[$v];
						}
					}
				}
				$result[$value['name']] = $value;
			}
		}
		// 再查属性值表是否已有同名属性值
		$remainArr = array_diff($nameArr, array_keys($result));
		if (!empty($remainArr)) {
			$directList = attr()->value()->getListData(['name'=>['in', $remainArr]], 'attrv_id,name');
			foreach ($directList as $row) {
				$result[$row['name']] = [
					'attrv_id'   => $row['attrv_id'],
					'name'       => $row['name'],
					'attrv_name' => $row['name'],
					'ext'        => [],
				];
			}
		}
		return $result;
	}

	public function addMap($fromName, $toName, $attr = [])
	{
		$fromName = strTrim($fromName);
		$upperTo = strtoupper(strTrim($toName));
		if ($fromName === '' || $upperTo === '') {
			return false;
		}
		$list = attr()->value()->addNotExist($upperTo);
		$attrvId = $list[$upperTo] ?? 0;
		if ($attrvId <= 0) {
			return false;
		}
		$info = $this->loadData(['name' => $fromName]);
		if (!$info) {
			$id = $this->insertGetId([
				'attrv_id' => $attrvId,
				'name'     => $fromName,
			]);
		} else {
			$id = $info['item_id'];
		}
		// 额外映射
		$tmp = [];
		if (!empty($attr['name']) && is_array($attr['name']) && !empty($attr['value']) && is_array($attr['value'])) {
			$validNames = [];
			$validValues = [];
			foreach ($attr['name'] as $k => $n) {
				$nUpper = strtoupper(strTrim($n));
				$vUpper = strtoupper(strTrim($attr['value'][$k] ?? ''));
				if ($nUpper !== '' && $vUpper !== '') {
					$validNames[$k] = $nUpper;
					$validValues[$k] = $vUpper;
				}
			}
			if (!empty($validNames)) {
				$nameArr = attr()->name()->addNotExist(array_values($validNames));
				$valueArr = attr()->value()->addNotExist(array_values($validValues));
				foreach ($validNames as $k => $nUpper) {
					$vUpper = $validValues[$k];
					if (isset($nameArr[$nUpper]) && isset($valueArr[$vUpper])) {
						$tmp[$nameArr[$nUpper]] = $valueArr[$vUpper];
					}
				}
			}
		}
		$extStr = $tmp ? json_encode($tmp, JSON_UNESCAPED_UNICODE) : '';
		return $this->updateData($id, ['attrv_id' => $attrvId, 'ext' => $extStr]) !== false;
	}

	/**
	 * 批量静默保存属性值映射词典（含衍生属性）
	 */
	public function batchSaveMap(array $valMapItems): void
	{
		if (empty($valMapItems)) {
			return;
		}
		foreach ($valMapItems as $fromVal => $item) {
			$fromVal = strTrim((string)$fromVal);
			if ($fromVal === '') {
				continue;
			}
			$mainVal = is_array($item) ? strTrim((string)($item['main'] ?? '')) : strTrim((string)$item);
			if ($mainVal === '') {
				continue;
			}
			$extAttr = [];
			if (is_array($item) && !empty($item['ext'])) {
				$extData = is_string($item['ext']) ? json_decode($item['ext'], true) : $item['ext'];
				if (is_array($extData)) {
					$extAttr = [
						'name'  => array_keys($extData),
						'value' => array_values($extData),
					];
				}
			}
			$this->addMap($fromVal, $mainVal, $extAttr);
		}
	}
}