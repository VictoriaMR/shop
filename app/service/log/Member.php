<?php

namespace app\service\log;
use app\service\Base;

class Member extends Base
{
	const TYPE_MEMBER     = 0; // 人员/登录日志
	const TYPE_DEPARTMENT = 1; // 部门管理日志
	const TYPE_POSITION   = 2; // 职位管理日志

	/**
	 * 获取日志模块类型字典或名称
	 *
	 * @param int|null $type
	 * @return array|string
	 */
	public function getTypeList($type = null)
	{
		$list = [
			self::TYPE_MEMBER     => '人员管理',
			self::TYPE_DEPARTMENT => '部门管理',
			self::TYPE_POSITION   => '职位管理',
		];
		return $type === null ? $list : ($list[(int)$type] ?? '人员管理');
	}

	/**
	 * 记录管理人员/部门/职位操作日志
	 *
	 * @param string $info 日志内容
	 * @param int $type 模块类型(0:人员/登录 1:部门管理 2:职位管理)
	 * @return mixed
	 */
	public function addLog($info = '', int $type = self::TYPE_MEMBER)
	{
		return $this->insertData([
			'mem_id'  => (int)userId(),
			'site_id' => (int)siteId(),
			'type'    => $type,
			'info'    => trim($info),
			'ip'      => frame('IP')->getIp(),
		]);
	}
}
