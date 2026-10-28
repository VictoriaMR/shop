<?php

namespace app\service\cart;
use app\service\Base;

class Cart extends Base
{
	protected $_model = 'app/model/Cart';

	public function getOwnerWhere(): array
	{
		$memId = userId();
		if ($memId > 0) {
			$this->mergeGuestCart($memId);
			return ['mem_id' => $memId];
		}
		$uuid = frame('Cookie')->get('uuid') ?: uuId();
		return ['mem_id' => 0, 'uuid' => (string)$uuid];
	}

	protected function mergeGuestCart(int $memId): void
	{
		static $merged = false;
		if ($merged || $memId <= 0) {
			return;
		}
		$merged = true;
		$uuid = frame('Cookie')->get('uuid');
		if (empty($uuid) || !is_string($uuid)) {
			return;
		}
		$guestRows = $this->getListData(['mem_id' => 0, 'uuid' => $uuid], 'cart_id,sku_id,quantity,price,checked');
		if (empty($guestRows)) {
			return;
		}
		foreach ($guestRows as $gRow) {
			$existing = $this->loadData(['mem_id' => $memId, 'sku_id' => $gRow['sku_id']], 'cart_id,quantity');
			if (!empty($existing)) {
				$this->updateData($existing['cart_id'], ['quantity' => (int)$existing['quantity'] + (int)$gRow['quantity']]);
				$this->deleteData($gRow['cart_id']);
			} else {
				$this->updateData($gRow['cart_id'], ['mem_id' => $memId]);
			}
		}
	}

	public function addToCart($skuId, $quantity = 1)
	{
		$skuId = (int)$skuId;
		$quantity = max(1, (int)$quantity);
		$skuInfo = service('product/Sku')->loadData(['sku_id' => $skuId, 'status' => 1], 'sku_id,stock,price');
		if (empty($skuInfo)) {
			return false;
		}
		$where = $this->getOwnerWhere();
		$where['sku_id'] = $skuId;
		$existing = $this->loadData($where, 'cart_id,quantity');
		if (!empty($existing)) {
			$newQty = min((int)$skuInfo['stock'], (int)$existing['quantity'] + $quantity);
			return $this->updateData($existing['cart_id'], [
				'quantity' => max(1, $newQty),
				'price'    => $skuInfo['price'],
				'checked'  => 1,
			]);
		}
		$where['site_id'] = siteId();
		$where['quantity'] = min((int)$skuInfo['stock'], $quantity);
		$where['price'] = $skuInfo['price'];
		$where['checked'] = 1;
		return $this->insert($where);
	}

	public function removeCart($cartId)
	{
		$where = $this->getOwnerWhere();
		$where['cart_id'] = (int)$cartId;
		return $this->deleteData($where);
	}

	public function reduceCart($cartId, $quantity = 1)
	{
		$where = $this->getOwnerWhere();
		$where['cart_id'] = (int)$cartId;
		return $this->where($where)->decrement('quantity', (int)$quantity);
	}

	public function checkCart($cartId, $check = 1)
	{
		$where = $this->getOwnerWhere();
		$where['cart_id'] = (int)$cartId;
		return $this->updateData($where, ['checked' => (int)$check]);
	}

	public function getCartCount()
	{
		$memId = userId();
		if ($memId > 0) {
			$this->mergeGuestCart($memId);
			$where = ['mem_id' => $memId, 'checked' => 1];
		} else {
			$uuid = frame('Cookie')->get('uuid');
			if (empty($uuid)) {
				return 0;
			}
			$where = ['mem_id' => 0, 'uuid' => (string)$uuid, 'checked' => 1];
		}
		return (int)($this->loadData($where, 'SUM(quantity) as quantity')['quantity'] ?? 0);
	}

	public function getList()
	{
		$where = $this->getOwnerWhere();
		$list = $this->getListData($where, 'cart_id,sku_id,quantity,price as cart_price,checked', 0, 0, ['cart_id' => 'desc']);
		if (empty($list)) {
			return [];
		}
		$skuService = service('product/Sku');
		$currencyService = service('system/Currency');
		$symbol = $currencyService->priceSymbol(2);
		$result = [];
		foreach ($list as $value) {
			$skuId = (int)$value['sku_id'];
			$tempData = $skuService->getInfoCache($skuId, lanId());
			if (empty($tempData)) {
				continue;
			}
			$item = array_merge($value, $tempData);
			$temp = $currencyService->priceFormat($value['cart_price']);
			$item['cart_price'] = $temp[1];
			$item['cart_price_format'] = $temp[2];
			$qty = max(1, (int)$item['quantity']);
			$unitPrice = (float)$item['price'];
			$origUnitPrice = (float)($item['original_price'] ?? $unitPrice);
			$item['price_total'] = sprintf('%.2f', $unitPrice * $qty);
			$item['price_total_format'] = $symbol . $item['price_total'];
			$item['original_total'] = sprintf('%.2f', $origUnitPrice * $qty);
			$item['original_total_format'] = $symbol . $item['original_total'];
			$result[$skuId] = $item;
		}
		return $result;
	}

	public function check()
	{
		$where = $this->getOwnerWhere();
		$where['checked'] = 1;
		$list = $this->getListData($where, 'cart_id,sku_id,quantity');
		if (empty($list)) {
			return false;
		}
		$sku = service('product/Sku');
		$skuList = $sku->getListData(['sku_id' => ['in', array_column($list, 'sku_id')]], 'sku_id,spu_id,status,stock');
		$skuList = array_column($skuList, null, 'sku_id');

		foreach ($list as $value) {
			$sid = $value['sku_id'];
			if (empty($skuList[$sid])) {
				return false;
			}
			if ($skuList[$sid]['status'] != $sku->getConst('STATUS_OPEN')) {
				return false;
			}
			if ($value['quantity'] > $skuList[$sid]['stock']) {
				return false;
			}
		}
		return true;
	}
}
