<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Site extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'        => '站点列表',
			'siteInfo'     => '站点设置',
			'staticCache'  => '静态文件管理',
			'staticDetail' => '静态文件详情',
			'siteLog'      => '站点日志',
			'logDetail'    => '日志详情',
		];
		$this->_ignore = ['siteInfo', 'staticDetail', 'logDetail'];
		$this->_default = '站点管理';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editSite', 'getInfo', 'modifySite', 'deleteLanguage', 'addLanguage', 'sortLanguage', 'deleteCurrency', 'addCurrency', 'sortCurrency', 'updateKeyword', 'editLanguage', 'getLanguage', 'transfer'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$status = (int)iget('status', -1);
		$keyword = trim((string)iget('keyword', ''));
		$where = [];
		if ($status === 0 || $status === 1) {
			$where['status'] = $status;
		}
		if ($keyword !== '') {
			$where['name,domain,path'] = ['like', '%' . $keyword . '%'];
		}

		$site = service('site/Site');
		$list = $site->getListData($where, 'site_id,type,path,name,domain,email,cate_id,view_cache,static_cache,debug,status,add_time,update_time', 0, 0, ['site_id' => 'asc']);
		if (!empty($list)) {
			$language = service('site/LanguageUsed');
			$currency = service('site/CurrencyUsed');
			foreach ($list as $key => $value) {
				$value['language'] = $language->getListCache($value['site_id']);
				$value['currency'] = $currency->getListCache($value['site_id']);
				$list[$key] = $value;
			}
		}

		$this->assign('search', [
			'status'  => $status,
			'keyword' => $keyword,
		]);
		$this->assign('list', $list);
		$this->view();
	}

	public function siteInfo()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['modifySite', 'deleteLanguage', 'addLanguage', 'sortLanguage', 'deleteCurrency', 'addCurrency', 'sortCurrency', 'updateKeyword', 'editLanguage', 'getLanguage', 'transfer'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		$id = (int)iget('id', 0);

		$site = $id > 0 ? service('site/Site')->loadData($id) : [];

		if (!empty($site)) {
			$currencyList = service('system/Currency')->getListData();
			$currencyList = array_column($currencyList, null, 'code');
			$languageList = service('system/Language')->getListData();
			$languageList = array_column($languageList, null, 'code');

			$siteLanguage = service('site/LanguageUsed')->getListData(['site_id' => $id], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
			$siteCurrency = service('site/CurrencyUsed')->getListData(['site_id' => $id], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);

			// 读取站点默认多语言文本 (keyword / description)
			$seoList = service('site/Language')->getListData(['site_id' => $id]);
			$seoMap = [];
			foreach ($seoList as $sRow) {
				$t = $sRow['type'] ?? '';
				if ($t !== '' && !isset($seoMap[$t])) {
					$seoMap[$t] = $sRow['name'] ?? '';
				}
			}
			$site['keyword']     = $seoMap['keyword'] ?? '';
			$site['description'] = $seoMap['description'] ?? '';

			// 读取站点已关联的支付方式
			$paymentUsed = service('payment/Used')->getListData(['site_id' => $id], '*', 0, 0, ['sort' => 'asc', 'item_id' => 'asc']);
			$paymentTypeMap = service('payment/Payment')->getTypeList();
			$paymentAccountMap = [];
			if (!empty($paymentUsed)) {
				$payIds = array_values(array_unique(array_filter(array_column($paymentUsed, 'payment_id'))));
				if (!empty($payIds)) {
					$payRows = service('payment/Payment')->getListData(['payment_id' => ['in', $payIds]], 'payment_id,name,is_sandbox');
					$paymentAccountMap = array_column($payRows, null, 'payment_id');
				}
			}

			$this->assign('currencyList', $currencyList);
			$this->assign('languageList', $languageList);
			$this->assign('siteLanguage', $siteLanguage);
			$this->assign('siteCurrency', $siteCurrency);
			$this->assign('paymentUsed', $paymentUsed);
			$this->assign('paymentTypeMap', $paymentTypeMap);
			$this->assign('paymentAccountMap', $paymentAccountMap);
		}
		$this->assign('id', $id);
		$this->assign('site', $site);
		$this->view();
	}

	protected function getInfo()
	{
		$id = (int)ipost('id', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$info = service('site/Site')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据失败');
		}
		$this->success('获取成功', $info);
	}

	protected function editSite()
	{
		$id          = (int)ipost('site_id', 0);
		$name        = trim((string)ipost('name', ''));
		$type        = trim((string)ipost('type', 'home')) ?: 'home';
		$path        = trim((string)ipost('path', ''));
		$domain      = trim((string)ipost('domain', ''));
		$email       = trim((string)ipost('email', ''));
		$viewCache   = (int)ipost('view_cache', 0) ? 1 : 0;
		$staticCache = (int)ipost('static_cache', 0) ? 1 : 0;
		$debug       = (int)ipost('debug', 0) ? 1 : 0;
		$status      = (int)ipost('status', 0) ? 1 : 0;

		if ($name === '' || $path === '') {
			$this->error('站点名称和模板路径不能为空');
		}

		$data = [
			'name'         => $name,
			'type'         => $type,
			'path'         => $path,
			'domain'       => $domain,
			'email'        => $email,
			'view_cache'   => $viewCache,
			'static_cache' => $staticCache,
			'debug'        => $debug,
			'status'       => $status,
		];

		$service = service('site/Site');
		if ($id <= 0) {
			$result = $service->insertData($data);
		} else {
			$result = $service->updateData($id, $data);
			$service->updateCache($id);
		}
		if ($result !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function modifySite()
	{
		$id = (int)ipost('id', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}

		$allowFields = ['status', 'name', 'path', 'domain', 'email', 'view_cache', 'static_cache', 'debug'];
		$data = [];
		foreach ($allowFields as $f) {
			$val = ipost($f, null);
			if ($val !== null) {
				$data[$f] = in_array($f, ['status', 'view_cache', 'static_cache', 'debug'], true) ? ((int)$val ? 1 : 0) : trim((string)$val);
			}
		}

		if (empty($data)) {
			$this->error('参数不正确');
		}

		$service = service('site/Site');
		$rst = $service->updateData($id, $data);
		if ($rst !== false) {
			$service->updateCache($id);
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteLanguage()
	{
		$id = (int)ipost('id', 0);
		$siteId = (int)ipost('site_id', 0);
		$code = trim((string)ipost('code', ''));
		if ($id <= 0 && ($siteId <= 0 || $code === '')) {
			$this->error('参数不正确');
		}
		$service = service('site/LanguageUsed');
		$where = $id > 0 ? ['item_id' => $id] : ['site_id' => $siteId, 'code' => $code];
		$info = $service->loadData($where, 'site_id,code');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->deleteData($where);
		if ($rst) {
			$service->delCache($info['site_id']);
			service('site/Language')->updateCache($info['site_id'], $info['code']);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addLanguage()
	{
		$siteId = (int)ipost('site_id', 0);
		$code = trim((string)ipost('code', ''));
		if ($siteId <= 0 || $code === '') {
			$this->error('参数不正确');
		}
		$service = service('site/LanguageUsed');
		$where = ['site_id' => $siteId, 'code' => $code];
		if ($service->getCountData($where)) {
			$this->error('语言已存在');
		}
		$rst = $service->insert($where);
		if ($rst) {
			$service->delCache($siteId);
			$this->success('添加成功');
		}
		$this->error('添加失败');
	}

	protected function sortLanguage()
	{
		$id = (int)ipost('id', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$type = trim((string)ipost('type', ''));
		$service = service('site/LanguageUsed');
		$info = $service->loadData($id, 'site_id');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		if ($type !== '') {
			$rst = service('tool/Sort')->sort('site_language_used', $id, $type, 'item_id', 'sort', ['site_id' => (int)$info['site_id']]);
		} else {
			$sort = (int)ipost('sort', 0);
			$rst = $service->updateData($id, ['sort' => $sort]);
		}
		if ($rst !== false) {
			$service->delCache($info['site_id']);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteCurrency()
	{
		$id = (int)ipost('id', 0);
		$siteId = (int)ipost('site_id', 0);
		$code = trim((string)ipost('code', ''));
		if ($id <= 0 && ($siteId <= 0 || $code === '')) {
			$this->error('参数不正确');
		}
		$service = service('site/CurrencyUsed');
		$where = $id > 0 ? ['item_id' => $id] : ['site_id' => $siteId, 'code' => $code];
		$info = $service->loadData($where, 'site_id,code');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->deleteData($where);
		if ($rst) {
			$service->delCache($info['site_id']);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addCurrency()
	{
		$siteId = (int)ipost('site_id', 0);
		$code = trim((string)ipost('code', ''));
		if ($siteId <= 0 || $code === '') {
			$this->error('参数不正确');
		}
		$service = service('site/CurrencyUsed');
		$where = ['site_id' => $siteId, 'code' => $code];
		if ($service->getCountData($where)) {
			$this->error('货币已存在');
		}
		$rst = $service->insert($where);
		if ($rst) {
			$service->delCache($siteId);
			$this->success('添加成功');
		}
		$this->error('添加失败');
	}

	protected function sortCurrency()
	{
		$id = (int)ipost('id', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$type = trim((string)ipost('type', ''));
		$service = service('site/CurrencyUsed');
		$info = $service->loadData($id, 'site_id');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		if ($type !== '') {
			$rst = service('tool/Sort')->sort('site_currency_used', $id, $type, 'item_id', 'sort', ['site_id' => (int)$info['site_id']]);
		} else {
			$sort = (int)ipost('sort', 0);
			$rst = $service->updateData($id, ['sort' => $sort]);
		}
		if ($rst !== false) {
			$service->delCache($info['site_id']);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function updateKeyword()
	{
		$siteId = (int)ipost('site_id', 0);
		$name = trim((string)ipost('name', ''));
		$value = trim((string)ipost('value', ''));
		if ($siteId <= 0 || !in_array($name, ['keyword', 'description'], true)) {
			$this->error('参数不正确');
		}
		service('site/Language')->setNxLanguage($siteId, 'en', $name, $value);
		$this->success('编辑成功');
	}

	protected function getLanguage()
	{
		$siteId = (int)ipost('site_id', 0);
		$type = trim((string)ipost('type', ''));
		if ($siteId <= 0 || $type === '') {
			$this->error('参数不正确');
		}
		$languageList = service('site/LanguageUsed')->getListData(['site_id' => $siteId], 'code', 0, 0, ['sort' => 'asc']);
		if (empty($languageList)) {
			$this->error('该站点暂未配置关联语言，请先添加站点语言');
		}
		$languageList = service('system/Language')->getListData(['code' => ['in', array_column($languageList, 'code')]]);
		$transArr = service('site/Language')->getListData(['site_id' => $siteId, 'type' => $type]);
		$transArr = array_column($transArr, null, 'lan_id');
		foreach ($languageList as $key => $value) {
			$languageList[$key]['tr_text'] = $transArr[$value['code']]['name'] ?? '';
		}
		$this->success('获取成功', $languageList);
	}

	protected function transfer()
	{
		$trCode = trim((string)ipost('tr_code', ''));
		$name = trim((string)ipost('name', ''));
		if ($name === '' || $trCode === '') {
			$this->success('翻译完成', '');
		}
		$rst = service('tool/Translate')->getText($name, $trCode);
		$this->success('翻译完成', $rst);
	}

	protected function editLanguage()
	{
		$siteId = (int)ipost('site_id', 0);
		$type = trim((string)ipost('type', ''));
		$language = ipost('language');
		if ($siteId <= 0 || $type === '' || empty($language) || !is_array($language)) {
			$this->error('参数不正确');
		}
		$service = service('site/Language');
		foreach ($language as $key => $value) {
			$service->setNxLanguage($siteId, $key, $type, trim((string)$value));
		}
		$this->success('保存成功');
	}

	public function staticCache()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteStaticCache'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$files = $this->collectStaticFiles();
		$list = [];
		foreach ($files as $file) {
			$relPath = str_replace([ROOT_PATH, '\\'], ['', '/'], $file);
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			$list[] = [
				'name'   => $relPath,
				'ext'    => $ext,
				'size'   => (int)@filesize($file),
				'c_time' => date('Y-m-d H:i:s', (int)@filemtime($file)),
			];
		}

		$this->assign('list', $list);
		$this->view();
	}

	public function staticDetail()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$name = trim((string)iget('name', ''));
		$content = '';
		if ($name !== '' && strpos($name, '..') === false) {
			$file = ROOT_PATH . ltrim(str_replace('\\', '/', $name), '/');
			if (is_file($file)) {
				$content = (string)file_get_contents($file);
			}
		}
		$this->assign('name', $name);
		$this->assign('content', $content);
		$this->view();
	}

	protected function deleteStaticCache()
	{
		$name = trim((string)ipost('name', ''));
		if ($name === '') {
			$this->error('名称不能为空');
		}
		if ($name === 'all') {
			$files = $this->collectStaticFiles();
			foreach ($files as $file) {
				if (is_file($file)) {
					@unlink($file);
				}
			}
		} else {
			if (strpos($name, '..') !== false) {
				$this->error('非法文件路径');
			}
			$file = ROOT_PATH . ltrim(str_replace('\\', '/', $name), '/');
			if (!is_file($file)) {
				$this->error('文件不存在');
			}
			@unlink($file);
		}
		$this->success('清除成功');
	}

	protected function collectStaticFiles(): array
	{
		$files = [];
		$tplRoot = ROOT_PATH . 'template';
		if (!is_dir($tplRoot)) {
			return $files;
		}
		foreach (scandir($tplRoot) ?: [] as $tplDir) {
			if ($tplDir === '.' || $tplDir === '..') continue;
			foreach (['computer', 'mobile'] as $device) {
				$staticDir = $tplRoot . '/' . $tplDir . '/' . $device . '/static';
				if (is_dir($staticDir)) {
					$this->getFileList($staticDir, $files);
				}
			}
		}
		return $files;
	}

	protected function getFileList($path, &$files)
	{
		if (is_dir($path)) {
			$dp = dir($path);
			while ($file = $dp->read()) {
				if ($file !== '.' && $file !== '..') {
					$this->getFileList($path . '/' . $file, $files);
				}
			}
			$dp->close();
		} elseif (is_file($path)) {
			$files[] = $path;
		}
	}

	public function siteLog()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteLog'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$path = ROOT_PATH . 'runtime';
		$files = [];
		$this->getFileList($path, $files);
		$list = [];
		if (!empty($files)) {
			$mtimeArr = [];
			foreach ($files as $file) {
				$relName = str_replace([$path . '/', '\\'], ['', '/'], $file);
				$mtime = (int)@filemtime($file);
				$logType = 'request';
				if (strpos($relName, '_error.log') !== false) {
					$logType = 'error';
				} elseif (strpos($relName, '_task.log') !== false) {
					$logType = 'task';
				}
				$mtimeArr[] = $mtime;
				$list[] = [
					'name'     => $relName,
					'log_type' => $logType,
					'size'     => (int)@filesize($file),
					'c_time'   => date('Y-m-d H:i:s', $mtime),
				];
			}
			array_multisort($mtimeArr, SORT_DESC, $list);
		}
		$this->assign('list', $list);
		$this->view();
	}

	protected function deleteLog()
	{
		$name = trim((string)ipost('name', ''));
		if ($name === '') {
			$this->error('名称不能为空');
		}
		$path = ROOT_PATH . 'runtime';
		if ($name === 'all') {
			$files = [];
			$this->getFileList($path, $files);
			foreach ($files as $value) {
				if (is_file($value)) {
					@unlink($value);
				}
			}
		} else {
			if (strpos($name, '..') !== false) {
				$this->error('非法文件路径');
			}
			$file = $path . '/' . ltrim(str_replace('\\', '/', $name), '/');
			if (!is_file($file)) {
				$this->error('文件不存在');
			}
			@unlink($file);
		}
		$this->success('删除成功');
	}

	public function logDetail()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$name = trim((string)iget('name', ''));
		$list = [];
		if ($name !== '' && strpos($name, '..') === false) {
			$file = ROOT_PATH . 'runtime/' . ltrim(str_replace('\\', '/', $name), '/');
			if (is_file($file)) {
				$content = trim((string)file_get_contents($file));
				if ($content !== '') {
					if (strpos($content, '---------------------------------------------------------------') !== false) {
						$list = explode('---------------------------------------------------------------', $content);
					} elseif (strpos($content, '[runtime]') !== false) {
						$list = preg_split('/(?=\[runtime\]\s+\d{4}-\d{2}-\d{2})/', $content, -1, PREG_SPLIT_NO_EMPTY);
					} else {
						$list = preg_split('/\r?\n\r?\n/', $content, -1, PREG_SPLIT_NO_EMPTY);
					}
				}
			}
		}
		$this->assign('name', $name);
		$this->assign('list', $list ?: []);
		$this->view();
	}
}