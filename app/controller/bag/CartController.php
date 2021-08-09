<?php

namespace app\controller\bag;
use app\controller\Controller;

class CartController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$list = make('app/service/CartService')->getList();
		
		$this->assign('list', $list);
		$this->assign('_title', 'My shopping cart - '.site()->getName());
		$this->view();
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
			$where['add_time'] = now();
			$where['update_time'] = now();
			$rst = $cartService->insert($where);
		} else {
			$num += $cartSkuIno['quantity'];
			if ($skuInfo['stock'] < $num) {
				$this->error('Sorry, That product was out of stock.');
			}
			$where = ['quantity' => $num, 'update_time' => now()];
			$rst = $cartService->updateData($cartSkuIno['cart_id'], $where);
		}
		if ($rst) {
			$this->success('Add to cart success, go and check it out.');
		} else {
			$this->error('Sorry, That product add to cart failed.');
		}
	}
}