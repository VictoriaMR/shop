<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Login extends HomeBase
{
	public function index()
	{	
		if (isAjax()) {
			$email = ipost('email');
			$verifyCode = ipost('verify_code');
			if (!verify($email, 'email')) {
				$this->error(distT('email_error'));
			}
			if (!verify($verifyCode, 'code')) {
				$this->error(distT('code_error'));
			}
			$code = redis(2)->get($this->getCacheKey($email, 'except'));
			if ($code == $verifyCode) {
				$rst = service('member/Member')->login($email, '', 'email');
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
		$cacheKey = $this->getCacheKey($email);
		$ttl = redis(2)->TTL($cacheKey);
		if ($ttl < -1) {
			$code = randString(6, false, false);
			$ttl = 120;
			$service = service('email/Email');
			$rst = $service->sendEmail($memId, $code, $service->getConst('TYPE_LOGIN_SEND_CODE'));
			if ($rst) {
				redis(2)->set($cacheKey, 1, $ttl);
				redis(2)->set($this->getCacheKey($email, 'except'), $code, 600);
			} else {
				$this->error('Sorry, we couldn\'t sent to “'.$email.'”, Please select another way to login.');
			}
		}
		$this->success($ttl, 'Verification code has been sent to “'.$email.'”, Please check your email.');
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

	protected function getCacheKey($email, $type='limit', $code='login-code')
	{
		return $code.'-'.siteId().':'.$type.':'.md5($email);
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
		$error = [];
		if (empty($email)) {
			$error['email'] = 'This Email is required.';
		}
		if (empty($password)) {
			$error['password'] = 'This Password is required.';
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
			$this->error(['email' => 'This Email has been register.']);
		}
		$where['password'] = $password;
		$where['status'] = 1;
		$rst = $service->create($where);
		if ($rst) {
			$rst = $service->login($email, $password, 'email');
			if ($rst) {
				$this->success(['token'=>$rst, 'url'=>frame('Session')->get('callback_url')], 'The "'.$email.'" register success, have a happy shopping day');
			}
		}
		$this->error(['alter' => 'Sorry, This Email register failed, Please try agin.']);
	}
}