<?php

namespace app\controller\bag;
use app\controller\;

class Product extends 
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
		$spu = make('app/service/product/Spu');
		if (empty($spuId)) {
			$spuId = make('app/service/product/Sku')->loadData(['sku_id'=>$skuId], 'spu_id')['spu_id'] ?? 0;
		}
		if (empty($spuId)) {
			redirect('pageNotFound');
		}
		$info = $spu->getInfoCache($spuId, lanId());
		if (empty($info)) {
			redirect('pageNotFound');
		}
		//浏览历史
		make('app/service/member/History')->addHistory($spuId);

		$this->assign('spuId', $spuId);
		$this->assign('skuId', $skuId);
		$this->assign('isLiked', make('app/service/member/Collect')->isCollect($spuId));
		$this->assign('info', $info);
		$this->assign('skuInfo', $skuId ? $info['sku'][$skuId] : []);
		$this->assign('skuNo', siteId().$info['cate_id'].($skuId ? 'S'.$skuId : $spuId));
		$this->assign('skuAttrSelect', $skuId ? $info['skuAttv'][$skuId] : []);
		$this->assign('stock', $skuId ? $info['sku'][$skuId]['stock'] : max(array_column($info['sku'], 'stock')));
		$this->assign('saleTotal', array_sum(array_column($info['sku'], 'sale_total')));
		$this->assign('_title', $info['name']);
		$this->assign('_seo', $info['name'].implode(' ', $info['attv']));

		$this->view();
	}

	public function check()
	{
		$skuId = (int)ipost('sku_id');
		$quantity = (int)ipost('quantity', 1);
		if (empty($skuId)) {
			$this->error('The product param was invalid.');
		}
		$sku = make('app/service/product/Sku');
		$where = [
			'sku_id' => $skuId,
			'site_id' => siteId(),
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$data = $sku->loadData($where, 'stock');
		if (empty($data)) {
			$this->error('The product was not exist.');
		}
		if ($data['stock'] < $quantity) {
			$this->error('The product out of stock.');
		}
		$this->success();
	}
}