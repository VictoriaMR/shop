<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Attribute extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '属性名管理',
			'attrValue' => '属性值管理',
		];
		$this->_default = '属性管理';
	}

	public function index()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getAttrInfo', 'getAttrLanguage', 'transfer', 'editAttrInfo', 'editAttrLanguage', 'deleteAttrInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 40);
		$keyword = trim(iget('keyword', ''));
		$status = iget('status', -1);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		$nameService = make('app/service/attr/Name');
		$total = $nameService->getCountData($where); 
		if ($total > 0) {
			$list = $nameService->getList($where, $page, $size);
		}

		$this->_init();
		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	protected function getAttrInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/Name')->loadData($id);
		$this->success($info, '');
	}

	protected function getAttrLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/NameLanguage')->getListData(['attrn_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getTransList();
		foreach ($languageList as $key => $value) {
			$info[$value['lan_id']] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['lan_id']]) ? '' : $info[$value['lan_id']]['name'],
				'language_name' => $value['name'],
			];
		}
		$this->success($info, '');
	}

	protected function editAttrInfo()
	{
		$id = (int) ipost('id');
		$name = trim(ipost('name'));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'name' => $name,
		];
		if (empty($id)) {
			$result = make('app/service/attr/Name')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = make('app/service/attr/Name')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function editAttrLanguage()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$language = array_filter(ipost('language'));
		if (empty($language)) {
			$this->error('翻译值不能全部为空');
		}
		$services = make('app/service/attr/NameLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(make('app/service/Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		make('app/service/attr/Name')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteAttrInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = make('app/service/attr/Name')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = make('app/service/product/AttrUsed')->deleteData(['attrn_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	public function attrValue()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getAttvInfo', 'getAttvLanguage', 'transfer', 'editAttvInfo', 'editAttvLanguage', 'deleteAttvInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 40);
		$keyword = trim(iget('keyword', ''));
		$status = iget('status', -1);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		$valueService = make('app/service/attr/Value');
		$total = $valueService->getCountData($where); 
		if ($total > 0) {
			$list = $valueService->getList($where, $page, $size);
		}

		$this->_init();
		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	protected function getAttvInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/Value')->loadData($id);
		$this->success($info, '');
	}

	protected function getAttvLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/ValueLanguage')->getListData(['attrv_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getTransList();
		foreach ($languageList as $key => $value) {
			$info[$value['lan_id']] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['lan_id']]) ? '' : $info[$value['lan_id']]['name'],
				'language_name' => $value['name'],
			];
		}
		$this->success($info, '');
	}

	protected function editAttvInfo()
	{
		$id = (int) ipost('id');
		$name = trim(ipost('name'));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'name' => $name,
		];
		if (empty($id)) {
			$result = make('app/service/attr/Value')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = make('app/service/attr/Value')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function editAttvLanguage()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$language = array_filter(ipost('language'));
		if (empty($language)) {
			$this->error('翻译值不能全部为空');
		}
		$services = make('app/service/attr/ValueLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(make('app/service/Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		make('app/service/attr/Value')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteAttvInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = make('app/service/attr/Value')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = make('app/service/product/AttrUsed')->deleteData(['attrv_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
}