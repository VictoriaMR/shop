<?php

namespace app\controller\admin;
use app\controller\Base;

class Login extends Base
{
	public function index()
	{	
		if (userId()) {
			redirect(url());
		}
		html()->addCss();
		html()->addJs();
		$this->assign('_title', '登录');
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
		$phone = trim(ipost('phone', ''));
		$code = trim(ipost('code', ''));
		$password = trim(ipost('password', ''));

		if (empty($phone) || empty($code) || empty($password)) {
			$this->error('参数错误');
		}
		if (strtolower($code) != session()->get('admin_login_code')) {
			$this->error('验证码错误');
		}
		$member = make('app/service/admin/Member');
		$result = $member->login($phone, $password);
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
		$log = make('app\service\admin\Logger');
		$log->addLog(['type' => $log->getConst('TYPE_LOGOUT')]);
		session()->set(APP_TEMPLATE_TYPE.'_info');
		redirect(url('login'));
	}

	public function signature()
    {
    	$info = session()->get('admin_info');
    	if (empty($info)) {
    		return '';
    	}
        make('app/service/Image')->text(ROOT_PATH.'admin/image/computer/signature.png', $info['name'], 12, 30, 10, 80, [235, 235, 235]);
    }
}