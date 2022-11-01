<?php

namespace app\service;
use app\service\Base;

class Cart extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Cart');
	}

	public function addToCart($skuId, $quantity=1)
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
		$where = $this->getWhere();
		$where['checked'] = 1;
		return $this->loadData($where, 'SUM(quantity) as quantity')['quantity'] ?? 0;
	}

	protected function getWhere()
	{
		$memId = $this->userId();
		if (empty($memId)) return ['uuid' => uuId(), 'mem_id'=>0];
		else return ['mem_id' => $memId];
	}

	public function getList()
	{
		$list = $this->getListData($this->getWhere(), 'cart_id,sku_id,quantity,price as cart_price,checked', 0, 0, ['cart_id'=>'desc']);
		if (!empty($list)) {
			$sku = make('app/service/product/Sku');
			$list = array_column($list, null, 'sku_id');
			$currencyService = make('app/service/currency/Currency');
			$symbol = $currencyService->priceSymbol(2);
			foreach ($list as $key => $value) {
				$tempData = $sku->getInfoCache($value['sku_id'], $this->lanId());
				$value = array_merge($value, $tempData);
				$temp = $currencyService->priceFormat($value['cart_price']);
				$value['cart_price'] = $temp[1];
				$value['cart_price_format'] = $temp[2];
				$value['price_total'] = $value['price'] * $value['quantity'];
				$value['price_total_format'] = $symbol.$value['price_total'];
				$list[$key] = $value;
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
		$sku = make('app/service/product/Sku');
		$skuList = $sku->getListData(['sku_id'=>['in', array_column($list, 'sku_id')]], 'sku_id,spu_id,status,stock');
		$skuList = array_column($skuList, null, 'sku_id');

		foreach ($list as $value) {
			if (empty($skuList[$value['sku_id']])) {
				return false;
			}
			if ($skuList[$value['sku_id']]['status'] != $sku->getConst('STATUS_OPEN')) {
				return false;
			}
			if ($value['quantity'] > $skuList[$value['sku_id']]['stock']) {
				return false;
			}
		}
		return true;
	}
}