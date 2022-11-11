<?php 

namespace app\service\supplier;
use app\service\Base;

class Image extends Base
{
	protected $_model = 'app/model/supplier/Image';

	public function addUrl($url)
	{
		$data = ['url'=>$url];
		if ($this->getCountData($data)) return true;
		return $this->insert($data);
	}
}