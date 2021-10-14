<?php 

namespace app\service\controller;
use app\service\Base;

class Controller extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/controller/Controller');
	}

	public function getList()
	{
		$list = $this->getListData([], '*', 0, 0, ['sort'=>'asc']);
		if (empty($list)) return [];
		return $this->listFormat($list);
	}

	protected function listFormat($list, $parentId=0) 
	{
		$returnData = [];
		foreach ($list as $value) {
			if ($value['parent_id'] == $parentId) {
				$temp = $this->listFormat($list, $value['con_id']);
				if (!empty($temp)) {
					$value['son'] = $temp;
				}
				$returnData[] = $value;
			}
		}
		return $returnData;
	}
}