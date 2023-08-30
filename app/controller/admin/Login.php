<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Login extends AdminBase
{
	public function __construct()
	{
		$this->_base_js = [];
		$this->_base_css = [];
		parent::_init();
	}

	public function index()
	{	
		dd(purchase()->product()->addUrl('https://detail.1688.com/offer/663595394231.html?spm=a262cb.19918180.ljxo5qvc.2.1e187c9cZRcK0r&sk=processing&scm=1007.45324.348050.0&pvid=fd05b9f1-d019-4586-a0f8-9cdee5cdf4bc'));

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
		$image = make('app/service/Image');
		$code = randString(4, true, false, true);
		session()->set('admin_login_code', $code);
		$image->verifyCode($code, 80, 34);
	}

	public function login() 
	{
		dd($_SERVER);
		$mobile = trim(ipost('mobile', ''));
		$code = trim(ipost('code', ''));
		$password = trim(ipost('password', ''));

		if (empty($mobile) || empty($code) || empty($password)) {
			$this->error('参数错误');
		}
		if (strtolower($code) != session()->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$result = make('app/service/admin/Member')->login($mobile, $password);
		if ($result) {
			$this->success(['url' => url('index')], '登录成功!');
		} else {
			$log = make('app\service\admin\Logger');
			$log->addLog(['type' => $log->getConst('TYPE_LOGIN_FAIL')]);
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
		$log = make('app\service\admin\Member')->logout();
		redirect(url('login'));
	}

	public function signature()
	{
		$info = session()->get('admin_info');
		if (empty($info)) return '';
		make('app/service/Image')->text(ROOT_PATH.'template'.DS.'admin/image/computer/signature.png', $info['name'], 12, 30, 10, 80, [235, 235, 235]);
	}
}