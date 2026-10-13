<?php 

namespace app\service\log;
use app\service\Base;

class Payment extends Base
{
	const TYPE_ACCOUNT = 0; // 账户配置
	const TYPE_USED    = 1; // 账户使用

	/**
	 * 记录支付独立日志
	 *
	 * @param int|array $type 类型0账户配置1账户使用，或者传包含全部字段的数组
	 * @param int $itemId 对应的被操作条目ID (payment_id 或 item_id)
	 * @param string $info 操作简述
	 * @param array|string $detail 按字段变更的详情/对比
	 * @return mixed
	 */
	public function addLog($type = 0, $itemId = 0, $info = '', $detail = [])
	{
		if (is_array($type)) {
			$data   = $type;
			$type   = (int)($data['type'] ?? self::TYPE_ACCOUNT);
			$itemId = (int)($data['item_id'] ?? ($data['payment_id'] ?? 0));
			$info   = trim($data['info'] ?? ($data['title'] ?? ''));
			$detail = $data['detail'] ?? [];
		}

		$logData = [
			'mem_id'   => (int)userId(),
			'item_id'  => (int)$itemId,
			'type'     => (int)$type,
			'info'     => trim($info),
			'detail'   => is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : trim($detail),
		];
		return $this->insertData($logData);
	}

