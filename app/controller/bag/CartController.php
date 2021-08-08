<?php

namespace app\controller\bag;
use app\controller\Controller;

class CartController extends Controller
{
	public function index()
	{	
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
}