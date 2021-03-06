<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Product extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		html()->addJs('slider');

		$spuId = iget('id', 0);
		$skuId = iget('sid', 0);
		$spu = make('app/service/product/Spu');
		if (!$spuId) {
			$spuId = make('app/service/product/Sku')->loadData(['sku_id'=>$skuId], 'spu_id')['spu_id'] ?? 0;
		}
		if (!$spuId) {
			redirect('pageNotFound');
		}
		$info = $spu->getInfoCache($spuId, lanId());
		if (empty($info)) {
			redirect('pageNotFound');
		}
		//浏览历史
		make('app/service/member/History')->addHistory($spuId);

		$crumbs = [];
		$cateList = make('app/service/category/Category')->getParentCategoryById($info['cate_id']);
		$cateList = array_reverse($cateList);
		foreach ($cateList as $value) {
			if ($value['status']) {
				$crumbs[] = [
					'name' => $value['name'],
					'url' => router()->buildUrl($value['name'].'-c', ['cate_id'=>$value['cate_id']]),
				];
			}
		}
		if (!empty($crumbs)) {
			$crumbs[] = [
				'name' => 'Spu:'.$spuId,
				'url' => $info['url'],
			];
		}

		$this->assign('spuId', $spuId);
		$this->assign('skuId', $skuId);
		$this->assign('isLiked', make('app/service/member/Collect')->isCollect($spuId));
		$this->assign('info', $info);
		$this->assign('skuInfo', $skuId ? $info['sku'][$skuId] : []);
		$this->assign('skuNo', siteId().$info['cate_id'].($skuId ? 'S'.$skuId : $spuId));
		$this->assign('skuAttrSelect', $skuId ? $info['skuAttv'][$skuId] : []);
		$this->assign('stock', $skuId ? $info['sku'][$skuId]['stock'] : max(array_column($info['sku'], 'stock')));
		$this->assign('saleTotal', array_sum(array_column($info['sku'], 'sale_total')));
		$this->assign('crumbs', $crumbs);
		$this->assign('_title', $info['name']);
		$this->assign('_seo', $info['name'].implode(' ', $info['attv']));

		$this->view(true);
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