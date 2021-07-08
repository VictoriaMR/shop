<?php

namespace app\controller\admin;

use app\controller\Controller;
use frame\Html;

class TransferController  extends Controller
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
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'reloadCache', 'autoTransfer'])) {
				$this->$opn();
			}
		}

		Html::addJs();
		$keyword = trim(iget('keyword'));
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 20);
		$where = [];
		if (!empty($keyword)) {
			$where['name'] = ['like', '%'.$keyword.'%'];
		}
		$service = make('App/Services/TranslateService');
		$total = $service->getCount($where);
		if ($total > 0) {
			$list = $service->getListByWhere($where, '*', $page, $size);
			if (!empty($list)) {
				$languageList = make('App/Services/LanguageService')->getInfo();
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
		return view();
	}

	protected function getInfo()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$info = make('App/Services/TranslateService')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据为空');
		}
		$languageList = make('App/Services/LanguageService')->getInfoCache();
		$languageList = array_column($languageList, 'name', 'code');
		$info['type_name'] = $languageList[$info['type']];
		$this->success($info, '');
	}

	protected function reloadCache()
	{
		$result = make('App/Services/TranslateService')->reloadCache();
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
		$languageList = make('App/Services/LanguageService')->getInfoCache();
		$languageList = array_column($languageList, 'tr_code', 'code');
		$trCode = $languageList[$code];
		$result = make('App\Services\TranslateService')->getTranslate($value, $trCode);
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
		$result = make('App\Services\TranslateService')->setNotExist($name, $type, $value);
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}
}