<?php

namespace app\controller\home;
use app\controller\HomeBase;

class PageNotFound extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view([
			'_title' => 'Sorry, Page not found - '.\App::get('domain', 'name')
		]);
	}
}