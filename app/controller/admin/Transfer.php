<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Transfer extends AdminBase
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '文本翻译',
        ];
        $this->_default = '站点文本';
		$this->_init();
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'reloadCache', 'autoTransfer'])) {
				$this->$opn();
			}
		}

		html()->addJs();
		$keyword = iget('keyword');
		$page = iget('page', 1);
		$size = iget('size', 20);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		$service = make('app/service/Translate');
		$total = $service->getCountData($where);
		if ($total > 0) {
			$list = $service->getListData($where, '*', $page, $size);
			if (!empty($list)) {
				$languageList = make('app/service/Language')->getListCache();
				$languageList = array_column($languageList, 'name', 'code');
				foreach ($list as $key => $value) {
					$value['type_name'] = $languageList[$value['type']] ?? '';
					$list[$key] = $value;
				}
			}
		}
		$this->assign('keyword', $keyword);
		$this->assign('size', $size);
		$this->assign('total', $total);
		$this->assign('list', $list ?? '');
		$this->view();
	}

	protected function getInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$info = make('app/service/Translate')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据为空');
		}
		$languageList = make('app/service/Language')->getInfoCache();
		$languageList = array_column($languageList, 'name', 'code');
		$info['type_name'] = $languageList[$info['type']];
		$this->success($info, '');
	}

	protected function reloadCache()
	{
		$result = make('app/service/Translate')->reloadCache();
		if ($result) {
			$this->success('重构成功');
		} else {
			$this->error('重构失败');
		}
	}

	protected function autoTransfer()
	{
		$value = trim(ipost('value'));
		$code = trim(ipost('code'));
		if (empty($value)) {
			$this->error('翻译文本为空');
		}
		if (empty($code)) {
			$this->error('翻译类型为空');
		}
		if ($code == 'zh') {
			$this->success($value, '');
		}
		$languageList = make('app/service/Language')->getInfoCache();
		$languageList = array_column($languageList, 'tr_code', 'code');
		$trCode = $languageList[$code];
		$result = make('App\s\Translate')->getTranslate($value, $trCode);
		$this->success($result, '');
	}

	protected function editInfo()
	{
		$name = trim(ipost('name'));
		$value = trim(ipost('value'));
		$type = trim(ipost('type'));
		if (empty($name)) {
			$this->error('翻译标本为空');
		}
		if (empty($value)) {
			$this->error('翻译文本为空');
		}
		if (empty($type)) {
			$this->error('翻译类型为空');
		}
		$result = make('App\s\Translate')->setNotExist($name, $type, $value);
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}
}