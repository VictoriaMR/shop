<?php

namespace app\controller\bag;
use app\controller\Base;

class Category extends Base
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$cateList = make('app/service/category/Category')->getListData(['parent_id'=>0]);


		$this->view();
	}
}