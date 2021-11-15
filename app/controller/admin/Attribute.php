<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Attribute extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '属性管理',
			'attrValue' => '属性值管理',
			'description' => '描述值管理',
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
		$keyword = trim(iget('keyword'));
		$status = iget('status', -1);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		$buteService = make('app/service/attr/Bute');
		$total = $buteService->getCountData($where); 
		if ($total > 0) {
			$list = $buteService->getList($where, $page, $size);
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
		$info = make('app/service/attr/Bute')->loadData($id);
		$this->success($info, '');
	}

	protected function getAttrLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/ButeLanguage')->getListData(['attr_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getTransList();
		foreach ($languageList as $key => $value) {
			$info[$value['code']] = [
				'lan_id' => $value['code'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['code']]) ? '' : $info[$value['code']]['name'],
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
			$result = make('app/service/attr/Bute')->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			$this->addLog('修改属性信息-'.$id);
			$result = make('app/service/attr/Bute')->updateData($id, $data);
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
		$services = make('app/service/attr/ButeLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(make('app/service/Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		make('app/service/attr/Bute')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteAttrInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = make('app/service/attr/Bute')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = make('app/service/product/AttrUsed')->deleteData(['attr_id'=>$id]);
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
		$keyword = trim(iget('keyword'));
		$status = iget('status', -1);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		$buteService = make('app/service/attr/Value');
		$total = $buteService->getCountData($where); 
		if ($total > 0) {
			$list = $buteService->getList($where, $page, $size);
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
		$info = make('app/service/attr/ValueLanguage')->getListData(['attv_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getTransList();
		foreach ($languageList as $key => $value) {
			$info[$value['code']] = [
				'lan_id' => $value['code'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['code']]) ? '' : $info[$value['code']]['name'],
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
			$rst = make('app/service/product/AttributeUsed')->deleteData(['attv_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	public function description()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getDescInfo', 'getDescLanguage', 'transfer', 'editDescInfo', 'editDescLanguage', 'deleteDescInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 40);
		$keyword = trim(iget('keyword'));
		$status = iget('status', -1);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		$buteService = make('app/service/attr/Description');
		$total = $buteService->getCountData($where); 
		if ($total > 0) {
			$list = $buteService->getList($where, $page, $size);
		}

		$this->_init();
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
		$info = make('app/service/attr/Description')->loadData($id);
		$this->success($info, '');
	}

	protected function getDescLanguage()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/attr/DescriptionLanguage')->getListData(['desc_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getTransList();
		foreach ($languageList as $key => $value) {
			$info[$value['code']] = [
				'lan_id' => $value['code'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['code']]) ? '' : $info[$value['code']]['name'],
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
		$descService = make('app/service/attr/Description');
		if (empty($id)) {
			$result = $descService->insert($data);
			$this->addLog('新增属性-'.$result);
		} else {
			if ($descService->getCountData(['desc_id'=>['<>', $id], 'name'=>$name])) {
				$this->error('名称已存在');
			}
			$this->addLog('修改属性信息-'.$id);
			$result = $descService->updateData($id, ['name'=>$name]);
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
		$services = make('app/service/attr/DescriptionLanguage');
		foreach ($language as $key => $value) {
			$services->setNxLanguage($id, $key, strTrim($value));
		}
		$len = count(make('app/service/Language')->getTransList());
		if ($len <= count($language)) {
			$status = 2;
		} else {
			$status = 1;
		}
		make('app/service/attr/Description')->updateData($id, ['status'=>$status]);
		$this->addLog('修改属性语言-'.$id);
		$this->success('操作成功');
	}

	protected function deleteDescInfo()
	{
		$id = (int) ipost('id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = make('app/service/attr/Description')->deleteData($id);
		if ($rst) {
			//删除属性关联
			$rst = make('app/service/product/DescriptionUsed')->deleteData(['name_id,value_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
}