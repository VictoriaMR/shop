<?php

namespace app\controller\admin;
use app\controller\Base;

class Category extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '品类管理',
		];
		$this->_default = '品类管理';
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'updateStat', 'transfer'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addCss();
		html()->addJs();

		$list = make('app/service/category/Category')->getListFormat();
		$cateArr = array_column($list, 'cate_id');
		$cateArr = make('app/service/category/Language')->where(['cate_id'=>['in', $cateArr]])->field('count(*) as count, cate_id')->groupBy('cate_id')->get();
		$cateArr = array_column($cateArr, 'count', 'cate_id');
		$languageList = make('app/service/Language')->getListCache();
		$languageList = array_column($languageList, null, 'code');
		unset($languageList['zh']);
		$len = count($languageList);
		foreach ($list as $key => $value) {
			$value['is_translate'] = empty($cateArr[$value['cate_id']]) ? 0 : ($cateArr[$value['cate_id']] < $len ? 1 : 2);
			$list[$key] = $value;
		}
		
		$this->_init();
		$this->assign('list', $list);
		$this->view();
	}

	protected function getCateInfo()
	{
		$cateId = (int)ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/category/Category')->loadData($cateId);
		$this->success($info, '');
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/category/Language')->getListData(['cate_id'=>$cateId]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getListCache();
		foreach ($languageList as $key => $value) {
			if ($value['code'] == 'zh') continue;
			$info[$value['code']] = [
				'lan_id' => $value['code'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['code']]) ? '' : $info[$value['code']]['name'],
				'language_name' => $value['name2'],
			];
		}
		$this->success($info, '');
	}

	protected function editLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/category/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, strTrim($value));
			}
		}
		$this->addLog('修改分类语言-'.$cateId);
		$this->success('操作成功');
	}

	protected function editInfo()
	{
		$cateId = (int) ipost('cate_id');
		$parentId = (int) ipost('parent_id');
		$name = trim(ipost('name'));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'parent_id' => $parentId,
			'name' => $name,
		];
		if (empty($cateId)) {
			$result = make('app/service/category/Category')->insert($data);
			$this->addLog('新增分类-'.$result);
		} else {
			$this->addLog('修改分类信息-'.$cateId);
			$result = make('app/service/category/Category')->updateData($cateId, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function deleteCategory()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$services = make('app/service/category/Category');
		if ($services->hasChildren($cateId)) {
			$this->error('该分类有子分类, 不能删除');
		}
		if ($services->hasProduct($cateId)) {
			$this->error('该分类有产品, 不能删除');
		}
		$result = $services->deleteDataById($cateId);
		if ($result) {
			$this->addLog('删除分类语言-'.$cateId);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	protected function updateStat()
	{
		$result = make('app/service/category/Category')->updateStat();
		if ($result) {
			$this->success('更新成功');
		} else {
			$this->error('更新失败');
		}
	}
}