<?php

namespace app\controller\bag;

use app\controller\Controller;
use frame\Html;

class ProductController extends Controller
{
	public function index()
	{	
		$spuId = iget('spu_id');
		$skuId = iget('sku_id');
		if (!empty($skuId)) {
			$spuId = make('App\Services\ProductSkuService')->getSpuId($skuId);
		}
		if (!empty($spuId)) {
			$spuInfo = make('App\Services\ProductSpuService')->getInfoCache($spuId);
			if (!empty($spuInfo) && !empty($skuId)) {
				$skuInfo = $spuInfo['sku'][$skuId] ?? [];
			}
		}
		$this->assign('spuInfo', $spuInfo ?? []);
		$this->assign('skuInfo', $skuInfo ?? []);
		return view();
	}
}