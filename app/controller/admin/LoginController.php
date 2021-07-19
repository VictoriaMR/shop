<?php

namespace app\controller\admin;
use app\controller\Controller;

class LoginController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		session()->set('admin');
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
			$data = [
				'remark' => '登陆尝试失败',
				'type_id' => 2,
				'param' => json_encode(request()->input(), JSON_UNESCAPED_UNICODE),
			];
			$memberService->addLoginLog($data);
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
		$info = session()->get('admin_info');
		if (empty($info)) {
			$this->error('非登陆状态');
		}
		$logService = make('app\service\admin\LogService');
		$data = [
            'mem_id' => $info['mem_id'],
            'remark' => '登出管理后台',
            'type_id' => 1,
        ];
        $logService->addLog($data);
		session()->set('admin_info');
		redirect(url('login'));
	}

	public function signature()
    {
    	$info = session()->get('admin_info');
    	if (empty($info)) {
    		return '';
    	}
        make('app/service/ImageService')->text(ROOT_PATH.'admin/image/computer/signature.png', $info['name'], 12, 30, 10, 80, [235, 235, 235]);
    }
}