<?php 

namespace app\service\email;
use app\service\Base;

class Used extends Base
{
	public function getSiteAccountId()
	{
		return $this->loadData(['site_id'=>siteId()], 'site_id,account_id');
	}
}