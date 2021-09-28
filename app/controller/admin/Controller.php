<?php

namespace app\controller\admin;
use app\controller\Base;

class Controller extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '功能管理',
		];
		$this->_default = '系统设置';
	}

	public function index()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo'])) {
				$this->$opn();
			}
		}

		html()->addJs();

		$list = make('app/service/function/Controller')->getList();
		$iconList = make('app/service/function/Icon')->getListData();
		$this->assign('iconList', $iconList);
		$this->assign('list', $list);
		$this->_init();
		$this->view();
	}

	protected function getInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = make('app/service/function/Controller')->loadData($id);
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
			$rst = make('app/service/function/Controller')->insert($data);
		} else {
			$rst = make('app/service/function/Controller')->updateData($id, $data);
		}
		if ($rst) {
			$this->success('操作成功');
		} else {
			$this->success('操作失败');
		}
	}
}