<?php

namespace app\controller\bag;

use app\controller\Controller;
use frame\Html;

class LoginController extends Controller
{
	public function index()
	{	
		Html::addCss();
		Html::addJs();
		return view();
	}
}