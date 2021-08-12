<?php

namespace app\controller\bag;
use app\controller\Controller;

class CategoryController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$cateList = make('app/service/CategoryService')->getList(['parent_id'=>$id]);


		$this->view();
	}
}