<?php 

namespace app\service\member;
use app\service\Base;

class Collect extends Base
{
	public function collectProduct($spuId)
	{
		if (!$this->userId()) {
			return false;
		}
		$where = [
			'mem_id' => $this->userId(),
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
		if (!$this->userId()) {
			return false;
		}
		$where = [
			'mem_id' => $this->userId(),
			'spu_id' => $spuId,
		];
		return $this->getCountData($where) > 0;
	}
}