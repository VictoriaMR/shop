<?php

namespace app\controller\admin;

use app\controller\Controller;
use frame\Html;

class CategoryController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '分类列表',
        ];
		$this->_default = '分类管理';
		$this->_init();
	}

	public function index()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'updateStat'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		Html::addCss();
		Html::addJs();
		$list = make('App\Services\CategoryService')->getListFormat();
		//语言列表
		$language = make('App\Services\LanguageService')->getInfo();

		$this->assign('language', $language);
		$this->assign('list', $list);
		return view();
	}

	protected function sortCategory()
	{
		$data = ipost('data');
		if (empty($data)) {
			$this->error('数据正确');
		}
		$arr = array_keys($data);
		$services = make('App\Services\CategoryService');
		$count = 1;
		foreach ($data as $key => $value) {
			$services->updateData((int) $key, ['sort'=>$count++]);
			foreach ($value as $k => $v) {
				$services->updateData((int) $v, ['sort'=>$k+1]);
			}
		}
		$this->success('排序成功');
	}

	protected function getCateInfo()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('App\Services\CategoryService')->getInfo($cateId);
		$this->success($info, '');
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('App\Services\CategoryService')->getLanguage($cateId);
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
			$services = make('App\Services\CategoryService');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, $value);
			}
		}
		$this->success('操作成功');
	}

	protected function editInfo()
	{
		$cateId = (int) ipost('cate_id');
		$parentId = (int) ipost('parent_id');
		$name = trim(ipost('name'));
		$avatar = trim(ipost('avatar'));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'parent_id' => $parentId,
			'name' => $name,
			'avatar' => $avatar,
		];
		if (empty($cateId)) {
			$result = make('App\Services\CategoryService')->create($data);
		} else {
			$result = make('App\Services\CategoryService')->updateDataById($cateId, $data);
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
		$services = make('App\Services\CategoryService');
		if ($services->hasChildren($cateId)) {
			$this->error('该分类有子分类, 不能删除');
		}
		if ($services->hasProduct($cateId)) {
			$this->error('该分类有产品, 不能删除');
		}
		$result = $services->deleteDataById($cateId);
		if ($result) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	protected function updateStat()
	{
		$result = make('App\Services\CategoryService')->updateStat();
		if ($result) {
			$this->success('更新成功');
		} else {
			$this->error('更新失败');
		}
	}
}