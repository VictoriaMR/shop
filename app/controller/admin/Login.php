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
		frame('Html')->addJs('common,verify', false);
		frame('Html')->addCss('common,space');
		frame('Html')->addCss();
		frame('Html')->addJs();

		$this->view([
			'_title' => '后台登录',
		]);
	}

	public function loginCode()
	{
		$image = service('tool/Image');
		$code = randString(4, true, false, true);
		frame('Session')->set('admin_login_code', $code);
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
		if (strtolower($code) != frame('Session')->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$result = service('member/Member')->login($mobile, $password);
		if ($result) {
			$this->success('登录成功', ['url' => frame('Session')->dGet('return_url')]);
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
		if (strtolower($code) != frame('Session')->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$this->success('验证码正确!');
	}

	public function logout()
	{
		$log = service('member/Member')->logout();
		redirect(url('login'), false);
	}

	public function signature()
	{
		service('tool/Image')->text(ROOT_PATH.'template'.DS.'admin/image/computer/signature.png', frame('Session')->get('admin_info', '', 'nickname'), 12, 30, 10, 80, [235, 235, 235]);
	}
}