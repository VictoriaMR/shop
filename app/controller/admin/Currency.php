<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Currency extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '货币管理',
			'log'   => '汇率日志',
		];
		$this->_default = '财务管理';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCurrencyInfo', 'updateCurrencyRate', 'editCurrencyInfo', 'deleteCurrencyInfo'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$keyword = trim(iget('keyword', ''));
		$list = sys()->currency()->getListData([], '*', 0, 0, ['code' => 'ASC']);
		
		if ($keyword !== '') {
			$list = array_values(array_filter($list, function ($item) use ($keyword) {
				return stripos($item['code'], $keyword) !== false || stripos($item['name'], $keyword) !== false || stripos($item['symbol'], $keyword) !== false;
			}));
		}

		$this->view([
			'list'    => $list,
			'keyword' => $keyword,
			'total'   => count($list),
		]);
	}

	public function log()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$code = strtoupper(trim(iget('code', '')));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));
		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));

		$where = [];
		if ($code !== '' && $code !== 'ALL') {
			$where['code'] = $code;
		}
		$stimeFull = $stime !== '' ? ($stime . (strlen($stime) === 10 ? ' 00:00:00' : '')) : '';
		$etimeFull = $etime !== '' ? ($etime . (strlen($etime) === 10 ? ' 23:59:59' : '')) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$logService = service('log/Currency');
		$total = $logService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'DESC']);
		}

		$currencyList = sys()->currency()->getListData([], 'code,name,symbol', 0, 0, ['code' => 'ASC']);
		$currencyMap = array_column($currencyList, null, 'code');

		$this->view([
			'list'         => $list,
			'total'        => $total,
			'currencyList' => $currencyList,
			'currencyMap'  => $currencyMap,
			'search'       => [
				'code'  => $code,
				'stime' => $stime,
				'etime' => $etime,
				'page'  => $page,
				'size'  => $size,
			],
		]);
	}

	protected function getCurrencyInfo()
	{
		$id = strtoupper(trim(ipost('id', '')));
		if (empty($id)) {
			$this->error('参数不正确, 货币代码不能为空');
		}

		$info = sys()->currency()->loadData($id, 'code,name,rate,symbol');
		if (!$info) {
			$this->error('货币信息不存在');
		}

		$this->success('获取成功', $info);
	}

	protected function editCurrencyInfo()
	{
		$id = strtoupper(trim(ipost('id', '')));
		$code = strtoupper(trim(ipost('code', '')));
		$name = trim(ipost('name', ''));
		$symbol = trim(ipost('symbol', ''));
		$rate = trim(ipost('rate', ''));

		if (empty($name)) {
			$this->error('货币名称不能为空');
		}

		if (empty($symbol)) {
			$this->error('货币符号不能为空');
		}

		if (!is_numeric($rate) || (float)$rate <= 0) {
			$this->error('汇率必须为大于0的有效数值');
		}
		$rate = number_format((float)$rate, 6, '.', '');

		if (empty($id)) {
			// 新增货币
			if (empty($code) || !preg_match('/^[A-Z]{3}$/', $code)) {
				$this->error('货币代码必须为3位大写英文字母(如: USD)');
			}

			$exist = sys()->currency()->loadData($code, 'code');
			if ($exist) {
				$this->error('货币代码【' . $code . '】已存在');
			}

			$data = [
				'code'   => $code,
				'name'   => $name,
				'symbol' => $symbol,
				'rate'   => $rate,
			];

			$rst = sys()->currency()->insertCurrency($data);
			if ($rst) {
				service('log/Currency')->insert([
					'code'     => $code,
					'old_rate' => 0.000000,
					'new_rate' => $rate,
				]);
				$this->addLog('新增货币-' . $code);
				$this->success('新增货币成功');
			}
			$this->error('新增货币失败');
		} else {
			// 编辑货币
			$oldInfo = sys()->currency()->loadData($id, 'code,rate');
			if (!$oldInfo) {
				$this->error('货币信息不存在');
			}
			$data = [
				'name'   => $name,
				'symbol' => $symbol,
				'rate'   => $rate,
			];

			$rst = sys()->currency()->updateInfo($id, $data);
			if ($rst !== false) {
				if ($oldInfo && (string)$oldInfo['rate'] !== (string)$rate) {
					service('log/Currency')->insert([
						'code'     => $id,
						'old_rate' => $oldInfo['rate'],
						'new_rate' => $rate,
					]);
				}
				$this->addLog('修改货币信息-' . $id);
				$this->success('修改货币成功');
			}
			$this->error('修改货币失败');
		}
	}

	protected function deleteCurrencyInfo()
	{
		$id = strtoupper(trim(ipost('id', '')));
		if (empty($id)) {
			$this->error('参数不正确, 货币代码不能为空');
		}

		if ($id === 'CNY') {
			$this->error('基准货币(CNY)不允许删除');
		}

		$rst = sys()->currency()->deleteCurrency($id);
		if ($rst) {
			$this->addLog('删除货币-' . $id);
			$this->success('删除货币成功');
		}
		$this->error('删除货币失败');
	}

	protected function updateCurrencyRate()
	{
		$rst = sys()->currency()->updateRate();
		if ($rst) {
			$this->addLog('在线更新货币汇率');
			$this->success('更新汇率成功');
		}
		$this->error('更新汇率失败，请稍后重试');
	}
}