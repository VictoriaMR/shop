<?php

namespace app\controller\bag;
use app\controller\Controller;

class IndexController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		//分类列表
		$hotCategory = make('app/service/CategoryService')->getHotCategory(16);
		$this->assign('hot_category', array_chunk($hotCategory, 2));

		$this->view();
	}
}