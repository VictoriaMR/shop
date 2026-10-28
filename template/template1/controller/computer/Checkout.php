<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Checkout extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$skuId = (int)input('id', 0);
		$quantity = max(1, (int)input('quantity', 1));

		$info = $this->buildCheckoutItems($skuId, $quantity);
		$error = '';
		$addressList = [];
		$shipAddress = [];
		$billAddress = [];
		$email = '';

		if (empty($info['list'])) {
			$error = distT('checkout_error');
		} else {
			$memId = userId();
			$countryService = service('system/Country');
			if ($memId <= 0) {
				$addressList = session()->get(type() . '_info.address') ?: [];
				$email = session()->get(type() . '_info.guest_email', '');
			} else {
				$memInfo = session()->get(type() . '_info') ?: [];
				$email = $memInfo['email'] ?? '';
				$addressList = service('member/Address')->getListData(
					['mem_id' => $memId],
					'*',
					0,
					10,
					['is_default' => 'desc', 'is_bill' => 'desc', 'address_id' => 'desc']
				);
			}
			foreach ($addressList as $k => $addr) {
				$addr['country'] = $countryService->getName($addr['country_code2'] ?? '');
				$addressList[$k] = $addr;
				if (!empty($addr['is_default']) && empty($shipAddress)) {
					$shipAddress = $addr;
				}
				if (!empty($addr['is_bill']) && empty($billAddress)) {
					$billAddress = $addr;
				}
			}
			if (empty($shipAddress) && !empty($addressList)) {
				$shipAddress = $addressList[0];
			}
			if (empty($billAddress) && !empty($shipAddress)) {
				$billAddress = $shipAddress;
			}
		}

		$orderService = service('order/Order');
		$symbol = service('system/Currency')->priceSymbol(2) ?: '$';
		$prodTotal = (float)($info['total'] ?? 0);
		$origTotal = (float)($info['original_total'] ?? 0);
		$shipFee = $prodTotal > 0 ? (float)$orderService->getShippingFee($prodTotal) : 0.00;
		$insFee = $prodTotal > 0 ? (float)$orderService->getInsurance($prodTotal) : 1.99;
		$grandTotal = $prodTotal + $shipFee;

		$summary = [
			'symbol'                => $symbol,
			'original_total_format' => $symbol . sprintf('%.2f', $origTotal),
			'subtotal_format'       => $symbol . sprintf('%.2f', $prodTotal),
			'discount'              => max(0, $origTotal - $prodTotal),
			'discount_format'       => '-' . $symbol . sprintf('%.2f', max(0, $origTotal - $prodTotal)),
			'shipping_fee'          => $shipFee,
			'shipping_format'       => $shipFee > 0 ? ($symbol . sprintf('%.2f', $shipFee)) : 'FREE',
			'insurance_fee'         => $insFee,
			'insurance_format'      => $symbol . sprintf('%.2f', $insFee),
			'grand_total'           => sprintf('%.2f', $grandTotal),
			'grand_total_format'    => $symbol . sprintf('%.2f', $grandTotal),
		];

		$this->assign('isLogin', userId() > 0);
		$this->assign('email', $email);
		$this->assign('skuId', $skuId);
		$this->assign('quantity', $quantity);
		$this->assign('skuList', $info['list'] ?? []);
		$this->assign('addressList', $addressList);
		$this->assign('shipAddress', $shipAddress);
		$this->assign('billAddress', $billAddress);
		$this->assign('summary', $summary);
		$this->assign('error', $error);
		$this->assign('_title', distT('checkout') . ' - ' . $siteName);
	}

	public function payOrder()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$orderId = (int)iget('id', 0);
		$method = (int)iget('method', 0);
		$error = '';
		$orderInfo = [];
		$methodList = [];

		if ($orderId <= 0) {
			$error = 'Invalid order reference.';
		} else {
			$orderInfo = service('order/Order')->getInfo($orderId);
			if (empty($orderInfo)) {
				$error = 'Order not found or you do not have permission to view it.';
			} else {
				$methodList = service('payment/Payment')->sortedMethodList();
				if (!empty($methodList) && $method <= 0) {
					$method = (int)key($methodList);
				}
			}
		}

		$this->assign('orderId', $orderId);
		$this->assign('method', $method);
		$this->assign('methodList', $methodList);
		$this->assign('orderInfo', $orderInfo);
		$this->assign('error', $error);
		$this->assign('_title', distT('pay_order_title') . ' - ' . $siteName);
	}

	protected function buildCheckoutItems(int $skuId, int $quantity): array
	{
		if ($skuId <= 0) {
			$cartService = service('cart/Cart');
			$where = $cartService->getOwnerWhere();
			$where['checked'] = 1;
			$rows = $cartService->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id' => 'desc']);
			$skuList = !empty($rows) ? array_column($rows, 'quantity', 'sku_id') : [];
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
