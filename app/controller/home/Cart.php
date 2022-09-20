<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Cart extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$list = make('app/service/Cart')->getList();
		$checkedList = [];
		$unCheckList = [];
		$summary = [];
		if (!empty($list)) {
			$originalPriceTotal = 0;
			$priceTotal = 0;
			foreach ($list as $key => $value) {
				if ($value['quantity'] == 0 || $value['quantity'] > $value['stock']) {
					$value['out_of_stock'] = true;
				} else {
					$value['out_of_stock'] = false;
				}
				if ($value['checked']) {
					$checkedList[] = $value;
					$originalPriceTotal += $value['original_price']*$value['quantity'];
					$priceTotal += $value['price']*$value['quantity'];
				} else {
					$unCheckList[] = $value;
				}
			}
			//收藏检查
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			$where = [
				'mem_id' => userId(),
				'spu_id' => ['in', $spuIdArr],
			];
			$list = make('app/service/member/Collect')->getListData($where, 'spu_id');
			$list = array_column($list, 'spu_id');
			$symbol = make('app/service/currency/Currency')->priceSymbol(2);
			$originalPriceTotal = sprintf('%.2f', $originalPriceTotal);
			$priceTotal = sprintf('%.2f', $priceTotal);
			$summary[] = [
				'type'=> 1,
				'name' => appT('original_price'),
				'price' => $originalPriceTotal,
				'price_format' => $symbol.$originalPriceTotal,
			];
			$summary[] = [
				'type'=> 2,
				'name' => appT('total'),
				'price' => $priceTotal,
				'price_format' => $symbol.$priceTotal,
			];
		}
		$this->assign('isLogin', userId());
		$this->assign('checkedList', $checkedList);
		$this->assign('unCheckList', $unCheckList);
		$this->assign('collectList', $list);
		$this->assign('summary', $summary);
		$this->assign('_title', distT('shopping_bag'));
		$this->view();
	}

	public function cartSummary()
	{
		$list = make('app/service/Cart')->getList();
		$originalPriceTotal = 0;
		$priceTotal = 0;
		if (!empty($list)) {
			foreach ($list as $key => $value) {
				if ($value['checked']) {
					$originalPriceTotal += $value['original_price']*$value['quantity'];
					$priceTotal += $value['price']*$value['quantity'];
				}
			}
		}
		$summary = [];
		$symbol = make('app/service/currency/Currency')->priceSymbol(2);
		$originalPriceTotal = sprintf('%.2f', $originalPriceTotal);
		$priceTotal = sprintf('%.2f', $priceTotal);
		$summary[] = [
			'type'=> 1,
			'name' => appT('original_price'),
			'price' => $originalPriceTotal,
			'price_format' => $symbol.$originalPriceTotal,
		];
		$summary[] = [
			'type'=> 2,
			'name' => appT('total'),
			'price' => $priceTotal,
			'price_format' => $symbol.$priceTotal,
		];
		$this->success($summary);
	}

	public function cartCount()
	{
		$rst = make('app/service/Cart')->getCartCount();
		if ($rst) {
			$this->success($rst);
		} else {
			$this->error();
		}
	}

	public function addToCart()
	{
		$skuId = ipost('sku_id');
		$num = ipost('num', 1);
		if (empty($skuId)) {
			$this->error(appT('param_error'));
		}
		$sku = make('app/service/product/Sku');
		$where = [
			'sku_id' => $skuId,
			'site_id' => siteId(),
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$skuInfo = $sku->loadData($where, 'stock,price');
		if (!$skuInfo) {
			$this->error(distT('add_invalid'));
		}
		$where = ['sku_id' => $skuId, 'mem_id' => userId()];
		if (!userId()) {
			$where['uuid'] = uuId();
		}
		$cart = make('app/service/Cart');
		$cartSkuIno = $cart->loadData($where);
		if (empty($cartSkuIno)) {
			if ($skuInfo['stock'] < $num) {
				$this->error(distT('add_out_stock'));
			}
			$where['quantity'] = $num;
			$where['price'] = $skuInfo['price'];
			$rst = $cart->insert($where);
		} else {
			$num += $cartSkuIno['quantity'];
			if ($skuInfo['stock'] > $cartSkuIno['quantity']) {
				if ($skuInfo['stock'] < $num) {
					$num = $skuInfo['stock'];
				}
				$where = ['quantity' => $num];
				$where['price'] = $skuInfo['price'];
				$rst = $cart->updateData($cartSkuIno['cart_id'], $where);
			} else {
				$rst = true;
			}
		}
		if ($rst) {
			$this->success(distT('add_success'), ['cart_count'=>$cart->getCartCount()]);
		} else {
			$this->error(distT('add_fail'));
		}
	}

	public function updateQuantity()
	{
		$id = ipost('id');
		$quantity = ipost('quantity', 1);
		if (empty($id)) {
			$this->error(appT('param_error'));
		}
		$where = ['mem_id' => userId(), 'cart_id' => $id];
		$cart = make('app/service/Cart');
		$cartInfo = $cart->loadData($where);
		if (empty($cartInfo)) {
			$this->error(distT('cart_not_exist'));
		}
		$sku = make('app/service/product/Sku');
		$where = [
			'sku_id' => $cartInfo['sku_id'],
			'site_id' => siteId(),
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$skuInfo = $sku->loadData($where, 'stock');
		if (empty($skuInfo)) {
			$this->error(distT('add_invalid'));
		}
		if ($skuInfo['stock'] < $quantity) {
			$this->error(distT('add_out_stock'));
		}
		$rst = $cart->updateData($id, ['quantity' => $quantity]);
		if ($rst) {
			$this->success(distT('add_success'));
		} else {
			$this->error(distT('add_fail'));
		}
	}

	public function remove()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('param_error'));
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$rst = make('app/service/Cart')->deleteData($where);
		if ($rst) {
			$this->success(distT('remove_success'));
		} else {
			$this->error(distT('remove_fail'));
		}
	}

	public function setChecked()
	{
		$id = (int)ipost('id');
		$check = (int)ipost('check', 0);
		if (empty($id)) {
			$this->error(appT('param_error'));
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$rst = make('app/service/Cart')->updateData($where, ['checked'=>$check]);
		if ($rst) {
			$this->success($check ? distT('move_to_cart_success') : distT('save_for_later_success'));
		} else {
			$this->error($check ? distT('move_to_cart_fail') : distT('save_for_later_fail'));
		}
	}

	public function check()
	{
		$rst = make('app/service/Cart')->check();
		if ($rst) {
			$this->success(url('checkout'));
		} else {
			$this->error(distT('check_error'));
		}
	}

	public function editInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('param_error'));
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$cart = make('app/service/Cart');
		$cartInfo = $cart->loadData($where, 'sku_id');
		if (empty($cartInfo)) {
			$this->error(distT('cart_not_exist'));
		}
		$info = make('app/service/product/Sku')->loadData($cartInfo['sku_id'], 'spu_id');
		if (empty($info)) {
			$this->error(distT('add_invalid'));
		}
		$info = make('app/service/product/Spu')->getInfoCache($info['spu_id'], lanId());
		if (empty($info)) {
			$this->error(distT('add_invalid'));
		} else {
			$info['sku_id'] = $cartInfo['sku_id'];
			$this->success($info);
		}
	}

	public function edit()
	{
		$cartId = (int)ipost('cart_id');
		$skuId = (int)ipost('sku_id');
		if (empty($cartId) || empty($skuId)) {
			$this->error(appT('param_error'));
		}
		$memId = userId();
		$where = [
			'cart_id' => $cartId,
			'mem_id' => $memId,
		];
		$cart = make('app/service/Cart');
		$cartInfo = $cart->loadData($where, 'sku_id,quantity');
		if (empty($cartInfo)) {
			$this->error(distT('cart_not_exist'));
		}
		if ($cartInfo['sku_id'] == $skuId) {
			$this->success(distT('add_success'));
		}
		$where = [
			'mem_id' => $memId,
			'sku_id' => $skuId,
		];
		$changeInfo = $cart->loadData($where);
		if (empty($changeInfo)) {
			$cart->updateData($cartId, ['sku_id'=>$skuId]);
		} else {
			$cart->updateData($changeInfo['cart_id'], ['quantity'=>$changeInfo['quantity']+$cartInfo['quantity']]);
			$cart->deleteData($cartId);
		}
		$this->success(distT('add_success'));
	}
}