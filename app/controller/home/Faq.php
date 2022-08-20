<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Faq extends HomeBase
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		$fid = (int)iget('fid', 0);
		if ($fid > 0) {
			
		}
		$this->view(true);
	}
}