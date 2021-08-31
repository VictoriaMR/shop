<?php

namespace app\controller\admin;
use app\controller\Base;

class Attribute extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '属性管理',
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

		html()->addCss();
		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 40);
		$keyword = trim(iget('keyword'));

		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		$buteService = make('app/service/attr/Bute');
		$total = $buteService->getCountData($where); 
		if ($total > 0) {
			$list = $buteService->getListData($where, '*', $page, $size, ['attr_id'=>'desc']);

			$tempArr = array_column($list, 'attr_id');
			$tempArr = make('app/service/attr/ButeLanguage')->where(['attr_id'=>['in', $tempArr]])->field('count(*) as count, attr_id')->groupBy('attr_id')->get();
			$tempArr = array_column($tempArr, 'count', 'attr_id');
			$languageList = make('app/service/Language')->getListCache();
			$languageList = array_column($languageList, null, 'code');
			unset($languageList['zh']);
			$len = count($languageList);
			foreach ($list as $key => $value) {
				$value['is_translate'] = empty($tempArr[$value['attr_id']]) ? 0 : ($tempArr[$value['attr_id']] < $len ? 1 : 2);
				$list[$key] = $value;
			}

		}

		$this->_init();
		$this->assign('total', $total);
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
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/attr/ButeLanguage');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($id, $key, strTrim($value));
			}
		}
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
			$rst = make('app/service/product/AttributeRelation')->deleteData(['attr_id'=>$id]);
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

		html()->addCss();
		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 40);
		$keyword = trim(iget('keyword'));

		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		$buteService = make('app/service/attr/Value');
		$total = $buteService->getCountData($where); 
		if ($total > 0) {
			$list = $buteService->getListData($where, '*', $page, $size, ['attv_id'=>'desc']);

			$tempArr = array_column($list, 'attv_id');
			$tempArr = make('app/service/attr/ValueLanguage')->where(['attv_id'=>['in', $tempArr]])->field('count(*) as count, attv_id')->groupBy('attv_id')->get();
			$tempArr = array_column($tempArr, 'count', 'attv_id');
			$languageList = make('app/service/Language')->getListCache();
			$languageList = array_column($languageList, null, 'code');
			unset($languageList['zh']);
			$len = count($languageList);
			foreach ($list as $key => $value) {
				$value['is_translate'] = empty($tempArr[$value['attv_id']]) ? 0 : ($tempArr[$value['attv_id']] < $len ? 1 : 2);
				$list[$key] = $value;
			}

		}

		$this->_init();
		$this->assign('total', $total);
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
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/attr/ValueLanguage');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($id, $key, strTrim($value));
			}
		}
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
			$rst = make('app/service/product/AttributeRelation')->deleteData(['attv_id'=>$id]);
		}
		if ($rst) {
			$this->addLog('删除属性-'.$id);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
}