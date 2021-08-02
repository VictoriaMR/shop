<?php 

namespace app\service;
use app\service\Base;

class MemberService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Member');
	}

	public function create($data)
	{
		$data['salt'] = randString(8);
		$data['password'] = $this->getPassword($data['password'], $data['salt']);
		$data['add_time'] = now();
		return $this->insertGetId($data);
	}

	public function login($mobile, $password='', $type='mobile')
	{
		if (empty($mobile)) return false;
		switch($type) {
			case 'mobile':
				$info = $this->loadData(['mobile'=>$mobile]);
				break;
			case 'email':
				$info = $this->loadData(['email'=>$mobile]);
				break;
		}
		if (empty($info)) return false;
		if (empty($info['status'])) return false;
		if (!empty($password) && !$this->checkPassword($password, $info['password'], $info['salt'])){
			return false;
		}
		return $this->loginSuccess($info,$type == 'email');
	}

	protected function loginSuccess($info, $keepLogin=false)
	{
		$data = [
			'mem_id' => $info['mem_id'],
			'name' => $info['name'],
			'nickname' => $info['nickname'],
			'avatar' => $this->getAvatar($info['avatar'], $info['sex']),
			'mobile' => $info['mobile'],
			'email' => $info['email'],
			'sex' => $info['sex'] ?? 0,
		];
		session()->set(APP_TEMPLATE_TYPE.'_info', $data);
		$this->updateData($info['mem_id'], ['login_time'=>now()]);
		$this->addLog(['type'=>0]);
		if ($keepLogin) {
			$tokenCacheKey = $this->getCacheKey('', $info['mem_id']);
			$token = redis(2)->get($tokenCacheKey);
			if ($token) {
				redis(2)->expire($tokenCacheKey, $this->getConst('TOKEN_CACHE_TIMEOUT'));
				redis(2)->expire($this->getCacheKey($token), $this->getConst('TOKEN_CACHE_TIMEOUT'));
			} else {
				$token = randString(32);
				redis(2)->set($tokenCacheKey, $token, $this->getConst('TOKEN_CACHE_TIMEOUT'));
				redis(2)->set($this->getCacheKey($token), $info['mem_id'], $this->getConst('TOKEN_CACHE_TIMEOUT'));
			}
		}
		return $token ?? true;
	}

	protected function getCacheKey($token='', $memId='')
	{
		return 'login-token:'.siteId().':'.$token.$memId;
	}

	public function loginByToken($token)
	{
		$tokenCacheKey = $this->getCacheKey($token);
		$memId = redis(2)->get($tokenCacheKey);
		if (empty($memId)) {
			return false;
		}
		$info = $this->loadData($memId);
		if (empty($info)) return false;
		if (empty($info['status'])) return false;
		return $this->loginSuccess($info);
	}

	protected function checkPassword($inPassword, $sourcePassword, $salt='')
	{
		return password_verify($this->saltPassword($inPassword, $salt), $sourcePassword);
	}

	protected function getPassword($password, $salt)
	{
		return password_hash($this->saltPassword($password, $salt), PASSWORD_DEFAULT);
	}

	protected function saltPassword($password, $salt)
	{
		if (empty($salt)) return $password;
		$slen = strlen($salt);
		$plen = strlen($password);
		$rePassword = '';
		if ($plen > $slen) {
			$split = (int)($plen/$slen);
			for($i=0; $i<$plen; $i++){
				$rePassword .= $password[$i].($salt[$i] ?? '');
			}
		} elseif ($plen < $slen){
			$split = (int)($slen/$plen);
			for($i=0; $i<$slen; $i++){
				$rePassword .= ($password[$i] ?? '').$salt[$i];
			}
		} else {
			for($i=0; $i<$plen; $i++){
				$rePassword .= $password[$i].$salt[$i];
			}
		}
		return $rePassword;
	}

	public function getAvatar($avatar='', $sex=0)
	{
		if (empty($avatar)) {
			return siteUrl('image/common/'.(empty($sex) ? 'female' : 'male').'.jpg');
		} else {
			return mediaUrl($avatar);
		}
	}
}