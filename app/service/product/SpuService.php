<?php 

namespace app\service\product;
use app\service\Base;

class SpuService extends Base
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

	public function getInfo($spuId, $lanId=1)
	{
		$info = $this->loadData(['spu_id'=>$spuId, 'status'=>$this->getConst('STATUS_OPEN')], 'cate_id,attach_id,min_price,max_price,original_price');
		if (empty($info)) {
			return false;
		}
		//获取sku列表
		$skuService = make('app/service/product/SkuService');
		$info['sku'] = $skuService->getListData(['spu_id'=>$spuId, 'status'=>$this->getConst('STATUS_OPEN')], 'sku_id,attach_id,stock,price,original_price,sale_total');
		if (empty($info['sku'])) {
			return false;
		}
		$info['sku'] = array_column($info['sku'], null, 'sku_id');
		//获取图片集
		$imageArr = $info['image'] = make('app/service/product/SpuImageService')->getListById($spuId);
		$imageArr = array_column($imageArr, null, 'attach_id');
		//价格格式化
		$languageService = make('app/service/LanguageService');
		$info['min_price_format'] = $languageService->priceFormat($info['min_price'], 2);
		$info['max_price_format'] = $languageService->priceFormat($info['max_price'], 2);
		$info['original_price_format'] = $languageService->priceFormat($info['original_price'], 2);
		//获取语言
		$info['name'] = make('app/service/product/LanguageService')->loadData(['spu_id'=>$spuId, 'lan_id'=>['in', [1, $lanId]]], 'name', ['lan_id'=>'desc'])['name'] ?? '';
		$info['url'] = router()->urlFormat($info['name'], 'p', ['id' => $spuId]);
		//spu介绍图片
		$info['introduce'] = make('app/service/product/IntroduceService')->getListById($spuId);
		//spu描述
		$info['description'] = make('app/service/product/DescriptionService')->getListById($spuId, $lanId);

		$info += make('app/service/product/AttrRelationService')->getListById(array_keys($info['sku']), $lanId);
		$skuImageList = array_merge(array_column($info['sku'], 'attach_id'), $info['attvImage']);

		if (!empty($tempArr = array_diff($skuImageList, array_keys($imageArr)))) {
			$list = make('app/service/AttachmentService')->getList(['attach_id'=>['in', array_unique($tempArr)]]);
			$imageArr += array_column($list, null, 'attach_id');
		}

		foreach ($info['attvImage'] as $key => $value) {
			if (empty($value)) continue;
			$info['attvImage'][$key] = $imageArr[$value] ?? [];
		}

		foreach ($info['sku'] as $key => $value) {
			$temp = $languageService->priceFormat($value['price']);
			$value['price'] = $temp[1];
			$value['price_format'] = $temp[2];
			$temp = $languageService->priceFormat($value['original_price']);
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
			$attachArr = make('app/service/AttachmentService')->getList(['attach_id'=>['in', $attachArr]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			//名称
			$spuIdArr = array_column($list, 'spu_id');
			$nameArr = make('app/service/product/LanguageService')->getListData(['spu_id'=>['in', $spuIdArr], 'lan_id'=>1]);
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
		if (empty($info)) {
			return false;
		}
		//名称
		$info['name'] = make('app/service/product/LanguageService')->loadData(['spu_id'=>$spuId,'lan_id'=>1])['name'];
		//图片
		$info['image'] = make('app/service/product/SpuImageService')->getListBySpuId($spuId);
		
		return $info;
	}

	public function addProduct($data)
	{
		if (empty($data['bc_product_category'])) {
			return '产品分类不能为空!';
		}
		$supplierId = $this->getSupplierSiteId($data['bc_site_id']);
		if (empty($supplierId)) {
			return '供应商不能为空!';
		}
		if (empty($data['bc_product_site'])) {
			return '站点不能为空!';
		}
		$data['bc_product_name'] = trim($data['bc_product_name']);
		if (empty($data['bc_product_name'])) {
			return '产品标题不能为空!';
		}
		if (empty($data['bc_sku'])) {
			return '产品SKU不能为空!';
		}
		if (empty($data['bc_product_img'])) {
			return '产品图片不能为空!';
		}
		//删除链接后缀
		$data['bc_product_url'] = $this->getSupplierItemUrl($data['bc_product_url']);
		if (empty($data['bc_product_url'])) {
			return '产品链接不能为空!';
		}
		$fileService = make('app/service/FileService');

		//上传或者更新图片
		$spuImageArr = [];
		$firstImage = [];
		if (!is_array($data['bc_product_img'])) {
			$data['bc_product_img'] = array_unique(explode(',', $data['bc_product_img']));
		}
		foreach ($data['bc_product_img'] as $key => $value) {
			$url = $this->filterUrl($value);
			$rst = $fileService->uploadUrlImage($url, 'product');
			if (!$rst) {
				continue;
			}
			$spuImageArr[$url] = $rst;
			if ($key == 0) {
				$firstImage = $spuImageArr[$url]['attach_id'];
			}
		}
		$spuImageArr = array_filter($spuImageArr);
		if (empty($spuImageArr)) {
			return '产品图片上传失败!';
		}
		//属性组
		$attributeService = make('app/service/attr/ButeService');
		$attrvalueService = make('app/service/attr/ValueService');
		$productLanguageService = make('app/service/product/LanguageService');
		$spuDataService = make('app/service/product/spuDataService');
		$spuImageService = make('app/service/product/SpuImageService');
		$attrArr = [];
		$attrValueArr = [];
		foreach ($data['bc_sku'] as $key => $value) {
			$attrArr = array_merge($attrArr, array_keys($value['attr']));
			$attrValueArr = array_merge($attrValueArr, array_column($value['attr'], 'text'));
		}
		//转换成键值对
		$attrArr = array_flip($attrArr);
		$attrValueArr = array_flip($attrValueArr);
		foreach ($attrArr as $key => $value) {
			$attrArr[$key] = $attributeService->addNotExist($key);
		}
		foreach ($attrValueArr as $key => $value) {
			$attrValueArr[$key] = $attrvalueService->addNotExist($key);
		}
		$where = [
			'site_id' => $data['bc_product_site'],
			'item_id' => $data['bc_product_id'],
			'supplier_id' => $supplierId,
		];	
		$info = $spuDataService->loadData($where, 'spu_id');
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
				'attach_id' => $firstImage,
				'min_price' => min($priceArr),
				'max_price' => max($priceArr),
				'original_price' => $this->getOriginalPrice(max($priceArr)), //虚拟原价
				'add_time' => now(),
			];
			//事务开启
			$spuId = $this->insertGetId($insert);
			//spu扩展数据
			$insert = [
				'spu_id' => $spuId,
				'site_id' => $data['bc_product_site'],
				'supplier_id' => $supplierId,
				'item_id' => $data['bc_product_id'],
				'item_url' => $data['bc_product_url'],
				'shop_name' => $data['bc_shop_name'],
				'shop_url' => $data['bc_shop_url'],
			];
			$spuDataService->insert($insert);
			//spu 多语言
			$insert = [
				'spu_id' => $spuId,
				'lan_id' => 1,
				'name' => $data['bc_product_name'],
			];
			$productLanguageService->insert($insert);

			//spu图片组
			$insert = [];
			$count = 1;
			foreach ($spuImageArr as $value) {
				$insert[] = [
					'spu_id' => $spuId,
					'attach_id' => $value['attach_id'],
					'sort' => $count++,
				];
			}
			if (!empty($insert)) {
				$spuImageService->addSpuImage($insert);
			}

			//sku
			$skuService = make('app/service/product/SkuService');
			foreach ($data['bc_sku'] as $key => $value) {
				if (empty($value['stock'])) continue;

				if (!empty($value['img'])) {
					$value['img'] = $this->filterUrl($value['img']);
					if (empty($spuImageArr[$value['img']])) {
						$rst = $fileService->uploadUrlImage($value['img'], 'product');
						if (empty($rst)) {
							continue;
						}
						$spuImageArr[$value['img']] = $rst;
					}
				}
				$insert = [
					'spu_id' => $spuId,
					'status' => 0,
					'attach_id' => $spuImageArr[$value['img']]['attach_id'] ?? 0,
					'stock' => $value['stock'],
					'price' => $value['sale_price'],
					'original_price' => $value['original_price'],
					'cost_price' => $value['price'],
					'item_id' => $value['sku_id'],
					'add_time' => now(),
				];
				$skuId = $skuService->insertGetId($insert);
				//属性关联
				$insert = [];
				$count = 1;
				foreach ($value['attr'] as $k => $v) {
					if (empty($v['img'])) {
						$attachId = 0;
					} else {
						$v['img'] = $this->filterUrl($v['img']);
						if (empty($spuImageArr[$v['img']])) {
							$spuImageArr[$v['img']] = $fileService->uploadUrlImage($v['img'], 'product');
						}
						$attachId = $spuImageArr[$v['img']]['attach_id'];
					}
					$insert[] = [
						'sku_id' => $skuId,
						'attr_id' => $attrArr[$k],
						'attv_id' => $attrValueArr[$v['text']],
						'attach_id' => $attachId,
						'sort' => $count++,
					];
				}
				if (!empty($insert)) {
					make('app/service/product/AttrRelationService')->insert($insert);
				}
			}
		} else {
			$spuId = $info['spu_id'];
		}
		if (empty($spuId)) {
			return '产品SPU入库失败!';
		}
		
		//spu 介绍图片
		$insert = [];
		$count = 1;
		$data['bc_product_des_picture'] = array_unique(explode(',', $data['bc_product_des_picture']));
		foreach ($data['bc_product_des_picture'] as $value) {
			$url = $this->filterUrl($value);
			if (empty($spuImageArr[$url])) {
				$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'introduce', false);
			}
			if (empty($spuImageArr[$url]['attach_id'])) continue;
			if (isset($insert[$spuImageArr[$url]['attach_id']])) continue;
			$insert[$spuImageArr[$url]['attach_id']] = [
				'spu_id' => $spuId,
				'attach_id' => $spuImageArr[$url]['attach_id'],
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			make('app/service/product/IntroduceService')->addIntroduceImage($insert);
		}

		//spu介绍文本
		$descService = make('app/service/product/DescriptionService');
		$descArr = [];
		$insert = [];
		foreach ($data['bc_des_text'] as $key => $value) {
			$value['key'] = mb_substr(trim($value['key'], chr(0xc2).chr(0xa0)), 0, 120);
			$value['value'] = mb_substr(trim($value['value'], chr(0xc2).chr(0xa0)), 0, 120);
			$descArr[$value['key']] = $descService->addNotExist($value['key']);
			$descArr[$value['value']] = $descService->addNotExist($value['value']);
			$insert[] = [
				'spu_id' => $spuId,
				'name_id' => $descArr[$value['key']],
				'value_id' => $descArr[$value['value']],
			];
		}
		make('app/service/product/DescriptionRelationService')->addDescRelation($insert);
		$cacheKey = 'queue-add-product:'.$data['bc_site_id'];
		redis(2)->hDel($cacheKey, $data['bc_product_id']);
		return true;
	}

	protected function getPrice($price)
	{
		if ($price < 200) {
			$price += rand(180, 240);
		} elseif ($price < 400) {
			$price += rand(240, 300);
		} else {
			$price += rand(300, 400);
		}
		return $price;
	}

	protected function getOriginalPrice($price)
	{
		return $price * (rand(10, 60)/100 + 1);
	}

	protected function getSupplierSiteId($name)
	{
		$siteIdArr = [
			'1688' => 1,
			'taobao' => 2,
			'tmall' => 3
		];
		return $siteIdArr[$name] ?? 0;
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

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800', '_.webp'], '', explode('?', $url)[0]);
	}

	public function getRecommend($page=1, $size=20)
	{
		//获取收藏商品分类
		$where = [
			'mem_id' => userId(),
		];
		$collSpuList = make('app/service/member/CollectService')->getListData($where, 'spu_id');
		$hisSpuList = make('app/service/member/HistoryService')->getListData($where, 'spu_id');
		if (!empty($collSpuList) || !empty($hisSpuList)) {
			$spuList = array_unique(array_column(array_merge($collSpuList, $hisSpuList), 'spu_id'));
		}
		$where = ['site_id' => siteId(), 'status'=>$this->getConst('STATUS_OPEN')];
		if (!empty($spuList)) {
			$cateList = $this->getListData(['spu_id'=>['in'=>$spuList]], 'cate_id');
			$where['cate_id'] = ['in', array_column($cateList, 'cate_id')];
		}
		$list = $this->getListData($where, 'spu_id,attach_id,min_price,max_price,original_price', $page, $size, ['sale_total+visit_total'=>'desc']);
		if (!empty($list)) {
			$spuList = array_column($list, 'spu_id');
			$collSpuList = array_column($collSpuList, 'spu_id');
			//获取语言
			$lanArr = make('app/service/product/LanguageService')->getListData(['spu_id'=>['in', $spuList], 'lan_id'=>['in', [1, lanId()]]], 'spu_id,name', 0, 0, ['lan_id'=>'asc']);
			$lanArr = array_column($lanArr, 'name', 'spu_id');
			//获取图片集
			$attachArr = array_unique(array_column($list, 'attach_id'));
			$attachArr = make('app/service/AttachmentService')->getList(['attach_id'=>['in', $attachArr]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			$languageService = make('app/service/LanguageService');
			//格式化数组
			foreach($list as $key => $value) {
				$value['name'] = $lanArr[$value['spu_id']] ?? '';
				$value['url'] = router()->urlFormat($value['name'], 'p', ['id'=>$value['spu_id']]);
				$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
				$temp = $languageService->priceFormat($value['min_price']);
				$value['min_price'] = $temp[1];
				$value['min_price_format'] = $temp[2];
				$temp = $languageService->priceFormat($value['max_price']);
				$value['max_price'] = $temp[1];
				$value['max_price_format'] = $temp[2];
				$temp = $languageService->priceFormat($value['original_price']);
				$value['original_price'] = $temp[1];
				$value['original_price_format'] = $temp[2];
				$value['is_liked'] = in_array($value['spu_id'], $collSpuList) ? 1 : 0;
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
		$lanArr = make('app/service/product/LanguageService')->getListData(['spu_id'=>['in', $spuList], 'lan_id'=>['in', [1, lanId()]]], 'spu_id,name', 0, 0, ['lan_id'=>'asc']);
		$lanArr = array_column($lanArr, 'name', 'spu_id');
		//获取图片集
		$attachArr = array_unique(array_column($list, 'attach_id'));
		$attachArr = make('app/service/AttachmentService')->getList(['attach_id'=>['in', $attachArr]]);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		$languageService = make('app/service/LanguageService');
		//格式化数组
		foreach($list as $key => $value) {
			$value['name'] = $lanArr[$value['spu_id']] ?? '';
			$value['url'] = router()->urlFormat($value['name'], 'p', ['id'=>$value['spu_id']]);
			$value['image'] = $attachArr[$value['attach_id']] ?? siteUrl('image/common/noimg.svg');
			$temp = $languageService->priceFormat($value['min_price']);
			$value['min_price'] = $temp[1];
			$value['min_price_format'] = $temp[2];
			$temp = $languageService->priceFormat($value['max_price']);
			$value['max_price'] = $temp[1];
			$value['max_price_format'] = $temp[2];
			$temp = $languageService->priceFormat($value['original_price']);
			$value['original_price'] = $temp[1];
			$value['original_price_format'] = $temp[2];
			$list[$key] = $value;
		}
		return $list;
	}
}