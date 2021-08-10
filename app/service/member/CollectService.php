<?php 

namespace app\service\member;
use app\service\Base;

class CollectService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/Collect');
	}

	public function collectProduct($spuId)
	{
		$memId = userId();
		if (empty($memId)) {
			return false;
		}
		$where = [
			'mem_id' => $memId,
			'spu_id' => $spuId,
		];
		if ($this->getCountData($where)) {
			$this->deleteData($where);
			return 2;
		} else {
			$where['add_time'] = now();
			$this->insert($where);
			return 1;
		}
	}

	public function isCollect($spuId)
	{
		$memId = userId();
		if (empty($memId)) {
			return false;
		}
		$where = [
			'mem_id' => $memId,
			'spu_id' => $spuId,
		];
		return $this->getCountData($where) > 0;
	}
}