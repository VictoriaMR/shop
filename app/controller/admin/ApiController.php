<?php

namespace app\controller\admin;

use app\controller\Controller;

class ApiController extends Controller
{
	protected $_cateArr = ['category'];

	public function getHelperData()
	{
		$data = [
			'version' => '1.0.0',
			'category' => make('App\Services\CategoryService')->getListFormat(),
			'site' => make('App\Services\SiteService')->getList(),
		];
		$this->success($data);
	}

	public function getHelperFunction()
	{
		$data = [
			[
				'title' => '数据爬取',
				'name' => 'crawler',
			]
		];
		$this->success($data);
	}

	public function upload()
	{	
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error('上传数据为空');
		}
		$cate = $_POST['cate'] ?? '';
		if (!in_array($cate, $this->_cateArr)) {
			$this->error('没有权限操作'.$cate.'文件夹');
		}
		$fileService = make('app/service/FileService');
		$result = $fileService->upload($file, $cate);
		if (empty($result)) {
			$this->error('上传失败');
		}
		$this->success($result);
	}

	public function stat()
	{
		make('App\Services\LoggerService')->addLog();
	}

	public function addProduct()
	{
		set_time_limit(120);
		$data = ipost();
		if (empty($data['bc_product_category'])) {
			$this->error('产品分类不能为空!');
		}
		$supplierId = $this->getSiteId($data['bc_site_id']);
		if (empty($supplierId)) {
			$this->error('供应商不能为空!');
		}
		if (empty($data['bc_product_site'])) {
			$this->error('站点不能为空!');
		}
		$data['bc_product_name'] = trim($data['bc_product_name']);
		if (empty($data['bc_product_name'])) {
			$this->error('产品标题不能为空!');
		}
		if (empty($data['bc_sku'])) {
			$this->error('产品SKU不能为空!');
		}
		if (empty($data['bc_product_img'])) {
			$this->error('产品图片不能为空!');
		}
		//删除链接后缀
		$data['bc_product_url'] = $this->getSupplierItemUrl($data['bc_product_url']);
		if (empty($data['bc_product_url'])) {
			$this->error('产品链接不能为空!');
		}
		$spuDataService = make('App\Services\ProductSpuDataService');
		$fileService = make('App\Services\FileService');

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
				$firstImage = $spuImageArr[$url];
			}
		}
		$spuImageArr = array_filter($spuImageArr);
		if (empty($spuImageArr)) {
			$this->error('产品图片上传失败!');
		}
		//属性组
		$attributeService = make('App\Services\AttributeService');
		$attrvalueService = make('App\Services\AttrvalueService');
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
		$productLanguageService = make('App\Services\ProductLanguageService');
		$where = [
			'site_id' => $data['bc_product_site'],
			'item_id' => $data['bc_product_id'],
			'supplier_id' => $supplierId,
		];	
		$info = $spuDataService->getInfoByWhere($where);
		$spuService = make('App\Services\ProductSpuService');
		if (empty($info)) {
			//价格合集
			foreach ($data['bc_sku'] as $key => $value) {
				$data['bc_sku'][$key]['p_price'] = $value['price'] + rand(150, 200);
			}
			$priceArr = array_column($data['bc_sku'], 'p_price');
			$insert = [
				'status' => 0,
				'site_id' => $data['bc_product_site'],
				'avatar' => $firstImage['cate'].'/'.$firstImage['name'].'.'.$firstImage['type'],
				'min_price' => min($priceArr),
				'max_price' => max($priceArr),
				'origin_price' => round(max($priceArr) * ((10 - rand(5, 9)) / 10 + 1), 2), //虚拟原价
				'name' => $data['bc_product_name'],
				'add_time' => now(),
			];
			//事务开启
			$spuDataService->start();
			$spuId = $spuService->create($insert);
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
			$spuDataService->create($insert);
			//spu 多语言
			$insert = [
				'spu_id' => $spuId,
				'lan_id' => 1,
				'name' => $data['bc_product_name']
			];
			$productLanguageService->create($insert);

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
				$spuService->addSpuImage($insert);
			}

			//sku
			$skuService = make('App\Services\ProductSkuService');
			foreach ($data['bc_sku'] as $key => $value) {
				if (empty($value['stock'])) continue;

				$name = '';
				//sku 属性
				foreach ($value['attr'] as $k => $v) {
					$name .= ' '.$v['text'];
				}
				$name = trim($name);
				$name = $data['bc_product_name'].' - '.$name;

				$avatar = '';
				if (!empty($value['img'])) {
					$value['img'] = $this->filterUrl($value['img']);
					if (empty($spuImageArr[$value['img']])) {
						$rst = $fileService->uploadUrlImage($value['img'], 'product');
						if (empty($rst)) {
							continue;
						}
						$spuImageArr[$value['img']] = $rst;
					}
					$avatar = $spuImageArr[$value['img']]['cate'].'/'.$spuImageArr[$value['img']]['name'].'.'.$spuImageArr[$value['img']]['type'];
				}
				$insert = [
					'spu_id' => $spuId,
					'status' => 1,
					'avatar' => $avatar,
					'stock' => $value['stock'],
					'price' => $value['p_price'],
					'cost_price' => $value['price'],
					'add_time' => now(),
				];
				$skuId = $skuService->create($insert);
				$insert = [
					'spu_id' => $spuId,
					'lan_id' => 1,
					'name' => $name,
				];
				$productLanguageService->create($insert);
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
					$skuService->addAttributeRelation($insert);
				}
			}
			$spuDataService->commit();
		} else {
			$spuId = $info['spu_id'];
		}
		if (empty($spuId)) {
			$this->error('产品SPU入库失败!');
		}
		//产品分类关联
		make('App\Services\CategoryService')->addCateProRelation($spuId, $data['bc_product_category']);
		
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
			$spuService->addIntroduceImage($insert);
		}

		//spu介绍文本
		$descService = make('App\Services\DescriptionService');
		$descArr = [];
		$insert = [];
		foreach ($data['bc_des_text'] as $key => $value) {
			$value['key'] = trim($value['key'], chr(0xc2).chr(0xa0));
			$value['value'] = trim($value['value'], chr(0xc2).chr(0xa0));
			$descArr[$value['key']] = $descService->setNotExit($value['key']);
			$descArr[$value['value']] = $descService->setNotExit($value['value']);
			$insert[] = [
				'spu_id' => $spuId,
				'name_id' => $descArr[$value['key']],
				'value_id' => $descArr[$value['value']],
			];
		}
		$descService->addDescRelation($insert);
		$this->success();
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

	protected function getSiteId($name)
	{
		$siteIdArr = [
			'1688' => 1,
			'taobao' => 2,
			'tmall' => 3
		];
		return $siteIdArr[$name] ?? 0;
	}
}