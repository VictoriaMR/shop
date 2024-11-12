<?php

namespace app\task;

abstract class TaskDriver
{
	public $config = [];

	protected $sleep = 1;
	protected $taskInfo = [];
	protected $tasker;

	public function start()
	{
		// 获取目标数据
		$this->tasker = frame('Task');
		$classKey = $this->tasker->getClassKey(get_class($this));
		if ($classKey == 'app-task-MainTask') {
			$this->allTask = $this->tasker->getInfo();
			$info = $this->allTask[$classKey] ?? [];
		} else {
			$info = $this->tasker->getInfo($classKey);
		}
		if (empty($info)) {
			return false;
		}

		if ($info['boot'] == 'offing') {
			$info['boot'] = 'off';
		}
		if ($info['boot'] == 'off' || $info['next_run'] > time()) {
			$result = false;
		} else {
			$result = true;
			$loopCount = 0;
		}
		// 任务正在进行中
		$info['start_at'] = time();
		$info['count'] = ($info['count'] ?? 0)+1;
		$info['run_at'] = time();
		// 当前进程ID
		if ($result) {
			$info['boot'] = 'on';
			$info['status'] = 'running';
			$info['process_pid'] = getmypid();
			$info['process_user'] = get_current_user();
		}
		while ($result) {
			$update = $loopCount % 5 == 0;
			// 延时更新次数
			if ($update && $loopCount > 0) {
				if ($classKey == 'app-task-MainTask') {
					$this->allTask = $this->tasker->getInfo();
					$info = $this->allTask[$classKey] ?? [];
				} else {
					$info = $this->tasker->getInfo($classKey);
				}
			}
			if ($info['boot'] != 'on') {
				$info['boot'] = 'off';
				break;
			}
			$info['remark'] = '';
			$this->taskInfo = [];
			$result = $this->run();
			$loopCount++;
			if ($result) {
				if ($update) {
					$info['loop_count'] = $loopCount;
					$info['memory_usage'] =  memory_get_usage()- APP_MEMORY_START;
					$info['run_at'] = time();
					$this->tasker->setInfo($classKey, $this->taskInfo + $info);
				}
				sleep($this->config['sleep'] ?? $this->sleep);
			}
		}
		// 任务已退出
		$info['status'] = 'stop';
		$info['remark'] = '任务已退出'.PHP_EOL.now();
		$info['memory_usage'] = 0;
		$info['next_run'] = $this->getNextTime($this->config['cron']);
		$this->tasker->setInfo($classKey, $info);
		return true;
	}

	public function getNextTime($cron)
	{
		$result=false;
		foreach ($cron as $val){
			$v=$this->getNextTimeByCron($val);
			if (is_bool($result) || $v<$result) {
				$result=$v;
			}
		}
		return $result ?: 0;
	}

