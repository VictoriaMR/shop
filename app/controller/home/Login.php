<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Login extends HomeBase
{
	public function index()
	{	
		if (isAjax()) {
			$email = ipost('email');
			$code = ipost('verify_code');
			$password = ipost('password');
			$cpassword = ipost('confirm_password');
			if (!$this->verify($email, 'email')) {
				$this->error(distT('email_invalid'));
			}
			if (empty($code) && empty($password)) {
				$this->error(distT('login_invalid'));
			}
			if ($code && !$this->verify($code, 'code')) {
				$this->error(distT('code_invalid'));
			}
			if ($password) {
				if ($password != $cpassword) {
					$this->error(distT('confirm_password_invalid'));
				}
				if (!$this->verify($password, 'password')) {
					$this->error(distT('password_invalid'));
				}
			}
			// 登录限制
			$limitKey = $this->getCacheKey(frame('IP')->getIp(), 'limit');
			$max = (int)redis(2)->get($limitKey);
			if ($max > 10) {
				$this->error(distT('login_limit_10'));
			}
			$ttl = redis(2)->ttl($limitKey);
			if ($ttl <= 0) {
				$ttl = 60*10;
			}
			redis(2)->set($limitKey, $max+1, $ttl);
			if ($code) {
				$codeCache = redis(2)->get($this->getCacheKey($email, 'code'));
				if ($code != $codeCache) {
					$this->error(distT('code_invalid'));
				}
			} else {
				$rst = service('member/Member')->loginByPassword($email, '', 'email');
			}
			if ($code == $verifyCode) {
				if ($rst) {
					frame('Session')->del('login_email');
					frame('Session')->del('login_exp_time');
					$this->success(distT('login_success'));
				}
			}
			$this->error(distT('verify_error'));
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view([
			'login_email' => frame('Session')->get('login_email'),
			'login_exp_time' => frame('Session')->get('login_exp_time'),
			'_title' => appT('login'),
		]);
	}

	public function forget()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->view([
			'_title' => distT('forget_password'),
		]);
	}

	public function sengCode()
	{
		$email = ipost('email');
		if (!$this->verify($email, 'email')) {
			$this->error(distT('email_invalid'));
		}
		$member = service('member/Member');
		$where = [
			'site_id' => siteId(),
			'email' => $email,
			'status' => 1,
		];
		$info = $member->loadData($where, 'mem_id');
		if (empty($info)) {
			$this->error(distT('account_not_exist'));
		}
		$cacheKey = $this->getCacheKey($email, 'code');
		$ttl = redis(2)->TTL($cacheKey);
		if ($ttl < -1) {
			$code = randString(6, false, false);
			$ttl = 120;
			$service = service('email/Email');
			$rst = $service->sendEmail($info['mem_id'], $code, $service->getConst('TYPE_LOGIN_SEND_CODE'));
			if ($rst) {
				redis(2)->set($cacheKey, $code, $ttl);
			} else {
				$this->error(distT('send_code_error', ['{email}'=>$email]));
			}
		}
		$this->success(distT('send_code_success', ['{email}'=>$email]), $ttl);
	}

	public function passwordVerify()
	{
		$email = ipost('email');
		if (empty($email)) {
			$this->error('Sorry, That email was Empty, Please try again.');
		}
		$member = service('member/Member');
		$where = [
			'site_id' => siteId(),
			'email' => $email,
			'status' => 1,
		];
		$memId = $member->loadData($where, 'mem_id')['mem_id'] ?? 0;
		if (empty($memId)) {
			$this->error('Sorry, we couldn\'t find an account matching that email.');
		}
		$cacheKey = $this->getCacheKey($email, 'limit', 'password-verify');
		if (!redis(2)->exists($cacheKey)) {
			$code = randString(32);
			$service = service('email/Email');
			$rst = $service->sendEmail($memId, $code, $service->getConst('TYPE_PASSWORD_RESET'));
			if ($rst) {
				redis(2)->set($cacheKey, 1, 120);
				redis(2)->set($this->getCacheKey($code, 'except', 'password-verify'), $email, 3600*36);
			} else {
				$this->error('Sorry, we couldn\'t sent to “'.$email.'”, Please try again later.');
			}
		}
		$this->success('Password reset\'s link has been sent to “'.$email.'”, Please check your email.');
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
		$this->assign('token', $token);
		$this->assign('_title', 'Reset Password');
		$this->view();
	}

	protected function getCacheKey($email, $type='limit', $code='login')
	{
		return siteId().':'.$code.':'.$type.':'.md5($email);
	}

	public function login()
	{
		$param = ipost();
		$error = [];
		if (empty($param['email'])) {
			$error['email'] = 'This Email is required.';
		}
		if (empty($param['password']) && empty($param['verify_code'])) {
			$error['verify_code'] = 'This Verification code is required.';
		} elseif (isset($param['password']) && empty($param['password'])) {
			$error['password'] = 'This Password is required.';
		} elseif (isset($param['verify_code']) && empty($param['verify_code'])) {
			$error['verify_code'] = 'This Verification code is required.';
		}
		if (!empty($error)) {
			$this->error($error);
		}
		if (empty($param['password'])) {
			$code = redis(2)->get($this->getCacheKey($param['email'], 'except'));
			if ($code == $param['verify_code']) {
				$rst = service('member/Member')->login($param['email'], '', 'email');
			} else {
				$this->error(['verify_code' => 'This Verification code was not match.']);
			}
		} else {
			$rst = service('member/Member')->login($param['email'], $param['password'], 'email');
		}
		if (empty($rst)) {
			$this->error('Sorry, login failed, Please try again.');
		}
		redis(2)->del($this->getCacheKey($param['email']));
		redis(2)->del($this->getCacheKey($param['email'], 'except'));
		$this->success(['url'=>frame('Session')->get('callback_url')]);
	}

	public function loginToken()
	{
		$token = ipost('token');
		if (empty($token)) {
			$this->error('login token empty');
		}
		$member = service('member/Member');
		$rst = service('member/Member')->loginByToken($token);
		if ($rst) {
			$this->success(['url'=>frame('Session')->get('callback_url')]);
		} else {
			$this->error('login error');
		}
	}

	public function logout()
	{
		service('member/Member')->logout();
		redirect(url('login'));
	}

	public function emailVerify()
	{
		$this->view();
	}

	public function checkRegister()
	{
		$email = ipost('email');
		if (empty($email)) {
			$this->error('This Email is required.');
		}
		$where = [
			'site_id' => siteId(),
			'email' => $email,
		];
		$rst = service('member/Member')->getCountData($where);
		if ($rst) {
			$this->error('This Email has been register.');
		} else {
			$this->success('This Email is effective.');
		}
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

	public function verify($info, $key)
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