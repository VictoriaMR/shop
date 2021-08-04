<?php 

namespace app\service\product;
use app\service\Base;

class DescriptionService extends Base
{
	public function getModel()
	{
		$this->baseModel = make('app/model/product/Description');
	}

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
		$name = trim($name);
		$info = $this->loadData(['name'=>$name], 'desc_id');
		if (!empty($info)) {
			return $info['desc_id'];
		}
		return $this->insertGetId(['name'=>$name]);
	}
}