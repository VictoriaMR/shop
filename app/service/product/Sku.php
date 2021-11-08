<?php 

namespace app\service\product;
use app\service\Base;

class Sku extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/Sku');
	}

	public function getInfoCache($skuId, $lanId=1)
	{
		$cacheKey = $this->getCacheKey($skuId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($skuId, $lanId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		return $info;
	}

	public function getInfo($skuId, $lanId=1)
	{
		$info = $this->loadData($skuId, 'spu_id,attach_id,stock,price,original_price');
		if (!$info) {
			return false;
		}
		//价格格式化
		$currency = make('app/service/Currency');
		$temp = $currency->priceFormat($info['price']);
		$info['price'] = $temp[1];
		$info['price_format'] = $temp[2];
		$temp = $currency->priceFormat($info['original_price']);
		$info['original_price'] = $temp[1];
		$info['original_price_format'] = $temp[2];
		//属性列表
		$info += make('app/service/product/AttrUsed')->getListById($skuId, $lanId, true);

		$imageArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', array_merge([$info['attach_id']], $info['attvImage'])]]);
		$imageArr = array_column($imageArr, null, 'attach_id');
		$info['image'] =  $imageArr[$info['attach_id']]['url'] ?? '';
		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $imageArr[$value] ?? [];
		}
		//获取语言
		$info['name'] = make('app/service/product/Language')->loadData(['spu_id'=>$info['spu_id'], 'lan_id'=>['in', [1, $lanId]]], 'name', ['lan_id'=>'desc'])['name'] ?? '';
		$info['name'] .= empty($info['name']) ? implode(' ', $info['attv']) : ' - '.implode(' ', $info['attv']);
		$info['url'] = router()->urlFormat($info['name'], 's', ['id' => $skuId]);
		return $info;
	}

	protected function getCacheKey($skuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY').$skuId.'_'.$lanId;
	}
}