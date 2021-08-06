<?php 

namespace app\service\member;
use app\service\Base;

class CollectService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/Collect');
	}
}