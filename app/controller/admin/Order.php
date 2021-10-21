<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Order extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '订单列表',
		];
		$this->_default = '订单管理';
	}

	public function index()
	{
		$siteList = make('app/service/site/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		dd($siteList);
		$this->_init();
		$this->view();
	}
}