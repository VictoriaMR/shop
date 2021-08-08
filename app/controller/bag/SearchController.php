<?php

namespace app\controller\bag;

use app\controller\Controller;
use frame\Html;

class SearchController extends Controller
{
	public function index()
	{	
		$this->assign('_title', '');
		$this->view();
	}
}