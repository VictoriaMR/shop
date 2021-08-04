<?php 

namespace app\service\product;
use app\service\Base;

class IntroduceService extends Base
{
	public function getModel()
	{
		$this->baseModel = make('app/model/product/Introduce');
	}

	public function addIntroduceImage(array $data)
	{
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($this->getCountData($value)) {
					unset($data[$key]);
				}
			}
		}
		return $this->insert($data);
	}
}