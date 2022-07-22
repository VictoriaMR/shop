<?php

namespace template\newsme\controller\computer;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addCss('slider');
		html()->addJs('slider');

		
	}
}