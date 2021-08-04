<?php 

namespace app\service\product;
use app\service\Base;

class SpuService extends Base
{
	const CACHE_INFO_KEY = 'spu_cache_info:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/product/Spu');
	}

	public function getInfoCache($spuId, $lanId)
	{
		$cacheKey = $this->getCacheKey($spuId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($spuId, $lanId);
			redis()->set($cacheKey, $info);
		}
		return $info;
	}

	public function getInfo($spuId, $lanId=1)
	{
		$info = $this->loadData($spuId);
		if (empty($info)) {
			return false;
		}
		$info['avatar'] = mediaUrl($info['avatar'], 200);
		//价格格式化
		$languageService = make('app\service\LanguageService');
		$priceFormat = $languageService->priceFormat($info['min_price'], $lanId);
		$info['price_format'] = $priceFormat['price'];
		$info['price_symbol'] = $priceFormat['symbol'];
		$info['url'] = filterUrl($info['name'], 'p', $spuId);
		//获取sku列表
		$skuService = make('app\service\ProductSkuService');
		$skuList = $skuService->getListBySpuId($spuId);
		$skuList = array_column($skuList, null, 'sku_id');
		$skuIdArr = array_keys($skuList);
		//sku属性关联
		$skuRelationArr = $skuService->getAttributeRelation($skuIdArr);
		$attrIdArr = array_unique(array_column($skuRelationArr, 'attr_id'));
		$attrData = make('app\service\AttributeService')->getInfo($attrIdArr, 1);
		$attrData = array_column($attrData, 'name', 'attr_id');
		$attvIdArr = array_unique(array_column($skuRelationArr, 'attv_id'));
		$attvData = make('app\service\AttrvalueService')->getInfo($attvIdArr, 1);
		$attvData = array_column($attvData, 'name', 'attv_id');

		//获取spu图片ID集
		$spuImageList = make('app/model/ProductSpuImage')->getInfoBySpuId($spuId);
		//获取sku图片ID集
		$skuImageList = $skuService->getInfoBySkuIds($skuIdArr);
		//全部图片合集
		$attachArr = array_unique(array_filter(array_merge($spuImageList, array_column($skuImageList, 'attach_id'), array_column($skuRelationArr, 'attach_id'))));
		$attachArr = make('app\service\AttachmentService')->getAttachmentListById($attachArr);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		foreach ($spuImageList as $value) {
			$info['image'][] = $attachArr[$value];
		}
		//属性归类
		$info['attr'] = [];
		$skuAttr = [];
		foreach ($skuRelationArr as $key => $value) {
			if (empty($info['attr'][$value['attr_id']])) {
				$info['attr'][$value['attr_id']] = [
					'id' => $value['attr_id'],
					'name' => $attrData[$value['attr_id']],
					'attv' => [],
				];
			}
			if (empty($info['attr'][$value['attr_id']]['attv'][$value['attv_id']])) {
				$info['attr'][$value['attr_id']]['attv'][$value['attv_id']] = [
					'id' => $value['attv_id'],
					'name' => $attvData[$value['attv_id']],
					'img' => $attachArr[$value['attach_id']] ?? '',
				];
			}
			$skuAttr[$value['sku_id']]['attr'][] = $value['attr_id'];
			$skuAttr[$value['sku_id']]['attv'][] = $value['attv_id'];
		}
		//处理sku
		foreach ($skuList as $key => $value) {
			$priceFormat = $languageService->priceFormat($value['price'], $lanId);
			$value['price_format'] = $priceFormat['price'];
			$value['price_symbol'] = $priceFormat['symbol'];
			$value['url'] = filterUrl($value['name'], 'k', $value['sku_id']);
			$value['image'] = [];
			foreach ($skuImageList as $k => $v) {
				if ($v['sku_id'] == $value['sku_id']) {
					$value['image'][] = $attachArr[$v['attach_id']];
				}
			}
			$value = array_merge($value, $skuAttr[$value['sku_id']]);
			$skuList[$key] = $value;
		}
		//获取翻译语言
		if ($lanId != env('DEFAULT_LANGUAGE_ID')) {
			$skuIdArr[] = 0;
			$textArr = make('app\service\ProductLanguageService')->getTextArr($spuId, $skuIdArr, $lanId);
			$textArr = array_column($textArr, 'name', 'sku_id');
			$info['name'] = empty($textArr[0]) ? $info['name'] : $textArr[0];
			foreach ($skuList as $key => $value) {
				$skuList[$key]['url'] = filterUrl($value['name'], 'k', $value['sku_id']);
				$skuList[$key]['name'] = empty($textArr[$value['sku_id']]) ? $skuList[$key]['name'] : $textArr[$value['sku_id']];
			}
		}
		$info['sku'] = $skuList;
		return $info;
	}

	protected function getCacheKey($spuId, $lanId)
	{
		return self::CACHE_INFO_KEY.$spuId.'_'.$lanId;
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
		if (is_array($data['bc_product_category'])) {
			$data['bc_product_category'] = array_shift($data['bc_product_category']);
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
				$data['bc_sku'][$key]['p_price'] = $value['price'] + rand(150, 200);
			}
			$priceArr = array_column($data['bc_sku'], 'p_price');
			$insert = [
				'status' => 0,
				'site_id' => $data['bc_product_site'],
				'cate_id' => $data['bc_product_category'],
				'attach_id' => $firstImage,
				'min_price' => min($priceArr),
				'max_price' => max($priceArr),
				'origin_price' => round(max($priceArr) * ((10 - rand(5, 9)) / 10 + 1), 2), //虚拟原价
				'add_time' => now(),
			];
			//事务开启
			$this->start();
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

				$name = '';
				//sku 属性
				foreach ($value['attr'] as $k => $v) {
					$name .= ' '.$v['text'];
				}
				$name = trim($name);
				$name = $data['bc_product_name'].' - '.$name;

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
					'price' => $value['p_price'],
					'cost_price' => $value['price'],
					'add_time' => now(),
				];
				$skuId = $skuService->insert($insert);
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
		$data['bc_product_des_picture'] = array_unique(explode(',', $data['bc_product_des_picture']));
		foreach ($data['bc_product_des_picture'] as $value) {
			$url = $this->filterUrl($value);
			if (empty($spuImageArr[$url])) {
				$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'introduce', false);
			}
			if (empty($spuImageArr[$url]['attach_id'])) continue;
			$insert[] = [
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
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800', '_.webp'], '', $url);
	}
}