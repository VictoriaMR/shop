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

		$skuId = (int)ipost('sku_id');
		$quantity = (int)ipost('quantity', 1);
		
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

		//获取选中的产品, 直接购买或者购物车购买
		if (empty($skuId)) {
			$where = [
				'mem_id' => userId(),
				'checked' => 1,
			];
			$skuList = make('app/service/CartService')->getListData($where, 'sku_id,quantity', 0, 0, ['cate_id'=>'desc']);
			$skuList = array_column($skuList, 'stock', 'sku_id');
		} else {
			$skuList = [$skuId => $quantity];
		}

		// foreach ()

		// dd($shipAddress, $billAddress);
		$this->assign('shipAddress', $shipAddress);
		$this->assign('billAddress', $billAddress);
		$this->assign('_title', 'Checkout - '.site()->getName());

		$this->view();
	}

	public function createOrder()
	{
		$skuIds = (int)ipost('sku_id');
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
			$skuList = make('app/service/CartService')->getListData($where, 'sku_id,quantity', 0, 0, ['cate_id'=>'desc']);
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
}