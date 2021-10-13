<?php

namespace app\controller\bag;
use app\controller\Base;

class Category extends Base
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$id = iget('id', 0);
		$cateList = make('app/service/category/Category')->getList(['parent_id'=>$id]);

		$this->view();
	}
}