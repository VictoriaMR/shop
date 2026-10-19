<?php

namespace app\service\log;
use app\service\Base;

class Product extends Base
{
	const TYPE_SPU      = 0; // SPU基础/扩展
	const TYPE_LANGUAGE = 1; // 多语言标题
	const TYPE_IMAGE    = 2; // SPU主图
	const TYPE_SKU      = 3; // SKU/规格
	const TYPE_DESC     = 4; // 描述属性
	const TYPE_INTRO    = 5; // 详情介绍图
	const TYPE_PURCHASE = 6; // 采购/入库

	/**
	 * 获取业务类型映射字典或单项名称
	 *
	 * @param int|null $type
	 * @return array|string
	 */
	public function getTypeList($type = null)
	{
		$list = [
			self::TYPE_SPU      => 'SPU基础/扩展',
			self::TYPE_LANGUAGE => '多语言标题',
			self::TYPE_IMAGE    => 'SPU主图',
			self::TYPE_SKU      => 'SKU/规格',
			self::TYPE_DESC     => '描述属性',
			self::TYPE_INTRO    => '详情介绍图',
			self::TYPE_PURCHASE => '采购/入库',
		];
		return $type === null ? $list : ($list[(int)$type] ?? '产品操作');
	}

	/**
	 * 记录产品独立操作日志
	 *
	 * @param int|array $type 业务类型，或包含日志数据的关联数组
	 * @param int $spuId 关联SPU ID
	 * @param string $info 操作简述
	 * @param array|string $detail 变更详情对比(Diff)或快照(Snapshot)
	 * @return mixed
	 */
	public function addLog($type = 0, $spuId = 0, $info = '', $detail = [])
	{
		if (is_array($type)) {
			$data   = $type;
			$type   = (int)($data['type'] ?? self::TYPE_SPU);
			$spuId  = (int)($data['spu_id'] ?? 0);
			$info   = trim((string)($data['info'] ?? ($data['title'] ?? '')));
			$detail = $data['detail'] ?? [];
		}

		$logData = [
			'mem_id' => (int)userId(),
			'spu_id' => (int)$spuId,
			'type'   => (int)$type,
			'info'   => mb_substr(trim((string)$info), 0, 120),
			'detail' => is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : trim((string)$detail),
		];
		return $this->insertData($logData);
	}

	/**
	 * 对比 SPU 基础字段的变更差值 (Diff)
	 *
	 * @param array $oldData 修改前数据
	 * @param array $newData 提交的新数据
	 * @return array
	 */
	public function diffSpuInfo(array $oldData, array $newData): array
	{
		$fields = [
			'status'    => '商品状态',
			'cate_id'   => '所属分类',
			'free_ship' => '包邮设置',
			'is_hot'    => '热门标记',
			'gender'    => '适用人群',
			'attach_id' => '主封面图ID',
		];
		$statusMap = service('product/Spu')->getStatusList();
		$diff = [];

		foreach ($fields as $key => $label) {
			if (!array_key_exists($key, $newData)) {
				continue;
			}
			$oldVal = $oldData[$key] ?? '';
			$newVal = $newData[$key];

			if ($key === 'status') {
				$oldInt = (int)$oldVal;
				$newInt = (int)$newVal;
				if ($oldInt !== $newInt) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => ($statusMap[$oldInt] ?? $oldInt) . ' (' . $oldInt . ')',
						'new'   => ($statusMap[$newInt] ?? $newInt) . ' (' . $newInt . ')',
					];
				}
			} elseif ($key === 'cate_id') {
				$oldInt = (int)$oldVal;
				$newInt = (int)$newVal;
				if ($oldInt !== $newInt) {
					$oldCate = $oldInt > 0 ? service('category/Category')->loadData($oldInt, 'name') : [];
					$newCate = $newInt > 0 ? service('category/Category')->loadData($newInt, 'name') : [];
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => !empty($oldCate['name']) ? ($oldCate['name'] . ' (ID:' . $oldInt . ')') : (string)$oldInt,
						'new'   => !empty($newCate['name']) ? ($newCate['name'] . ' (ID:' . $newInt . ')') : (string)$newInt,
					];
				}
			} elseif ($key === 'free_ship') {
				$oldInt = (int)$oldVal === 1 ? 1 : 0;
				$newInt = (int)$newVal === 1 ? 1 : 0;
				if ($oldInt !== $newInt) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldInt === 1 ? '包邮' : '不包邮',
						'new'   => $newInt === 1 ? '包邮' : '不包邮',
					];
				}
			} elseif ($key === 'is_hot') {
				$oldInt = (int)$oldVal === 1 ? 1 : 0;
				$newInt = (int)$newVal === 1 ? 1 : 0;
				if ($oldInt !== $newInt) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldInt === 1 ? '热门' : '普通',
						'new'   => $newInt === 1 ? '热门' : '普通',
					];
				}
			} else {
				if ((string)$oldVal !== (string)$newVal) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldVal !== '' ? (string)$oldVal : '-',
						'new'   => $newVal !== '' ? (string)$newVal : '-',
					];
				}
			}
		}

		return $diff;
	}

	/**
	 * 对比多语言标题变更 (Diff)
	 *
	 * @param array $newLanguage 提交的语言数组 [lan_id => name]
	 * @param array $oldMap 修改前语言数组 [lan_id => name]
	 * @param array $langMap 语言名称映射 [lan_id => language_name]
	 * @return array
	 */
	public function diffLanguage(array $newLanguage, array $oldMap, array $langMap): array
	{
		$diff = [];
		foreach ($newLanguage as $lanId => $val) {
			$newVal = strTrim((string)$val);
			$oldVal = trim((string)($oldMap[$lanId] ?? ''));
			if ($oldVal !== $newVal) {
				$langName = $lanId == 0 ? '中文名称' : ($langMap[$lanId] ?? ('语言#' . $lanId));
				$diff[] = [
					'field' => 'lan_' . $lanId,
					'label' => $langName,
					'old'   => $oldVal !== '' ? $oldVal : '(空)',
					'new'   => $newVal !== '' ? $newVal : '(空)',
				];
			}
		}
		return $diff;
	}
}
