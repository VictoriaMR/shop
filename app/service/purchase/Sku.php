<?php 

namespace app\service\purchase;
use app\service\Base;

class Sku extends Base
{
	public function addNotExist(int $spuId, $skuArr)
	{
		$where = [
			'purchase_spu_id' => $spuId,
		];
		if (!empty($skuArr)) {
			$where['unique_id'] = ['not in', array_keys($skuArr)];
		}
		$this->deleteData($where);
		foreach ($skuArr as $key => $value) {
			$where = [
				'purchase_spu_id' => $spuId,
				'unique_id' => $key,
			];
			$tmp = [];
			if (!empty($value['pvs']) && is_array($value['pvs'])) {
				foreach ($value['pvs'] as $k => $v) {
					$tmp[] = $k . ':' . $v;
				}
			}
			$attr = trim(implode(';', $tmp));
			$stock = (int)($value['stock'] ?? 0);
			$price = (float)($value['price'] ?? 0);
			if ($this->getCountData($where) > 0) {
				$this->updateData($where, [
					'stock' => $stock,
					'price' => $price,
					'attr' => $attr,
				]);
			} else {
				$where['stock'] = $stock;
				$where['price'] = $price;
				$where['attr'] = $attr;
				$this->insertData($where);
			}
		}
		return true;
	}
}