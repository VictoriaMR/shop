<?php

namespace app\controller\bag;
use app\controller\Controller;

class ProductController extends Controller
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		html()->addJs('slider');

		$spuId = iget('id', 0);
		$skuId = iget('sid', 0);
		if (empty($spuId) && empty($skuId)) {
			redirect('pageNotFound');
		}
		$spuService = make('app/service/product/SpuService');
		if (empty($spuId)) {
			$spuId = make('app/service/product/SkuService')->loadData(['sku_id'=>$skuId], 'spu_id')['spu_id'] ?? 0;
		}
		if (empty($spuId)) {
			redirect('pageNotFound');
		}
		$info = $spuService->getInfoCache($spuId, lanId());
		if (empty($info)) {
			redirect('pageNotFound');
		}
		//浏览历史
		make('app/service/member/HistoryService')->addHistory($spuId);

		$this->assign('spuId', $spuId);
		$this->assign('skuId', $skuId);
		$this->assign('isLiked', make('app/service/member/CollectService')->isCollect($spuId));
		$this->assign('info', $info);
		$this->assign('skuInfo', $skuId ? $info['sku'][$skuId] : []);
		$this->assign('skuNo', siteId().$info['cate_id'].($skuId ? 'S'.$skuId : $spuId));
		$this->assign('skuAttrSelect', $skuId ? $info['skuAttv'][$skuId] : []);
		$this->assign('maxStock', max(array_column($info['sku'], 'stock')));
		$this->assign('saleTotal', array_sum(array_column($info['sku'], 'sale_total')));
		$this->assign('_title', $info['name']);
		$this->assign('_seo', $info['name'].implode(' ', $info['attv']));

		$this->view();
	}
}