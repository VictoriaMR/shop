<?php 

namespace app\service\supplier;
use app\service\Base;

class Shop extends Base
{
	protected $_model = 'app/model/supplier/Shop';

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