<?php 

namespace app\service\product;
use app\service\Base;

class Spu extends Base
{
	public function getInfoCache($spuId, $lanId=1, $siteId=0)
	{
		$cacheKey = $this->getCacheKey($spuId, $lanId);
		$info = redis()->get($cacheKey);
		if ($info === false) {
			$info = $this->getInfo($spuId, $lanId, $siteId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		if ($info) {
			$info = $this->infoFormat($info);
		}
		return $info;
	}

	protected function infoFormat($info)
	{
		//价格格式化
		$currencyService = service('currency/Currency');
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
		$info['url'] = url($info['name'], ['p' => $info['spu_id']]);

		$info['image'] = $this->completeImage($info['image']);
		foreach ($info['image_list'] as $key=>$value) {
			$info['image_list'][$key] = $this->completeImage($value);
		}
		foreach ($info['introduce'] as $key=>$value) {
			$info['introduce'][$key] = $this->completeImage($value);
		}
		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $this->completeImage($value);
		}

		foreach ($info['sku'] as $key => $value) {
			$value['original_price'] = $this->getOriginalPrice($value['price']);
			$temp = $currencyService->priceFormat($value['price']);
			$value['price'] = $temp[1];
			$value['price_format'] = $temp[2];
			$temp = $currencyService->priceFormat($value['original_price']);
			$value['original_price'] = $temp[1];
			$value['original_price_format'] = $temp[2];
			$name = [];
			foreach ($info['skuAttv'][$key] as $v) {
				$name[] = $info['attv'][$v];
			}
			$name = implode(' ', $name);
			$value['name'] = $name ? $info['name'].' - '.$name : $info['name'];
			$value['url'] = url($value['name'], ['s'=>$key]);
			$value['image'] = $this->completeImage($value['image']);
			$info['sku'][$key] = $value;
		}
		return $info;
	}

	protected function completeImage($image)
	{
		if (empty($image)) {
			return $image;
		}
		return mediaUrl($image);
	}

	public function getInfo($spuId, $lanId=1, $siteId=0)
	{
		$where = ['spu_id'=>$spuId];
		if ($siteId) {
			$where['site_id'] = $siteId;
		}
		$info = $this->loadData($where, 'spu_id,status,cate_id,attach_id,min_price,max_price,free_ship,is_hot');
		if (!$info) return [];
		if ($info['status'] != $this->getConst('STATUS_OPEN')) {
			$info['image'] = $this->attachmentFormat(service('attachment/Attachment')->getList(['attach_id'=>$info['attach_id']]), 400, false);
			$lanArr = array_unique([1, $lanId]);
			$info['name'] = service('product/Language')->loadData(['spu_id'=>$spuId, 'lan_id'=>['in', $lanArr]], 'name', ['lan_id'=>'desc'])['name'] ?? '';
			return $info;
		}
		//获取sku列表
		$sku = service('product/Sku');
		$info['sku'] = $sku->getListData(['spu_id'=>$spuId, 'status'=>$this->getConst('STATUS_OPEN')], 'sku_id,attach_id,stock,price,sale_total');
		if (!$info['sku']) {
			return $info;
		}
		$info['sku'] = array_column($info['sku'], null, 'sku_id');
		//获取图片集
		$info['image_list'] = service('product/SpuImage')->getListById($spuId);
		$imageArr = array_column($info['image_list'], 'attach_id');
		//获取语言,默认拿英文
		$lanArr = array_unique([1, $lanId]);
		$info['name'] = service('product/Language')->loadData(['spu_id'=>$spuId, 'lan_id'=>['in', $lanArr]], 'name', ['lan_id'=>'desc'])['name'] ?? '';
		//spu介绍图片
		$info['introduce'] = service('product/IntroUsed')->getListById($spuId);
		//spu描述
		$info['description'] = service('product/DescUsed')->getListById($spuId, $lanId);
		$info += service('product/AttrUsed')->getListBySkuIds(array_keys($info['sku']), $lanId);

		$imageArr = array_filter(array_merge($imageArr, array_column($info['sku'], 'attach_id'), $info['attvImage'], array_column($info['introduce'], 'attach_id')));
		if (!empty($imageArr)) {
			$imageArr = service('attachment/Attachment')->getList(['attach_id'=>['in', array_unique($imageArr)]], 400, false);
			$imageArr = array_column($imageArr, null, 'attach_id');
			foreach ($imageArr as $key=>$value) {
				$imageArr[$key] = $this->attachmentFormat($value);
			}
		}
		//图片
		$info['image'] = $imageArr[$info['attach_id']] ?? '';
		foreach ($info['image_list'] as $key=>$value) {
			$info['image_list'][$key] = $imageArr[$value['attach_id']] ?? '';
		}
		foreach ($info['introduce'] as $key=>$value) {
			$info['introduce'][$key] = $imageArr[$value['attach_id']] ?? '';
		}
		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $imageArr[$value] ?? '';
		}
		foreach ($info['sku'] as $key => $value) {
			$info['sku'][$key]['image'] = $imageArr[$value['attach_id']] ?? '';
		}
		return $info;
	}

