<?php

namespace app\controller\bag;
use app\controller\Base;

class Category extends Base
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

<<<<<<< HEAD
		$cateList = make('app/service/category/Category')->getListData(['parent_id'=>0]);

=======
		$id = iget('id', 0);
		$cateList = make('app/service/category/Category')->getList(['parent_id'=>$id]);
>>>>>>> 93d136b03aba79b77063f51194dfd7e0115eba27

		$this->view();
	}
}