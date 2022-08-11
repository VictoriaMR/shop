<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Site extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '站点列表',
			'staticCache' => '静态文件管理',
			'siteLog' => '站点日志',
		];
		$this->_default = '站点管理';
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editSite', 'getInfo', 'modifySite', 'modifySite', 'deleteLanguage', 'addLanguage', 'sortLanguage', 'domainInfo', 'deleteDomain', 'editDomain', 'modifyDomain', 'deleteCurrency', 'addCurrency', 'sortCurrency', 'updateKeyword', 'editLanguage', 'getLanguage', 'transfer'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$site = make('app/service/site/Site');
		$list = $site->getListData([], 'site_id,name,path,status,remark,add_time');
		if (!empty($list)) {
			//获取站点对应的语言和货币
			$language = make('app/service/site/LanguageUsed');
			$currency = make('app/service/site/CurrencyUsed');
			foreach ($list as $key => $value) {
				$value['language'] = $language->getListCache($value['site_id']);
				$value['currency'] = $currency->getListCache($value['site_id']);
				$list[$key] = $value;
			}
		}
		$this->assign('list', $list);
		$this->_init();
		$this->view();
	}

	public function siteInfo()
	{
		html()->addCss();
		html()->addJs();
		$id = iget('id');

		$site = make('app/service/site/Site')->loadData($id);

		if (!empty($site)) {
			$currencyList = make('app/service/currency/Currency')->getListData();
			$currencyList = array_column($currencyList, null, 'code');
			$languageList = make('app/service/Language')->getListData();
			$languageList = array_column($languageList, null, 'code');
			//语言关联
			$siteLanguage = make('app/service/site/LanguageUsed')->getListData(['site_id' => $id], '*', 0, 0, ['sort'=>'asc']);
			//货币关联
			$siteCurrency = make('app/service/site/CurrencyUsed')->getListData(['site_id' => $id], '*', 0, 0, ['sort'=>'asc']);
			$this->assign('currencyList', $currencyList);
			$this->assign('languageList', $languageList);
			$this->assign('siteLanguage', $siteLanguage);
			$this->assign('siteCurrency', $siteCurrency);
		}
		$this->assign('id', $id);
		$this->assign('site', $site);
		$this->_arr['siteInfo'] = '站点配置';
		$this->_init();
		$this->view();
	}

	protected function getInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = make('app/service/site/Site')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据失败');
		} else {
			$this->success($info, '获取成功');
		}
	}

	protected function editSite()
	{
		$id = ipost('site_id');
		$name = ipost('name');
		$path = ipost('path');
		$keyword = ipost('keyword');
		$description = ipost('description');
		$data = [
			'name' => $name,
			'path' => $path,
			'keyword' => $keyword,
			'description' => $description,
		];
		if (empty($id)) {
			$result = make('app/service/site/Site')->insert($data);
		} else {
			$result = make('app/service/site/Site')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function modifySite()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$status = ipost('status');
		$name = ipost('name');
		$path = ipost('path');
		$keyword = ipost('keyword');
		$description = ipost('description');
		$remark = ipost('remark');
		$data = [];
		if (!is_null($status)) $data['status'] = $status;
		if (!is_null($name)) $data['name'] = $name;
		if (!is_null($path)) $data['path'] = $path;
		if (!is_null($keyword)) $data['keyword'] = $keyword;
		if (!is_null($description)) $data['description'] = $description;
		if (!is_null($remark)) $data['remark'] = $remark;
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/Site');
		$rst = $service->updateData($id, $data);
		if ($rst) {
			if (empty($status)) $service->updateCache($id);
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function editDomain()
	{
		$id = ipost('domain_id');
		$domain = ipost('domain');
		$status = ipost('status');
		$siteId = ipost('site_id');
		$remark = ipost('remark');
		$where = ['domain'=>$domain];
		if (!empty($id)) {
			$where['domain_id'] = ['<>', $id];
		}
		$service = make('app/service/site/Domain');
		if ($service->getCountData($where)) {
			$this->error('域名已经存在');
		}
		$data = [
			'domain' => $domain,
			'status' => $status,
			'site_id' => $siteId,
			'remark' => $remark,
		];
		if (empty($id)) {
			$rst = $id = $service->insertGetId($data);
		} else {
			$rst = $service->updateData($id, $data);
		}
		if ($rst) {
			$service->updateCache($id);
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function modifyDomain()
	{
		$id = ipost('id');
		$status = ipost('status');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/Domain');
		$rst = $service->updateData($id, ['status'=>$status]);
		if ($rst) {
			$service->updateCache($id);
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteDomain()
	{
		$id = ipost('id');
		$siteId = ipost('site_id');
		$domain = ipost('domain');
		if (empty($id) && (empty($siteId) || empty($domain))) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/Domain');
		$where = [];
		if (empty($id)) {
			$where['site_id'] = $siteId;
			$where['domain'] = $domain;
		} else {
			$where['domain_id'] = $id;
		}
		$info = $service->loadData($where, 'domain');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->deleteData($id);
		if ($rst) {
			$service->updateCacheByDomain($info['domain']);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function deleteLanguage()
	{
		$id = ipost('id');
		$siteId = ipost('site_id');
		$code = ipost('code');
		if (empty($id) && (empty($siteId) || empty($code))) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/LanguageUsed');
		$where = [];
		if (empty($id)) {
			$where['site_id'] = $siteId;
			$where['code'] = $code;
		} else {
			$where['item_id'] = $id;
		}
		$info = $service->loadData($where, 'site_id,code');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->deleteData($where);
		if ($rst) {
			//删除站点翻译缓存
			make('app/service/site/Language')->updateCache($info['site_id'], $info['code']);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addLanguage()
	{
		$siteId = ipost('site_id');
		$code = ipost('code');
		if (empty($siteId) || empty($code)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/LanguageUsed');
		$where = [];
		$where['site_id'] = $siteId;
		$where['code'] = $code;
		if ($service->getCountData($where)) {
			$this->error('语言已存在');
		}
		$rst = $service->insert($where);
		if ($rst) {
			$this->success('添加成功');
		}
		$this->error('添加失败');
	}

	protected function sortLanguage()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort');
		$service = make('app/service/site/LanguageUsed');
		$info = $service->loadData($id, 'site_id');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$service->delCache($info['site_id']);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteCurrency()
	{
		$id = ipost('id');
		$siteId = ipost('site_id');
		$code = ipost('code');
		if (empty($id) && (empty($siteId) || empty($code))) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/CurrencyUsed');
		$where = [];
		if (empty($id)) {
			$where['site_id'] = $siteId;
			$where['code'] = $code;
		} else {
			$where['item_id'] = $id;
		}
		$info = $service->loadData($where, 'site_id,code');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->deleteData($where);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addCurrency()
	{
		$siteId = ipost('site_id');
		$code = ipost('code');
		if (empty($siteId) || empty($code)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/CurrencyUsed');
		$where = [];
		$where['site_id'] = $siteId;
		$where['code'] = $code;
		if ($service->getCountData($where)) {
			$this->error('语言已存在');
		}
		$rst = $service->insert($where);
		if ($rst) {
			$this->success('添加成功');
		}
		$this->error('添加失败');
	}

	protected function sortCurrency()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort');
		$service = make('app/service/site/CurrencyUsed');
		$info = $service->loadData($id, 'site_id');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = $service->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$service->delCache($info['site_id']);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function updateKeyword()
	{
		$siteId = ipost('site_id');
		$name = ipost('name');
		$value = ipost('value');
		if (empty($siteId) || empty($name)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/site/Site')->updateData($siteId, [$name=>$value]);
		if ($rst) {
			$this->success('编辑成功');
		}
		$this->error('编辑失败');
	}

	protected function getLanguage()
	{
		$siteId = ipost('site_id');
		$type = ipost('type');
		if (empty($siteId) && empty($type)) {
			$this->error('参数不正确');
		}
		//站点语言列表
		$languageList = make('app/service/site/LanguageUsed')->getListData(['site_id'=>$siteId], 'code', 0, 0, ['sort'=>'asc']);
		if (empty($languageList)) {
			$this->error('该站点没有配置语言');
		}
		$languageList = make('app/service/Language')->getListData(['code'=>['in', array_column($languageList, 'code')]]);
		//获取已有的翻译文本
		$transArr = make('app/service/site/Language')->getListData(['site_id'=>$siteId, 'type'=>$type]);
		$transArr = array_column($transArr, null, 'lan_id');
		foreach ($languageList as $key => $value) {
			$languageList[$key]['tr_text'] = empty($transArr[$value['code']]) ? '' : $transArr[$value['code']]['name'];
		}
		$this->success($languageList);
	}

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$name = ipost('name');
		$rst = make('app/service/Translate')->getText($name, $trCode);
		$this->success($rst, '');
	}

	protected function editLanguage()
	{
		$siteId = ipost('site_id');
		$type = ipost('type');
		$language = ipost('language');
		if (empty($siteId) || empty($type) || empty($language)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/Language');
		foreach ($language as $key => $value) {
			$service->setNxLanguage($siteId, $key, $type, $value);
		}
		$this->success('保存成功');
	}

	protected function domainInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$service = make('app/service/site/Domain');
		$info = $service->loadData($id);
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$this->success($info);
	}

	public function staticCache()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteStaticCache'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$files = [];
		$siteList = make('app/service/site/Site')->getListData([], 'path');
		foreach ($siteList as $key => $value) {
			$path = ROOT_PATH.$value['path'].DS.'static';
			$this->getFileList($path, $files);
		}
		$list = [];
		foreach ($files as $key => $value) {
			$list[] = [
				'name' => str_replace(ROOT_PATH, '', $value),
				'size' => filesize($value),
				'c_time' => date('Y-m-d H:i:s', filemtime($value)),
			];
		}

		$this->assign('list', $list);
		$this->_init();
		$this->view();
	}

	public function staticDetail()
	{
		$name = iget('name');
		$file = ROOT_PATH.$name;
		if (is_file($file)) {
			$content = file_get_contents($file);
			$this->assign('content', $content);
		}
		$this->_arr['staticDetail'] = '静态文件详情';
		$this->_init();
		$this->view();
	}

	protected function deleteStaticCache()
	{
		$name = ipost('name');
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		if ($name == 'all') {
			$files = [];
			$siteList = make('app/service/site/Site')->getListData([], 'path');
			foreach ($siteList as $key => $value) {
				$path = ROOT_PATH.$value['path'].DS.'static';
				$this->getFileList($path, $files);
			}
			if (!empty($files)) {
				foreach ($files as $key => $value) {
					unlink($value);
				}
			}
			make('app/service/site/StaticFile')->deleteData(['static_id'=>['>', 0]]);
		} else {
			$file = ROOT_PATH.$name;
			if (!is_file($file)) {
				$this->error('非法请求, 文件不存在');
			}
			unlink($file);
			list($name, $type) = explode('.', $name);
			make('app/service/site/StaticFile')->deleteData(['name'=>$name, 'type'=>$type]);
		}
		$this->success('操作成功');
	}

	protected function getFileList($path, &$files)
	{
		if (is_dir($path)) {
			$dp = dir($path);
			while ($file = $dp ->read()){
	            if($file != '.' && $file != '..') {
	                $this->getFileList($path.DS.$file, $files);
	            }
	        }
		} else if (is_file($path)) {
			$files[] = $path;
		}
	}

	public function siteLog()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteLog'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$path = ROOT_PATH.'runtime';
		$files = [];
		$this->getFileList($path, $files);
		if (!empty($files)) {
			$list = [];
			$timeArr = [];
			foreach ($files as $key => $value) {
				$time = str_replace([$path, '.log', DS], '', $value);
				$time = substr($time, 0, 4).'-'.substr($time, 4, 2).'-'.substr($time, 6, 2);
				$timeArr[] = strtotime($time);
				$list[] = [
					'name' => str_replace($path.DS, '', $value),
					'size' => filesize($value),
					'c_time' => $time,
				];
			}
			array_multisort($timeArr, SORT_DESC, $list);
		}
		$this->assign('list', $list ?? []);
		$this->_init();
		$this->view();
	}

	protected function deleteLog()
	{
		$name = ipost('name');
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		if ($name == 'all') {
			$files = [];
			$path = ROOT_PATH.'runtime';
			$this->getFileList($path, $files);
			foreach ($files as $key => $value) {
				unlink($value);
			}
		} else {
			$file = ROOT_PATH.'runtime'.DS.$name;
			if (!is_file($file)) {
				$this->error('非法请求, 文件不存在');
			}
			unlink($file);
		}
		$this->success('操作成功');
	}

	public function logDetail()
	{
		$name = iget('name');
		$file = ROOT_PATH.'runtime'.DS.$name;
		if (is_file($file)) {
			$content = file_get_contents($file);
			$list = explode('---------------------------------------------------------------', $content);
		}
		$this->_arr['logDetail'] = '日志详情';
		$this->assign('list', $list ?? []);
		$this->_init();
		$this->view();
	}
}