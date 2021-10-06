<?php

namespace app\controller\admin;
use app\controller\Base;

class Index extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '首页',
			'statInfo' => '统计信息',
		];
		$this->_tagShow = false;
		$this->_default = '概览';
	}

	public function index()
	{
		html()->addCss();
		html()->addJs();
		//功能列表
		$funcList = make('app/service/controller/Controller')->getList();
		$this->assign('funcList', $funcList);
		$this->assign('info', session()->get('admin_info'));
		$this->_init();
		$this->view();
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
		$cpuInfo = $this->getCpuInfo();
		$mysqlVersion = $log->getQuery('SELECT version() AS version')[0] ?? [];

		$this->assign('viewAgentInfo', $viewAgentInfo);
		$this->assign('viewerInfo', $viewerInfo);
		$this->assign('cpuInfo', $cpuInfo);
		$this->assign('mysqlVersion', $mysqlVersion['version'] ?? '');
		$this->_init();
		$this->view();
	}

    protected function getCpuInfo()
    {
        $returnData = [];
        if (isWin()) {
            $cmd = 'wmic cpu get name,numberofcores';
            exec($cmd, $out);
            if (empty($out)) {
                return $returnData;
            }
            $nameArr = array_values(array_filter(explode('  ', $out[0])));
            $valueArr = array_values(array_filter(explode('  ', $out[1])));
            foreach ($nameArr as $key => $value) {
                $returnData[$value] = $valueArr[$key] ?? '';
            }
        }
        return $returnData;
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
		$data['memory_used'] = sprintf('%.2f', ($out[1] ?? 0) / 1024);
		$data['memory_free'] = sprintf('%.2f', ($data['memory_total'] - $data['memory_used'])).'M';
		$data['memory_total'] .= 'M';
		$data['memory_used'] .= 'M';

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
		$data['memory_free_rate'] = sprintf('%.2f', ($data['memory_total'] - $data['memory_used']) / $data['memory_total'] * 100);
		$data['loadpercentage'] = sys_getloadavg()[0] ?? 0;
		$data['disk_total_space'] = get1024Peck(disk_total_space('/'));
		$data['disk_free_space'] = get1024Peck(disk_free_space('/'));
		$data['disk_used_space'] = disk_free_space(disk_total_space('/') - disk_free_space('/'));
		return $data;
	}
}