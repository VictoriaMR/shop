<?php 

namespace app\service;
use app\service\Base;

class MemberService extends Base
{	
	protected $login_key;
	protected function getModel(){}

	public function create($data)
	{
		$data['salt'] = randString(8);
		$data['password'] = $this->getPassword($data['password'], $data['salt']);
		$data['add_time'] = now();
		return $this->insertGetId($data);
	}

	public function login($mobile, $password, $type='mobile')
	{
		if (empty($mobile) || empty($password)) return false;
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
		//验证密码
		if ($this->checkPassword($password, $info['password'], $info['salt'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'nickname' => $info['nickname'],
				'avatar' => $this->getAvatar($info['avatar'], $info['sex']),
				'mobile' => $info['mobile'],
				'email' => $info['email'],
				'sex' => $info['sex'] ?? 0,
			];
			session()->set($this->login_key.'_info', $data);
			$data = [
				'remark' => '登录管理后台',
				'type_id' => 0,
			];
			$this->updateData($info['mem_id'], ['login_time'=>now()]);
			$this->addLog($data);
			return true;
		}
		return false;
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