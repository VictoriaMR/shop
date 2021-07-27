<?php

namespace app\controller\bag;
use app\controller\Controller;

class CategoryController extends Controller
{
	public function index()
	{	
		$id = iget('id');
		if (empty($id)) {
			$this->error('找不到分类数据');
		}
		html()->addCss();
		html()->addJs();


		$this->view();
	}
}