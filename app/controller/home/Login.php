<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Login extends HomeBase
{
	public function index()
	{	
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view([
			'_title' => distT('login'),
		]);
	}

	public function forgot()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view([
			'_title' => distT('forget_password'),
		]);
	}

	public function sendForgotEmail()
	{
		$email = ipost('email');
		if (!$this->verify($email, 'email')) {
			$this->error(['email'=>distT('email_invalid')]);
		}
		// 登录限制
		if (!$this->limit()) {
			$this->error(distT('login_limit_10'));
		}
		$member = service('member/Member');
		$where = [
			'email' => $email,
			'status' => 1,
		];
		$info = $member->loadData($where, 'mem_id');
		if (empty($info)) {
			$this->error(['email'=>distT('account_not_exist')]);
		}
		$service = service('email/Email');
		$token = randString(32);
		$rst = $service->sendEmail($info['mem_id'], $token, $service->getConst('TYPE_PASSWORD_RESET'), 24*60*60);
		if ($rst) {
			$this->success(distT('send_email_success', ['{email}'=>$email]));
		} else {
			$this->error(distT('send_email_error', ['{email}'=>$email]));
		}
	}

	protected function limit()
	{
		$limitKey = $this->getCacheKey(frame('IP')->getIp(), 'limit');
		$max = (int)redis(2)->get($limitKey);
		if ($max > 10) {
			return false;
		}
		$ttl = redis(2)->ttl($limitKey);
		if ($ttl <= 0) {
			$ttl = 60*10;
		}
		redis(2)->set($limitKey, $max+1, $ttl);
		return true;
	}

	public function sengCode()
	{
		$email = ipost('email');
		if (!$this->verify($email, 'email')) {
			$this->error(distT('email_invalid'));
		}
		// 登录限制
		if (!$this->limit()) {
			$this->error(distT('login_limit_10'));
		}
		$member = service('member/Member');
		$where = [
			'email' => $email,
			'status' => 1,
		];
		$info = $member->loadData($where, 'mem_id');
		if (empty($info)) {
			$this->error(distT('account_not_exist'));
		}
		$cacheKey = $this->getCacheKey($email, 'send_code');
		$ttl = redis(2)->TTL($cacheKey);
		if ($ttl < -1) {
			$cacheKey2 = $this->getCacheKey($email, 'code');
			$count = count(redis(2)->keys($cacheKey2.'-*'));
			if ($count > 5) {
				$this->error(distT('send_code_error', ['{email}'=>$email]));
			}
			$code = randString(6, false, false);
			$service = service('email/Email');
			$rst = $service->sendEmail($info['mem_id'], $code, $service->getConst('TYPE_LOGIN_SEND_CODE'), 10*60);
			if ($rst) {
				$ttl = 2*60;
				redis(2)->set($cacheKey, 1, $ttl);
				redis(2)->set($cacheKey2.'-'.($count+1), $code, 10 * 60);
			} else {
				$this->error(distT('send_code_error', ['{email}'=>$email]));
			}
		}
		$this->success(distT('send_code_success', ['{email}'=>$email]), $ttl);
	}

	public function resetPassword()
	{
		$token = input('token');
		if (frame('Request')->isPost()) {
			$password = ipost('password');
			if (empty($token) || empty($password)) {
				$this->error('Token or Password was Empty, Please try again!');
			}
			$cacheKey = $this->getCacheKey($token, 'except', 'password-verify');
			$email = redis(2)->get($cacheKey);
			if (empty($email)) {
				$this->error('error', 'Token was invalid, Please check your email and find the right link!');
			}
			$rst = service('member/Member')->resetPassword($email, $password, 'email');
			if ($rst) {
				redis(2)->del($cacheKey);
				$this->success(['url'=>url('login')],'Password was reset success!');
			} else {
				$this->error('Password was reset success!');
			}
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		if (empty($token)) {
			$this->assign('error', 'Token was Empty, Please check your email and find the right link!');
		} else {
			$cacheKey = $this->getCacheKey($token, 'except', 'password-verify');
			if (!redis(2)->exists($cacheKey)) {
				$this->assign('error', 'Token was invalid, Please check your email and find the right link!');
			}
		}

		$this->view([
			'_title' => distT('reset_password'),
		]);
	}

	protected function getCacheKey($email, $type='limit', $code='login')
	{
		return siteId().':'.$code.':'.$type.':'.md5($email);
	}

	public function login()
	{
		$email = ipost('email');
		$code = ipost('verify_code');
		$password = ipost('password');
		$error = [];
		if (!$this->verify($email, 'email')) {
			$error['email'] = distT('email_invalid');
		}
		if ($code && !$this->verify($code, 'code')) {
			$error['verify_code'] = distT('code_invalid');
		}
		if ($password && !$this->verify($password, 'password')) {
			$error['password'] = distT('password_invalid');
		}
		if (!empty($error)) {
			$this->error($error);
		}
		// 登录限制
		if (!$this->limit()) {
			$this->error(distT('login_limit_10'));
		}
		// 账户数据
		$member = service('member/Member');
		$where = [
			'email' => $email,
			'status' => 1,
		];
		$info = $member->loadData($where, 'mem_id');
		if (empty($info)) {
			$this->error(['email' => distT('account_not_exist')]);
		}

		if ($code) {
			$cacheKey = $this->getCacheKey($email, 'code');
			$list = redis(2)->keys($cacheKey.'-*');
			if (empty($list)) {
				$this->error(['verify_code' => distT('code_not_match')]);
			}
			$list = array_reverse($list);
			$check = false;
			foreach ($list as $value) {
				if ($code == redis(2)->get($value)) {
					$check = true;
					break;
				}
			}
			if (!$check) {
				$this->error(['verify_code' => distT('code_not_match')]);
			}
			$rst = $member->login($email, '', 'email');
		} else {
			$rst = $member->login($email, $password, 'email');
		}
		if (!$rst) {
			$this->error(['verify_code' => distT('login_fail')]);
		}
		$this->success(distT('login_success'), ['url'=>frame('Session')->get('callback_url')]);
	}

	public function logout()
	{
		service('member/Member')->logout();
		redirect(url('login'));
	}

	public function register()
	{
		$email = ipost('email');
		$password = ipost('password');
		$cpassword = ipost('confirm_password');
		$error = [];
		if (!$this->verify($email, 'email')) {
			$error['email'] = distT('email_invalid');
		}
		if (!$this->verify($password, 'password')) {
			$error['password'] = distT('password_invalid');
		}
		if (!$this->verify($cpassword, 'password') || $password != $cpassword) {
			$error['confirm_password'] = distT('confirm_password_invalid');
		}
		if (!empty($error)) {
			$this->error($error);
		}
		// 登录限制
		if (!$this->limit()) {
			$this->error(distT('login_limit_10'));
		}
		$where = [
			'site_id' => siteId(),
			'email' => $email,
		];
		$service = service('member/Member');
		$rst = $service->getCountData($where);
		if ($rst) {
			$this->error(['email' => distT('email_registed')]);
		}
		$where['password'] = $password;
		$where['status'] = 1;
		$service->create($where);
		$rst = $service->login($email, $password, 'email');
		if ($rst) {
			$this->success(distT('register_success', ['{email}' => $email]), ['url'=>frame('Session')->get('callback_url')]);
		}
		$this->error(['email' => distT('register_error')]);
	}

	protected function verify($info, $key)
	{
		switch ($key) {
			case 'email':
				return preg_match('/^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/', $info);
			case 'mobile':
				return preg_match('/^1[3456789]\d{9}$/', $info);
			case 'password':
				return preg_match('/^[0-9A-Za-z]{6,}/', $info);
			case 'code':
				return preg_match('/^[0-9]{6}/', $info);
			default:
				return false;
		}
	}
}