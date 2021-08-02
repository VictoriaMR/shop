<?php

namespace app\controller\bag;
use app\controller\Controller;

class LoginController extends Controller
{
	public function index()
	{	
		if (userId()) {
			redirect();
		}
		$email = iget('email');
		$verifyCode = iget('verify_code');
		if (!empty($email) && !empty($verifyCode)) {
			$code = redis(2)->get($this->getCacheKey($email, 'except'));
			if ($code == $verifyCode) {
				$rst = make('app/service/MemberService')->login($email, '', 'email');
				if ($rst) {
					redis(2)->del($this->getCacheKey($email));
					redis(2)->del($this->getCacheKey($email, 'except'));
					redirect();
				}
			} else {
				$this->assign('error', 'This Verification code was not match.');
			}
		}
		html()->addCss();
		html()->addJs();
		$this->assign('_title', 'login');
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
		$memId = $memberService->loadData($where, 'mem_id')['mem_id'] ?? 0;
		if (empty($memId)) {
			$this->error('Sorry, we couldn\'t find an account matching that email.');
		}
		$cacheKey = $this->getCacheKey($email);
		$ttl = redis(2)->TTL($cacheKey);
		if ($ttl < -1) {
			$code = randString(6, false, false);
			$ttl = 120;
			//todo send email
			$rst = make('app/service/email/EmailService')->sendLoginCode($memId, $code);
			if ($rst) {
				redis(2)->set($cacheKey, 1, $ttl);
				redis(2)->set($this->getCacheKey($email, 'except'), $code, 600);
			} else {
				$this->error('Sorry, we couldn\'t sent to “'.$email.'”, Please select another way to login.');
			}
		}
		$this->success($ttl, 'Verification code has been sent to “'.$email.'”, Please check your email.');
	}

	protected function getCacheKey($email, $type='limit')
	{
		return 'login-code-'.siteId().':'.$type.':'.md5($email);
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
				$rst = make('app/service/MemberService')->login($param['email'], '', 'email');
			} else {
				$this->error(['verify_code' => 'This Verification code was not match.']);
			}
		} else {
			$rst = make('app/service/MemberService')->login($param['email'], $param['password'], 'email');
		}
		if (empty($rst)) {
			$this->error('Sorry, login failed, Please try agin.');
		}
		redis(2)->del($this->getCacheKey($param['email']));
		redis(2)->del($this->getCacheKey($param['email'], 'except'));
		$this->success(['token'=>$rst, 'url'=>session()->get('callback_url')]);
	}

	public function loginToken()
	{
		$token = ipost('token');
		if (empty($token)) {
			return false;
		}
		$memberService = make('app/service/MemberService');
		$rst = make('app/service/MemberService')->loginByToken($token);
		if ($rst) {
			$this->success(['url'=>session()->get('callback_url')]);
		} else {
			$this->error('login error');
		}
	}

	public function logout()
	{
		$logService = make('app\service\LoggerService');
		$logService->addLog(['type' => $logService->getConst('TYPE_LOGOUT')]);
		session()->set(APP_TEMPLATE_TYPE.'_info');
		redirect(url('login'));
	}
}