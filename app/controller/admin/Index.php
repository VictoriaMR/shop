<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Index extends AdminBase
{
	//初始化
	public function __construct()
	{
		$this->_arr = [
			'statInfo' => '统计信息',
		];
		$this->_default = '概览';
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['setLeft'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		frame('Html')->addJs('socket');
		$funcList = service('controller/Controller')->getList();
		$funcList = service('controller/Permission')->filterMenuList($funcList);
		$this->view([
			'_loading' => false,
			'_title' => '后台首页',
			'funcList' => $funcList,
			'info' => frame('Session')->get(\App::get('domain', 'class') . '_info'),
			'leftInfo' => frame('Session')->get('left_info'),
		]);
	}

	protected function setLeft()
	{
		$key = ipost('key');
		$value = ipost('value');
		if (empty($key)) {
			$this->error('非法参数');
		}
		frame('Session')->set('left_info', $value, $key);
		$this->success('设置成功');
	}

	public function statInfo()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getSystemInfo'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		$log = service('log/Visitor');
		//浏览设备统计
		$viewAgentInfo = $log->getStats('browser');
		//每日浏览人数统计
		$viewerInfo = $log->getIpDateStat();
		//系统信息
		$cpuInfo = isWin() ? $this->sys_windows() : $this->sys_linux();
		$mysqlVersion = $log->getQuery('SELECT version() AS version');
		if (isset($mysqlVersion[0]['version'])) {
			$mysqlVersion = $mysqlVersion[0]['version'];
		}
		$this->view([
			'viewAgentInfo' => $viewAgentInfo,
			'viewerInfo' => $viewerInfo,
			'cpuInfo' => $cpuInfo,
			'mysqlVersion' => $mysqlVersion,
		]);
	}

	protected function getSystemInfo()
	{
		$returnData = isWin() ? $this->sys_windows() : $this->sys_linux();
		$this->success('获取成功', $returnData);
	}

	protected function sys_windows(): array
	{
		$data = [];
		$out = [];
		@exec('wmic cpu get loadpercentage 2>NUL', $out);
		$load = isset($out[1]) && is_numeric(trim($out[1])) ? (int)trim($out[1]) : 0;

		$out = [];
		@exec('wmic ComputerSystem get TotalPhysicalMemory 2>NUL', $out);
		$memTotalBytes = isset($out[1]) && is_numeric(trim($out[1])) ? (float)trim($out[1]) : 0.0;

		$out = [];
		@exec('wmic OS get FreePhysicalMemory 2>NUL', $out);
		$memFreeKb = isset($out[1]) && is_numeric(trim($out[1])) ? (float)trim($out[1]) : 0.0;

		// 若新版 Windows 移除了 wmic，回退使用 PowerShell CIM 查询
		if ($memTotalBytes <= 0) {
			$psOut = [];
			@exec('powershell -NoProfile -Command "$os=Get-CimInstance Win32_OperatingSystem;$cpu=(Get-CimInstance Win32_Processor | Measure-Object -Property LoadPercentage -Average).Average;Write-Output \"$cpu|$($os.TotalVisibleMemorySize)|$($os.FreePhysicalMemory)\"" 2>NUL', $psOut);
			if (!empty($psOut[0])) {
				$parts = explode('|', trim($psOut[0]));
				$load = (int)($parts[0] ?? 0);
				$memTotalBytes = (float)($parts[1] ?? 0) * 1024;
				$memFreeKb = (float)($parts[2] ?? 0);
			}
		}

		$data['loadpercentage'] = $load . '%';
		$memTotal = $memTotalBytes / 1024 / 1024;
		$memFree = $memFreeKb / 1024;
		$memUsed = max(0.0, $memTotal - $memFree);
		$data['memory_used_rate'] = ($memTotal > 0 ? sprintf('%.2f', $memUsed / $memTotal * 100) : '0.00') . '%';
		$data['memory_total'] = $this->formatMemory($memTotal);
		$data['memory_used'] = $this->formatMemory($memUsed);
		$data['memory_free'] = $this->formatMemory($memFree);

		$totalDisk = (float)@disk_total_space(ROOT_PATH);
		$freeDisk = (float)@disk_free_space(ROOT_PATH);
		$data['disk_total_space'] = get1024Peck($totalDisk);
		$data['disk_free_space'] = get1024Peck($freeDisk);
		$data['disk_used_space'] = get1024Peck(max(0.0, $totalDisk - $freeDisk));
		return $data;
	}

	protected function sys_linux(): array
	{
		$str = (string)@shell_exec('free');
		$memTotal = $memUsed = 0.0;
		if (str_contains($str, 'Mem:')) {
			$str = preg_replace('/\s(?=\s)/', '\\1', explode('Mem:', $str)[1]);
			$parts = explode('Swap:', $str);
			$memData = explode(' ', trim($parts[0] ?? ''));
			$swapData = explode(' ', trim($parts[1] ?? ''));
			$memTotal = ((float)($memData[0] ?? 0) + (float)($swapData[0] ?? 0)) / 1024;
			$memUsed = ((float)($memData[1] ?? 0) + (float)($swapData[1] ?? 0)) / 1024;
		}
		$memFree = max(0.0, $memTotal - $memUsed);
		$data = [];
		$data['memory_used_rate'] = ($memTotal > 0 ? sprintf('%.2f', $memUsed / $memTotal * 100) : '0.00') . '%';
		$data['memory_total'] = $this->formatMemory($memTotal);
		$data['memory_used'] = $this->formatMemory($memUsed);
		$data['memory_free'] = $this->formatMemory($memFree);
		$loadAvg = function_exists('sys_getloadavg') ? sys_getloadavg() : [0];
		$data['loadpercentage'] = round(((float)($loadAvg[0] ?? 0)) * 100, 2) . '%';
		$total = (float)@disk_total_space('/');
		$free = (float)@disk_free_space('/');
		$data['disk_total_space'] = get1024Peck($total);
		$data['disk_free_space'] = get1024Peck($free);
		$data['disk_used_space'] = get1024Peck(max(0.0, $total - $free));
		return $data;
	}

	protected function formatMemory(float $mb): string
	{
		if ($mb > 1024) {
			return sprintf('%.2f', $mb / 1024) . ' G';
		}
		return sprintf('%.2f', $mb) . ' M';
	}
}
