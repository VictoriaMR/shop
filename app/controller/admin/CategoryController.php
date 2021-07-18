<?php

namespace app\controller\admin;
use app\controller\Controller;

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
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'updateStat'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addCss();
		html()->addJs();
		$list = make('app/service/CategoryService')->getListFormat();
		//语言列表
		$language = make('app/service/LanguageService')->getInfo();

		$this->assign('language', $language);
		$this->assign('list', $list);
		$this->view();
	}

	protected function sortCategory()
	{
		$data = ipost('data');
		if (empty($data)) {
			$this->error('数据正确');
		}
		$arr = array_keys($data);
		$services = make('app/service/CategoryService');
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
		$info = make('app/service/CategoryService')->getInfo($cateId);
		$this->success($info, '');
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/CategoryService')->getLanguage($cateId);
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
			$services = make('app/service/CategoryService');
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
			$result = make('app/service/CategoryService')->create($data);
		} else {
			$result = make('app/service/CategoryService')->updateDataById($cateId, $data);
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
		$services = make('app/service/CategoryService');
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
		$result = make('app/service/CategoryService')->updateStat();
		if ($result) {
			$this->success('更新成功');
		} else {
			$this->error('更新失败');
		}
	}
}