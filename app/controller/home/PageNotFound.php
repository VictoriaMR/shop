<?php

namespace app\controller\home;
use app\controller\HomeBase;

class PageNotFound extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/productList');

		$siteName = \App::get('site_name');
		$this->assign('siteName', $siteName);
		$this->assign('_title', 'Sorry, Page not found - '.$siteName);
		$this->view();
	}
}