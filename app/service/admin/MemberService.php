<?php 

namespace app\service\admin;

use app\service\MemberService as BaseService;

class MemberService extends BaseService
{	
	public function create($data)
	{
		if (empty($data['password'])) return false;

		$data['salt'] = $this->getSalt();

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);

		$data['status'] = 1;
		$data['create_at'] = now();

		return make('app/models/admin/Member')->insertGetId($data);
	}

	public function updateData($id, $data)
	{
		if (empty($id) || empty($data['password'])) return false;

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);

		$data['update_at'] = now();

		return make('app/models/admin/Member')->updateDataById($id, $data);
	}
}