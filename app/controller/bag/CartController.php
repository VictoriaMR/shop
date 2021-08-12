<?php

namespace app\controller\bag;
use app\controller\Controller;

class CartController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/productList');
		html()->addJs();

		$list = make('app/service/CartService')->getList();
		$checkedList = [];
		$unCheckList = [];
		$originalPriceTotal = 0;
		$priceTotal = 0;
		if (!empty($list)) {
			foreach ($list as $key => $value) {
				if ($value['quantity'] > $value['stock']) {
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
			$list = make('app/service/member/CollectService')->getListData($where, 'spu_id');
			$list = array_column($list, 'spu_id');
		}
		$summary = [];
		$languageService = make('app/service/LanguageService');
		$symbol = $languageService->priceSymbol(2);
		$originalPriceTotal = sprintf('%.2f', $originalPriceTotal);
		$priceTotal = sprintf('%.2f', $priceTotal);
		$summary[] = [
			'type'=> 1,
			'name' => 'Original Price',
			'price' => $originalPriceTotal,
			'price_format' => $symbol.$originalPriceTotal,
		];
		$summary[] = [
			'type'=> 2,
			'name' => 'Total',
			'price' => $priceTotal,
			'price_format' => $symbol.$priceTotal,
		];
		
		$this->assign('checkedList', $checkedList);
		$this->assign('unCheckList', $unCheckList);
		$this->assign('collectList', $list);
		$this->assign('summary', $summary);
		$this->assign('_title', 'My shopping cart - '.site()->getName());
		$this->view();
	}

	public function cartSummary()
	{
		$list = make('app/service/CartService')->getList();
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
		$languageService = make('app/service/LanguageService');
		$symbol = $languageService->priceSymbol(2);
		$originalPriceTotal = sprintf('%.2f', $originalPriceTotal);
		$priceTotal = sprintf('%.2f', $priceTotal);
		$summary[] = [
			'type'=> 1,
			'name' => 'Original Price',
			'price' => $originalPriceTotal,
			'price_format' => $symbol.$originalPriceTotal,
		];
		$summary[] = [
			'type'=> 2,
			'name' => 'Total',
			'price' => $priceTotal,
			'price_format' => $symbol.$priceTotal,
		];
		$this->success($summary);
	}

	public function cartCount()
	{
		$rst = make('app/service/CartService')->getCartCount();
		if ($rst) {
			$this->success($rst, 'success');
		} else {
			$this->error('cart count error');
		}
	}

	public function addToCart()
	{
		$skuId = ipost('sku_id');
		$num = ipost('num', 1);
		if (empty($skuId)) {
			$this->error('Sorry, That product id is required.');
		}
		$skuService = make('app/service/product/SkuService');
		$where = [
			'sku_id' => $skuId,
			'site_id' => siteId(),
			'status' => $skuService->getConst('STATUS_OPEN'),
		];
		$skuInfo = $skuService->loadData($where, 'stock');
		if (!$skuInfo) {
			$this->error('Sorry, That product was invalid.');
		}
		$where = ['mem_id' => userId(), 'sku_id' => $skuId];
		$cartService = make('app/service/CartService');
		$cartSkuIno = $cartService->loadData($where);
		if (empty($cartSkuIno)) {
			if ($skuInfo['stock'] < $num) {
				$this->error('Sorry, That product was out of stock.');
			}
			$where['quantity'] = $num;
			$rst = $cartService->insert($where);
		} else {
			$num += $cartSkuIno['quantity'];
			if ($skuInfo['stock'] < $num) {
				$num = $skuInfo['stock'];
			}
			$where = ['quantity' => $num];
			$rst = $cartService->updateData($cartSkuIno['cart_id'], $where);
		}
		if ($rst) {
			$this->success('Add to cart success, go and check it out.');
		} else {
			$this->error('Sorry, That product add to cart failed.');
		}
	}

	public function updateQuantity()
	{
		$id = ipost('id');
		$quantity = ipost('quantity', 1);
		if (empty($id)) {
			$this->error('Sorry, That cart id is required.');
		}
		$where = ['mem_id' => userId(), 'cart_id' => $id];
		$cartService = make('app/service/CartService');
		$cartIno = $cartService->loadData($where);
		if (empty($cartIno)) {
			$this->error('Sorry, That cart was not exist!');
		}
		$skuService = make('app/service/product/SkuService');
		$where = [
			'sku_id' => $cartIno['sku_id'],
			'site_id' => siteId(),
			'status' => $skuService->getConst('STATUS_OPEN'),
		];
		$skuInfo = $skuService->loadData($where, 'stock');
		if (empty($skuInfo)) {
			$this->error('Sorry, That cart product was invalid.');
		}
		if ($skuInfo['stock'] < $quantity) {
			$this->error('Sorry, That cart product was out of stock.');
		}
		$rst = $cartService->updateData($id, ['quantity' => $quantity]);
		if ($rst) {
			$this->success('Update the quantity success, go and check it out.');
		} else {
			$this->error('Sorry, Update the quantity failed.');
		}
	}

	public function remove()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('Move cart\'s Param error');
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$rst = make('app/service/CartService')->deleteData($where);
		if ($rst) {
			$this->success('This item has been removed from your cart list.');
		} else {
			$this->error('This item remove from your cart list failed!');
		}
	}

	public function check()
	{
		$id = (int)ipost('id');
		$check = (int)ipost('check', 0);
		if (empty($id)) {
			$this->error('Move cart\'s Param error');
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$rst = make('app/service/CartService')->updateData($where, ['checked'=>$check]);
		if ($rst) {
			$this->success($check ? 'This item has been move to your cart list.' : 'This item has been save for later from your cart list.');
		} else {
			$this->error($check ? 'This item move to your cart list failed.' : 'This item save for later from your cart list failed.');
		}
	}

	public function editInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('Edit cart\'s Param error');
		}
		$where = [
			'cart_id' => $id,
			'mem_id' => userId(),
		];
		$cartService = make('app/service/CartService');
		$cartIno = $cartService->loadData($where, 'sku_id');
		if (empty($cartIno)) {
			$this->error('Sorry, That cart was not exist!');
		}
		$info = make('app/service/product/SkuService')->loadData($cartIno['sku_id'], 'spu_id');
		if (empty($info)) {
			$this->error('Sorry, That cart product was invalid.');
		}
		$info = make('app/service/product/SpuService')->getInfoCache($info['spu_id'], lanId());
		if (empty($info)) {
			$this->error('Sorry, That cart product was invalid.');
		} else {
			$info['sku_id'] = $cartIno['sku_id'];
			$this->success($info);
		}
	}

	public function edit()
	{
		$cartId = (int)ipost('cart_id');
		$skuId = (int)ipost('sku_id');
		if (empty($cartId) || empty($skuId)) {
			$this->error('Edit cart\'s Param error');
		}
		$memId = userId();
		$where = [
			'cart_id' => $cartId,
			'mem_id' => $memId,
		];
		$cartService = make('app/service/CartService');
		$cartIno = $cartService->loadData($where, 'sku_id,quantity');
		if (empty($cartIno)) {
			$this->error('Sorry, That cart was not exist!');
		}
		if ($cartIno['sku_id'] == $skuId) {
			$this->error('Sorry, The cart\'s product was no changes!');
		}
		$where = [
			'mem_id' => $memId,
			'sku_id' => $skuId,
		];
		$changeInfo = $cartService->loadData($where);
		if (empty($changeInfo)) {
			$cartService->updateData($cartId, ['sku_id'=>$skuId]);
		} else {
			$cartService->updateData($changeInfo['cart_id'], ['quantity'=>$changeInfo['quantity']+$cartIno['quantity']]);
			$cartService->deleteData($cartId);
		}
		$this->success('The cart\'s product edit success.');
	}
}