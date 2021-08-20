<?php

namespace app\controller\bag;
use app\controller\Controller;

class OrderController extends Controller
{
	public function index()
	{	
		$this->view();
	}

	protected function getOrderList()
	{
		$status = input('status');
		if (!is_null($status)) {
			
		}
	}
}