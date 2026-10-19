<?php 

namespace app\service\product;
use app\service\Base;

class SpuImage extends Base
{
	public function addSpuImage(array $data)
	{
		if (empty($data)) {
			return false;
		}
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($this->getCountData(['spu_id' => $value['spu_id'], 'attach_id' => $value['attach_id']])) {
					unset($data[$key]);
				}
			}
			$data = array_values($data);
			if (empty($data)) {
				return true;
			}
		}
		return $this->insert($data);
	}

	public function getListById($spuId, $attactId=[])
	{
		return $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
	}

	public function updateSort(int $spuId, array $attachIds)
	{
		$attachIds = array_values(array_unique(array_filter(array_map('intval', $attachIds))));
		if ($spuId <= 0 || empty($attachIds)) {
			return false;
		}
		$caseSql = [];
		foreach ($attachIds as $index => $attachId) {
			$sort = $index + 1;
			$caseSql[] = "WHEN {$attachId} THEN {$sort}";
		}
		$inSql = implode(',', $attachIds);
		$sql = "UPDATE `product_spu_image` SET `sort` = CASE `attach_id` " . implode(' ', $caseSql) . " END WHERE `spu_id` = ? AND `attach_id` IN ({$inSql})";
		return $this->getQuery($sql, ['i', $spuId]);
	}
}