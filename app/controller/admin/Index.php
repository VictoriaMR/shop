<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Index extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '首页',
			'statInfo' => '统计信息',
		];
		$this->_default = '概览';
		parent::_init();
	}

	public function index()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['setLeft'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addCss();
		html()->addJs();
		$this->assign('funcList', make('app/service/controller/Controller')->getList());
		$this->assign('info', session()->get(type().'_info'));
		$this->assign('leftInfo', session()->get('left_info'));
		$this->view();
	}

	protected function setLeft()
	{
		$key = ipost('key');
		$value = ipost('value');
		if (empty($key)) {
			$this->error('非法参数');
		}
		session()->set('left_info', $value, $key);
		$this->success('设置成功');
	}

	public function statInfo()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getSystemInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$log = make('app/service/Logger');
		//浏览设备统计
		$viewAgentInfo = $log->getStats('browser');
		//每日浏览人数统计
		$viewerInfo = $log->getIpDateStat();
		//系统信息
		if (isWin()) {
			$cpuInfo = $this->sys_windows();
		} else {
			$cpuInfo = $this->sys_linux();
		}
		$mysqlVersion = $log->getQuery('SELECT version() AS version')[0] ?? [];

		$this->assign('viewAgentInfo', $viewAgentInfo);
		$this->assign('viewerInfo', $viewerInfo);
		$this->assign('cpuInfo', $cpuInfo);
		$this->assign('mysqlVersion', $mysqlVersion['version'] ?? '');
		$this->view();
	}

	protected function getSystemInfo()
	{
		if (isWin()) {
			$returnData = $this->sys_windows();
		} else {
			$returnData = $this->sys_linux();
		}
		$this->success($returnData, '');
	}

	protected function sys_windows()
	{
		$out = [];
		$data = [];
		//cmd Cpu 使用
		$cmd = 'wmic cpu get loadpercentage';
		exec($cmd, $out);
		$data['loadpercentage'] = ($out[1] ?? 0).'%';
		//内存总量
		$out = [];
		$cmd = 'wmic ComputerSystem get TotalPhysicalMemory';
		exec($cmd, $out);
		$data['memory_total'] = sprintf('%.2f', ($out[1] ?? 0) / 1024 / 1024);
		$out = [];
		$cmd = 'wmic OS get FreePhysicalMemory';
		exec($cmd, $out);
		$data['memory_free'] = sprintf('%.2f', ($out[1] ?? 0) / 1024);
		$data['memory_used'] = sprintf('%.2f', $data['memory_total'] - $data['memory_free']);
		$data['memory_used_rate'] = sprintf('%.2f', $data['memory_used'] / $data['memory_total']*100).'%';
		$data['memory_total'] .= ' M';
		$data['memory_used'] .= ' M';
		$data['memory_free'] .= ' M';
		//内存使用

		exec('wmic logicaldisk get FreeSpace,size /format:list', $out);
		$size = $freespace = 0;
		for ($i=0; $i<count($out);$i++) {
			if (empty($out[$i])) continue;
			$arr = explode('=', $out[$i]);
			$freespace += $arr[1] ?? 0;
			$i++;
			if (is_numeric($out[$i])) {
				$size += $out[$i];
			} else {
				$arr = explode('=', $out[$i]);
				$size += $arr[1] ?? 0;
			}
		}
		$data['disk_total_space'] = get1024Peck($size);
		$data['disk_free_space'] = get1024Peck($freespace);
		$data['disk_used_space'] = get1024Peck($size - $freespace);
		return $data;
	}

	protected function sys_linux()
	{
		$str = shell_exec('free');
		$str = preg_replace('/\s(?=\s)/', '\\1', explode('Mem:', $str)[1]);
		$data = explode('Swap:', $str);
		$memData = explode(' ', trim($data[0]));
		$swapData = explode(' ', trim($data[1]));
		$data = [];
		$data['memory_total'] = sprintf('%.2f', ($memData[0] + ($swapData[0] ?? 0)) / 1024);
		$data['memory_used'] = sprintf('%.2f', ($memData[1] + ($swapData[1] ?? 0)) / 1024);
		$data['memory_free'] = sprintf('%.2f', ($data['memory_total'] - $data['memory_used']));
		$data['memory_used_rate'] = sprintf('%.2f', $data['memory_used'] / $data['memory_total']*100).'%';
		$data['memory_total'] .= ' M';
		$data['memory_used'] .= ' M';
		$data['memory_free'] .= ' M';
		$data['loadpercentage'] = ((sys_getloadavg()[0] ?? 0)*100).'%';
		$total = disk_total_space('/');
		$free = disk_free_space('/');
		$data['disk_total_space'] = get1024Peck($total);
		$data['disk_free_space'] = get1024Peck($free);
		$data['disk_used_space'] = get1024Peck($total - $free);
		return $data;
	}
}