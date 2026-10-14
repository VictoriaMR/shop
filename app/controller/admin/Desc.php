<?php

namespace app\controller\admin;

use app\controller\AdminBase;

/**
 * 商品描述管理控制器 (描述名、描述值、描述分组)
 * 路由：
 * - https://admin.cn/desc (描述名称)
 * - https://admin.cn/desc/descValue (描述值)
 * - https://admin.cn/desc/descGroup (描述分组)
 */
class Desc extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'     => '描述名称',
			'descValue' => '描述值',
			'descGroup' => '描述分组',
		];
		$this->_default = '描述管理';
		$this->_init();
	}

	/**
	 * 描述名称列表
	 */
	public function index(): void
	{
		if (isPost()) {
			$this->dispatchPost([
				'getDescInfo',
				'getDescLanguage',
				'transfer',
				'editDescInfo',
				'editDescLanguage',
				'deleteDescInfo',
			]);
		}

		$this->renderList('desc/Name');
	}

	protected function getDescInfo(): void
	{
		$this->handleGetInfo('desc/Name', 'descn_id', '描述名称');
	}

	protected function getDescLanguage(): void
	{
		$this->handleGetLanguage('desc/Name', 'desc/NameLanguage', 'descn_id', '描述名称');
	}

	protected function editDescInfo(): void
	{
		$this->handleEditInfo('desc/Name', 'descn_id', '描述名称');
	}

	protected function editDescLanguage(): void
	{
		$this->handleEditLanguage('desc/Name', 'desc/NameLanguage', 'descn_id', '描述名称');
	}

	protected function deleteDescInfo(): void
	{
		$this->handleDeleteInfo('desc/Name', 'desc/NameLanguage', 'descn_id', '描述名称');
	}

	/**
	 * 描述值列表
	 */
	public function descValue(): void
	{
		if (isPost()) {
			$this->dispatchPost([
				'getDescValueInfo',
				'getDescValueLanguage',
				'transfer',
				'editDescValueInfo',
				'editDescValueLanguage',
				'deleteDescValueInfo',
			]);
		}

		$this->renderList('desc/Value');
	}

	protected function getDescValueInfo(): void
	{
		$this->handleGetInfo('desc/Value', 'descv_id', '描述值');
	}

	protected function getDescValueLanguage(): void
	{
		$this->handleGetLanguage('desc/Value', 'desc/ValueLanguage', 'descv_id', '描述值');
	}

	protected function editDescValueInfo(): void
	{
		$this->handleEditInfo('desc/Value', 'descv_id', '描述值');
	}

	protected function editDescValueLanguage(): void
	{
		$this->handleEditLanguage('desc/Value', 'desc/ValueLanguage', 'descv_id', '描述值');
	}

	protected function deleteDescValueInfo(): void
	{
		$this->handleDeleteInfo('desc/Value', 'desc/ValueLanguage', 'descv_id', '描述值');
	}

	/**
	 * 描述分组列表
	 */
	public function descGroup(): void
	{
		if (isPost()) {
			$this->dispatchPost([
				'getDescGroupInfo',
				'getDescGroupLanguage',
				'transfer',
				'editDescGroupInfo',
				'editDescGroupLanguage',
				'deleteDescGroupInfo',
			]);
		}

		$this->renderList('desc/Group');
	}

	protected function getDescGroupInfo(): void
	{
		$this->handleGetInfo('desc/Group', 'descg_id', '描述分组');
	}

	protected function getDescGroupLanguage(): void
	{
		$this->handleGetLanguage('desc/Group', 'desc/GroupLanguage', 'descg_id', '描述分组');
	}

	protected function editDescGroupInfo(): void
	{
		$this->handleEditInfo('desc/Group', 'descg_id', '描述分组');
	}

	protected function editDescGroupLanguage(): void
	{
		$this->handleEditLanguage('desc/Group', 'desc/GroupLanguage', 'descg_id', '描述分组');
	}

	protected function deleteDescGroupInfo(): void
	{
		$this->handleDeleteInfo('desc/Group', 'desc/GroupLanguage', 'descg_id', '描述分组');
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
	 * 统一分发 POST 请求操作
	 */
	private function dispatchPost(array $allowed): void
	{
		$opn = ipost('opn', '');
		if (in_array($opn, $allowed, true) && method_exists($this, $opn)) {
			$this->$opn();
		}
		$this->error('非法请求');
	}

	/**
	 * 通用列表渲染
	 */
	private function renderList(string $serviceName): void
	{
		frame('Html')->addJs();
		frame('Html')->addCss();

		$page    = max(1, iget('page/d', 1));
		$size    = max(1, iget('size/d', 40));
		$keyword = trim((string)iget('keyword', ''));
		$status  = iget('status/d', -1);

		$where = [];
		if ($keyword !== '') {
			$where['name'] = ['like', '%' . $keyword . '%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}

		$service = service($serviceName);
		$total   = $service->getCountData($where);
		$list    = [];
		if ($total > 0) {
			$list = $service->getList($where, $page, $size);
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
	 * 通用获取单条详情
	 */
	private function handleGetInfo(string $serviceName, string $idField, string $label): void
	{
		$id = ipost('id/d', 0) ?: ipost($idField . '/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service($serviceName)->loadData($id);
		if (empty($info)) {
			$this->error($label . '不存在');
		}

		$this->success('获取成功', $info);
	}

	/**
	 * 通用获取多语言配置
	 */
	private function handleGetLanguage(string $serviceName, string $langServiceName, string $idField, string $label): void
	{
		$id = ipost('id/d', 0) ?: ipost($idField . '/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service($serviceName)->loadData($id);
		if (empty($info)) {
			$this->error($label . '不存在');
		}

		$languageList    = sys()->language()->getTransList();
		$rawTranslations = service($langServiceName)->getListData([$idField => $id]);
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
	 * 通用新增 / 编辑名称
	 */
	private function handleEditInfo(string $serviceName, string $idField, string $label): void
	{
		$id   = ipost('id/d', 0) ?: ipost($idField . '/d', 0);
		$name = ipost('name', '');

		if ($name === '') {
			$this->error($label . '不能为空');
		}

		$service = service($serviceName);
		$where   = ['name' => $name];
		if ($id > 0) {
			$where[$idField] = ['<>', $id];
		}

		if ($service->getCountData($where)) {
			$this->error($label . '「' . $name . '」已存在，请勿重复添加');
		}

		$data = ['name' => $name];

		if ($id <= 0) {
			$newId = $service->insert($data);
			if ($newId) {
				$this->addLog('新增' . $label . '-' . $newId);
				$this->success('操作成功', [$idField => $newId]);
			}
		} else {
			$oldInfo = $service->loadData($id);
			if (empty($oldInfo)) {
				$this->error('要修改的' . $label . '不存在');
			}

			if ($oldInfo['name'] === $name) {
				$this->success('操作成功', [$idField => $id]);
			}

			$result = $service->updateData($id, $data);
			if ($result !== false) {
				$this->addLog('修改' . $label . '-' . $id);
				$this->success('操作成功', [$idField => $id]);
			}
		}

		$this->error('操作失败');
	}

	/**
	 * 通用保存多语言配置
	 */
	private function handleEditLanguage(string $serviceName, string $langServiceName, string $idField, string $label): void
	{
		$id = ipost('id/d', 0) ?: ipost($idField . '/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service($serviceName)->loadData($id);
		if (empty($info)) {
			$this->error($label . '不存在');
		}

		$language    = ipost('language/a', []);
		$langService = service($langServiceName);
		$validCount  = 0;

		foreach ($language as $key => $value) {
			$lanId = (int)$key;
			if ($lanId <= 0) {
				continue;
			}
			$val = strTrim((string)$value);
			if ($val !== '') {
				$langService->setNxLanguage($id, $lanId, $val);
				$validCount++;
			} else {
				$langService->deleteData([$idField => $id, 'lan_id' => $lanId]);
			}
		}

		$totalLanguages = count(sys()->language()->getTransList());
		if ($validCount >= $totalLanguages && $totalLanguages > 0) {
			$status = 2; // 已配置
		} elseif ($validCount > 0) {
			$status = 1; // 部分配置
		} else {
			$status = 0; // 未配置
		}

		service($serviceName)->updateData($id, ['status' => $status]);
		$this->addLog('修改' . $label . '多语言-' . $id);

		$this->success('操作成功', ['status' => $status]);
	}

	/**
	 * 通用删除记录及关联数据
	 */
	private function handleDeleteInfo(string $serviceName, string $langServiceName, string $idField, string $label): void
	{
		$id = ipost('id/d', 0) ?: ipost($idField . '/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}

		$info = service($serviceName)->loadData($id);
		if (empty($info)) {
			$this->error($label . '不存在');
		}

		$rst = service($serviceName)->deleteData($id);
		if ($rst) {
			service('product/DescUsed')->deleteData([$idField => $id]);
			service($langServiceName)->deleteData([$idField => $id]);

			$this->addLog('删除' . $label . '-' . $id . ' (' . ($info['name'] ?? '') . ')');
			$this->success('删除成功');
		}

		$this->error('删除失败');
	}
}