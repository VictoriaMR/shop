<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Product extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view();
	}

	public function check()
	{
		$skuId = (int)ipost('sku_id');
		$quantity = max(1, (int)ipost('quantity', 1));
		if ($skuId <= 0) {
			$this->error('Please select product options first.');
		}
		$sku = service('product/Sku');
		$where = [
			'sku_id' => $skuId,
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$data = $sku->loadData($where, 'stock');
		if (empty($data)) {
			$this->error('This product option is currently unavailable.');
		}
		if ((int)$data['stock'] < $quantity) {
			$this->error('Requested quantity exceeds available stock.');
		}
		$this->success();
	}

	public function getInfoAjax()
	{
		$spuId = (int)ipost('pid');
		if ($spuId <= 0) {
			$this->error(distT('id_error'));
		}
		$info = service('product/Spu')->getInfoCache($spuId, lanId());
		if ($info) {
			unset($info['description'], $info['introduce']);
			$this->success('get_info_success', $info);
		}
		$this->error(distT('get_info_fail'));
	}
}
