<?php

namespace app\service;
use app\service\Base;

class CartService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Cart');
	}

	public function addToCart($skuId, $quantity=1, $spuId=0)
	{
		$where = [
			'mem_id' => $this->userId(),
			'sku_id' => $skuId,
		];
		$cartId = $this->loadData($where, 'cart_id')['cart_id'] ?? 0;
		if ($cartId) {
			return $this->where('cart_id', $cartId)->increment('quantity');
		}
		$where['quantity'] = $quantity;
		$where['add_time'] = now();
		$where['update_time'] = now();
		return $this->insert($where);
	}

	public function removeCart($cartId)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => $this->userId(),
		];
		return $this->deleteData($where);
	}

	public function reduceCart($cartId, $quantity=1)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => $this->userId(),
		];
		return $this->where($where)->decrement('quantity', $quantity);
	}

	public function checkCart($cartId, $check=1)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => $this->userId(),
		];
		return $this->updateData($where, ['checked'=>$check]);
	}

	public function getCartCount()
	{
		$where = [
			'mem_id' => $this->userId(),
			'checked' => $this->getConst('CART_CHECKED'),
		];
		return $this->loadData($where, 'SUM(quantity) as quantity')['quantity'] ?? 0;
	}

	public function getList()
	{
		$list = $this->getListData(['mem_id' => $this->userId()], 'cart_id,sku_id,quantity,checked', 0, 0, ['cart_id'=>'desc']);
		if (!empty($list)) {
			$skuService = make('app/service/product/SkuService');
			$skuList = $skuService->getListData(['sku_id'=>['in', array_column($list, 'sku_id')]], 'sku_id,status,stock');
			$skuList = array_column($skuList, null, 'sku_id');
			foreach ($list as $key => $value) {
				$list[$key] = array_merge($value, $skuService->getInfoCache($value['sku_id'], $this->lanId()), $skuList[$value['sku_id']]);
			}
		}
		return $list;
	}

	public function check()
	{
		$list = $this->getListData(['mem_id' => $this->userId(), 'checked'=>1], 'cart_id,sku_id,quantity');
		if (empty($list)) {
			return false;
		}
		$skuService = make('app/service/product/SkuService');
		$skuList = $skuService->getListData(['sku_id'=>['in', array_column($list, 'sku_id')]], 'sku_id,spu_id,status,stock');
		$skuList = array_column($skuList, null, 'sku_id');

		foreach ($list as $value) {
			if (empty($skuList[$value['sku_id']])) {
				return false;
			}
			if ($skuList[$value['sku_id']]['status'] != $skuService->getConst('STATUS_OPEN')) {
				return false;
			}
			if ($value['quantity'] > $skuList[$value['sku_id']]['stock']) {
				return false;
			}
		}
		return true;
	}
}