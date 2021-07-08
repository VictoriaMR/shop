<?php 

namespace app\service;

use app\service\Base as BaseService;

/**
 * 	产品类
 */
class ProductSkuService extends BaseService
{
	public function create(array $data)
	{
		return make('App\Models\ProductSku')->create($data);
	}

	public function addAttributeRelation(array $data)
	{
		return make('App\Models\ProductAttributeRelation')->create($data);
	}

	public function getAttributeRelation($skuId)
	{
		if (empty($skuId)) {
			return false;
		}
		if (!is_array($skuId)) {
			$skuId = [$skuId];
		}
		return make('App\Models\ProductAttributeRelation')->whereIn('sku_id', $skuId)->orderBy('sort', 'asc')->get();
	}

	public function getListBySpuId($spuId)
	{
		$spuId = (int)$spuId;
		if ($spuId < 1) {
			return [];
		}
		return make('App\Models\ProductSku')->where(['spu_id'=>$spuId, 'status' => 1])->get();
	}

	public function getSpuId($skuId)
	{
		$info = make('App\Models\ProductSku')->loadData($skuId, 'spu_id');
		return $info['spu_id'] ?? 0;
	}
}