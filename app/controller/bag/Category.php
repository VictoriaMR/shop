<?php

namespace app\controller\bag;
use app\controller\;

class Category extends 
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$cateList = make('app/service/Category')->getList(['parent_id'=>$id]);


		$this->view();
	}
}