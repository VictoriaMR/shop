<?php

namespace app\controller\admin;

use app\controller\AdminBase;

/**
 * 商品属性管理控制器 (属性名与属性值)
 * 路由：https://admin.cn/attribute (属性名) & https://admin.cn/attribute/attrValue (属性值)
 */
class Attribute extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'     => '属性名管理',
			'attrValue' => '属性值管理',
			'log'       => '属性日志',
		];
		$this->_default = '属性管理';
		$this->_init();
	}

	/**
	 * 属性名列表
	 */
	public function index(): void
	{
		if (isPost()) {
			$opn = ipost('opn', '');
			$allowed = [
				'getAttrInfo',
				'getAttrLanguage',
				'transfer',
				'editAttrInfo',
				'editAttrLanguage',
				'deleteAttrInfo',
			];
			if (in_array($opn, $allowed, true) && method_exists($this, $opn)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$page    = max(1, iget('page/d', 1));
		$size    = max(1, min(100, iget('size/d', 40)));
		$keyword = iget('keyword', '');
		$status  = iget('status/d', -1);

		$where = [];
		if ($keyword !== '') {
			$where['name'] = ['like', '%' . $keyword . '%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}

		$nameService = service('attr/Name');
		$total       = $nameService->getCountData($where);
		$list        = [];
		if ($total > 0) {
			$list = $nameService->getList($where, $page, $size);
		}

		$this->view([
			'status'  => $status,
			'keyword' => $keyword,
			'total'   => $total,
			'size'    => $size,
			'list'    => $list,
		]);
	}

	/**
	 * 获取单个属性名详情
	 */
	protected function getAttrInfo(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrn_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service('attr/Name')->loadData($id);
		if (empty($info)) {
			$this->error('属性名不存在');
		}

		$this->success('获取成功', $info);
	}

	/**
	 * 获取属性名多语言配置
	 */
	protected function getAttrLanguage(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrn_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$nameInfo = service('attr/Name')->loadData($id);
		if (empty($nameInfo)) {
			$this->error('属性名不存在');
		}

		$languageList    = sys()->language()->getTransList();
		$rawTranslations = service('attr/NameLanguage')->getListData(['attrn_id' => $id]);
		$transMap        = array_column($rawTranslations, 'name', 'lan_id');

		$data = [];
		foreach ($languageList as $item) {
			$lanId = $item['lan_id'];
			$data[$lanId] = [
				'lan_id'        => $lanId,
				'tr_code'       => $item['tr_code'] ?? '',
				'name'          => $transMap[$lanId] ?? '',
				'language_name' => $item['name'] ?? '',
			];
		}

		$this->success('获取成功', $data);
	}

	/**
	 * 智能多语言翻译
	 */
	protected function transfer(): void
	{
		$trCode = ipost('tr_code', '');
		$name   = ipost('name', '');

		if ($name === '' || $trCode === '') {
			$this->error('参数不正确');
		}

		$rst = service('tool/Translate')->getText($name, $trCode);
		if ($rst !== false && $rst !== null && $rst !== '') {
			$this->success('翻译成功', $rst);
		}

		$this->error('翻译失败');
	}

	/**
	 * 新增 / 编辑属性名
	 */
	protected function editAttrInfo(): void
	{
		$id   = ipost('id/d', 0) ?: ipost('attrn_id/d', 0);
		$name = ipost('name', '');

		if ($name === '') {
			$this->error('属性名不能为空');
		}

		$service = service('attr/Name');
		$where   = ['name' => $name];
		if ($id > 0) {
			$where['attrn_id'] = ['<>', $id];
		}

		if ($service->getCountData($where)) {
			$this->error('属性名「' . $name . '」已存在，请勿重复添加');
		}

		$data = ['name' => $name];

		if ($id <= 0) {
			$newId = $service->insert($data);
			if ($newId) {
				$logTitle = '新增属性名 #' . $newId;
				$detail   = service('log/Attribute')->snapshotAttribute($data, '属性名');
				$this->addLog($logTitle);
				service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_NAME, $newId, $logTitle, $detail);
				$this->success('操作成功', ['attrn_id' => $newId]);
			}
		} else {
			$oldInfo = $service->loadData($id);
			if (empty($oldInfo)) {
				$this->error('要修改的属性名不存在');
			}

			// 如果名称未做改动，直接返回成功
			if ($oldInfo['name'] === $name) {
				$this->success('操作成功', ['attrn_id' => $id]);
			}

			$result = $service->updateData($id, $data);
			if ($result !== false) {
				$logTitle = '修改属性名 #' . $id;
				$detail   = service('log/Attribute')->diffAttribute($oldInfo, $data, '属性名');
				$this->addLog($logTitle);
				service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_NAME, $id, $logTitle, $detail);
				$this->success('操作成功', ['attrn_id' => $id]);
			}
		}

		$this->error('操作失败');
	}

	/**
	 * 保存属性名多语言配置
	 */
	protected function editAttrLanguage(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrn_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$nameInfo = service('attr/Name')->loadData($id);
		if (empty($nameInfo)) {
			$this->error('属性名不存在');
		}

		$language   = ipost('language/a', []);
		$services   = service('attr/NameLanguage');
		$oldTranslations = $services->getListData(['attrn_id' => $id]);
		$oldLangMap      = array_column($oldTranslations, 'name', 'lan_id');
		$validCount = 0;

		foreach ($language as $key => $value) {
			$lanId = (int)$key;
			if ($lanId <= 0) {
				continue;
			}
			$val = strTrim((string)$value);
			if ($val !== '') {
				$services->setNxLanguage($id, $lanId, $val);
				$validCount++;
			} else {
				$services->deleteData(['attrn_id' => $id, 'lan_id' => $lanId]);
			}
		}

		$transList      = sys()->language()->getTransList();
		$totalLanguages = count($transList);
		if ($validCount >= $totalLanguages && $totalLanguages > 0) {
			$status = 2; // 全部配置
		} elseif ($validCount > 0) {
			$status = 1; // 部分配置
		} else {
			$status = 0; // 未配置
		}

		service('attr/Name')->updateData($id, ['status' => $status]);

		// 记录多语言独立变更日志
		$langNameMap = array_column($transList, 'name', 'lan_id');
		$diff        = service('log/Attribute')->diffLanguage($language, $oldLangMap, $langNameMap, '属性名');
		$logTitle    = '修改属性名语言 #' . $id . ' (' . ($nameInfo['name'] ?? '') . ')';
		$this->addLog($logTitle);
		service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_NAME_LANG, $id, $logTitle, $diff);

		$this->success('操作成功', ['status' => $status]);
	}

	/**
	 * 删除属性名
	 */
	protected function deleteAttrInfo(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrn_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service('attr/Name')->loadData($id);
		if (empty($info)) {
			$this->error('属性名不存在');
		}

		$rst = service('attr/Name')->deleteData($id);
		if ($rst) {
			// 清理关联表与多语言数据
			service('product/AttrUsed')->deleteData(['attrn_id' => $id]);
			service('attr/NameLanguage')->deleteData(['attrn_id' => $id]);
			service('attr/NameMap')->deleteData(['attrn_id' => $id]);

			$logTitle = '删除属性名 #' . $id . ' (' . ($info['name'] ?? '') . ')';
			$detail   = service('log/Attribute')->snapshotAttribute($info, '属性名');
			$this->addLog($logTitle);
			service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_NAME, $id, $logTitle, $detail);

			$this->success('删除成功');
		}

		$this->error('删除失败');
	}

	/**
	 * 属性值列表
	 */
	public function attrValue(): void
	{
		if (isPost()) {
			$opn = ipost('opn', '');
			$allowed = [
				'getAttvInfo',
				'getAttvLanguage',
				'transfer',
				'editAttvInfo',
				'editAttvLanguage',
				'deleteAttvInfo',
			];
			if (in_array($opn, $allowed, true) && method_exists($this, $opn)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$page    = max(1, iget('page/d', 1));
		$size    = max(1, min(100, iget('size/d', 40)));
		$keyword = iget('keyword', '');
		$status  = iget('status/d', -1);

		$where = [];
		if ($keyword !== '') {
			$where['name'] = ['like', '%' . $keyword . '%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}

		$valueService = service('attr/Value');
		$total        = $valueService->getCountData($where);
		$list         = [];
		if ($total > 0) {
			$list = $valueService->getList($where, $page, $size);
		}

		$this->view([
			'status'  => $status,
			'keyword' => $keyword,
			'total'   => $total,
			'size'    => $size,
			'list'    => $list,
		]);
	}

	/**
	 * 获取单个属性值详情
	 */
	protected function getAttvInfo(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrv_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service('attr/Value')->loadData($id);
		if (empty($info)) {
			$this->error('属性值不存在');
		}

		$this->success('获取成功', $info);
	}

	/**
	 * 获取属性值多语言配置
	 */
	protected function getAttvLanguage(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrv_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$valueInfo = service('attr/Value')->loadData($id);
		if (empty($valueInfo)) {
			$this->error('属性值不存在');
		}

		$languageList    = sys()->language()->getTransList();
		$rawTranslations = service('attr/ValueLanguage')->getListData(['attrv_id' => $id]);
		$transMap        = array_column($rawTranslations, 'name', 'lan_id');

		$data = [];
		foreach ($languageList as $item) {
			$lanId = $item['lan_id'];
			$data[$lanId] = [
				'lan_id'        => $lanId,
				'tr_code'       => $item['tr_code'] ?? '',
				'name'          => $transMap[$lanId] ?? '',
				'language_name' => $item['name'] ?? '',
			];
		}

		$this->success('获取成功', $data);
	}

	/**
	 * 新增 / 编辑属性值
	 */
	protected function editAttvInfo(): void
	{
		$id   = ipost('id/d', 0) ?: ipost('attrv_id/d', 0);
		$name = ipost('name', '');

		if ($name === '') {
			$this->error('属性值不能为空');
		}

		$service = service('attr/Value');
		$where   = ['name' => $name];
		if ($id > 0) {
			$where['attrv_id'] = ['<>', $id];
		}

		if ($service->getCountData($where)) {
			$this->error('属性值「' . $name . '」已存在，请勿重复添加');
		}

		$data = ['name' => $name];

		if ($id <= 0) {
			$newId = $service->insert($data);
			if ($newId) {
				$logTitle = '新增属性值 #' . $newId;
				$detail   = service('log/Attribute')->snapshotAttribute($data, '属性值');
				$this->addLog($logTitle);
				service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_VALUE, $newId, $logTitle, $detail);
				$this->success('操作成功', ['attrv_id' => $newId]);
			}
		} else {
			$oldInfo = $service->loadData($id);
			if (empty($oldInfo)) {
				$this->error('要修改的属性值不存在');
			}

			// 如果名称未做改动，直接返回成功
			if ($oldInfo['name'] === $name) {
				$this->success('操作成功', ['attrv_id' => $id]);
			}

			$result = $service->updateData($id, $data);
			if ($result !== false) {
				$logTitle = '修改属性值 #' . $id;
				$detail   = service('log/Attribute')->diffAttribute($oldInfo, $data, '属性值');
				$this->addLog($logTitle);
				service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_VALUE, $id, $logTitle, $detail);
				$this->success('操作成功', ['attrv_id' => $id]);
			}
		}

		$this->error('操作失败');
	}

	/**
	 * 保存属性值多语言配置
	 */
	protected function editAttvLanguage(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrv_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$valueInfo = service('attr/Value')->loadData($id);
		if (empty($valueInfo)) {
			$this->error('属性值不存在');
		}

		$language   = ipost('language/a', []);
		$services   = service('attr/ValueLanguage');
		$oldTranslations = $services->getListData(['attrv_id' => $id]);
		$oldLangMap      = array_column($oldTranslations, 'name', 'lan_id');
		$validCount = 0;

		foreach ($language as $key => $value) {
			$lanId = (int)$key;
			if ($lanId <= 0) {
				continue;
			}
			$val = strTrim((string)$value);
			if ($val !== '') {
				$services->setNxLanguage($id, $lanId, $val);
				$validCount++;
			} else {
				$services->deleteData(['attrv_id' => $id, 'lan_id' => $lanId]);
			}
		}

		$transList      = sys()->language()->getTransList();
		$totalLanguages = count($transList);
		if ($validCount >= $totalLanguages && $totalLanguages > 0) {
			$status = 2; // 全部配置
		} elseif ($validCount > 0) {
			$status = 1; // 部分配置
		} else {
			$status = 0; // 未配置
		}

		service('attr/Value')->updateData($id, ['status' => $status]);

		// 记录多语言独立变更日志
		$langNameMap = array_column($transList, 'name', 'lan_id');
		$diff        = service('log/Attribute')->diffLanguage($language, $oldLangMap, $langNameMap, '属性值');
		$logTitle    = '修改属性值语言 #' . $id . ' (' . ($valueInfo['name'] ?? '') . ')';
		$this->addLog($logTitle);
		service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_VALUE_LANG, $id, $logTitle, $diff);

		$this->success('操作成功', ['status' => $status]);
	}

	/**
	 * 删除属性值
	 */
	protected function deleteAttvInfo(): void
	{
		$id = ipost('id/d', 0) ?: ipost('attrv_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service('attr/Value')->loadData($id);
		if (empty($info)) {
			$this->error('属性值不存在');
		}

		$rst = service('attr/Value')->deleteData($id);
		if ($rst) {
			// 清理关联表与多语言数据
			service('product/AttrUsed')->deleteData(['attrv_id' => $id]);
			service('attr/ValueLanguage')->deleteData(['attrv_id' => $id]);
			service('attr/ValueMap')->deleteData(['attrv_id' => $id]);

			$logTitle = '删除属性值 #' . $id . ' (' . ($info['name'] ?? '') . ')';
			$detail   = service('log/Attribute')->snapshotAttribute($info, '属性值');
			$this->addLog($logTitle);
			service('log/Attribute')->addLog(\app\service\log\Attribute::TYPE_VALUE, $id, $logTitle, $detail);

			$this->success('删除成功');
		}

		$this->error('删除失败');
	}

	/**
	 * 查看属性操作日志
	 */
	public function log(): void
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$type     = iget('type', -1);
		$targetId = trim((string)iget('target_id', ''));
		$keyword  = trim((string)iget('keyword', ''));
		$stime    = trim((string)iget('stime', ''));
		$etime    = trim((string)iget('etime', ''));
		$page     = max(1, iget('page/d', 1));
		$size     = max(1, min(100, iget('size/d', 20)));

		$where = [];
		if ($type !== '' && (int)$type >= 0) {
			$where['type'] = (int)$type;
		}
		if ($targetId !== '') {
			$where['target_id'] = (int)$targetId;
		}
		if ($keyword !== '') {
			$where['info'] = ['like', '%' . $keyword . '%'];
		}
		$stimeFull = $stime !== '' ? $stime . (strlen($stime) === 10 ? ' 00:00:00' : '') : '';
		$etimeFull = $etime !== '' ? $etime . (strlen($etime) === 10 ? ' 23:59:59' : '') : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$logService = service('log/Attribute');
		$total      = $logService->getCountData($where);
		$list       = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'desc']);

			// 关联操作人信息
			$memIds    = array_unique(array_filter(array_column($list, 'mem_id')));
			$memberMap = [];
			if (!empty($memIds)) {
				$memberService = service('member/Member');
				$members       = $memberService->getListData(
					['mem_id' => count($memIds) === 1 ? reset($memIds) : ['in', array_values($memIds)]],
					'mem_id, avatar, sex, first_name, last_name, nick_name, mobile',
					0,
					0
				);
				foreach ($members as $m) {
					$firstName               = $m['first_name'] ?? '';
					$lastName                = $m['last_name'] ?? '';
					$m['fullName']           = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
					$m['avatar']             = $memberService->getAvatar($m['avatar'], $m['sex']);
					$memberMap[$m['mem_id']] = $m;
				}
			}

			// 分别收集属性名与属性值 ID，避免无效及重复查询
			$nameIds  = [];
			$valueIds = [];
			foreach ($list as $item) {
				$tId = (int)$item['target_id'];
				if ($tId <= 0) {
					continue;
				}
				$itemType = (int)$item['type'];
				if ($itemType === \app\service\log\Attribute::TYPE_NAME || $itemType === \app\service\log\Attribute::TYPE_NAME_LANG) {
					$nameIds[] = $tId;
				} else {
					$valueIds[] = $tId;
				}
			}

			$nameMap = [];
			if (!empty($nameIds)) {
				$names   = service('attr/Name')->getListData(
					['attrn_id' => ['in', array_unique($nameIds)]],
					'attrn_id, name'
				);
				$nameMap = array_column($names, 'name', 'attrn_id');
			}

			$valueMap = [];
			if (!empty($valueIds)) {
				$values   = service('attr/Value')->getListData(
					['attrv_id' => ['in', array_unique($valueIds)]],
					'attrv_id, name'
				);
				$valueMap = array_column($values, 'name', 'attrv_id');
			}

			foreach ($list as &$item) {
				$item['member'] = $memberMap[$item['mem_id']] ?? [];
				$tId            = (int)$item['target_id'];
				$itemType       = (int)$item['type'];
				if ($itemType === \app\service\log\Attribute::TYPE_NAME || $itemType === \app\service\log\Attribute::TYPE_NAME_LANG) {
					$item['target_name'] = $nameMap[$tId] ?? '';
					$item['target_desc'] = $tId > 0 ? ('属性名 #' . $tId) : '-';
				} else {
					$item['target_name'] = $valueMap[$tId] ?? '';
					$item['target_desc'] = $tId > 0 ? ('属性值 #' . $tId) : '-';
				}
			}
			unset($item);
		}

		$this->assign('total', $total);
		$this->assign('list', $list);
		$this->assign('size', $size);
		$this->assign('typeList', $logService->getTypeList());
		$this->assign('search', [
			'type'      => $type,
			'target_id' => $targetId,
			'keyword'   => $keyword,
			'stime'     => $stime,
			'etime'     => $etime,
		]);
		$this->view();
	}
}