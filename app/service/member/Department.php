<?php

namespace app\service\member;
use app\service\Base;

class Department extends Base
{
	/**
	 * 获取扁平化树形结构部门列表 (单次查询内存中组装树, 零递归SQL开销)
	 */
	public function getTreeList(array $where = []): array
	{
		$rows = $this->getListData($where, '*', 0, 0, ['sort' => 'asc', 'dept_id' => 'asc']);
		if (empty($rows)) {
			return [];
		}

		$childrenMap = [];
		$itemMap = [];
		foreach ($rows as $row) {
			$pid = (int)$row['parent_id'];
			$childrenMap[$pid][] = $row;
			$itemMap[(int)$row['dept_id']] = $row;
		}

		$result = [];
		$this->buildFlatTree($childrenMap, 0, 1, [], $result, $itemMap);
		return $result;
	}

	private function buildFlatTree(array &$childrenMap, int $parentId, int $level, array $pathNames, array &$result, array &$itemMap): void
	{
		if (empty($childrenMap[$parentId])) {
			return;
		}
		$siblings = array_values($childrenMap[$parentId]);
		$cnt = count($siblings);
		foreach ($siblings as $idx => $item) {
			$id = (int)$item['dept_id'];
			$subChildren = $childrenMap[$id] ?? [];
			$currentPath = array_merge($pathNames, [$item['name']]);

			$item['level']       = $level;
			$item['child_count'] = count($subChildren);
			$item['has_child']   = $item['child_count'] > 0;
			$item['is_first']    = ($idx === 0);
			$item['is_last']     = ($idx === $cnt - 1);
			$item['parent_name'] = $parentId > 0 ? ($itemMap[$parentId]['name'] ?? '') : '顶级部门';
			$item['path_name']   = implode(' / ', $currentPath);

			$result[] = $item;
			if (!empty($subChildren)) {
				$this->buildFlatTree($childrenMap, $id, $level + 1, $currentPath, $result, $itemMap);
			}
		}
	}

	/**
	 * 获取指定部门的所有下级子孙部门ID集合 (用于防循环挂载及按部门筛选下属人员)
	 */
	public function getDescendantIds(int $deptId): array
	{
		if ($deptId <= 0) {
			return [];
		}
		$rows = $this->getListData([], 'dept_id,parent_id', 0, 0);
		if (empty($rows)) {
			return [];
		}
		$childrenMap = [];
		foreach ($rows as $r) {
			$childrenMap[(int)$r['parent_id']][] = (int)$r['dept_id'];
		}
		$ids = [];
		$queue = [$deptId];
		while (!empty($queue)) {
			$curr = array_shift($queue);
			if (!empty($childrenMap[$curr])) {
				foreach ($childrenMap[$curr] as $cid) {
					if (!in_array($cid, $ids, true)) {
						$ids[] = $cid;
						$queue[] = $cid;
					}
				}
			}
		}
		return $ids;
	}

	/**
	 * 获取部门 ID => 信息 映射字典
	 */
	public function getMap(): array
	{
		$tree = $this->getTreeList();
		return array_column($tree, null, 'dept_id');
	}

	/**
	 * 获取部门 ID => 名称 映射字典
	 */
	public function getNameMap(): array
	{
		$rows = $this->getListData([], 'dept_id,name', 0, 0);
		return empty($rows) ? [] : array_column($rows, 'name', 'dept_id');
	}
}
