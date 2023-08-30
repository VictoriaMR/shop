<?php 

namespace app\service\supplier;
use app\service\Base;

class Shop extends Base
{
	public function addNotExist(array $data)
	{
		if (empty($data['url'])) return false;
		$info = $this->loadData(['url'=>$data['url']], 'shop_id');
		if (empty($info)) {
			return $this->insertGetId($data);
		}
		return $info['shop_id'];
	}
}