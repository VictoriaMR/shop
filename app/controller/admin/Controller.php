<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Controller extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '功能管理',
		];
		$this->_default = '系统设置';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'deleteInfo', 'sortInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

		$list = service('controller/Controller')->getList();
		
		$this->assign('list', $list);
		$this->view();
	}

	protected function getInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = service('controller/Controller')->loadData($id);
		if (empty($info)) {
			$this->error('查询不到有效数据');
		}
		$this->success($info, '');
	}

	protected function editInfo()
	{
		$id = ipost('con_id');
		$name = ipost('name');
		$value = ipost('value');
		$icon = ipost('icon');
		$sort = ipost('sort');
		if (empty($name) || empty($value) || empty($icon)) {
			$this->error('参数不正确');
		}
		$data = [
			'name' => $name,
			'value' => $value,
			'icon' => $icon,
			'sort' => $sort,
		];
		if (empty($id)) {
			$data['parent_id'] = ipost('parent_id');
			$rst = service('controller/Controller')->insert($data);
		} else {
			$rst = service('controller/Controller')->updateData($id, $data);
		}
		if ($rst) {
			$this->success('操作成功');
		} else {
			$this->success('操作失败');
		}
	}

	protected function deleteInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$service = service('controller/Controller');
		if ($service->getCountData(['parent_id'=>$id])) {
			$this->error('此功能下有子功能, 不能删除');
		}
		$res = $service->deleteData($id);
		if ($res) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function sortInfo()
	{
		$id = ipost('id');
		$sort = ipost('sort');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$rst = service('controller/Controller')->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}
}