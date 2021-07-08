<?php

namespace app\controller\admin;

use app\controller\Controller;
use frame\Html;
use frame\Session;

class IndexController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'statInfo' => '统计信息',
        ];
        $this->_tagShow = false;
        $this->_default = '概览';
	}

	public function index()
	{	
		Html::addCss();
		Html::addJs();
		
		$this->assign('info', Session::get('admin'));
		return view();
	}

	public function statInfo()
	{
        Html::addCss();
        Html::addJs();
        if (isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, ['getSystemInfo'])) {
                $this->$opn();
            }
            $this->error('非法请求');
        }
        $logService = make('App/Services/LoggerService');
        //浏览设备统计
        $viewAgentInfo = $logService->getStats('browser');
        //每日浏览人数统计
        $viewerInfo = $logService->getIpDateStat();

        $cpuInfo = $this->getCpuInfo();

        $this->assign('viewAgentInfo', $viewAgentInfo);
        $this->assign('viewerInfo', $viewerInfo);
        $this->assign('cpuInfo', $cpuInfo);
        $this->_init();
		return view();
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
        $returnData['disk_total_space'] = sprintf('%.2f', disk_total_space('/') / 1024 / 1024);
        $returnData['disk_free_space'] = sprintf('%.2f', disk_free_space('/') / 1024 / 1024);
        $returnData['disk_used_space'] = sprintf('%.2f', $returnData['disk_total_space'] - $returnData['disk_free_space']).'M';
        $returnData['disk_total_space'] .= 'M';
        $returnData['disk_free_space'] .= 'M';
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
        //磁盘使用
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
    }
}