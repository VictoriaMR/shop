<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Controller extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '功能管理',
			'icon' => '功能图标管理'
		];
		$this->_default = '系统设置';
		parent::_init();
	}

	public function index()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'deleteInfo', 'sortInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

		$list = service('controller/Controller')->getList();
		$iconList = service('controller/Icon')->getListData([], 'icon_id,name,remark', 0, 0, ['sort'=>'asc']);
		$this->assign('iconList', $iconList);
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

	public function icon()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getIconInfo', 'editIconInfo', 'deleteIconInfo', 'sortIconInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

		$list = service('controller/Icon')->getListData([], 'icon_id,name,sort,remark', 0, 0, ['sort'=>'asc']);

		$this->assign('list', $list);
		$this->view();
	}

	protected function getIconInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$service = service('controller/Icon');
		$info = $service->loadData($id, 'icon_id,name,sort,remark');
		if ($info) {
			$this->success($info);
		}
		$this->error('获取失败');
	}

	protected function editIconInfo()
	{
		$id = ipost('icon_id');
		$name = ipost('name');
		$sort = ipost('sort');
		$remark = ipost('remark');
		if (empty($name)) {
			$this->error('参数不正确');
		}
		$where = ['name' => $name];
		if (!empty($id)) {
			$where['icon_id'] = ['<>', $id];
		}
		$service = service('controller/Icon');
		if ($service->getCountData($where)) {
			$this->error('名称已经存在');
		}
		$data = [
			'name' => $name,
			'sort' => $sort,
			'remark' => $remark,
		];
		if (empty($id)) {
			$rst = $service->insert($data);
		} else {
			$rst = $service->updateData($id, $data);
		}
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteIconInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$rst = service('controller/Icon')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function sortIconInfo()
	{
		$id = ipost('id');
		$sort = ipost('sort');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$rst = service('controller/Icon')->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}
}