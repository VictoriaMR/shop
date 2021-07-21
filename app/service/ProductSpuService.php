<?php 

namespace app\service;
use app\service\Base;

class ProductSpuService extends Base
{
	const CACHE_INFO_KEY = 'spu_cache_info:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/ProductSpu');
	}

	public function addSpuImage(array $data)
	{
		$model = make('app/model/ProductSpuImage');
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($model->getCount($value)) {
					unset($data[$key]);
				}
			}
		}
		return $model->insert($data);
	}

	public function addIntroduceImage(array $data)
	{
		$model = make('app/model/ProductIntroduce');
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($model->getCount($value)) {
					unset($data[$key]);
				}
			}
		}
		return $model->insert($data);
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
			$spuIdArr = array_column($list, 'spu_id');
			$cateService = make('app/service/CategoryService');
			$linkArr = $cateService->getRelationList(['spu_id'=>['in', $spuIdArr]]);
			$cateIdArr = array_unique(array_column($linkArr, 'cate_id'));
			$cateArr = $cateService->getList(['cate_id'=>['in', $cateIdArr]]);
			$cateArr = array_column($cateArr, 'name', 'cate_id');
			$tempArr = [];
			foreach ($linkArr as $value) {
				$tempArr[$value['spu_id']][] = $cateArr[$value['cate_id']];
			}
			$siteArr = make('app/service/SiteService')->getListCache();
			dd($siteArr);
			$siteArr = array_column($siteArr, 'name', 'site_id');
			foreach ($list as $key => $value) {
				$value['avatar'] = mediaUrl($value['avatar'], 400);
				$value['status_text'] = $this->getStatusList($value['status']);
				$value['url'] = url('product/view', ['id'=>$value['spu_id']]);
				$value['cate_name'] = implode(' | ', $tempArr[$value['spu_id']]);
				$value['site_name'] = $siteArr[$value['site_id']];
				$list[$key] = $value;
			}
		}
		return $list;
	}
}