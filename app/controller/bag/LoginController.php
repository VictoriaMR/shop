<?php

namespace app\controller\bag;
use app\controller\Controller;


class LoginController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		$this->view();
	}

	public function forget()
    {
    	html()->addCss();
		html()->addJs();
		$this->assign('_title', 'Forget Password');
		$this->view();
    }
}