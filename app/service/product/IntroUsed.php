<?php 

namespace app\service\product;
use app\service\Base;

class IntroUsed extends Base
{
	public function addIntroUsed($spuId, array $data)
	{
		$allAttachId = array_column($data, 'attach_id');
		$list = $this->getListData(['spu_id'=>$spuId, 'attach_id'=>['in', $allAttachId]], 'attach_id');
		if (!empty($list)) {
			$list = array_column($list, 'attach_id');
			$list = array_diff($allAttachId, $list);
			if (empty($list)) {
				return true;
			}
			$data = array_column($data, null, 'attach_id');
			$tempData = [];
			foreach ($list as $value) {
				$tempData[] = $data[$value];
			}
			$data = $tempData;
		}
		return $this->insert($data);
	}

	public function getListById($spuId)
	{
		return $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc', 'item_id'=>'asc']);
	}

	public function updateSort(int $spuId, array $itemIds)
	{
		$itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds))));
		if ($spuId <= 0 || empty($itemIds)) {
			return false;
		}
		$caseSql = [];
		foreach ($itemIds as $index => $itemId) {
			$sort = $index + 1;
			$caseSql[] = "WHEN {$itemId} THEN {$sort}";
		}
		$inSql = implode(',', $itemIds);
		$sql = "UPDATE `product_intro_used` SET `sort` = CASE `item_id` " . implode(' ', $caseSql) . " END WHERE `spu_id` = ? AND `item_id` IN ({$inSql})";
		return $this->getQuery($sql, ['i', $spuId]);
	}
}