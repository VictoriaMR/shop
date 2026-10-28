<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Cart extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view();
	}

	public function cartSummary()
	{
		$list = service('cart/Cart')->getList();
		$originalPriceTotal = 0;
		$priceTotal = 0;
		$checkedCount = 0;
		if (!empty($list)) {
			foreach ($list as $value) {
				if (!empty($value['checked'])) {
					$qty = max(1, (int)$value['quantity']);
					$originalPriceTotal += (float)$value['original_price'] * $qty;
					$priceTotal += (float)$value['price'] * $qty;
					$checkedCount += $qty;
				}
			}
		}
		$symbol = service('system/Currency')->priceSymbol(2) ?: '$';
		$discountTotal = max(0, $originalPriceTotal - $priceTotal);
		$shippingFee = $priceTotal > 0 ? service('order/Order')->getShippingFee($priceTotal) : 0;
		$grandTotal = $priceTotal + $shippingFee;

		$summary = [
			'checked_count'         => $checkedCount,
			'symbol'                => $symbol,
			'original_total'        => sprintf('%.2f', $originalPriceTotal),
			'original_total_format' => $symbol . sprintf('%.2f', $originalPriceTotal),
			'subtotal'              => sprintf('%.2f', $priceTotal),
			'subtotal_format'       => $symbol . sprintf('%.2f', $priceTotal),
			'discount'              => sprintf('%.2f', $discountTotal),
			'discount_format'       => '-' . $symbol . sprintf('%.2f', $discountTotal),
			'shipping'              => sprintf('%.2f', $shippingFee),
			'shipping_format'       => $shippingFee > 0 ? ($symbol . sprintf('%.2f', $shippingFee)) : 'FREE',
			'grand_total'           => sprintf('%.2f', $grandTotal),
			'grand_total_format'    => $symbol . sprintf('%.2f', $grandTotal),
			'free_shipping_diff'    => sprintf('%.2f', max(0, 59 - $priceTotal)),
		];
		$this->success($summary);
	}

	public function cartCount()
	{
		$rst = service('cart/Cart')->getCartCount();
		$this->success($rst);
	}

	public function addToCart()
	{
		$skuId = (int)ipost('sku_id', (int)ipost('id', 0));
		$num = max(1, (int)ipost('num', (int)ipost('quantity', 1)));
		if ($skuId <= 0) {
			$this->error(appT('param_error'));
		}
		$sku = service('product/Sku');
		$where = [
			'sku_id' => $skuId,
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$skuInfo = $sku->loadData($where, 'stock,price');
		if (!$skuInfo) {
			$this->error(distT('add_invalid'));
		}
		if ((int)$skuInfo['stock'] < $num) {
			$this->error(distT('add_out_stock'));
		}
		$cart = service('cart/Cart');
		$rst = $cart->addToCart($skuId, $num);
		if ($rst) {
			$this->success(distT('add_success'), ['cart_count' => $cart->getCartCount()]);
		} else {
			$this->error(distT('add_fail'));
		}
	}

	public function updateQuantity()
	{
		$id = (int)ipost('id');
		$quantity = max(1, (int)ipost('quantity', 1));
		if ($id <= 0) {
			$this->error(appT('param_error'));
		}
		$cart = service('cart/Cart');
		$where = $cart->getOwnerWhere();
		$where['cart_id'] = $id;
		$cartInfo = $cart->loadData($where);
		if (empty($cartInfo)) {
			$this->error(distT('cart_not_exist'));
		}
		$sku = service('product/Sku');
		$skuInfo = $sku->loadData([
			'sku_id' => $cartInfo['sku_id'],
			'status' => $sku->getConst('STATUS_OPEN'),
		], 'stock');
		if (empty($skuInfo)) {
			$this->error(distT('add_invalid'));
		}
		if ((int)$skuInfo['stock'] < $quantity) {
			$this->error(distT('add_out_stock'));
		}
		$rst = $cart->updateData($id, ['quantity' => $quantity]);
		if ($rst !== false) {
			$this->success(distT('update_success'), ['cart_count' => $cart->getCartCount()]);
		} else {
			$this->error(distT('add_fail'));
		}
	}

	public function remove()
	{
		$id = (int)ipost('id');
		if ($id <= 0) {
			$this->error(appT('param_error'));
		}
		$cart = service('cart/Cart');
		$rst = $cart->removeCart($id);
		if ($rst) {
			$this->success(distT('remove_success'), ['cart_count' => $cart->getCartCount()]);
		} else {
			$this->error(distT('remove_fail'));
		}
	}

	public function setChecked()
	{
		$id = (int)ipost('id');
		$check = (int)ipost('check', 0) ? 1 : 0;
		$cart = service('cart/Cart');
		if ($id === -1) {
			// 全选 / 取消全选
			$where = $cart->getOwnerWhere();
			$rst = $cart->updateData($where, ['checked' => $check]);
		} else {
			if ($id <= 0) {
				$this->error(appT('param_error'));
			}
			$rst = $cart->checkCart($id, $check);
		}
		if ($rst !== false) {
			$this->success(
				$check ? distT('move_to_cart_success') : distT('save_for_later_success'),
				['cart_count' => $cart->getCartCount()]
			);
		} else {
			$this->error($check ? distT('move_to_cart_fail') : distT('save_for_later_fail'));
		}
	}

	public function check()
	{
		$rst = service('cart/Cart')->check();
		if ($rst) {
			$this->success(distT('check_ok'), ['url' => url('checkout')]);
		} else {
			$this->error(distT('check_error'));
		}
	}
}
