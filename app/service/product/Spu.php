<?php

namespace app\service\product;
use app\service\Base;

class Spu extends Base
{
	public function getInfoCache($spuId, $lanId = 1)
	{
		$cacheKey = $this->getCacheKey($spuId, $lanId);
		$info = redis()->get($cacheKey);
		if ($info === false) {
			$info = $this->getInfo($spuId, $lanId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		if ($info) {
			$info = $this->infoFormat($info);
		}
		return $info;
	}

	protected function infoFormat($info)
	{
		// 价格格式化
		$currencyService = service('system/Currency');
		$info['original_price'] = $this->getOriginalPrice($info['min_price']);
		$info['show_price'] = $this->showPrice($info['spu_id']);
		$temp = $currencyService->priceFormat($info['min_price']);
		$info['min_price'] = $temp[1];
		$info['min_price_format'] = $temp[2];
		$temp = $currencyService->priceFormat($info['max_price']);
		$info['max_price'] = $temp[1];
		$info['max_price_format'] = $temp[2];
		$temp = $currencyService->priceFormat($info['original_price']);
		$info['original_price'] = $temp[1];
		$info['original_price_format'] = $temp[2];
		if ($info['status'] != $this->getConst('STATUS_OPEN')) {
			return $info;
		}
		$info['url'] = url('product', ['pid' => $info['spu_id']]);

		$noImg = siteUrl('image/common/noimg.svg');
		$info['image'] = !empty($info['image']) ? $this->completeImage($info['image']) : $noImg;
		if (!empty($info['image_list']) && is_array($info['image_list'])) {
			foreach ($info['image_list'] as $key => $value) {
				$info['image_list'][$key] = !empty($value) ? $this->completeImage($value) : $info['image'];
			}
		} else {
			$info['image_list'] = [$info['image']];
		}
		if (!empty($info['introduce']) && is_array($info['introduce'])) {
			foreach ($info['introduce'] as $key => $value) {
				if (empty($value)) {
					unset($info['introduce'][$key]);
					continue;
				}
				$info['introduce'][$key] = $this->completeImage($value);
			}
			$info['introduce'] = array_values($info['introduce']);
		} else {
			$info['introduce'] = [];
		}
		if (!empty($info['attvImage']) && is_array($info['attvImage'])) {
			foreach ($info['attvImage'] as $key => $value) {
				if (empty($value)) continue;
				$info['attvImage'][$key] = $this->completeImage($value);
			}
		} else {
			$info['attvImage'] = [];
		}

		if (!empty($info['sku']) && is_array($info['sku'])) {
			foreach ($info['sku'] as $key => $value) {
				$value['original_price'] = $this->getOriginalPrice($value['price']);
				$temp = $currencyService->priceFormat($value['price']);
				$value['price'] = $temp[1];
				$value['price_format'] = $temp[2];
				$temp = $currencyService->priceFormat($value['original_price']);
				$value['original_price'] = $temp[1];
				$value['original_price_format'] = $temp[2];
				$name = [];
				foreach (($info['skuAttv'][$key] ?? []) as $v) {
					if (isset($info['attv'][$v])) {
						$name[] = $info['attv'][$v];
					}
				}
				$nameStr = implode(' / ', $name);
				$value['spec_label'] = $nameStr;
				$value['name'] = $nameStr ? $info['name'] . ' - ' . $nameStr : $info['name'];
				$value['url'] = url('product', ['pid' => $info['spu_id'], 'sid' => $key]);
				$value['image'] = !empty($value['image']) ? $this->completeImage($value['image']) : $info['image'];
				$info['sku'][$key] = $value;
			}
		}
		return $info;
	}

	protected function completeImage($image)
	{
		if (empty($image)) {
			return $image;
		}
		if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, '/computer/') === 0) {
			return $image;
		}
		return mediaUrl($image);
	}

