<?php

namespace app\controller\bag;
use app\controller\;

class PageNotFound extends 
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/productList');

		$siteName = site()->getName();
		$this->assign('siteName', $siteName);
		$this->assign('_title', 'Sorry, Page not found - '.$siteName);
		$this->view();
	}
}