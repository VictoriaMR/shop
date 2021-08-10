<?php 

namespace app\service\member;
use app\service\Base;

class HistoryService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/History');
	}

	public function addHistory($spuId)
	{
		if (!$this->userId()) {
			return false;
		}
		$where = [
			'mem_id' => $this->userId(),
			'spu_id' => $spuId,
			'add_date' => date('Y-m-d'),
		];
		if ($this->getCountData($where)) {
			return true;
		}
		$where['add_time'] = now();
		return $this->insert($where);
	}
}