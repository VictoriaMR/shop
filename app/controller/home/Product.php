<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Product extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$spuId = (int)iget('pid', 0);
		$skuId = (int)iget('sid', 0);
		$crumbs = [];
		if ($spuId>0 || $skuId>0) {
			$spu = service('product/Spu');
			if (!$spuId) {
				$spuId = service('product/Sku')->loadData(['sku_id'=>$skuId], 'spu_id')['spu_id'] ?? 0;
			}
			$lanId = lanId();
			$info = $spu->getInfoCache($spuId, $lanId, siteId());
			if ($info) {
				$cateList = service('category/Category')->pCate($info['cate_id']);
				$cateList = array_reverse($cateList);
				if ($lanId == 1) {
					$lanArr = array_column($cateList, 'name_en', 'cate_id');
				} else {
					//分类语言
					$where = ['cate_id'=>['in', array_column($cateList, 'cate_id')]];
					$where['lan_id'] = $lanId;
					$lanArr = service('category/Language')->getListData($where, 'cate_id,name');
					$lanArr = array_column($lanArr, 'name', 'cate_id');
				}
				foreach ($cateList as $value) {
					if ($value['status'] && $value['is_show']) {
						$name = $lanArr[$value['cate_id']] ?? $value['en'];
						$crumbs[] = [
							'name' => $name,
							'url' => url($name, ['c'=>$value['cate_id']]),
						];
					}
				}
				$crumbs[] = [
					'name' => 'Spu:'.$spuId,
					'url' => $info['url'] ?? 'javascript:;',
				];
				$skuAttv = [];
				$image = '';
				if ($info['status'] == $spu->getConst('STATUS_OPEN')) {
					if ($skuId) {
						$stock = $info['sku'][$skuId]['stock'];
						if (!empty($info['skuAttv'][$skuId])) {
							$skuAttv = $info['skuAttv'][$skuId];
						}
					} else {
						$stock = max(array_column($info['sku'], 'stock'));
					}
					$this->assign('stock', $stock);
					$this->assign('saleTotal', array_sum(array_column($info['sku'], 'sale_total')));
				}
				if ($skuId) {
					$name = $title = $seo = $info['sku'][$skuId]['name'];
					$image = $info['sku'][$skuId]['image'];
					$crumbs[] = [
						'name' => 'Sku:'.$skuId,
						'url' => $info['sku'][$skuId]['url'] ?? 'javascript:;',
					];
				} else {
					$name = $title = $info['name'];
					$seo = $title.implode(' ', $info['attv']??[]);
				}
				$isLiked = userId() ? service('member/Collect')->isCollect($spuId) : false;
				$this->assign('isLiked', $isLiked);
				$this->assign('info', $info);
				$this->assign('name', $name);
				$this->assign('skuAttv', $skuAttv);
				$this->assign('image', $image);
				$this->assign('_title', $title);
				$this->assign('_desc', $seo);
				$this->assign('_keyword', $seo);
				$this->assign('spuId', $spuId);
				$this->assign('skuId', $skuId);
				if ($skuId) {
					$this->assign('skuInfo', $info['sku'][$skuId]??[]);	
				}
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
		$sku = service('product/Sku');
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

	public function getInfoAjax()
	{
		$spuId = (int)ipost('pid');
		if ($spuId <= 0) {
			$this->error(distT('id_error'));
		}
		$info = service('product/Spu')->getInfoCache($spuId, lanId(), siteId());
		if ($info) {
			unset($info['description']);
			unset($info['introduce']);
			$this->success('get_info_success', $info);
		}
		$this->error(distT('get_info_fail'));
	}
}