	public function getInfo($spuId, $lanId = 1)
	{
		$where = ['spu_id' => $spuId];
		$info = $this->loadData($where, 'spu_id,status,cate_id,attach_id,min_price,max_price,free_ship,is_hot');
		if (!$info) return [];
		if ($info['status'] != $this->getConst('STATUS_OPEN')) {
			$info['image'] = $this->attachmentFormat(service('attachment/Attachment')->getList(['attach_id' => $info['attach_id']]), 400);
			$lanArr = array_unique([0, 1, $lanId]);
			$info['name'] = service('product/Language')->loadData(['spu_id' => $spuId, 'lan_id' => ['in', $lanArr]], 'name', ['lan_id' => 'desc'])['name'] ?? '';
			return $info;
		}
		// 获取sku列表
		$sku = service('product/Sku');
		$info['sku'] = $sku->getListData(['spu_id' => $spuId, 'status' => $this->getConst('STATUS_OPEN')], 'sku_id,attach_id,stock,price,sale_total');
		if (!$info['sku']) {
			return $info;
		}
		$info['sku'] = array_column($info['sku'], null, 'sku_id');
		// 获取图片集
		$info['image_list'] = service('product/SpuImage')->getListById($spuId);
		$imageArr = array_column($info['image_list'], 'attach_id');
		// 获取语言, 默认拿英文
		$lanArr = array_unique([0, 1, $lanId]);
		$info['name'] = service('product/Language')->loadData(['spu_id' => $spuId, 'lan_id' => ['in', $lanArr]], 'name', ['lan_id' => 'desc'])['name'] ?? '';
		// spu介绍图片
		$info['introduce'] = service('product/IntroUsed')->getListById($spuId);
		// spu描述
		$info['description'] = service('product/DescUsed')->getListById($spuId, $lanId);
		$info += service('product/AttrUsed')->getListBySkuIds(array_keys($info['sku']), $lanId);

		$imageArr = array_filter(array_merge([$info['attach_id']], $imageArr, array_column($info['sku'], 'attach_id'), $info['attvImage'], array_column($info['introduce'], 'attach_id')));
		$attachMap = [];
		if (!empty($imageArr)) {
			$rawAttaches = service('attachment/Attachment')->getList(['attach_id' => ['in', array_unique($imageArr)]], 400, false);
			$rawAttaches = array_column($rawAttaches, null, 'attach_id');
			foreach ($rawAttaches as $key => $value) {
				$attachMap[$key] = $this->attachmentFormat($value);
			}
		}
		// 图片
		$info['image'] = $attachMap[$info['attach_id']] ?? '';
		foreach ($info['image_list'] as $key => $value) {
			$info['image_list'][$key] = $attachMap[$value['attach_id']] ?? '';
		}
		$info['image_list'] = array_values(array_filter($info['image_list']));
		if (empty($info['image_list']) && !empty($info['image'])) {
			$info['image_list'] = [$info['image']];
		}
		foreach ($info['introduce'] as $key => $value) {
			$info['introduce'][$key] = $attachMap[$value['attach_id']] ?? '';
		}
		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $attachMap[$value] ?? '';
		}
		foreach ($info['sku'] as $key => $value) {
			$info['sku'][$key]['image'] = $attachMap[$value['attach_id']] ?? '';
		}
		return $info;
	}

	public function attachmentFormat($data, $type = 400)
	{
		if (empty($data) || !is_array($data)) {
			return '';
		}
		$path = str_replace('\\', '/', $data['path'] ?? $data['cate'] ?? '');
		if ($path === '' || empty($data['name'])) {
			return '';
		}
		if ($type !== '') {
			return $path . '/' . $data['name'] . '_' . $type . '.webp';
		}
		return $path . '/' . $data['name'] . '.' . ($data['type'] ?? 'webp');
	}

	protected function getCacheKey($spuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY') . $spuId . ':' . $lanId;
	}

	public function getListById($spuIds, $lanId = 0)
	{
		$spuIds = is_array($spuIds) ? array_values(array_unique(array_filter(array_map('intval', $spuIds)))) : [(int)$spuIds];
		if (empty($spuIds)) {
			return [];
		}
		$list = $this->getList(
			['spu_id' => ['in', $spuIds]],
			'spu_id,cate_id,status,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
			0,
			0,
			[],
			$lanId ?: lanId(),
			true,
			true
		);
		foreach ($list as $k => $row) {
			$list[$k]['url'] = url('product', ['pid' => $row['spu_id']]);
		}
		return $list;
	}

	public function getList(array $where = [], $fields = '*', $page = 0, $size = 20, $order = [], $lanId = 0, $priceFormat = false, $isLiked = false)
	{
		$list = $this->getListData($where, $fields, $page, $size, $order);
		if (!$list) {
			return [];
		}
		$spuList = array_column($list, 'spu_id');
		// 获取语言
		$where = ['spu_id' => ['in', $spuList]];
		if (isAdmin()) {
			$where['lan_id'] = 0;
		} else {
			$where['lan_id'] = ['in', array_values(array_unique([0, 1, (int)$lanId]))];
		}
		$lanArr = service('product/Language')->getListData($where, 'spu_id,name', 0, 0, ['lan_id' => 'asc']);
		$lanArr = array_column($lanArr, 'name', 'spu_id');
		// 获取图片集
		$attachIds = array_values(array_unique(array_filter(array_column($list, 'attach_id'))));
		$attachArr = [];
		if (!empty($attachIds)) {
			$attachArr = service('attachment/Attachment')->getList(['attach_id' => ['in', $attachIds]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
		}
		$currency = $priceFormat ? service('system/Currency') : null;
		$likeArr = [];
		if ($isLiked && $this->userId()) {
			$likeArr = service('member/Collect')->getListData(['mem_id' => $this->userId(), 'spu_id' => ['in', $spuList]], 'spu_id');
			$likeArr = array_column($likeArr, 'spu_id');
		}
		// 格式化数组
		foreach ($list as $key => $value) {
			$value['name'] = $lanArr[$value['spu_id']] ?? '';
			$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
			if ($priceFormat && $currency) {
				$value['original_price'] = $this->getOriginalPrice($value['min_price']);
				$temp = $currency->priceFormat($value['min_price']);
				$value['min_price'] = $temp[1];
				$value['min_price_format'] = $temp[2];
				$temp = $currency->priceFormat($value['max_price']);
				$value['max_price'] = $temp[1];
				$value['max_price_format'] = $temp[2];
				$temp = $currency->priceFormat($value['original_price']);
				$value['original_price'] = $temp[1];
				$value['original_price_format'] = $temp[2];
				$value['show_price'] = $this->showPrice($value['spu_id']);
			}
			if ($isLiked) {
				$value['is_liked'] = in_array($value['spu_id'], $likeArr);
			}
			$list[$key] = $value;
		}
		return $list;
	}

	public function getAdminInfo($spuId)
	{
		$spuId = (int)$spuId;
		if ($spuId <= 0) return false;
		$info = $this->loadData($spuId);
		if (empty($info)) return false;
		// 分类
		$info['category'] = array_reverse(service('category/Category')->pCate($info['cate_id']));
		// SpuData
		$info['data'] = service('product/SpuData')->loadData($spuId) ?: [
			'shop_id'  => 0,
			'supplier' => '',
			'item_id'  => '',
			'item_url' => '',
			'post_fee' => 0,
			'volume'   => '',
			'weight'   => 0,
		];
		$info['shop'] = !empty($info['data']['shop_id']) ? (service('purchase/Shop')->loadData($info['data']['shop_id']) ?: []) : [];
		$info['purchase_spu_id'] = 0;
		if (!empty($info['data']['item_id'])) {
			$purRow = service('purchase/Spu')->loadData(['item_id' => $info['data']['item_id']], 'purchase_spu_id');
			$info['purchase_spu_id'] = (int)($purRow['purchase_spu_id'] ?? 0);
		}
		// 名称
		$lanRow = service('product/Language')->loadData(['spu_id' => $spuId, 'lan_id' => 0], 'name');
		$info['name'] = $lanRow['name'] ?? '';
		// 图片
		$info['image'] = service('product/SpuImage')->getListData(['spu_id' => $spuId], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
		// 获取sku列表
		$info['sku'] = service('product/Sku')->getListData(['spu_id' => $spuId]);
		// skuData & sku属性
		$skuIdArr = array_column($info['sku'], 'sku_id');
		$skuData = [];
		$attrArr = [];
		$attrBute = [];
		$attrValue = [];
		if (!empty($skuIdArr)) {
			$skuData = service('product/SkuData')->getListData(['sku_id' => ['in', $skuIdArr]]);
			$skuData = array_column($skuData, null, 'sku_id');
			$attrArr = service('product/AttrUsed')->getListData(['sku_id' => ['in', $skuIdArr]], '*', 0, 0, ['sort' => 'asc']);
			if (!empty($attrArr)) {
				$attrnIds = array_values(array_unique(array_filter(array_column($attrArr, 'attrn_id'))));
				$attrvIds = array_values(array_unique(array_filter(array_column($attrArr, 'attrv_id'))));
				if (!empty($attrnIds)) {
					$attrBute = service('attr/Name')->getListData(['attrn_id' => ['in', $attrnIds]]);
					$attrBute = array_column($attrBute, null, 'attrn_id');
				}
				if (!empty($attrvIds)) {
					$attrValue = service('attr/Value')->getListData(['attrv_id' => ['in', $attrvIds]]);
					$attrValue = array_column($attrValue, null, 'attrv_id');
				}
			}
		}
		// 描述图片
		$info['introduce'] = service('product/IntroUsed')->getListData(['spu_id' => $spuId], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
		// 图片
		$attachIds = array_values(array_unique(array_filter(array_merge(
			array_column($info['image'], 'attach_id'),
			array_column($info['sku'], 'attach_id'),
			array_column($attrArr, 'attach_id'),
			array_column($info['introduce'], 'attach_id')
		))));
		$attachArr = [];
		if (!empty($attachIds)) {
			$attachArr = service('attachment/Attachment')->getList(['attach_id' => ['in', $attachIds]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
		}
		// sku 属性归类
		$skuAttrArr = [];
		$attrMap = [];
		foreach ($attrArr as $value) {
			if (!isset($skuAttrArr[$value['sku_id']])) $skuAttrArr[$value['sku_id']] = [];
			$value['attr_name'] = $attrBute[$value['attrn_id']]['name'] ?? '';
			$value['attv_name'] = $attrValue[$value['attrv_id']]['name'] ?? '';
			$value['image'] = $attachArr[$value['attach_id']] ?? '';
			if (!isset($attrMap[$value['attrn_id']])) {
				$attrMap[$value['attrn_id']] = [
					'attrn_id'  => $value['attrn_id'],
					'attr_name' => $value['attr_name'],
					'son'       => [],
				];
			}
			$prevCount = $attrMap[$value['attrn_id']]['son'][$value['attrv_id']]['sku_count'] ?? 0;
			$attrMap[$value['attrn_id']]['son'][$value['attrv_id']] = [
				'attrv_id'  => $value['attrv_id'],
				'attv_name' => $value['attv_name'],
				'attach_id' => $value['attach_id'],
				'image'     => $value['image'],
				'sku_count' => $prevCount + 1,
			];
			$skuAttrArr[$value['sku_id']][] = $value;
		}
		$info['attr_map'] = $attrMap;
		foreach ($info['sku'] as $key => $value) {
			if (isset($skuData[$value['sku_id']])) {
				$value = array_merge($value, $skuData[$value['sku_id']]);
			}
			$value['image'] = $attachArr[$value['attach_id']] ?? '';
			$value['attr'] = $skuAttrArr[$value['sku_id']] ?? [];
			$info['sku'][$key] = $value;
		}
		foreach ($info['image'] as $key => $value) {
			$info['image'][$key]['image'] = $attachArr[$value['attach_id']] ?? '';
		}
		foreach ($info['introduce'] as $key => $value) {
			$info['introduce'][$key]['image'] = str_replace(['_400.webp', '/400', '.220x220'], ['.webp', '', ''], $attachArr[$value['attach_id']] ?? '');
		}
		// 描述
		$descArr = service('product/DescUsed')->getListData(['spu_id' => $spuId], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
		if (!empty($descArr)) {
			$descnIds = array_values(array_unique(array_filter(array_column($descArr, 'descn_id'))));
			$descvIds = array_values(array_unique(array_filter(array_column($descArr, 'descv_id'))));
			$descgIds = array_values(array_unique(array_filter(array_column($descArr, 'descg_id'))));
			$descNameArr = !empty($descnIds) ? array_column(service('desc/Name')->getListData(['descn_id' => ['in', $descnIds]]), 'name', 'descn_id') : [];
			$descValueArr = !empty($descvIds) ? array_column(service('desc/Value')->getListData(['descv_id' => ['in', $descvIds]]), 'name', 'descv_id') : [];
			$descGroupArr = !empty($descgIds) ? array_column(service('desc/Group')->getListData(['descg_id' => ['in', $descgIds]]), 'name', 'descg_id') : [];
			foreach ($descArr as $key => $value) {
				$descArr[$key]['group'] = $descGroupArr[$value['descg_id']] ?? '';
				$descArr[$key]['name'] = $descNameArr[$value['descn_id']] ?? '';
				$descArr[$key]['value'] = $descValueArr[$value['descv_id']] ?? '';
			}
		}
		$info['desc'] = $descArr;
		return $info;
	}

	/**
	 * 同步重算SPU的最低价与最高价区间
	 */
	public function syncSpuPriceRange(int $spuId): array
	{
		if ($spuId <= 0) {
			return ['min_price' => 0, 'max_price' => 0];
		}
		$skuService = service('product/Sku');
		$skus = $skuService->getListData(['spu_id' => $spuId], 'price,status');
		if (empty($skus)) {
			return ['min_price' => 0, 'max_price' => 0];
		}
		$activePrices = [];
		$allPrices = [];
		foreach ($skus as $row) {
			$p = (float)$row['price'];
			if ($p > 0) {
				$allPrices[] = $p;
				if ((int)$row['status'] === 1) {
					$activePrices[] = $p;
				}
			}
		}
		$prices = !empty($activePrices) ? $activePrices : $allPrices;
		if (empty($prices)) {
			return ['min_price' => 0, 'max_price' => 0];
		}
		$minPrice = min($prices);
		$maxPrice = max($prices);
		$this->updateData($spuId, [
			'min_price' => $minPrice,
			'max_price' => $maxPrice,
		]);
		return ['min_price' => $minPrice, 'max_price' => $maxPrice];
	}

	public function getPrice($price)
	{
		if ($price <= 100) {
			$price += 88;
		} elseif ($price > 100 && $price <= 300) {
			$price += 128;
		} elseif ($price > 300 && $price <= 500) {
			$price += 188;
		} elseif ($price > 500 && $price <= 700) {
			$price += 288;
		} elseif ($price > 700 && $price <= 900) {
			$price += 388;
		} elseif ($price > 900 && $price <= 1100) {
			$price += 488;
		} elseif ($price > 1100 && $price <= 2000) {
			$price += 688;
		} elseif ($price > 2000) {
			$price += 1288;
		}
		return $price;
	}

	public function getOriginalPrice($price)
	{
		if ($price <= 100) {
			$rate = 0.75;
		} elseif ($price > 100 && $price <= 300) {
			$rate = 0.85;
		} elseif ($price > 300 && $price <= 500) {
			$rate = 0.8;
		} elseif ($price > 500 && $price <= 700) {
			$rate = 0.75;
		} elseif ($price > 700 && $price <= 900) {
			$rate = 0.65;
		} elseif ($price > 900 && $price <= 1100) {
			$rate = 0.65;
		} elseif ($price > 1100) {
			$rate = 0.65;
		} else {
			$rate = 0.75;
		}
		return number_format((float)$price / $rate, 2, '.', '');
	}

	protected function getSupplierItemUrl($url)
	{
		if (empty($url)) {
			return '';
		}
		$url = explode('?', $url);
		if (strpos($url[0], '1688.com') !== false) {
			return $url[0];
		} else {
			parse_str($url[1] ?? '', $params);
			$id = $params['id'] ?? '';
			if (empty($id)) {
				return '';
			}
			return $url[0] . (empty($id) ? '' : '?id=' . $id);
		}
	}

	public function getRecommend($page = 1, $size = 20, &$total = 0)
	{
		$where = ['status' => $this->getConst('STATUS_OPEN')];
		$siteCateIds = service('category/Category')->getSiteCateIds();
		if (!empty($siteCateIds)) {
			$where['cate_id'] = ['in', $siteCateIds];
		}
		$total = $this->getCountData($where);
		$list = $this->getList($where, 'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot', $page, $size, ['visit_total' => 'desc', 'is_hot' => 'desc', 'spu_id' => 'desc'], lanId(), true, true);
		foreach ($list as $key => $value) {
			$list[$key]['url'] = url('product', ['pid' => $value['spu_id']]);
		}
		return $list;
	}

	public function showPrice($spuId)
	{
		$sale_rate = 75;
		$i = substr((string)$spuId, -2);
		if (strlen($i) == 1) {
			$i = '0' . $i;
		}
		$i = strrev($i);
		$i = (int)$i;
		return $i < (int)$sale_rate ? true : false;
	}

	public function getSpuIdByKeyword($keyword)
	{
		$keyword = trim((string)$keyword);
		if ($keyword === '') {
			return false;
		}
		$spuIdArr = [];
		if (ctype_digit($keyword) && (int)$keyword > 0) {
			$spuIdArr[] = (int)$keyword;
		}
		$lanIds = array_values(array_unique([0, 1, (int)lanId()]));
		$lanMatches = service('product/Language')->getListData([
			'lan_id' => ['in', $lanIds],
			'name'   => ['like', '%' . $keyword . '%'],
		], 'spu_id');
		if (!empty($lanMatches)) {
			$spuIdArr = array_merge($spuIdArr, array_column($lanMatches, 'spu_id'));
		}

		// 属性值与规格搜索
		$skuAttrs = service('product/SkuAttr')->getListData([
			'attr_value' => ['like', '%' . $keyword . '%'],
		], 'sku_id');
		if (!empty($skuAttrs)) {
			$skuRows = service('product/Sku')->getListData([
				'sku_id' => ['in', array_values(array_unique(array_column($skuAttrs, 'sku_id')))],
			], 'spu_id');
			if (!empty($skuRows)) {
				$spuIdArr = array_merge($spuIdArr, array_column($skuRows, 'spu_id'));
			}
		}

		return array_values(array_unique(array_map('intval', $spuIdArr)));
	}
}
