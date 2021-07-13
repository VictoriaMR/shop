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
}