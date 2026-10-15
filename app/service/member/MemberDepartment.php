<?php

namespace app\service\member;
use app\service\Base;

class MemberDepartment extends Base
{
	/**
	 * 绑定或更新人员的部门与职位关联
	 */
	public function bindMember(int $memId, int $deptId, int $posId): bool
	{
		if ($memId <= 0) {
			return false;
		}
		$deptId = max(0, $deptId);
		$posId = max(0, $posId);

		$exist = $this->loadData(['mem_id' => $memId], 'item_id');
		if ($deptId === 0 && $posId === 0) {
			if (!empty($exist)) {
				$this->deleteData((int)$exist['item_id']);
			}
			return true;
		}

		if (!empty($exist)) {
			$this->updateData((int)$exist['item_id'], [
				'dept_id'     => $deptId,
				'pos_id'      => $posId,
				'update_time' => now(),
			]);
		} else {
			$this->insertData([
				'mem_id'  => $memId,
				'dept_id' => $deptId,
				'pos_id'  => $posId,
			]);
		}
		return true;
	}

	/**
	 * 获取单个人员的部门与职位关联信息
	 */
	public function getByMemId(int $memId): array
	{
		if ($memId <= 0) {
			return [];
		}
		$row = $this->loadData(['mem_id' => $memId], 'mem_id,dept_id,pos_id');
		return is_array($row) ? $row : [];
	}

	/**
	 * 批量获取指定人员ID集合的部门与职位映射 (消除 N+1 查询)
	 */
	public function getMapByMemIds(array $memIds): array
	{
		$memIds = array_values(array_unique(array_filter(array_map('intval', $memIds))));
		if (empty($memIds)) {
			return [];
		}
		$where = [
			'mem_id' => count($memIds) === 1 ? $memIds[0] : ['in', $memIds],
		];
		$rows = $this->getListData($where, 'mem_id,dept_id,pos_id', 0, 0);
		if (empty($rows)) {
			return [];
		}
		return array_column($rows, null, 'mem_id');
	}

	/**
	 * 统计各部门关联的人数 [dept_id => count]
	 */
	public function getDeptMemberCountMap(): array
	{
		$rows = $this->getListData(['dept_id' => ['>', 0]], 'dept_id,COUNT(*) as total', 0, 0, [], 'dept_id');
		if (empty($rows)) {
			return [];
		}
		return array_column($rows, 'total', 'dept_id');
	}

	/**
	 * 统计各职位关联的人数 [pos_id => count]
	 */
	public function getPosMemberCountMap(): array
	{
		$rows = $this->getListData(['pos_id' => ['>', 0]], 'pos_id,COUNT(*) as total', 0, 0, [], 'pos_id');
		if (empty($rows)) {
			return [];
		}
		return array_column($rows, 'total', 'pos_id');
	}
}
