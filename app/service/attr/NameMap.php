<?php 

namespace app\service\attr;
use app\service\Base;

class NameMap extends Base
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
		// 首先获取属性名中是否有相同的名称
		$list = attr()->name()->getListData(['name'=>['in', $nameArr]], 'attrn_id,name');
		$list = array_column($list, null, 'name');
		foreach ($list as $k => $v) {
			$list[$k]['attrn_name'] = $v['name'];
		}
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
		$fromName = strTrim($fromName);
		$upperTo = strtoupper(strTrim($toName));
		if ($fromName === '' || $upperTo === '') {
			return false;
		}
		$list = attr()->name()->addNotExist($upperTo);
		$attrnId = $list[$upperTo] ?? 0;
		if ($attrnId <= 0) {
			return false;
		}
		$info = $this->loadData(['name'=>$fromName]);
		if (!$info) {
			return $this->insertGetId(['name'=>$fromName, 'attrn_id'=>$attrnId]);
		}
		return $this->updateData($info['item_id'], ['attrn_id' => $attrnId]) !== false;
	}

	/**
	 * 批量静默保存属性名映射词典
	 */
	public function batchSaveMap(array $mapPairs): void
	{
		$cleanPairs = [];
		foreach ($mapPairs as $from => $to) {
			$from = strTrim((string)$from);
			$to = strtoupper(strTrim((string)$to));
			if ($from !== '' && $to !== '') {
				$cleanPairs[$from] = $to;
			}
		}
		if (empty($cleanPairs)) {
			return;
		}
		$attrnIdMap = attr()->name()->addNotExist(array_values(array_unique(array_values($cleanPairs))));
		if (empty($attrnIdMap)) {
			return;
		}
		$existing = $this->getListData(['name' => ['in', array_keys($cleanPairs)]], 'item_id,name,attrn_id');
		$existing = array_column($existing ?: [], null, 'name');
		foreach ($cleanPairs as $from => $to) {
			$attrnId = (int)($attrnIdMap[$to] ?? 0);
			if ($attrnId <= 0) {
				continue;
			}
			if (!isset($existing[$from])) {
				$this->insert(['name' => $from, 'attrn_id' => $attrnId]);
			} elseif ((int)$existing[$from]['attrn_id'] !== $attrnId) {
				$this->updateData($existing[$from]['item_id'], ['attrn_id' => $attrnId]);
			}
		}
	}
}