	/**
	 * 对比账户配置(type=0)各字段的变更差值 (Diff)
	 *
	 * @param array $oldData 修改前的数据
	 * @param array $newData 提交的新数据
	 * @return array 发生变动的字段列表
	 */
	public function diffPayment(array $oldData, array $newData): array
	{
		$typeList = service('payment/Payment')->getTypeList();
		$fields = [
			'name'        => '账户名称',
			'type'        => '账号类型',
			'status'      => '账号状态',
			'is_sandbox'  => '运行环境',
			'app_key'     => 'App Key',
			'secret_key'  => 'Secret Key',
			'webhook_key' => 'Webhook Key',
			'remark'      => '内部备注',
		];

		$diff = [];
		foreach ($fields as $key => $label) {
			if (!array_key_exists($key, $newData)) {
				continue;
			}
			$oldVal = $oldData[$key] ?? '';
			$newVal = $newData[$key];

			// 格式化与比对
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
			} elseif ($key === 'is_sandbox') {
				$oldValNorm = (int)$oldVal === 1 ? 1 : 0;
				$newValNorm = (int)$newVal === 1 ? 1 : 0;
				if ($oldValNorm !== $newValNorm) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $oldValNorm === 1 ? '沙盒环境' : '生产环境',
						'new'   => $newValNorm === 1 ? '沙盒环境' : '生产环境',
					];
				}
			} elseif ($key === 'type') {
				if ((int)$oldVal !== (int)$newVal) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => $typeList[(int)$oldVal] ?? '类型-' . $oldVal,
						'new'   => $typeList[(int)$newVal] ?? '类型-' . $newVal,
					];
				}
			} elseif (in_array($key, ['secret_key', 'webhook_key'])) {
				// 敏感秘钥脱敏比较
				if (trim($oldVal) !== trim($newVal)) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => empty($oldVal) ? '(空)' : '[原密钥]',
						'new'   => empty($newVal) ? '(空)' : '[新密钥已更新]',
					];
				}
			} elseif ($key === 'app_key') {
				if (trim($oldVal) !== trim($newVal)) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => substr(trim($oldVal), 0, 4) . '***',
						'new'   => substr(trim($newVal), 0, 4) . '***',
					];
				}
			} else {
				if (trim((string)$oldVal) !== trim((string)$newVal)) {
					$diff[] = [
						'field' => $key,
						'label' => $label,
						'old'   => (string)$oldVal,
						'new'   => (string)$newVal,
					];
				}
			}
		}

		return $diff;
	}

	/**
	 * 对比账户使用(type=1)各字段的变更差值 (Diff)
	 *
	 * @param array $oldData
	 * @param array $newData
	 * @param array $siteList 站点对照表
	 * @param array $paymentMap 账户对照表
	 * @return array
	 */
	public function diffUsed(array $oldData, array $newData, array $siteList = [], array $paymentMap = []): array
	{
		$typeList = service('payment/Payment')->methodList();
		$typeList = array_column($typeList, 'name', 'type');

		$fields = [
			'site_id'    => '站点',
			'type'       => '支付类型',
			'payment_id' => '支付账户',
		];

		$diff = [];
		foreach ($fields as $key => $label) {
			if (!array_key_exists($key, $newData)) {
				continue;
			}
			$oldVal = $oldData[$key] ?? '';
			$newVal = $newData[$key];

			if ((string)$oldVal !== (string)$newVal) {
				if ($key === 'site_id') {
					$oldTxt = $siteList[$oldVal] ?? ($oldVal > 0 ? '站点-' . $oldVal : '全局');
					$newTxt = $siteList[$newVal] ?? ($newVal > 0 ? '站点-' . $newVal : '全局');
				} elseif ($key === 'type') {
					$oldTxt = $typeList[$oldVal] ?? '类型-' . $oldVal;
					$newTxt = $typeList[$newVal] ?? '类型-' . $newVal;
				} elseif ($key === 'payment_id') {
					$oldTxt = $paymentMap[$oldVal] ?? '账户-' . $oldVal;
					$newTxt = $paymentMap[$newVal] ?? '账户-' . $newVal;
				} else {
					$oldTxt = (string)$oldVal;
					$newTxt = (string)$newVal;
				}
				$diff[] = [
					'field' => $key,
					'label' => $label,
					'old'   => $oldTxt,
					'new'   => $newTxt,
				];
			}
		}

		return $diff;
	}

	/**
	 * 生成账户配置的完整快照 (用于新增/删除/查看)
	 */
	public function snapshotPayment(array $data): array
	{
		$typeList = service('payment/Payment')->getTypeList();
		$snapshot = [];
		if (isset($data['name'])) {
			$snapshot[] = ['field' => 'name', 'label' => '账户名称', 'val' => $data['name']];
		}
		if (isset($data['type'])) {
			$snapshot[] = ['field' => 'type', 'label' => '账号类型', 'val' => $typeList[$data['type']] ?? '类型-' . $data['type']];
		}
		if (isset($data['status'])) {
			$snapshot[] = ['field' => 'status', 'label' => '账号状态', 'val' => (int)$data['status'] === 1 ? '启用' : '停用'];
		}
		if (isset($data['is_sandbox'])) {
			$snapshot[] = ['field' => 'is_sandbox', 'label' => '运行环境', 'val' => (int)$data['is_sandbox'] === 1 ? '沙盒环境' : '生产环境'];
		}
		if (!empty($data['app_key'])) {
			$snapshot[] = ['field' => 'app_key', 'label' => 'App Key', 'val' => substr(trim($data['app_key']), 0, 4) . '***'];
		}
		if (!empty($data['remark'])) {
			$snapshot[] = ['field' => 'remark', 'label' => '内部备注', 'val' => $data['remark']];
		}
		return $snapshot;
	}

	/**
	 * 生成账户使用的完整快照 (用于新增/删除/查看)
	 */
	public function snapshotUsed(array $data, array $siteList = [], array $paymentMap = []): array
	{
		$typeList = service('payment/Payment')->methodList();
		$typeList = array_column($typeList, 'name', 'type');

		$snapshot = [];
		if (isset($data['site_id'])) {
			$sid = $data['site_id'];
			$snapshot[] = ['field' => 'site_id', 'label' => '站点', 'val' => $siteList[$sid] ?? ($sid > 0 ? '站点-' . $sid : '全局')];
		}
		if (isset($data['type'])) {
			$tid = $data['type'];
			$snapshot[] = ['field' => 'type', 'label' => '支付类型', 'val' => $typeList[$tid] ?? '类型-' . $tid];
		}
		if (isset($data['payment_id'])) {
			$pid = $data['payment_id'];
			$snapshot[] = ['field' => 'payment_id', 'label' => '支付账户', 'val' => $paymentMap[$pid] ?? '账户-' . $pid];
		}
		return $snapshot;
	}
}
