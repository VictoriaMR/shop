<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Desc extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '描述名管理',
			'descValue' => '描述值管理',
			'descGroup' => '描述分组管理',
		];
		$this->_default = '描述管理';
		parent::_init();
	}

	public function index()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getDescInfo', 'getDescLanguage', 'transfer', 'editDescInfo', 'editDescLanguage', 'deleteDescInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

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
		$nameService = service('desc/Name');
		$total = $nameService->getCountData($where);
		if ($total > 0) {
			$list = $nameService->getList($where, $page, $size);
		}

		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	protected function getDescInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/Name')->loadData($id);
		$this->success($info, '');
	}

	protected function getDescLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/NameLanguage')->getListData(['descn_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = service('Language')->getTransList();
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

	protected function editDescInfo()
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
			$result = service('desc/Name')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = service('desc/Name')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function editDescLanguage()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$language = array_filter(ipost('language'));
		if (empty($language)) {
			$this->error('翻译值不能全部为空');
		}
		$services = service('desc/NameLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(service('Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		service('desc/Name')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteDescInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = service('desc/Name')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = service('product/DescUsed')->deleteData(['descn_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	public function descValue()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getDescValueInfo', 'getDescValueLanguage', 'transfer', 'editDescValueInfo', 'editDescValueLanguage', 'deleteDescValueInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

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
		$valueService = service('desc/Value');
		$total = $valueService->getCountData($where); 
		if ($total > 0) {
			$list = $valueService->getList($where, $page, $size);
		}

		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	protected function getDescValueInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/Value')->loadData($id);
		$this->success($info, '');
	}

	protected function getDescValueLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/ValueLanguage')->getListData(['descv_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = service('Language')->getTransList();
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

	protected function editDescValueInfo()
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
			$result = service('desc/Value')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = service('desc/Value')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function editDescValueLanguage()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$language = array_filter(ipost('language'));
		if (empty($language)) {
			$this->error('翻译值不能全部为空');
		}
		$services = service('desc/ValueLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(service('Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		service('desc/Value')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteDescValueInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = service('desc/Value')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = service('product/AttrUsed')->deleteData(['attv_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	public function descGroup()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getDescGroupInfo', 'getDescGroupLanguage', 'transfer', 'editDescGroupInfo', 'editDescGroupLanguage', 'deleteDescGroupInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addJs();

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
		$nameService = service('desc/Group');
		$total = $nameService->getCountData($where);
		if ($total > 0) {
			$list = $nameService->getList($where, $page, $size);
		}

		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	protected function getDescGroupInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/Group')->loadData($id);
		$this->success($info, '');
	}

	protected function getDescGroupLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('desc/GroupLanguage')->getListData(['descn_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = service('Language')->getTransList();
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

	protected function editDescGroupInfo()
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
			$result = service('desc/Group')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = service('desc/Group')->updateData($id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function editDescGroupLanguage()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$language = array_filter(ipost('language'));
		if (empty($language)) {
			$this->error('翻译值不能全部为空');
		}
		$services = service('desc/GroupLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(service('Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		service('desc/Group')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteDescGroupInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = service('desc/Group')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = service('product/DescUsed')->updateData(['descg_id'=>$id], ['descg_id'=>0]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
}