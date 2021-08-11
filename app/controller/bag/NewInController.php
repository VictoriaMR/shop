<?php

namespace app\controller\bag;
use app\controller\Controller;

class NewInController extends Controller
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		$this->assign('_title', 'Product\'s New In - '.site()->getName());
		$this->view();
	}
}