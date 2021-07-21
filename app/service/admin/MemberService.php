<?php 

namespace app\service\admin;
use app\service\MemberService as Base;

class MemberService extends Base
{	
	protected $login_key = 'admin';

	protected function getModel()
	{
		$this->baseModel = make('app/model/admin/Member');
	}

	public function getMemIdsByName($name)
	{
		return array_column($this->getListData(['name'=>$name], 'mem_id', 0), 'mem_id');
	}

	public function getMemIdsByMobile($mobile)
	{
		return array_column($this->getListData(['mobile'=>$mobile], 'mem_id', 0), 'mem_id');
	}
}