	public function attachmentFormat($data, $type=400)
	{
		if (empty($data)) {
			return '';
		}
		return $data['cate'].DS.$data['name'].DS.$type.'.'.$data['type'];
	}

	protected function getCacheKey($spuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY').$spuId.':'.$lanId;
	}

	public function getList(array $where=[], $fields='*', $page=0, $size=20, $order=[], $lanId=0, $priceFormat=false, $isLiked=false)
	{
		$list = $this->getListData($where, $fields, $page, $size, $order);
		if (!$list) {
			return [];
		}
		$spuList = array_column($list, 'spu_id');
		//获取语言
		$where = ['spu_id'=>['in', $spuList]];
		if (isAdmin()) {
			$where['lan_id'] = 0;
		} else {
			if ($lanId == 1 || !$lanId) {
				$where['lan_id'] = $lanId;
			} else {
				$where['lan_id'] = ['in', [1, $lanId]];
			}
		}
		$lanArr = service('product/Language')->getListData($where, 'spu_id,name', 0, 0, ['lan_id'=>'asc']);
		$lanArr = array_column($lanArr, 'name', 'spu_id');
		//获取图片集
		$attachArr = array_unique(array_column($list, 'attach_id'));
		$attachArr = service('attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		$currency = service('currency/Currency');
		$likeArr = [];
		if ($isLiked && $this->userId()) {
			$likeArr = service('member/Collect')->getListData(['mem_id'=>$this->userId(), 'spu_id'=>['in', $spuList]], 'spu_id');
			$likeArr = array_column($likeArr, 'spu_id');
		}
		//格式化数组
		foreach($list as $key => $value) {
			$value['name'] = $lanArr[$value['spu_id']] ?? '';
			$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
			if ($priceFormat) {
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
		$info = $this->loadData($spuId);
		if (empty($info)) return false;
		//分类
		$info['category'] = array_reverse(service('category/Category')->pCate($info['cate_id']));
		//SpuData
		$info['data'] = service('product/SpuData')->loadData($spuId);
		$info['shop'] = service('supplier/Shop')->loadData($info['data']['shop_id']);
		//名称
		$info['name'] = service('product/Language')->loadData(['spu_id'=>$spuId,'lan_id'=>'zh'], 'name')['name'];
		//图片
		$info['image'] = service('product/SpuImage')->getListData(['spu_id'=>$spuId], '*', 0, 0, ['sort'=>'asc']);
		//获取sku列表
		$info['sku'] = service('product/Sku')->getListData(['spu_id'=>$spuId]);
		//skuData
		$skuIdArr = array_column($info['sku'], 'sku_id');
		$skuData = service('product/SkuData')->getListData(['sku_id'=>['in', $skuIdArr]]);
		$skuData = array_column($skuData, null, 'sku_id');
		//sku属性
		$attrArr = service('product/AttrUsed')->getListData(['sku_id'=>['in', $skuIdArr]], '*', 0, 0, ['sort'=>'asc']);
		//属性值名称
		$attrBute = service('attr/Name')->getListData(['attrn_id'=>['in', array_unique(array_column($attrArr, 'attrn_id'))]]);
		$attrBute = array_column($attrBute, null, 'attrn_id');
		$attrValue = service('attr/Value')->getListData(['attrv_id'=>['in', array_unique(array_column($attrArr, 'attrv_id'))]]);
		$attrValue = array_column($attrValue, null, 'attrv_id');
		//描述
		$info['description'] = service('product/DescUsed')->getListData(['spu_id'=>$spuId]);
		//描述图片
		$info['introduce'] = service('product/IntroUsed')->getListData(['spu_id'=>$spuId], '*', 0, 0, ['sort'=>'asc']);
		//图片
		$attachArr = array_unique(array_merge(array_column($info['image'], 'attach_id'), array_column($info['sku'], 'attach_id'), array_column($attrArr, 'attach_id'), array_column($info['introduce'], 'attach_id')));
		$attachArr = service('attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		//sku 属性归类
		$skuAttrArr = [];
		$attrMap = [];
		foreach ($attrArr as $value) {
			if (!isset($skuAttrArr[$value['sku_id']])) $skuAttrArr[$value['sku_id']] = [];
			$value['attr_name'] = isset($attrBute[$value['attrn_id']]['name']) ? $attrBute[$value['attrn_id']]['name'] : '';
			$value['attv_name'] = isset($attrValue[$value['attrv_id']]['name']) ? $attrValue[$value['attrv_id']]['name'] : '';
			$value['image'] = $attachArr[$value['attach_id']] ?? '';
			if (!isset($attrMap[$value['attrn_id']])) {
				$attrMap[$value['attrn_id']] = [
					'attrn_id' => $value['attrn_id'],
					'attr_name' => $value['attr_name'],
					'son' => [],
				];
			}
			$attrMap[$value['attrn_id']]['son'][$value['attrv_id']] = [
				'attrv_id' => $value['attrv_id'],
				'attv_name' => $value['attv_name'],
				'attach_id' => $value['attach_id'],
				'image' => $value['image'],
			];
			$skuAttrArr[$value['sku_id']][] = $value;
		}
		$info['attr_map'] = $attrMap;
		foreach ($info['sku'] as $key => $value) {
			$value = array_merge($value, $skuData[$value['sku_id']]);
			$value['image'] = $attachArr[$value['attach_id']] ?? '';
			$value['attr'] = $skuAttrArr[$value['sku_id']];
			$info['sku'][$key] = $value;
		}
		foreach ($info['image'] as $key => $value) {
			$info['image'][$key]['image'] = $attachArr[$value['attach_id']] ?? '';
		}
		foreach ($info['introduce'] as $key => $value) {
			$info['introduce'][$key]['image'] = str_replace(['/400', '.220x220'], '', $attachArr[$value['attach_id']] ?? '');
		}
		//描述
		$descArr = service('product/DescUsed')->getListData(['spu_id'=>$spuId], '*', 0, 0, ['sort'=>'asc']);
		$descNameArr = service('desc/Name')->getListData(['descn_id'=>['in', array_column($descArr, 'descn_id')]]);
		$descValueArr = service('desc/Value')->getListData(['descv_id'=>['in', array_column($descArr, 'descv_id')]]);
		$descGroupArr = service('desc/Group')->getListData(['descg_id'=>['in', array_column($descArr, 'descg_id')]]);
		$descGroupArr = array_column($descGroupArr, 'name', 'descg_id');
		$descNameArr = array_column($descNameArr, 'name', 'descn_id');
		$descValueArr = array_column($descValueArr, 'name', 'descv_id');
		foreach($descArr as $key=>$value) {
			$descArr[$key]['group'] = $descGroupArr[$value['descg_id']] ?? '';
			$descArr[$key]['name'] = $descNameArr[$value['descn_id']] ?? '';
			$descArr[$key]['value'] = $descValueArr[$value['descv_id']] ?? '';
		}
		$info['desc'] = $descArr;
		return $info;
	}

	protected function getPrice($price)
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
		return number_format($price / $rate, 2);
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
			parse_str($url[1], $params);
			$id = $params['id'] ?? '';
			if (empty($id)) {
				return '';
			}
			return $url[0].(empty($id) ? '': '?id='.$id);
		}
	}

	public function getRecommend($page=1, $size=20, &$total=0)
	{
		//获取收藏商品分类
		$memId = $this->userId();
		$collSpuList = [];
		if (!empty($memId)) {
			$where = [
				'mem_id' => $memId,
			];
			$collSpuList = service('member/Collect')->getListData($where, 'spu_id');
			$hisSpuList = service('member/History')->getListData($where, 'spu_id');
			if (!empty($collSpuList) || !empty($hisSpuList)) {
				$spuList = array_unique(array_column(array_merge($collSpuList, $hisSpuList), 'spu_id'));
			}
			$collSpuList = array_column($collSpuList, 'spu_id');
		}
		$where = ['site_id' => siteId(), 'status'=>$this->getConst('STATUS_OPEN')];
		$orderBy = [];
		if (!empty($spuList)) {
			$cateList = $this->getListData(['spu_id'=>['in', $spuList]], 'cate_id');
			if (!empty($cateList)) {
				$orderBy['case when cate_id in ('.implode(',', array_column($cateList, 'cate_id')).') then 1 else 0 end desc'] = 'desc';
			}
		}
		$orderBy['rank'] = 'desc';
		$total = $this->getCountData($where);
		$list = $this->getList($where, 'spu_id,attach_id,min_price,max_price,free_ship,is_hot', $page, $size, [], lanId(), true);
		foreach ($list as $key=>$value) {
			$list[$key]['url'] = url($value['name'], ['p'=>$value['spu_id']]);
		}
		return $list;
	}

	public function showPrice($spuId)
	{
		$sale_rate = 75;
		$i = substr((string)$spuId, -2);
		if(strlen($i) == 1){
			$i = '0' . $i;
		}
		$i = strrev($i);
		$i = (int)$i;
		return $i<(int)$sale_rate?true:false;
	}

	public function getSpuIdByKeyword($keyword)
	{
		if (empty($keyword)) {
			return false;
		}
		$spuIdArr = service('product/Language')->getListData(['lan_id'=>lanId(), 'name'=>['like', '%'.$keyword.'%']], 'spu_id');
		$spuIdArr = array_column($spuIdArr, 'spu_id');
		//attrname
		$attrnIdarr = service('attr/NameLanguage')->getListData(['lan_id'=>lanId(), 'name'=>['like', '%'.$keyword.'%']], 'attrn_id');
		$attrvIdarr = service('attr/ValueLanguage')->getListData(['lan_id'=>lanId(), 'name'=>['like', '%'.$keyword.'%']], 'attrv_id');
		$skuIdArr = [];
		if (!empty($attrnIdarr)) {
			$tempIdArr = service('product/AttrUsed')->getListData(['attrn_id'=>['in', array_column($attrnIdarr, 'attrn_id')]]);
			$skuIdArr = array_column($tempIdArr, 'sku_id');
		}
		if (!empty($attrvIdarr)) {
			$tempIdArr = service('product/AttrUsed')->getListData(['attrv_id'=>['in', array_column($attrvIdarr, 'attrv_id')]]);
			$skuIdArr = array_unique(array_merge($skuIdArr, array_column($tempIdArr, 'sku_id')));
		}
		if (!empty($skuIdArr)) {
			$tempIdArr = service('product/Sku')->getListData(['sku_id'=>['in', $skuIdArr]], 'spu_id');
			$spuIdArr = array_unique(array_merge($spuIdArr, array_column($tempIdArr, 'spu_id')));
		}
		return $spuIdArr;
	}
}