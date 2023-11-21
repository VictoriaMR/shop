<?php 

namespace app\service;
use app\service\Base;

class Member extends Base
{
	public function create($data)
	{
		$data['salt'] = randString(4);
		$data['password'] = $this->getPassword($data['password'], $data['salt']);
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
		return $this->loginSuccess($info);
	}

	public function loginById($memId)
	{
		$info = $this->loadData($memId);
		if (empty($info)) {
			return false;
		}
		return $this->loginSuccess($info);
	}

	public function logout()
	{
		$this->addLog(['type'=>1]);
		session()->set(type().'_info');
		make('frame/Cookie')->clear();
		return true;
	}

	protected function loginSuccess($info)
	{
		$except = ['status', 'password', 'salt', 'add_time', 'update_time', 'login_time'];
		foreach ($info as $key=>$value) {
			if (in_array($key, $except)) {
				unset($info[$key]);
			}
		}
		if (!empty($info['avatar'])) {
			$info['avatar'] = $this->getAvatar($info['avatar'], $info['sex']);
		}
		session()->set(type().'_info', $info);
		$this->updateData($info['mem_id'], ['login_time'=>now()]);
		$this->addLog();
		make('frame/Cookie')->login($info['mem_id']);
		return true;
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
		return $avatar ? mediaUrl($avatar, '', false) : siteUrl('image/common/'.(empty($sex) ? 'female' : 'male').'.jpg');
	}

	public function resetPassword($mobile, $password, $type='email')
	{
		if (empty($mobile)) return false;
		$where = [$type=>$mobile];
		$info = $this->loadData($where);
		if (empty($info)) return false;
		return $this->updateData($where, ['password'=>$this->getPassword($password, $info['salt']), 'update_time'=>now()]);
	}
}