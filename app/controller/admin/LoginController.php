<?php

namespace app\controller\admin;

use app\controller\Controller;

class LoginController extends Controller
{
	public function index()
	{	
		// make('frame/Task')->start();
		// dd();
		html()->addCss();
		html()->addJs();
		session()->set('admin', []);
		$this->assign('_title', '登录');
		$this->view();
	}

	public function loginCode()
	{
		$imageService = make('app/service/ImageService');
		$code = make('app/service/Base')->getSalt();
		session()->set('admin_login_code', $code);
		$imageService->verifyCode($code, 80, 34);
	}

	public function login() 
	{
		$phone = trim(ipost('phone', ''));
		$code = trim(ipost('code', ''));
		$password = trim(ipost('password', ''));

		if (empty($phone) || empty($code) || empty($password)) {
			return $this->result(10000, [], ['message' => '输入错误!']);
		}
		if (strtolower($code) != strtolower(Session::get('admin_login_code'))) {
			return $this->result(10000, [], ['message' => '验证码错误!']);
		}
		$memberService = make('App/Services/Admin/MemberService');
		$result = $memberService->login($phone, $password, 'admin');

		if ($result) {
	        $logService = make('app/service/admin/LogService');
			$data = [
	            'mem_id' => Session::get('admin_mem_id'),
	            'remark' => '登录管理后台',
	            'type_id' => $logService::constant('TYPE_LOGIN'),
	        ];
	        $logService->addLog($data);
			$this->result(200, ['url' => url('index')], ['message' => '登录成功!']);
		} else {
			$this->result(10000, $result, ['message' => '账号或者密码不匹配!']);
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