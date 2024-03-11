<?php 

namespace app\service\product;
use app\service\Base;

class Sku extends Base
{
	public function getInfoCache($skuId, $lanId=1)
	{
		$cacheKey = $this->getCacheKey($skuId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($skuId, $lanId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		if (!empty($info)) {
			$info['url'] = url($info['name'], ['s'=>$skuId]);
			$info['image'] = siteUrl($info['image']);
			foreach ($info['attr'] as $key=>$value) {
				if (!empty($value['image'])) {
					$info['attr'][$key]['image'] = siteUrl($value['image']);
				}
			}
			$currencyService = make('app/service/currency/Currency');
			$spu = make('app/service/product/Spu');
			$info['original_price'] = $spu->getOriginalPrice($info['price']);
			$info['show_price'] = $spu->showPrice($info['spu_id']);
			$temp = $currencyService->priceFormat($info['price']);
			$info['price'] = $temp[1];
			$info['price_format'] = $temp[2];
			$temp = $currencyService->priceFormat($info['original_price']);
			$info['original_price'] = $temp[1];
			$info['original_price_format'] = $temp[2];
		}
		return $info;
	}

	public function getInfo($skuId, $lanId=1)
	{
		$info = $this->loadData($skuId, 'spu_id,attach_id,stock,price');
		if (!$info) {
			return false;
		}
		//属性列表
		$info['attr'] = make('app/service/product/AttrUsed')->getListById($skuId, $lanId);
		$imageArr = array_unique(array_filter(array_merge([$info['attach_id']], array_column($info['attr'], 'attach_id'))));
		$imageArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $imageArr]], 200, false);
		$imageArr = array_column($imageArr, null, 'attach_id');
		$spu = make('app/service/product/Spu');
		foreach ($imageArr as $key => $value) {
			$imageArr[$key] = $spu->attachmentFormat($value, 200);
		}
		$info['image'] = $imageArr[$info['attach_id']] ?? '';
		foreach ($info['attr'] as $key=>$value) {
			if (!empty($imageArr[$value['attach_id']])) {
				$info['attr'][$key]['image'] = $imageArr[$value['attach_id']];
			}
		}
		//获取语言
		$info['name'] = make('app/service/product/Language')->loadData(['spu_id'=>$info['spu_id'], 'lan_id'=>['in', array_unique([1, $lanId])]], 'name', ['lan_id'=>'desc'])['name'] ?? '';
		if ($info['name']) {
			$info['name'] .= ' - ';
		}
		$info['name'] .= implode(' ', array_column($info['attr'], 'attrv_name'));
		return $info;
	}

	protected function getCacheKey($skuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY').$skuId.':'.$lanId;
	}
}