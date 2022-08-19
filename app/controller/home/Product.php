<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Product extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();

		$spuId = iget('id', 0);
		$skuId = iget('sid', 0);
		$crumbs = [];
		if ($spuId || $skuId) {
			$spu = make('app/service/product/Spu');
			if (!$spuId) {
				$spuId = make('app/service/product/Sku')->loadData(['sku_id'=>$skuId], 'spu_id')['spu_id'] ?? 0;
			}
			$lanId = lanId();
			$info = $spu->getInfoCache($spuId, $lanId, siteId());
			if ($info) {
				$cateList = make('app/service/category/Category')->getParentCategoryById($info['cate_id']);
				$cateList = array_reverse($cateList);
				if ($lanId == 1) {
					$lanArr = array_column($cateList, 'name_en', 'cate_id');
				} else {
					//分类语言
					$where = ['cate_id'=>['in', array_column($cateList, 'cate_id')]];
					$where['lan_id'] = $lanId;
					$lanArr = make('app/service/category/Language')->getListData($where, 'cate_id,name');
					$lanArr = array_column($lanArr, 'name', 'cate_id');
				}
				foreach ($cateList as $value) {
					if ($value['status'] && $value['is_show']) {
						$name = $lanArr[$value['cate_id']] ?? $value['en'];
						$crumbs[] = [
							'name' => $name,
							'url' => router()->buildUrl($name.'-c', ['cate_id'=>$value['cate_id']]),
						];
					}
				}
				if (!empty($crumbs)) {
					$crumbs[] = [
						'name' => 'Spu:'.$spuId,
						'url' => $info['url'],
					];
				}
				if ($skuId) {
					$stock = $info['sku'][$skuId]['stock'];
					$this->assign('skuInfo', $info['sku'][$skuId]);
					$this->assign('skuAttrSelect', $skuId ? $info['skuAttv'][$skuId] : []);
				} else {
					$stock = max(array_column($info['sku'], 'stock'));
				}
				$this->assign('stock', $stock);
				$this->assign('skuNo', 'S'.siteId().'-C'.$info['cate_id'].'-'.($skuId ? 'S'.$skuId : 'P'.$spuId));
				$isLiked = userId() ? make('app/service/member/Collect')->isCollect($spuId) : false;
				$this->assign('isLiked', $isLiked);
				$this->assign('info', $info);
				$this->assign('saleTotal', array_sum(array_column($info['sku'], 'sale_total')));
				$this->assign('_title', $info['name']);
				$this->assign('_seo', $info['name'].implode(' ', $info['attv']));
				$this->assign('spuId', $spuId);
				$this->assign('skuId', $skuId);
			}
		}
		$this->assign('crumbs', $crumbs);
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