<?php

namespace app\service\product;
use app\service\Base;

class Sku extends Base
{
	public function getInfoCache($skuId, $lanId = 1)
	{
		$cacheKey = $this->getCacheKey($skuId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($skuId, $lanId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		if (!empty($info)) {
			$info['url'] = url('product', ['pid' => $info['spu_id'], 'sid' => $skuId]);
			$info['image'] = !empty($info['image']) ? mediaUrl($info['image']) : siteUrl('image/common/noimg.svg');
			if (!empty($info['attr']) && is_array($info['attr'])) {
				foreach ($info['attr'] as $key => $value) {
					if (!empty($value['image'])) {
						$info['attr'][$key]['image'] = mediaUrl($value['image']);
					}
				}
			} else {
				$info['attr'] = [];
			}
			$currencyService = service('system/Currency');
			$spu = service('product/Spu');
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

	public function getInfo($skuId, $lanId = 1)
	{
		$info = $this->loadData($skuId, 'sku_id,spu_id,attach_id,stock,price');
		if (!$info) {
			return false;
		}
		// 属性列表
		$info['attr'] = service('product/AttrUsed')->getListById($skuId, $lanId);
		$imageArr = array_values(array_unique(array_filter(array_merge([$info['attach_id']], array_column($info['attr'], 'attach_id')))));
		if (empty($imageArr) && !empty($info['spu_id'])) {
			$spuAttach = service('product/Spu')->loadData($info['spu_id'], 'attach_id')['attach_id'] ?? 0;
			if ($spuAttach) {
				$info['attach_id'] = $spuAttach;
				$imageArr[] = $spuAttach;
			}
		}
		$attachMap = [];
		if (!empty($imageArr)) {
			$rawAttaches = service('attachment/Attachment')->getList(['attach_id' => ['in', $imageArr]], 200, false);
			$rawAttaches = array_column($rawAttaches, null, 'attach_id');
			$spu = service('product/Spu');
			foreach ($rawAttaches as $key => $value) {
				$attachMap[$key] = $spu->attachmentFormat($value, 200);
			}
		}
		$info['image'] = $attachMap[$info['attach_id']] ?? '';
		foreach ($info['attr'] as $key => $value) {
			if (!empty($attachMap[$value['attach_id']])) {
				$info['attr'][$key]['image'] = $attachMap[$value['attach_id']];
			}
		}
		// 获取语言
		$lanArr = array_values(array_unique([0, 1, (int)$lanId]));
		$info['spu_name'] = service('product/Language')->loadData(['spu_id' => $info['spu_id'], 'lan_id' => ['in', $lanArr]], 'name', ['lan_id' => 'desc'])['name'] ?? '';
		$info['name'] = $info['spu_name'];
		$attrText = implode(' / ', array_filter(array_column($info['attr'], 'attrv_name')));
		if ($info['name'] && $attrText !== '') {
			$info['name'] .= ' - ' . $attrText;
		} elseif ($attrText !== '') {
			$info['name'] = $attrText;
		}
		return $info;
	}

	protected function getCacheKey($skuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY') . $skuId . ':' . $lanId;
	}
}
