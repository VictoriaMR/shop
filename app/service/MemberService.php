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
		$data['password'] = $this->getPassword($data['password']);
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
		if ($this->checkPassword($password, $info['password'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'nickname' => $info['nickname'],
				'avatar' => $info['avatar'],
				'mobile' => $info['mobile'],
				'email' => $info['email'],
			];
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

	protected function checkPassword($inPassword, $sourcePassword)
	{
		return password_verify($inPassword, $sourcePassword);
	}

	protected function getPassword($password)
	{
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
}