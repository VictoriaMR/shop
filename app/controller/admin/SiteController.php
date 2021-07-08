<?php

namespace app\controller\admin;

use app\controller\Controller;
use frame\Html;

class SiteController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '站点配置',
            'staticCache' => 'CSS/JS缓存',
            'siteLog' => '站点日志',
        ];
        $this->_default = '站点管理';
		$this->_init();
	}

	public function index()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editSite', 'getSiteLanguage', 'getTransfer', 'editLanguage', 'getInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		Html::addJs();
		$page = iget('page', 1);
		$size = iget('size', 20);
		$total = make('App\Services\SiteService')->getCount([]);
		if ($total > 0) {
			$list = make('App\Services\SiteService')->getListByWhere([], '*', $page, $size);
		}

		//语言列表
		$language = make('App\Services\LanguageService')->getInfo();

		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('language', $language);
		return view();
	}

	protected function getInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = make('App\Services\SiteService')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据失败');
		} else {
			$this->success($info, '获取成功');
		}
	}

	protected function editSite()
	{
		$id = (int) ipost('site_id');
		$name = trim(ipost('name'));
		$domain = trim(ipost('domain'));
		$title = trim(ipost('title'));
		$keyword = trim(ipost('keyword'));
		$description = trim(ipost('description'));
		$data = [
			'name' => $name,
			'domain' => $domain,
			'title' => $title,
			'keyword' => $keyword,
			'description' => $description,
		];
		if (empty($id)) {
			$result = make('App\Services\SiteService')->insert($data);
		} else {
			$result = make('App\Services\SiteService')->updateDataById($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function getSiteLanguage()
	{
		$name = trim(ipost('name'));
		$id = (int) ipost('id');
		if (empty($name) || empty($id)) {
			$this->error('参数不正确');
		}
		$where = [
			'site_id' => $id,
			'name' => $name,
		];
		$info = make('App\Services\SiteService')->getLanguage($where);
		$this->success($info ?? [], '');
	}

	protected function getTransfer()
	{
		$value = trim(ipost('value'));
		$code = trim(ipost('code'));
		if (empty($value)) {
			$this->error('翻译文本为空');
		}
		if (empty($code)) {
			$this->error('翻译类型为空');
		}
		if ($code == 'zh') {
			$this->success($value, '');
		}
		$result = make('App\Services\TranslateService')->getTranslate($value, $code);
		$this->success($result, '');
	}

	protected function editLanguage()
	{
		$name = trim(ipost('name'));
		$siteId = (int)ipost('site_id');
		if (empty($name) || empty($siteId)) {
			$this->error('参数不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('App\Services\SiteService');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($siteId, $name, $key, $value);
			}
		}
		$this->success('操作成功');
	}

	public function staticCache()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteStaticCache'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		Html::addJs();
		$files = [];
		$siteList = make('App\Services\SiteService')->getListByWhere([], 'name');
		foreach ($siteList as $key => $value) {
			$path = ROOT_PATH.$value['name'].DS.'static';
			$this->getFileList($path, $files);
		}
		if (!empty($files)) {
			$list = [];
			foreach ($files as $key => $value) {
				$list[] = [
					'name' => str_replace(ROOT_PATH, '', $value),
					'size' => filesize($value),
					'c_time' => date('Y-m-d H:i:s', filemtime($value)),
				];
			}
		}

		$this->assign('list', $list ?? []);
		return view();
	}

	protected function deleteStaticCache()
	{
		$name = ipost('name');
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		if ($name == 'all') {
			$files = [];
			$siteList = make('App\Services\SiteService')->getListByWhere([], 'name');
			foreach ($siteList as $key => $value) {
				$path = ROOT_PATH.$value['name'].DS.'static';
				$this->getFileList($path, $files);
			}
			if (!empty($files)) {
				foreach ($files as $key => $value) {
					unlink($value);
				}
			}
		} else {
			$file = ROOT_PATH.$name;
			if (!is_file($file)) {
				$this->error('非法请求, 文件不存在');
			}
			unlink($file);
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
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteLog'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		Html::addJs();
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
		return view();
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
			if (!empty($files)) {
				foreach ($files as $key => $value) {
					unlink($value);
				}
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
		$this->_arr['logDetail'] = '日志详情';
		$this->_init();
		$name = iget('name');
		$file = ROOT_PATH.'runtime'.DS.$name;
		if (is_file($file)) {
			$content = file_get_contents($file);
			$list = explode('---------------------------------------------------------------', $content);
		}
		$this->assign('list', $list ?? []);
		return view();
	}
}