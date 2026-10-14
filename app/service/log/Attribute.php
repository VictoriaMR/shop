<?php

namespace app\service\log;

use app\service\Base;

class Attribute extends Base
{
	const TYPE_NAME       = 0; // 属性名
	const TYPE_NAME_LANG  = 1; // 属性名多语言
	const TYPE_VALUE      = 2; // 属性值
	const TYPE_VALUE_LANG = 3; // 属性值多语言

	/**
	 * 获取业务类型映射字典或单项名称
	 *
	 * @param int|null $type
	 * @return array|string
	 */
	public function getTypeList($type = null)
	{
		$list = [
			self::TYPE_NAME       => '属性名',
			self::TYPE_NAME_LANG  => '属性名多语言',
			self::TYPE_VALUE      => '属性值',
			self::TYPE_VALUE_LANG => '属性值多语言',
		];
		return $type === null ? $list : ($list[$type] ?? '未知');
	}

	/**
	 * 记录属性独立操作日志
	 *
	 * @param int|array $type 业务类型，或者包含日志数据的关联数组
	 * @param int $targetId 关联目标ID (attrn_id 或 attrv_id)
	 * @param string $info 操作简述
	 * @param array|string $detail 变更详情对比 (Diff) 或数据快照 (Snapshot)
	 * @return mixed
	 */
	public function addLog($type = 0, $targetId = 0, $info = '', $detail = [])
	{
		if (is_array($type)) {
			$data     = $type;
			$type     = (int)($data['type'] ?? self::TYPE_NAME);
			$targetId = (int)($data['target_id'] ?? ($data['id'] ?? 0));
			$info     = trim($data['info'] ?? ($data['title'] ?? ''));
			$detail   = $data['detail'] ?? [];
		}

		$logData = [
			'mem_id'    => (int)userId(),
			'type'      => (int)$type,
			'target_id' => (int)$targetId,
			'info'      => trim($info),
			'detail'    => is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : trim($detail),
		];
		return $this->insertData($logData);
	}

	/**
	 * 对比属性名/属性值的字段修改差值 (Diff)
	 *
	 * @param array $oldData 修改前的数据
	 * @param array $newData 提交的新数据
	 * @param string $nameLabel 字段中文标签
	 * @return array 发生变动的字段列表
	 */
	public function diffAttribute(array $oldData, array $newData, string $nameLabel = '属性名称'): array
	{
		$diff = [];
		if (array_key_exists('name', $newData)) {
			$oldVal = (string)($oldData['name'] ?? '');
			$newVal = (string)$newData['name'];
			if ($oldVal !== $newVal) {
				$diff[] = [
					'field' => 'name',
					'label' => $nameLabel,
					'old'   => $oldVal !== '' ? $oldVal : '(空)',
					'new'   => $newVal !== '' ? $newVal : '(空)',
				];
			}
		}
		return $diff;
	}

	/**
	 * 生成属性新增/删除时的数据快照 (Snapshot)
	 *
	 * @param array $data 属性数据
	 * @param string $nameLabel 字段中文标签
	 * @return array 快照列表
	 */
	public function snapshotAttribute(array $data, string $nameLabel = '属性名称'): array
	{
		$snapshot = [];
		if (isset($data['name']) && $data['name'] !== '') {
			$snapshot[] = [
				'field' => 'name',
				'label' => $nameLabel,
				'val'   => (string)$data['name'],
			];
		}
		return $snapshot;
	}

	/**
	 * 对比多语言翻译变更差值 (Diff)
	 *
	 * @param array $newLanguages 新提交的翻译 [lan_id => text]
	 * @param array $oldLanguages 修改前的翻译 [lan_id => text]
	 * @param array $langMap 语言名称映射表 [lan_id => 语言中文名]
	 * @param string $prefix 提示前缀
	 * @return array 发生变动的语言列表
	 */
	public function diffLanguage(array $newLanguages, array $oldLanguages = [], array $langMap = [], string $prefix = '属性'): array
	{
		$diff = [];
		foreach ($newLanguages as $lanId => $newVal) {
			$newVal = trim((string)$newVal);
			$oldVal = trim((string)($oldLanguages[$lanId] ?? ''));
			if ($newVal !== $oldVal) {
				$langName = $langMap[$lanId] ?? ('语言ID-' . $lanId);
				$diff[] = [
					'field' => 'lang_' . $lanId,
					'label' => $langName . ' (' . $prefix . '翻译)',
					'old'   => $oldVal !== '' ? $oldVal : '(空)',
					'new'   => $newVal !== '' ? $newVal : '(空)',
				];
			}
		}
		return $diff;
	}
}
