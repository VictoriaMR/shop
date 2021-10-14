<?php 

namespace app\service;
use app\service\Base;

class Member extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Member');
	}

	public function create($data)
	{
		$data['salt'] = randString(4);
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
		session()->set(APP_TEMPLATE_TYPE.'_info');
		make('frame/Cookie')->clear();
		$this->addLog(['type'=>1]);
		return true;
	}

	protected function loginSuccess($info)
	{
		$data = [
			'mem_id' => $info['mem_id'],
			'name' => $info['name'] ?? '',
			'nickname' => $info['nickname'] ?? '',
			'first_name' => $info['first_name'] ?? '',
			'last_name' => $info['last_name'] ?? '',
			'avatar' => $this->getAvatar($info['avatar'], $info['sex']),
			'mobile' => $info['mobile'],
			'email' => $info['email'],
			'sex' => $info['sex'] ?? 0,
		];
		session()->set(APP_TEMPLATE_TYPE.'_info', $data);
		$this->updateData($info['mem_id'], ['login_time'=>now()]);
		$this->addLog(['type'=>0]);
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
		if (empty($avatar)) {
			return siteUrl('image/common/'.(empty($sex) ? 'female' : 'male').'.jpg');
		} else {
			return mediaUrl($avatar);
		}
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