	// 在读取corn配置是个做基本检查和过滤， 包括：格式， 运行的字符，等， 传过来的必须是合规的字串
	//   按下面格式配置， 可同时配置多条, 日与周同时配置， 忽略周配置
	//   * * * * *	分 时 日 月 周 (全为*表示持续运行)
	//   0 3 * * *	数字精确配置, 星号为任意.(每天凌晨3点整)
	//   15,30 3 * * *	逗号表示枚举 (每天3点15分和3点30分)
	//   15-30 3 * * *	短线表示范围 (每天的3点15分到30分持续运行)
	//   0-30/10 3 * * *	斜杠表示间隔 (每天3点0分到30分之间, 每10分钟一次)
	//   */10 5-8 * * *	斜杠表示间隔 (每天5-8点, 每10分钟一次)
	// 获取类似linux crontab格式单条配置的下一次运行时间
	protected function getNextTimeByCron($cornStr)
	{
		$cornStr=preg_replace('/\s+/', ' ', trim($cornStr));
		if ($cornStr=='* * * * *') {
			return 0;
		}
		$arr=explode(' ', $cornStr);
		$now=explode('-', date('i-H-d-m-w')); //'m-d-H-i' 月日时分

		//确定取值范围
		$year=date('Y');
		$minuteRange=$this->cronUnitParse($arr[0], range(0,59));
		$hourRange=$this->cronUnitParse($arr[1], range(0,23));
		$dayRange=$this->cronUnitParse($arr[2], range(1, date('t')));
		$monthRange=$this->cronUnitParse($arr[3], range(1,12));
		$weekRange=$this->cronUnitParse($arr[4], range(0,6));
		//取值
		$minute=$this->cronNextVal($minuteRange, $now[0]+1);
		$step=0;
		if ($minute<0) {
			$minute=$minuteRange[0];
			$step=1;
		}
		$hour=$this->cronNextVal($hourRange, $now[1]+$step);
		$step=0;
		if ($hour<0) {
			$hour=$hourRange[0];
			$step=1;
		}
		if($arr[4]=='*'||$arr[3]!='*') { // 按日参数计算
			$day=$this->cronNextVal($dayRange,$now[2]+$step);
			$step=0;
			if($day<0){
				$day=$dayRange[0];
				$step=1;
			}
			$month = $this->cronNextVal($monthRange,$now[3]+$step);
			if($month<0){
				$month = $monthRange[0];
				$year++;
			}
		} else { // 按周参数计算
			$week = $this->cronNextVal($weekRange,$now[4]+$step);
			$basetime=time();
			if($week<0){
				$week=$weekRange[0];
				$basetime=$basetime+(7-date('w',$basetime)+$week)*24*60*60; // 基础时间递增一周
			}
			$basemonth = date('m',$basetime);
			$month = $this->cronNextVal($monthRange,$basemonth);
			if($month<0||date('m',$basetime)!=$month){
				if($month<0) {
					$month = $monthRange[0];
					$year++;
				}
				$basetime = strtotime($year.'-'.$month.'-1 00:00:01');
				for($i=0;$i<7;$i++){
					$basetime = $basetime + $i*24*3600;
					$calweek = date('w',$basetime);
					if($calweek==$week){
						break;
					}
				}
				$day=date('d',$basetime);
			} else {
				$day=date('d',$basetime);
			}

		}
		if($year!=date('Y')){
			$month=$monthRange[0];
			$day=$dayRange[0];
			$hour=$hourRange[0];
			$minute=$minuteRange[0];
		}
		if($month!=date('m')){
			$day=$dayRange[0];
			$hour=$hourRange[0];
			$minute=$minuteRange[0];
		}
		if($day!=date('d')){
			$hour=$hourRange[0];
			$minute=$minuteRange[0];
		}
		if($hour!=date('H')){
			$minute=$minuteRange[0];
		}
		return mktime($hour,$minute,0,$month,$day,$year);
	}

	protected function cronUnitParse($unit, $allowRange)
	{
		if ($unit=='*') {
			$range = $allowRange;
			$step = 1;
		} else {
			$step = 1;
			$str = $unit;
			if (strpos($str,'/')) {
				list($str,$step)=explode('/',$str);
			}
			if ($str=='*') {
				$range=$allowRange;
			} else {
				$range=[];
				$str=explode(',', $str);
				foreach ($str as $val) {
					if (strpos($val, '-')) {
						$tmp=explode('-', $val);
						$range=array_merge($range, range($tmp[0], $tmp[1]));
					} else {
						$range[]=intval($val);
					}
				}
			}
		}
		sort($range);
		$step=(int)$step;
		if ($step<1) {
			$step=1;
		}
		$i=0;
		$result=[];
		while (isset($range[$i])) {
			$result[]=$range[$i];
			$i=$i + $step;
		}
		return $result;
	}

	protected function cronNextVal($range, $val)
	{
		reset($range);
		foreach ($range as $v){
			if($v>=$val){
				return $v;
			}
		}
		return -1;
	}

	protected function echo($info)
	{
		$this->taskInfo['remark'] = $info;
	}

	abstract protected function run();
}