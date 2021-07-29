<?php

namespace app\controller\bag;
use app\controller\Controller;


class LoginController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		session()->set(APP_TEMPLATE_TYPE.'_info');
		$this->assign('_title', '登录');
		$this->view();
	}

	public function forget()
	{
		html()->addCss();
		html()->addJs();
		$this->assign('_title', 'Forget Password');
		$this->view();
	}

	public function sengCode()
	{
		$email = ipost('email');
		if (empty($email)) {
			$this->error('Sorry, That email was Empty, Please try agin.');
		}
		$memberService = make('app/service/MemberService');
		$where = [
			'site_id' => siteId(),
			'email' => $email,
			'status' => 1,
		];
		if (!$memberService->getCountData($where)) {
			$this->error('Sorry, we couldn\'t find an account matching that email.');
		}
		$cacheKey = 'login-code-'.siteId().':'.$email;
		$ttl = redis()->TTL($cacheKey);
		if ($ttl < -1) {
			$code = randString(6, false, false);
			$ttl = 120;
			//todo send email
			redis()->set($cacheKey, $code, $ttl);
		}
		$this->success($ttl, 'Verification code has been sent to “'.$email.'”, Please check your email.');
	}

	public function login()
	{
		$param = ipost();
		$code = ipost('verify_code');
		$passwd = ipost('password');
		$error = [];
		if (empty($param['email'])) {
			$error['email'] = 'This Email is required.';
		}
		if (isset($param['password']) && empty($param['password'])) {
			$error['password'] = 'This Password is required.';
		}
		if (isset($param['verify_code']) && empty($param['verify_code'])) {
			$error['verify_code'] = 'This Verification code is required.';
		}
		if (!empty($error)) {
			$this->error($error);
		}
	}
}