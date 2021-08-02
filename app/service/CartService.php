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
			'mem_id' => userId(),
			'site_id' => siteId(),
			'sku_id' => $skuId,
		];
		$cartId = $this->loadData($where, 'cart_id')['cart_id'] ?? 0;
		if ($cartId) {
			return $this->where('cart_id', $cartId)->increment('quantity');
		}
		if (empty($spuId)) {
			$spuId = make('app/service/product/SkuService')->loadData($skuId, 'spu_id')['spu_id'] ?? 0;
		}
		$where['spu_id'] = $spuId;
		$where['quantity'] = $quantity;
		$where['add_time'] = now();
		return $this->insert($where);
	}

	public function removeCart($cartId)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => userId(),
		];
		return $this->deleteData($where);
	}

	public function reduceCart($cartId, $quantity=1)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => userId(),
		];
		return $this->where($where)->decrement('quantity', $quantity);
	}

	public function checkCart($cartId, $check=1)
	{
		$where = [
			'cart_id' => $cartId,
			'mem_id' => userId(),
		];
		return $this->updateData($where, ['checked'=>$check]);
	}
}