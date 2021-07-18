<?php 

namespace app\service\admin;
use app\service\MemberService as Base;

class MemberService extends Base
{	
	protected $login_key = 'admin';

	public function __construct()
	{
		$this->baseModel = make('app/model/admin/Member');
	}

	public function getMemIdsByName($name)
	{
		return array_column($this->baseModel->getListData(['name'=>$name], 'mem_id', 0), 'mem_id');
	}

	public function getMemIdsByMobile($mobile)
	{
		return array_column($this->baseModel->getListData(['mobile'=>$mobile], 'mem_id', 0), 'mem_id');
	}
}