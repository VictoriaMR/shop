<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class PageNotFound extends AdminBase
{
	public function index()
	{	
		frame('Html')->addCss();
		frame('Html')->addJs();
		$siteName = \App::get('site_name');
		$this->assign('siteName', $siteName);
		$this->assign('_title', 'Sorry, Page not found - '.$siteName);
		$this->view();
	}
}