<?php 

namespace app\service;
use app\service\Base;

/**
 * 	用户公共类
 */
class MemberService extends Base
{	
	public function create($data)
	{
		$data['salt'] = randString(8);
		$data['password'] = $this->getPassword($data['password'], $data['salt']);
		return $this->baseModel->insertGetId($data);
	}

	public function login($mobile, $password, $type='mobile')
	{
		if (empty($mobile) || empty($password)) return false;
		switch($type) {
			case 'mobile':
				$key = 'getInfoByMobile';
				break;
			case 'email':
				$key = 'getInfoByEmail';
				break;
			default:
				return false;
				break;
		}
		$info = $this->$key($mobile);
		if (empty($info)) return false;
		if (empty($info['status'])) return false;
		//验证密码
		if ($this->checkPassword($password, $info['password'], $info['salt'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'nickname' => $info['nickname'],
				'avatar' => $info['avatar'],
				'mobile' => $info['mobile'],
				'email' => $info['email'],
				'sex' => $info['sex'],
			];
			$data = $this->dataFormat($data);
			session()->set($this->login_key.'_info', $data);
			$data = [
				'mem_id' => $info['mem_id'],
				'remark' => '登录管理后台',
				'type_id' => 0,
			];
			$this->addLoginLog($data);
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
		$password = $this->saltPassword($password, $salt);
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public function isExistByMobile($mobile) 
	{
		return $this->getCountData(['mobile'=>$mobile]) > 0;
	}

	protected function getInfoByMobile($mobile)
	{
		return $this->loadData(['mobile'=>$mobile]);
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

	protected function dataFormat(array $data)
	{
		if (empty($data['avatar'])) {
			$data['avatar'] = $this->getAvatar($data['avatar'], $data['sex']);
		}
		return $data;
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