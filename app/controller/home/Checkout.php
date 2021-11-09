<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Checkout extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/address');
		html()->addJs();
		html()->addJs('common/address');

		$info = $this->getCheckoutData();

		if (empty($info['list'])) {
			$error = distT('checkout_error');
		} else {
			//获取地址
			$memId = userId();
			if (empty($memId)) {
				$addressData = session()->get(APP_TEMPLATE_TYPE.'_info.address');
			} else {
				$addressData = make('app/service/member/Address')->getListData(['mem_id'=>$memId], '*', 0, 2, ['is_default'=>'desc','is_bill'=>'desc', 'address_id' => 'desc']);
			}
			if (!empty($addressData)) {
				foreach ($addressData as $value) {
					if ($value['is_default']) {
						$shipAddress = $value;
					}
					if ($value['is_bill']) {
						$billAddress = $value;
					}
				}
				if (empty($shipAddress)) {
					$shipAddress = array_shift($addressData);
				}
				if (empty($billingAddress)) {
					$billAddress = $shipAddress;
				}
				$order = make('app/service/order/Order');
				$symbol = make('app/service/Currency')->priceSymbol(2);
				$logisticsList = [[
					'name' => appT('express_shipping'),
					'fee' => $symbol.$order->getShippingFee($info['total']),
					'time_first' => 5,
					'time_second' => 9,
					'tips' => appT('shipping_tips', ['{start}'=>5, '{end}'=>9]),
				]];
				$insuranceFee = $symbol.$order->getInsurance($info['total']);
				$this->assign('insuranceFee', $insuranceFee);
				$this->assign('logisticsList', $logisticsList);
				$this->assign('shipAddress', $shipAddress);
				$this->assign('billAddress', $billAddress);
			}
			$this->assign('skuList', $info['list']);
		}

		$this->assign('skuId', ipost('id'));
		$this->assign('quantity', ipost('quantity'));
		$this->assign('error', $error ?? '');
		$this->assign('_title', distT('checkout'));

		$this->view();
	}

	public function createOrder()
	{
		$skuIds = ipost('id');
		$quantity = ipost('quantity', 1);
		$shippingAddressId = ipost('shipping_address_id');
		$billingAddressId = ipost('billing_address_id');
		$insurance = ipost('insurance', 0);
		//游客登录
		if (empty(userId())) {

		}
		if (empty($shippingAddressId)) {
			$this->error(distT('shipping_address_required'));
		}
		if (empty($billingAddressId)) {
			$this->error(distT('billing_address_required'));
		}
		$skuList = [];
		if (empty($skuIds)) {
			$where = [
				'mem_id' => userId(),
				'checked' => 1,
			];
			$skuList = make('app/service/Cart')->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id'=>'desc']);
			if (empty($skuList)) {
				$this->error(appT('checked_empty'));
			}
			$skuList = array_column($skuList, 'quantity', 'sku_id');
		} else {
			if (empty($skuIds)) {
				$this->error(appT('checked_empty'));
			}
			$skuList = [$skuIds=>$quantity];
		}
		$rst = make('app/service/order/Order')->createOrder($skuList, $shippingAddressId, $billingAddressId, $insurance);
		if ($rst) {
			//清空购物车
			if (empty($skuIds)) {
				make('app/service/Cart')->deleteData($where);
			}
			$this->success(url('checkout/payOrder', ['id'=>$rst]));
		} else {
			$this->error(appT('create_order_error'));
		}
	}

	public function calculateOrderFee()
	{
		$info = $this->getCheckoutData();
		$currencyService = make('app/service/Currency');
		$order = make('app/service/order/Order');
		$list = [];
		$orderTotal = $info['total'];
		$symbol = $currencyService->priceSymbol(2);
		$list[] = [
			'type' => 0,
			'name' => appT('product_original_total'),
			'value' => $info['original_total'],
			'value_format' => $symbol.$info['original_total'],
		];
		$list[] = [
			'type' => 1,
			'name' => appT('product_total'),
			'value' => $info['total'],
			'value_format' => $symbol.$info['total'],
		];
		$shippingFee = $order->getShippingFee($info['total']);
		$list[] = [
			'type' => 2,
			'name' => appT('shipping_fee'),
			'value' => $shippingFee,
			'value_format' => $symbol.$shippingFee,
		];
		$orderTotal += $shippingFee;
		if (!empty(ipost('insurance'))) {
			$insuranceFee = $order->getInsurance($info['total']);
			$list[] = [
				'type' => 3,
				'name' => appT('shipping_guarantee'),
				'value' => $insuranceFee,
				'value_format' => $symbol.$insuranceFee,
			];
			$orderTotal += $insuranceFee;
		}
		$data = [
			'fee_list' => $list,
			'name' => appT('order_total'),
			'value' => sprintf('%.2f', $orderTotal),
			'value_format' => $symbol.$orderTotal,
		];
		$this->success($data, '');
	}

	public function selectLogistics()
	{
		$info = $this->getCheckoutData();
		$language = make('app/service/Language');
		$order = make('app/service/order/Order');
		$logisticsList = [[
			'name' => appT('express_shipping'),
			'fee' => $language->priceSymbol(2).$order->getShippingFee($info['total']),
			'time_first' => 5,
			'time_second' => 9,
			'tips' => appT('shipping_tips', ['start'=>5, 'end'=>9]),
		]];
		$this->success($logisticsList);
	}

	protected function getCheckoutData()
	{
		$skuId = (int)ipost('id');
		$quantity = (int)ipost('quantity');
		$address_id = (int)ipost('shipping_address_id');

		if (empty($skuId)) {
			$where = [
				'mem_id' => userId(),
				'checked' => 1,
			];
			if (empty(userId())) {
				$where['uuid'] = uuId();
			}
			$skuList = make('app/service/Cart')->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id'=>'desc']);
			if (!empty($skuList)) {
				$skuList = array_column($skuList, 'quantity', 'sku_id');
			}
		} else {
			$skuList = [$skuId => $quantity];
		}
		if (empty($skuList)) {
			return [];
		}
		//获取数值总额
		$sku = make('app/service/product/Sku');
		$tempSkuList = $sku->getListData(['sku_id'=> ['in', array_keys($skuList)]], 'sku_id,spu_id,status,stock');
		$tempSkuList = array_column($tempSkuList, null, 'sku_id');
		$productTotal = 0;
		$productOriginalTotal = 0;
		foreach ($skuList as $key => $value) {
			unset($skuList[$key]);
			if (empty($tempSkuList[$key])) {
				continue;
			}
			if ($tempSkuList[$key]['status'] != $sku->getConst('STATUS_OPEN')) {
				continue;
			}
			if ($value > $tempSkuList[$key]['stock']) {
				continue;
			}
			$tempInfo = $sku->getInfoCache($key, lanId());
			$skuList[$key] = ['quantity' => $value];
			$productTotal += $tempInfo['price']*$value;
			$productOriginalTotal += $tempInfo['original_price']*$value;
			$skuList[$key] += $tempInfo;
		}
		return [
			'total' => sprintf('%.2f', $productTotal),
			'original_total' => sprintf('%.2f', $productOriginalTotal),
			'list' => $skuList,
		];
	}

	public function payOrder()
	{
		html()->addCss();
		html()->addCss('common/address');
		html()->addJs('common/address');
		html()->addJs();

		$orderId = (int)iget('id');
		$method = iget('method');
		$error = '';
		if (empty($orderId)) {
			$error = appT('order_error');
		}
		if (empty($error)) {
			$orderInfo = make('app/service/order/Order')->getInfo($orderId);
			if (empty($orderInfo)) {
				$error = appT('order_error');
			} else {
				//获取支付列表
				$methodList = make('app/payment/PaymentMethod')::sortedMethodList();
				if (!empty($methodList) && empty($method)) {
					$method = key($methodList);
				}
				$this->assign('method', $method);
				$this->assign('methodList', $methodList);
				$this->assign('orderInfo', $orderInfo);
			}
		}

		$this->assign('error', $error);
		$this->assign('_title', 'Checkout, Pay Order - ');
		$this->view();
	}

	public function payOrderAjax()
	{
		$method = (int)ipost('method');
		$orderId = (int)ipost('order_id');
		$orderTotal = ipost('order_total');
		$currency = ipost('currency');
		$countryCode2 = ipost('country_code2');
		if (empty($method)) {
			$this->error(appT('payment_method_error'));
		}
		if (empty($orderId)) {

		} else {
			
		}
	}
}