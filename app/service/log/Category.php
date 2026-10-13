<?php 

namespace app\service\log;
use app\service\Base;

class Category extends Base
{
	const TYPE_CATEGORY = 0; // 类目
	const TYPE_LANGUAGE = 1; // 多语言
	const TYPE_SORT     = 2; // 排序

	/**
	 * 获取业务类型映射或名称
	 *
	 * @param int|null $type
	 * @return array|string
	 */
	public function getTypeList($type = null)
	{
		$list = [
			self::TYPE_CATEGORY => '类目',
			self::TYPE_LANGUAGE => '多语言',
			self::TYPE_SORT     => '排序',
		];
		return $type === null ? $list : ($list[$type] ?? '未知');
	}

	/**
	 * 记录分类独立日志
	 *
	 * @param int|array $type 类型0类目1多语言2排序，或者包含数据的关联数组
	 * @param int $cateId 对应的分类ID
	 * @param string $info 操作简述
	 * @param array|string $detail 按字段变更的详情/对比
	 * @return mixed
	 */
	public function addLog($type = 0, $cateId = 0, $info = '', $detail = [])
	{
		if (is_array($type)) {
			$data   = $type;
			$type   = (int)($data['type'] ?? self::TYPE_CATEGORY);
			$cateId = (int)($data['cate_id'] ?? 0);
			$info   = trim($data['info'] ?? ($data['title'] ?? ''));
			$detail = $data['detail'] ?? [];
		}

		$logData = [
			'mem_id'   => (int)userId(),
			'cate_id'  => (int)$cateId,
			'type'     => (int)$type,
			'info'     => trim($info),
			'detail'   => is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : trim($detail),
		];
		return $this->insertData($logData);
	}

	/**
	 * 对比分类各字段的变更差值 (Diff)
	 *
	 * @param array $oldData 修改前的数据
	 * @param array $newData 提交的新数据
	 * @return array 发生变动的字段列表
	 */
	public function diffCategory(array $oldData, array $newData): array
	{
		$fields = [
			'name'      => '分类中文名',
			'name_en'   => '分类英文名',
			'parent_id' => '父级分类',
			'status'    => '分类状态',
			'is_show'   => '前台展示',
			'is_hot'    => '热门标识',
			'attach_id' => '分类图标',
			'sort'      => '排序权重',
		];

		$diff = [];
		foreach ($fields as $key => $label) {
			if (!array_key_exists($key, $newData)) {
				continue;
			}
			$oldVal = $oldData[$key] ?? '';
			$newVal = $newData[$key];

			if ($key === 'status') {
				$oldValNorm = (int)$oldVal === 1 ? 1 : 0;
				$newValNorm = (int)$newVal === 1 ? 1 : 0;
				if ($oldValNorm !== $newValNorm) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldValNorm === 1 ? '启用' : '停用',
						'new'   => $newValNorm === 1 ? '启用' : '停用',
					];
				}
			} elseif ($key === 'is_show') {
				$oldValNorm = (int)$oldVal === 1 ? 1 : 0;
				$newValNorm = (int)$newVal === 1 ? 1 : 0;
				if ($oldValNorm !== $newValNorm) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldValNorm === 1 ? '展示' : '隐藏',
						'new'   => $newValNorm === 1 ? '展示' : '隐藏',
					];
				}
			} elseif ($key === 'is_hot') {
				$oldValNorm = (int)$oldVal === 1 ? 1 : 0;
				$newValNorm = (int)$newVal === 1 ? 1 : 0;
				if ($oldValNorm !== $newValNorm) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldValNorm === 1 ? '是' : '否',
						'new'   => $newValNorm === 1 ? '是' : '否',
					];
				}
			} elseif ($key === 'parent_id') {
				if ((int)$oldVal !== (int)$newVal) {
					$oldParentName = (int)$oldVal === 0 ? '顶级分类' : ('分类ID-' . $oldVal);
					$newParentName = (int)$newVal === 0 ? '顶级分类' : ('分类ID-' . $newVal);
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldParentName,
						'new'   => $newParentName,
					];
				}
			} elseif ($key === 'attach_id') {
				if ((int)$oldVal !== (int)$newVal) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldVal ? ('附件ID: ' . $oldVal) : '无图标',
						'new'   => $newVal ? ('附件ID: ' . $newVal) : '无图标',
					];
				}
			} else {
				if ((string)$oldVal !== (string)$newVal) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => (string)$oldVal !== '' ? (string)$oldVal : '(空)',
						'new'   => (string)$newVal !== '' ? (string)$newVal : '(空)',
					];
				}
			}
		}

		return $diff;
	}

	/**
	 * 生成分类新增/删除时的数据快照 (Snapshot)
	 *
	 * @param array $data 分类数据
	 * @return array 快照列表
	 */
	public function snapshotCategory(array $data): array
	{
		$snapshot = [];
		if (!empty($data['name'])) {
			$snapshot[] = ['field' => 'name', 'label' => '分类中文名', 'val' => $data['name']];
		}
		if (isset($data['name_en']) && $data['name_en'] !== '') {
			$snapshot[] = ['field' => 'name_en', 'label' => '分类英文名', 'val' => $data['name_en']];
		}
		if (isset($data['parent_id'])) {
			$pName = (int)$data['parent_id'] === 0 ? '顶级分类' : ('父级ID: ' . $data['parent_id']);
			$snapshot[] = ['field' => 'parent_id', 'label' => '父级分类', 'val' => $pName];
		}
		if (isset($data['status'])) {
			$snapshot[] = ['field' => 'status', 'label' => '分类状态', 'val' => (int)$data['status'] === 1 ? '启用' : '停用'];
		}
		if (isset($data['is_show'])) {
			$snapshot[] = ['field' => 'is_show', 'label' => '前台展示', 'val' => (int)$data['is_show'] === 1 ? '展示' : '隐藏'];
		}
		if (!empty($data['attach_id'])) {
			$snapshot[] = ['field' => 'attach_id', 'label' => '分类图标', 'val' => '附件ID: ' . $data['attach_id']];
		}
		if (isset($data['sort'])) {
			$snapshot[] = ['field' => 'sort', 'label' => '排序权重', 'val' => (int)$data['sort']];
		}
		return $snapshot;
	}

	/**
	 * 对比多语言翻译变更
	 *
	 * @param int $cateId 分类ID
	 * @param int $type 翻译类型 (0名称 1关键词 2描述)
	 * @param array $newLanguages 新翻译键值对 [lan_id => text]
	 * @param array $oldLanguages 旧翻译键值对 [lan_id => text]
	 * @param array $langMap 语言名称字典 [lan_id => 语言名称]
	 * @return array
	 */
	public function diffLanguage($cateId, $type, array $newLanguages, array $oldLanguages = [], array $langMap = []): array
	{
		$typeNames = [0 => '名称翻译', 1 => 'SEO关键词', 2 => 'SEO描述'];
		$typeName = $typeNames[$type] ?? '语言文本';

		$diff = [];
		foreach ($newLanguages as $lanId => $newVal) {
			$newVal = trim($newVal);
			$oldVal = trim($oldLanguages[$lanId] ?? '');
			if ($newVal !== $oldVal) {
				$langName = $langMap[$lanId] ?? ('语言ID-' . $lanId);
				$diff[] = [
					'field' => 'lang_' . $lanId,
					'label' => $langName . ' (' . $typeName . ')',
					'old'   => $oldVal !== '' ? $oldVal : '(空)',
					'new'   => $newVal !== '' ? $newVal : '(空)',
				];
			}
		}
		return $diff;
	}
}
