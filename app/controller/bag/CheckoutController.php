<?php

namespace app\controller\bag;
use app\controller\Controller;

class CheckoutController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/address');
		html()->addJs();
		html()->addJs('common/address');

		$info = $this->getCheckoutData();

		$skuId = (int)input('id');
		$quantity = (int)input('quantity');

		if (empty($info['list'])) {
			$error = 'Sorry, we don\'t find any product here, Maybe your shopping cart was Empty. Please check your shopping cart and check it out';
		} else {
			//获取地址
			$memId = userId();
			$addressData = make('app/service/member/AddressService')->getListData(['mem_id'=>$memId], '*', 0, 2, ['is_default'=>'desc','is_bill'=>'desc', 'address_id' => 'desc']);
			if (empty($addressData)) {
				$shipAddress = $billAddress = [];
			} else {
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
			}

			if (empty($shipAddress)) {
				$logisticsList = [];
				$insuranceFee = 0;
			} else {
				$orderService = make('app/service/order/OrderService');
				$symbol = make('app/service/LanguageService')->priceSymbol(2);
				$logisticsList = [[
					'name' => 'Express Shipping',
					'fee' => $symbol.$orderService->getShippingFee($info['total']),
					'time_first' => 5,
					'time_second' => 9,
					'tips' => 'It may takes 5 - 9 Days. With Detailed Tracking Information. Need Correct Phone Number.'
				]];
				$insuranceFee = $symbol.$orderService->getInsurance($info['total']);
			}
			$this->assign('insuranceFee', $insuranceFee);
			$this->assign('logisticsList', $logisticsList);
			$this->assign('skuList', $info['list']);
			$this->assign('shipAddress', $shipAddress);
			$this->assign('billAddress', $billAddress);
		}

		$this->assign('skuId', $skuId);
		$this->assign('quantity', $quantity);
		$this->assign('error', $error ?? '');
		$this->assign('_title', 'Checkout - '.site()->getName());

		$this->view();
	}

	public function createOrder()
	{
		$skuIds = (int)ipost('id');
		$quantity = (int)ipost('quantity', 1);
		$shippingAddressId = ipost('shipping_address_id');
		$billingAddressId = ipost('billing_address_id');
		$insurance = ipost('insurance', 0);
		if (empty($shippingAddressId)) {
			$this->error('Sorry, That shipping address is required.');
		}
		if (empty($billingAddressId)) {
			$this->error('Sorry, That billing address is required.');
		}
		$skuList = [];
		if (empty($skuIds)) {
			$where = [
				'mem_id' => userId(),
				'checked' => 1,
			];
			$skuList = make('app/service/CartService')->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id'=>'desc']);
			if (empty($skuList)) {
				$this->error('Sorry, Your cart\'s checked product was empty.');
			}
			$skuList = array_column($skuList, 'quantity', 'sku_id');
		} else {
			if (empty($skuIds)) {
				$this->error('Sorry, Product was empty.');
			}
			$skuList = [$skuIds=>$quantity];
		}
		$rst = make('app/service/order/OrderService')->createOrder($skuList, $shippingAddressId, $billingAddressId, $insurance);
		if ($rst) {
			//清空购物车
			if (empty($skuIds)) {
				make('app/service/CartService')->deleteData($where);
			}
			$this->success(url('checkout/payOrder', ['id'=>$rst]));
		} else {
			$this->error('Sorry, Create order failed.');
		}
	}

	public function calculateOrderFee()
	{
		$info = $this->getCheckoutData();
		$languageService = make('app/service/LanguageService');
		$orderService = make('app/service/order/OrderService');
		$list = [];
		$orderTotal = $info['total'];
		$symbol = $languageService->priceSymbol(2);
		$list[] = [
			'type' => 0,
			'name' => 'Product Original Total',
			'value' => $info['original_total'],
			'value_format' => $symbol.$info['original_total'],
		];
		$list[] = [
			'type' => 1,
			'name' => 'Product Total',
			'value' => $info['total'],
			'value_format' => $symbol.$info['total'],
		];
		$shippingFee = $orderService->getShippingFee($info['total']);
		$list[] = [
			'type' => 2,
			'name' => 'Shipping Fee',
			'value' => $shippingFee,
			'value_format' => $symbol.$shippingFee,
		];
		$orderTotal += $shippingFee;
		if (!empty(input('insurance'))) {
			$insuranceFee = $orderService->getInsurance($info['total']);
			$list[] = [
				'type' => 3,
				'name' => 'Shipping Guarantee:',
				'value' => $insuranceFee,
				'value_format' => $symbol.$insuranceFee,
			];
			$orderTotal += $insuranceFee;
		}
		$data = [
			'fee_list' => $list,
			'name' => 'Order Total',
			'value' => sprintf('%.2f', $orderTotal),
			'value_format' => $symbol.$orderTotal,
		];
		$this->success($data, '');
	}

	public function selectLogistics()
	{
		$info = $this->getCheckoutData();
		$languageService = make('app/service/LanguageService');
		$orderService = make('app/service/order/OrderService');
		$logisticsList = [[
			'name' => 'Express Shipping',
			'fee' => $languageService->priceSymbol(2).$orderService->getShippingFee($info['total']),
			'time_first' => 5,
			'time_second' => 9,
			'tips' => 'It may takes 5 - 9 Days. With Detailed Tracking Information. Need Correct Phone Number.'
		]];
		$this->success($logisticsList);
	}

	protected function getCheckoutData()
	{
		$skuId = (int)input('id');
		$quantity = (int)input('quantity');
		$address_id = (int)input('shipping_address_id');

		if (empty($skuId)) {
			$where = [
				'mem_id' => userId(),
				'checked' => 1,
			];
			$skuList = make('app/service/CartService')->getListData($where, 'sku_id,quantity', 0, 0, ['cart_id'=>'desc']);
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
		$skuService = make('app/service/product/SkuService');
		$tempSkuList = $skuService->getListData(['sku_id'=> ['in', array_keys($skuList)]], 'sku_id,spu_id,status,stock');
		$tempSkuList = array_column($tempSkuList, null, 'sku_id');
		$productTotal = 0;
		$productOriginalTotal = 0;
		foreach ($skuList as $key => $value) {
			unset($skuList[$key]);
			if (empty($tempSkuList[$key])) {
				continue;
			}
			if ($tempSkuList[$key]['status'] != $skuService->getConst('STATUS_OPEN')) {
				continue;
			}
			if ($value > $tempSkuList[$key]['stock']) {
				continue;
			}
			$tempInfo = $skuService->getInfoCache($key, lanId());
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
		html()->addJs();

		$this->assign('_title', 'Checkout, Pay Order - '.site()->getName());
		$this->view();
	}
}