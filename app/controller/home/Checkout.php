<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Checkout extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addCss('common/address');
		frame('Html')->addJs();
		frame('Html')->addJs('common/address');
		$this->view();
	}

	public function payOrder()
	{
		frame('Html')->addCss();
		frame('Html')->addCss('common/address');
		frame('Html')->addJs('common/address');
		frame('Html')->addJs();
		$this->view();
	}

	public function createOrder()
	{
		$skuIds = (int)input('id', 0);
		$quantity = max(1, (int)input('quantity', 1));
		$shippingAddressId = (int)ipost('shipping_address_id', 0);
		$billingAddressId = (int)ipost('billing_address_id', 0);
		$insurance = (int)ipost('insurance', 0) ? true : false;

		// 游客快捷下单：自动创建或关联账户并持久化地址
		if (userId() <= 0) {
			$email = trim((string)ipost('guest_email', session()->get(type() . '_info.guest_email', '')));
			if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$this->error(distT('email_empty'));
			}
			$memberService = service('member/Member');
			$existingMem = $memberService->loadData(['site_id' => siteId(), 'email' => $email], 'mem_id');
			if (!empty($existingMem['mem_id'])) {
				$memId = (int)$existingMem['mem_id'];
			} else {
				$memId = (int)$memberService->create([
					'site_id'  => siteId(),
					'email'    => $email,
					'password' => randString(10),
					'status'   => 1,
				]);
			}
			if ($memId <= 0 || !$memberService->loginById($memId)) {
				$this->error(distT('regist_error'));
			}
			$addressData = session()->get(type() . '_info.address') ?: [];
			if (!empty($addressData)) {
				$addressService = service('member/Address');
				$idMap = [];
				foreach ($addressData as $value) {
					$oldId = (int)($value['address_id'] ?? 0);
					unset($value['address_id'], $value['country']);
					$value['mem_id'] = $memId;
					$value['site_id'] = siteId();
					$newId = (int)$addressService->insertGetId($value);
					if ($oldId > 0) {
						$idMap[$oldId] = $newId;
					}
					if ($shippingAddressId <= 0) {
						$shippingAddressId = $newId;
					}
				}
				if (isset($idMap[$shippingAddressId])) {
					$shippingAddressId = $idMap[$shippingAddressId];
				}
				if (isset($idMap[$billingAddressId])) {
					$billingAddressId = $idMap[$billingAddressId];
				}
			}
		}

		if ($shippingAddressId <= 0) {
			$this->error(distT('shipping_address_required'));
		}
		if ($billingAddressId <= 0) {
			$billingAddressId = $shippingAddressId;
		}

		$cartService = service('cart/Cart');
		if ($skuIds <= 0) {
			$where = $cartService->getOwnerWhere();
			$where['checked'] = 1;
			$rows = $cartService->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id' => 'desc']);
			if (empty($rows)) {
				$this->error(distT('checkout_error'));
			}
			$skuList = array_column($rows, 'quantity', 'sku_id');
		} else {
			$skuList = [$skuIds => $quantity];
		}

		$orderId = service('order/Order')->createOrder($skuList, $shippingAddressId, $billingAddressId, $insurance);
		if ($orderId) {
			if ($skuIds <= 0) {
				$delWhere = $cartService->getOwnerWhere();
				$delWhere['checked'] = 1;
				$cartService->deleteData($delWhere);
			}
			$this->success(distT('order_created'), ['url' => url('checkout/payOrder', ['id' => $orderId])]);
		} else {
			$this->error(distT('create_order_error'));
		}
	}

	public function calculateOrderFee()
	{
		$info = $this->getCheckoutData();
		if (empty($info['list'])) {
			$this->error(distT('checkout_error'));
		}
		$currencyService = service('system/Currency');
		$order = service('order/Order');
		$symbol = $currencyService->priceSymbol(2) ?: '$';

		$productTotal = (float)$info['total'];
		$origTotal = (float)$info['original_total'];
		$shippingFee = (float)$order->getShippingFee($productTotal);
		$insuranceFee = !empty(ipost('insurance')) ? (float)$order->getInsurance($productTotal) : 0.00;
		$orderTotal = $productTotal + $shippingFee + $insuranceFee;

		$list = [
			[
				'type'         => 0,
				'name'         => 'Original Retail Price',
				'value'        => sprintf('%.2f', $origTotal),
				'value_format' => $symbol . sprintf('%.2f', $origTotal),
			],
			[
				'type'         => 1,
				'name'         => 'Items Subtotal',
				'value'        => sprintf('%.2f', $productTotal),
				'value_format' => $symbol . sprintf('%.2f', $productTotal),
			],
			[
				'type'         => 2,
				'name'         => 'Express Shipping',
				'value'        => sprintf('%.2f', $shippingFee),
				'value_format' => $shippingFee > 0 ? ($symbol . sprintf('%.2f', $shippingFee)) : 'FREE',
			],
		];
		if ($insuranceFee > 0) {
			$list[] = [
				'type'         => 3,
				'name'         => 'Shipping Protection Guarantee',
				'value'        => sprintf('%.2f', $insuranceFee),
				'value_format' => $symbol . sprintf('%.2f', $insuranceFee),
			];
		}

		$data = [
			'fee_list'     => $list,
			'name'         => 'Order Total',
			'value'        => sprintf('%.2f', $orderTotal),
			'value_format' => $symbol . sprintf('%.2f', $orderTotal),
		];
		$this->success('ok', $data);
	}

	public function payOrderAjax()
	{
		$method = (int)ipost('method', 1);
		$orderId = (int)ipost('order_id', 0);
		if ($method <= 0) {
			$this->error('Please select a payment method.');
		}
		if ($orderId <= 0) {
			$this->error('Invalid order ID.');
		}
		$orderService = service('order/Order');
		$orderInfo = $orderService->loadData([
			'order_id' => $orderId,
			'mem_id'   => userId(),
		]);
		if (empty($orderInfo)) {
			$this->error('Order not found.');
		}
		$paidStatus = $orderService->getConst('STATUS_PAIED');
		$orderService->updateData($orderId, [
			'payment_id' => $method,
			'status'     => $paidStatus,
			'pay_time'   => now(),
		]);
		service('order/StatusHistory')->addLog($orderId, $paidStatus, $orderInfo['lan_id']);
		$this->success('Payment confirmed! Redirecting to order details...', [
			'url' => url('order/detail', ['id' => $orderId]),
		]);
	}

	public function setGuestEmail()
	{
		$email = trim((string)ipost('email', ''));
		if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->error(distT('email_empty'));
		}
		session()->set(type() . '_info.guest_email', $email);
		$this->success(distT('regist_able'));
	}

	protected function getCheckoutData()
	{
		$skuId = (int)input('id', 0);
		$quantity = max(1, (int)input('quantity', 1));

		if ($skuId <= 0) {
			$cartService = service('cart/Cart');
			$where = $cartService->getOwnerWhere();
			$where['checked'] = 1;
			$skuRows = $cartService->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id' => 'desc']);
			$skuList = !empty($skuRows) ? array_column($skuRows, 'quantity', 'sku_id') : [];
		} else {
			$skuList = [$skuId => $quantity];
		}
		if (empty($skuList)) {
			return [];
		}

		$sku = service('product/Sku');
		$tempSkuList = $sku->getListData(['sku_id' => ['in', array_keys($skuList)]], 'sku_id,spu_id,status,stock');
		$tempSkuList = array_column($tempSkuList, null, 'sku_id');
		$productTotal = 0;
		$productOriginalTotal = 0;
		$symbol = service('system/Currency')->priceSymbol(2) ?: '$';

		foreach ($skuList as $key => $value) {
			unset($skuList[$key]);
			if (empty($tempSkuList[$key])) continue;
			if ($tempSkuList[$key]['status'] != $sku->getConst('STATUS_OPEN')) continue;
			if ($value > $tempSkuList[$key]['stock']) continue;

			$tempInfo = $sku->getInfoCache($key, lanId());
			if (empty($tempInfo)) continue;
			$lineTotal = (float)$tempInfo['price'] * (int)$value;
			$lineOrig = (float)$tempInfo['original_price'] * (int)$value;
			$productTotal += $lineTotal;
			$productOriginalTotal += $lineOrig;
			$tempInfo['quantity'] = (int)$value;
			$tempInfo['subtotal_format'] = $symbol . sprintf('%.2f', $lineTotal);
			$skuList[$key] = $tempInfo;
		}
		return [
			'total'          => sprintf('%.2f', $productTotal),
			'original_total' => sprintf('%.2f', $productOriginalTotal),
			'list'           => $skuList,
		];
	}
}
