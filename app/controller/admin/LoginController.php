<?php

namespace app\controller\admin;

use app\controller\Controller;

class LoginController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		session()->set('admin', []);
		$this->assign('_title', '登录');
		$this->view();
	}

	public function loginCode()
	{
		$imageService = make('app/service/ImageService');
		$code = randString(4, true, false, true);
		session()->set('admin_login_code', $code);
		$imageService->verifyCode($code, 80, 34);
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
		$memberService = make('app/service/admin/MemberService');
		$result = $memberService->login($phone, $password);
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
			return $this->result(10000, [], ['message' => '验证码格式错误!']);
		}
		if (strtolower($code) != strtolower(Session::get('admin_login_code'))) {
			return $this->result(10000, [], ['message' => '验证码错误!']);
		}
		$this->result(200, '', ['message' => '验证码正确!']);
	}

	public function logout()
	{
		$logService = \App::make('App\Services\Admin\LogService');
		$data = [
            'mem_id' => Session::get('admin_mem_id'),
            'remark' => '登出管理后台',
            'type_id' => $logService::constant('TYPE_LOGOUT'),
        ];
        $logService->addLog($data);
		Session::set('admin');
		redirect(url('login'));
	}

	public function signature()
    {
    	$text = !empty(Session::get('admin_name')) ? Session::get('admin_name') : '管理后台';
        make('App/Services/ImageService')->text(ROOT_PATH.'admin/image/computer/signature.png', $text, 12, 30, 10, 80, [235, 235, 235]);
        exit();
    }
}