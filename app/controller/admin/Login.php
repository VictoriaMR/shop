<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Login extends AdminBase
{
	public function __construct()
	{
		parent::_init();
	}

	public function index()
	{	
		html()->addJs('common', false);
		html()->addJs('verify', false);
		html()->addCss('common');
		html()->addCss('space');
		html()->addCss();
		html()->addJs();
		$this->assign('_title', '后台登录');
		$this->view();
	}

	public function loginCode()
	{
		$image = service('Image');
		$code = randString(4, true, false, true);
		session()->set('admin_login_code', $code);
		$image->verifyCode($code, 80, 34);
	}

	public function login() 
	{
		$mobile = trim(ipost('mobile', ''));
		$code = trim(ipost('code', ''));
		$password = trim(ipost('password', ''));

		if (empty($mobile) || empty($code) || empty($password)) {
			$this->error('参数错误');
		}
		if (strtolower($code) != session()->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$result = service('Member')->login($mobile, $password);
		if ($result) {
			$this->success(['url' => url('index')], '登录成功!');
		} else {
			$this->error('账号或者密码不匹配!');
		}
	}

	public function checkCode()
	{
		$code = ipost('code', '');
		if (empty($code)) {
			$this->error('验证码错误!');
		}
		if (strtolower($code) != session()->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$this->success('验证码正确!');
	}

	public function logout()
	{
		$log = make('app\service\Member')->logout();
		redirect(url('login'));
	}

	public function signature()
	{
		service('Image')->text(ROOT_PATH.'template'.DS.'admin/image/computer/signature.png', session()->get(type().'_info', '', 'nickname'), 12, 30, 10, 80, [235, 235, 235]);
	}
}