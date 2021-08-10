<?php

namespace app\controller\bag;
use app\controller\Controller;

class PageNotFoundController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addCss('common/recommend');

		$siteName = site()->getName();
		$this->assign('siteName', $siteName);
		$this->assign('_title', 'Sorry, Page not found - '.$siteName);
		$this->view();
	}
}