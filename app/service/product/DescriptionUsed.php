<?php 

namespace app\service\product;
use app\service\Base;

class DescriptionUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/DescriptionUsed');
	}

	public function addDescUsed(array $insert)
	{
		if (empty($insert)) {
			return false;
		}
		if (!empty($insert[0]) && is_array($insert[0])) {
			foreach ($insert as $key => $value) {
				if ($this->getCountData($value)) {
					unset($insert[$key]);
				}
			}
		}
		return $this->insert($insert);
	}
}