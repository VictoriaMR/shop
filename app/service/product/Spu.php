<?php 

namespace app\service\product;
use app\service\Base;

class Spu extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/Spu');
	}

	public function getInfoCache($spuId, $lanId=1)
	{
		$cacheKey = $this->getCacheKey($spuId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($spuId, $lanId);
			redis()->set($cacheKey, $info, $this->getConst('CACHE_EXPIRE_TIME'));
		}
		return $info;
	}

	public function getInfo($spuId, $lanId='en')
	{
		$info = $this->loadData(['spu_id'=>$spuId, 'status'=>$this->getConst('STATUS_OPEN')], 'cate_id,attach_id,min_price,max_price,original_price');
		if (empty($info)) {
			return false;
		}
		//获取sku列表
		$sku = make('app/service/product/Sku');
		$info['sku'] = $sku->getListData(['spu_id'=>$spuId, 'status'=>$this->getConst('STATUS_OPEN')], 'sku_id,attach_id,stock,price,original_price,sale_total');
		if (empty($info['sku'])) {
			return false;
		}
		$info['sku'] = array_column($info['sku'], null, 'sku_id');
		//获取图片集
		$imageArr = $info['image'] = make('app/service/product/SpuImage')->getListById($spuId);
		$imageArr = array_column($imageArr, null, 'attach_id');
		//价格格式化
		$currencyService = make('app/service/Currency');
		$info['min_price_format'] = $currencyService->priceFormat($info['min_price'], 2);
		$info['max_price_format'] = $currencyService->priceFormat($info['max_price'], 2);
		$info['original_price_format'] = $currencyService->priceFormat($info['original_price'], 2);
		//获取语言
		$info['name'] = make('app/service/product/Language')->loadData(['spu_id'=>$spuId, 'lan_id'=>$lanId], 'name', ['lan_id'=>'desc'])['name'] ?? '';
		$info['url'] = router()->urlFormat($info['name'], 'p', ['id' => $spuId]);
		//spu介绍图片
		$info['introduce'] = make('app/service/product/Introduce')->getListById($spuId);
		//spu描述
		$info['description'] = make('app/service/attr/Description')->getListById($spuId, $lanId);

		$info += make('app/service/product/AttrUsed')->getListById(array_keys($info['sku']), $lanId);
		$skuImageList = array_merge(array_column($info['sku'], 'attach_id'), $info['attvImage']);

		if (!empty($tempArr = array_diff($skuImageList, array_keys($imageArr)))) {
			$list = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', array_unique($tempArr)]]);
			$imageArr += array_column($list, null, 'attach_id');
		}

		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $imageArr[$value] ?? [];
		}

		foreach ($info['sku'] as $key => $value) {
			$temp = $currencyService->priceFormat($value['price']);
			$value['price'] = $temp[1];
			$value['price_format'] = $temp[2];
			$temp = $currencyService->priceFormat($value['original_price']);
			$value['original_price'] = $temp[1];
			$value['original_price_format'] = $temp[2];
			$value['image'] = $imageArr[$value['attach_id']]['url'] ?? '';
			$name = [];
			foreach ($info['skuAttv'][$key] as $v) {
				$name[] = $info['attv'][$v];
			}
			$name = implode(' ', $name);
			$value['name'] = $name ? $info['name'].' - '.$name : $info['name'];
			$value['url'] = router()->urlFormat($value['name'], 's', ['id'=>$key]);
			$info['sku'][$key] = $value;
		}
		return $info;
	}

	protected function getCacheKey($spuId, $lanId)
	{
		return $this->getConst('CACHE_INFO_KEY').$spuId.'_'.$lanId;
	}

	public function getAdminList(array $where=[], $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size);
		if (!empty($list)) {
			//图片
			$attachArr = array_unique(array_column($list, 'attach_id'));
			$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			//名称
			$spuIdArr = array_column($list, 'spu_id');
			$nameArr = make('app/service/product/Language')->getListData(['spu_id'=>['in', $spuIdArr], 'lan_id'=>1]);
			$nameArr = array_column($nameArr, 'name', 'spu_id');
			foreach ($list as $key => $value) {
				$value['avatar'] = $attachArr[$value['attach_id']] ?? '';
				$value['name'] = $nameArr[$value['spu_id']] ?? '';
				$value['status_text'] = $this->getStatusList($value['status']);
				$value['url'] = url('product/detail', ['id'=>$value['spu_id']]);
				$list[$key] = $value;
			}
		}
		return $list;
	}

	public function getAdminInfo($spuId)
	{
		$info = $this->loadData($spuId);
		if (empty($info)) return false;
		//分类
		$info['category'] = array_reverse(make('app/service/category/Category')->getParentCategoryById($info['cate_id']));
		//SpuData
		$info['data'] = make('app/service/product/SpuData')->loadData($spuId);
		$info['shop'] = make('app/service/supplier/Shop')->loadData($info['data']['shop_id']);
		//名称
		$info['name'] = make('app/service/product/Language')->loadData(['spu_id'=>$spuId,'lan_id'=>'zh'])['name'];
		//图片
		$info['image'] = make('app/service/product/SpuImage')->getListData(['spu_id'=>$spuId], '*', 0, 0, ['sort'=>'asc']);
		//获取sku列表
		$info['sku'] = make('app/service/product/Sku')->getListData(['spu_id'=>$spuId]);
		//skuData
		$skuIdArr = array_column($info['sku'], 'sku_id');
		$skuData = make('app/service/product/SkuData')->getListData(['sku_id'=>['in', $skuIdArr]]);
		$skuData = array_column($skuData, null, 'sku_id');
		//sku属性
		$attrArr = make('app/service/product/AttrUsed')->getListData(['sku_id'=>['in', $skuIdArr]], '*', 0, 0, ['sort'=>'asc']);
		//属性值名称
		$attrBute = make('app/service/attr/Bute')->getListData(['attr_id'=>['in', array_unique(array_column($attrArr, 'attr_id'))]]);
		$attrBute = array_column($attrBute, null, 'attr_id');
		$attrValue = make('app/service/attr/Value')->getListData(['attv_id'=>['in', array_unique(array_column($attrArr, 'attv_id'))]]);
		$attrValue = array_column($attrValue, null, 'attv_id');
		//图片
		$attachArr = array_unique(array_merge(array_column($info['image'], 'attach_id'), array_column($info['sku'], 'attach_id'), array_column($attrArr, 'attach_id')));
		$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		//sku 属性归类
		$skuAttrArr = [];
		$attrMap = [];
		foreach ($attrArr as $value) {
			if (!isset($skuAttrArr[$value['sku_id']])) $skuAttrArr[$value['sku_id']] = [];
			$value['attr_name'] = isset($attrBute[$value['attr_id']]['name']) ? $attrBute[$value['attr_id']]['name'] : '';
			$value['attv_name'] = isset($attrValue[$value['attv_id']]['name']) ? $attrValue[$value['attv_id']]['name'] : '';
			$value['image'] = $attachArr[$value['attach_id']] ?? '';
			if (!isset($attrMap[$value['attr_id']])) {
				$attrMap[$value['attr_id']] = [
					'attr_id' => $value['attr_id'],
					'attr_name' => $value['attr_name'],
					'son' => [],
				];
			}
			$attrMap[$value['attr_id']]['son'][$value['attv_id']] = [
				'attv_id' => $value['attv_id'],
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
		return $info;
	}

	public function addProduct($data)
	{
		if (empty($data['bc_product_category'])) return false;
		if (empty($data['bc_site_id'])) return false;
		if (empty($data['bc_product_site'])) return false;
		if (empty($data['bc_product_name'])) return false;
		if (empty($data['bc_sku'])) return false;
		if (empty($data['bc_product_img'])) return false;
		//删除链接后缀
		$data['bc_product_url'] = $this->getSupplierItemUrl($data['bc_product_url']);
		if (empty($data['bc_product_url'])) return false;

		//上传或者更新图片
		$spuImageArr = [];
		$firstImage = '';
		if (!is_array($data['bc_product_img'])) {
			$data['bc_product_img'] = array_unique(explode(',', $data['bc_product_img']));
		}
		foreach ($data['bc_product_img'] as $key => $value) {
			$spuImageArr[] = $value;
			if ($key == 0) {
				$firstImage = $value;
			}
		}
		if (empty($spuImageArr)) return false;

		$allImageArr = $spuImageArr;
		//属性组
		$attribute = make('app/service/attr/Bute');
		$attrvalue = make('app/service/attr/Value');
		$attrArr = [];
		$attrValueArr = [];
		//获取属性|属性图片
		$tempImageArr = [];
		foreach ($data['bc_sku'] as $key => $value) {
			if (!empty($value['img'])) {
				$tempImageArr[] = $value['img'];
			}
			$attrArr = array_merge($attrArr, array_keys($value['attr']));
			$attrValueArr = array_merge($attrValueArr, array_column($value['attr'], 'text'));
			$tempImageArr = array_merge($tempImageArr, array_column($value['attr'], 'img'));
		}
		$allImageArr = array_unique(array_merge($allImageArr, array_filter($tempImageArr)));
		$fileService = make('app/service/File');
		$allImageArr = $fileService->uploadUrlImage($allImageArr, 'product');

		//转换成键值对
		$attrArr = $attribute->addNotExist($attrArr);
		$attrValueArr = $attrvalue->addNotExist($attrValueArr);

		$where = [
			'item_id' => $data['bc_product_id'],
			'supplier' => $data['bc_site_id'],
		];	
		$spuData = make('app/service/product/SpuData');
		$info = $spuData->loadData($where, 'spu_id');
		if (empty($info)) {
			//价格合集
			foreach ($data['bc_sku'] as $key => $value) {
				$price = $this->getPrice($value['price']);
				$data['bc_sku'][$key]['sale_price'] = $price;
				$data['bc_sku'][$key]['original_price'] = $this->getOriginalPrice($price);
			}
			$priceArr = array_column($data['bc_sku'], 'sale_price');
			$insert = [
				'status' => 0,
				'site_id' => $data['bc_product_site'],
				'cate_id' => $data['bc_product_category'],
				'attach_id' => $allImageArr[$firstImage] ?? 0,
				'min_price' => min($priceArr),
				'max_price' => max($priceArr),
				'original_price' => $this->getOriginalPrice(max($priceArr)), //虚拟原价
			];
			$this->start();
			$spuId = $this->insertGetId($insert);
			//spu扩展数据
			$insert = [
				'spu_id' => $spuId,
				'supplier' => $data['bc_site_id'],
				'item_id' => $data['bc_product_id'],
				'item_url' => $data['bc_product_url'],
				'shop_id' => make('app/service/supplier/Shop')->addNotExist(['url'=>$data['bc_shop_url'], 'name'=>$data['bc_shop_name']]),
			];
			$spuData->insert($insert);
			//中文语言
			make('app/service/product/Language')->insert(['spu_id'=>$spuId, 'lan_id'=>'zh', 'name'=>$data['bc_product_name']]);
			//spu图片组
			$insert = [];
			$count = 1;
			foreach ($spuImageArr as $value) {
				if (isset($allImageArr[$value])) {
					$insert[] = [
						'spu_id' => $spuId,
						'attach_id' => $allImageArr[$value],
						'sort' => $count++,
					];
				}
			}
			if (!empty($insert)) {
				make('app/service/product/SpuImage')->addSpuImage($insert);
			}
			//sku
			$sku = make('app/service/product/Sku');
			$skuData = make('app/service/product/SkuData');
			foreach ($data['bc_sku'] as $key => $value) {
				if (empty($value['stock'])) continue;
				$insert = [
					'spu_id' => $spuId,
					'status' => 1,
					'attach_id' => empty($value['img']) ? 0 : $allImageArr[$value['img']] ?? 0,
					'stock' => $value['stock'],
					'price' => $value['sale_price'],
					'original_price' => $value['original_price'],
				];
				$skuId = $sku->insertGetId($insert);
				$insert = [
					'sku_id' => $skuId,
					'item_id' => $value['sku_id'],
					'cost_price' => $value['price'],
				];
				$skuData->insert($insert);
				//属性关联
				$insert = [];
				$count = 1;
				foreach ($value['attr'] as $k => $v) {
					$insert[] = [
						'sku_id' => $skuId,
						'attr_id' => $attrArr[$k],
						'attv_id' => $attrValueArr[$v['text']],
						'attach_id' => empty($v['img']) ? 0 : $allImageArr[$v['img']] ?? 0,
						'sort' => $count++,
					];
				}
				if (!empty($insert)) {
					make('app/service/product/AttrUsed')->addAttrUsed($skuId, $insert);
				}
			}
			$this->commit();
		} else {
			$spuId = $info['spu_id'];
		}
		if (empty($spuId)) {
			return '产品SPU入库失败!';
		}
		//spu 介绍图片
		$insert = [];
		$count = 1;
		$allImageArr = array_unique(explode(',', $data['bc_product_des_picture']));
		$allImageArr = $fileService->uploadUrlImage($allImageArr, 'introduce', false);
		foreach ($allImageArr as $value) {
			$insert[$spuId.'-'.$value] = [
				'spu_id' => $spuId,
				'attach_id' => $value,
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			make('app/service/product/IntroduceUsed')->addIntroduceUsed($spuId, $insert);
		}

		//spu介绍文本
		$descArr = [];
		$insert = [];
		$descService = make('app/service/attr/Description');
		$descArr = array_merge(array_column($data['bc_des_text'], 'key'), array_column($data['bc_des_text'], 'value'));
		$descArr = $descService->addNotExist($descArr);
		foreach ($data['bc_des_text'] as $key => $value) {
			$nameId = $descArr[$value['key']];
			$valueId = $descArr[$value['value']];
			$uniqueid = $nameId.'-'.$valueId;
			$insert[$uniqueid] = [
				'spu_id' => $spuId,
				'name_id' => $nameId,
				'value_id' => $valueId,
			];
		}
		if (!empty($insert)) {
			make('app/service/product/DescriptionUsed')->addDescUsed($spuId, $insert);
		}
		$cacheKey = 'queue-add-product:'.$data['bc_site_id'];
		redis(2)->hDel($cacheKey, $data['bc_product_id']);
		return true;
	}

	protected function getPrice($price)
	{
		if ($price < 200) {
			$price += 200;
		} elseif ($price < 400) {
			$price += 300;
		} else {
			$price += 400;
		}
		return $price;
	}

	protected function getOriginalPrice($price)
	{
		if ($price < 200) {
			$price += 300;
		} elseif ($price < 400) {
			$price += 400;
		} else {
			$price += 500;
		}
		return $price;
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

	public function getRecommend($page=1, $size=20)
	{
		//获取收藏商品分类
		$memId = $this->userId();
		if (!empty($memId)) {
			$where = [
				'mem_id' => $memId,
			];
			$collSpuList = make('app/service/member/Collect')->getListData($where, 'spu_id');
			$hisSpuList = make('app/service/member/History')->getListData($where, 'spu_id');
			if (!empty($collSpuList) || !empty($hisSpuList)) {
				$spuList = array_unique(array_column(array_merge($collSpuList, $hisSpuList), 'spu_id'));
			}
			$collSpuList = array_column($collSpuList, 'spu_id');
		}
		$where = ['site_id' => siteId(), 'status'=>$this->getConst('STATUS_OPEN')];
		if (!empty($spuList)) {
			$cateList = $this->getListData(['spu_id'=>['in'=>$spuList]], 'cate_id');
			$where['cate_id'] = ['in', array_column($cateList, 'cate_id')];
		}
		$list = $this->getListData($where, 'spu_id,attach_id,min_price,max_price,original_price', $page, $size, ['sale_total+visit_total'=>'desc']);
		if (!empty($list)) {
			$spuList = array_column($list, 'spu_id');
			//获取语言
			$lanArr = make('app/service/product/Language')->getListData(['spu_id'=>['in', $spuList], 'lan_id'=>$this->lanId()], 'spu_id,name');
			$lanArr = array_column($lanArr, 'name', 'spu_id');
			//获取图片集
			$attachArr = array_unique(array_column($list, 'attach_id'));
			$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			$currencyService = make('app/service/Currency');
			//格式化数组
			foreach($list as $key => $value) {
				$value['name'] = $lanArr[$value['spu_id']] ?? '';
				$value['url'] = router()->urlFormat($value['name'], 'p', ['id'=>$value['spu_id']]);
				$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
				$temp = $currencyService->priceFormat($value['min_price']);
				$value['min_price'] = $temp[1];
				$value['min_price_format'] = $temp[2];
				$temp = $currencyService->priceFormat($value['max_price']);
				$value['max_price'] = $temp[1];
				$value['max_price_format'] = $temp[2];
				$temp = $currencyService->priceFormat($value['original_price']);
				$value['original_price'] = $temp[1];
				$value['original_price_format'] = $temp[2];
				$value['is_liked'] = empty($collSpuList) ? false : in_array($value['spu_id'], $collSpuList);
				$list[$key] = $value;
			}
		}
		return $list;
	}

	public function getListById($id)
	{
		$list = $this->getListData(['spu_id'=>['in'=>$id]], 'spu_id,attach_id,min_price,max_price,original_price');
		$spuList = array_column($list, 'spu_id');
		//获取语言
		$lanArr = make('app/service/product/Language')->getListData(['spu_id'=>['in', $spuList], 'lan_id'=>['in', ['en', lanId()]]], 'spu_id,name', 0, 0, ['lan_id'=>'asc']);
		$lanArr = array_column($lanArr, 'name', 'spu_id');
		//获取图片集
		$attachArr = array_unique(array_column($list, 'attach_id'));
		$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		$currency = make('app/service/Currency');
		//格式化数组
		foreach($list as $key => $value) {
			$value['name'] = $lanArr[$value['spu_id']] ?? '';
			$value['url'] = router()->urlFormat($value['name'], 'p', ['id'=>$value['spu_id']]);
			$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
			$temp = $currency->priceFormat($value['min_price']);
			$value['min_price'] = $temp[1];
			$value['min_price_format'] = $temp[2];
			$temp = $currency->priceFormat($value['max_price']);
			$value['max_price'] = $temp[1];
			$value['max_price_format'] = $temp[2];
			$temp = $currency->priceFormat($value['original_price']);
			$value['original_price'] = $temp[1];
			$value['original_price_format'] = $temp[2];
			$list[$key] = $value;
		}
		return $list;
	}
}