<?php

namespace app\service\member;
use app\service\Base;

class Position extends Base
{
	/**
	 * 获取职位列表 (按 sort ASC, pos_id ASC 排序)
	 */
	public function getList(array $where = [], int $page = 0, int $size = 0): array
	{
		return $this->getListData($where, '*', $page, $size, ['sort' => 'asc', 'pos_id' => 'asc']);
	}

	/**
	 * 获取职位 ID => 信息 映射字典
	 */
	public function getMap(): array
	{
		$list = $this->getListData([], '*', 0, 0, ['sort' => 'asc', 'pos_id' => 'asc']);
		return empty($list) ? [] : array_column($list, null, 'pos_id');
	}

	/**
	 * 获取职位 ID => 名称 映射字典
	 */
	public function getNameMap(): array
	{
		$rows = $this->getListData([], 'pos_id,name', 0, 0);
		return empty($rows) ? [] : array_column($rows, 'name', 'pos_id');
	}
}
