<?php

namespace app\task;

abstract class TaskDriver
{
	const TASKPREFIX ='frame-task:';
	public $config = [];

	protected $locker;
	protected $tasker;
	protected $lock ='';
	protected $cas ='';
    protected $sleep = 1;
	protected $lockTimeout = 600;
    protected $mainTask = false;

	public function __construct($process=[])
	{
        if ($process) {
            //init 基础类
            $this->locker = make('frame/Locker');
            $this->tasker = make('frame/Task');
            //解析参数
            $process['lock'] = json_decode(base64_decode($process['lock']), true);
            list($this->lock, $this->cas) = $process['lock'];
        }
	}

    protected function cache()
    {
        return cache(2);
    }

	protected function getKey($key)
	{
		return self::TASKPREFIX.$key;
	}

	protected function setInfo($field, $value, $key='')
	{
		if (!$key) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		return $this->cache()->hSet($key, $field, $value);
	}

	protected function delInfo($key='')
	{
		if (empty($key)) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		return $this->cache()->del($key);
	}

	protected function setInfoArray(array $data, $key='')
	{
		if (empty($key)) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		return $this->cache()->hMset($key, $data);
	}

	protected function getInfo($field='', $key='')
	{
		if (empty($key)) {
			$key = $this->lock;
		}
		$key = $this->getKey($key);
		if(empty($field)){
			return $this->cache()->hGetAll($key);
		} else {
			return $this->cache()->hGet($key, $field);
		}
	}

	protected function echo($msg, $name='')
	{
		$this->setInfo('remark', $msg, $name);
	}

	protected function before() {}
	protected function beforeShutdown() {}

	protected function continueRuning()
	{
		if (!$this->updateLock()) {
			return false;
		}
		// 关闭的不运行
		$info = $this->getInfo();
		if ($info['boot'] != 'on' || $info['status'] != 'running') {
			$this->beforeShutdown();
			return false;
		}
		return true;
	}

	protected function updateLock()
	{
		if ($this->locker->update($this->lock, $this->lockTimeout)) {
			return true;
		}
		return false;
	}

	public function start()
	{
		if ($this->locker->getLock($this->lock, $this->cas)) {
			$this->before();
            // 设置任务当次启动时间
            $data = [
                'boot' => 'on',
                'start_time' => now(),
                'status' => 'running',
                'process_pid' => getmypid(),
                'process_user' => get_current_user(),
                'loop_count' => 0,
                'info' => '',
            ];
            $this->setInfoArray($data);
            if (!$this->mainTask) {
                $value = $this->getInfo($this->lock, 'all');
                $value['boot'] = 'on';
                $value['status'] = $data['status'];
                $value['process_pid'] = $data['process_pid'];
                $this->setInfo($this->lock, $value, 'all');
            }

            //启动次数加1
            $cacheKey = $this->getKey($this->lock);
            $this->cache()->hIncrBy($cacheKey, 'count', 1);

            $result = true;
            while ($result) {
                if ($this->continueRuning()) {
                    $this->cache()->hIncrBy($cacheKey, 'loop_count', 1);
                    $result = $this->run();
                    $usgaMem = memory_get_usage();
                    $this->setInfo('memory_usage', get1024Peck($usgaMem - APP_MEMORY_START).'/'.get1024Peck($usgaMem));
                	if ($result) {
                		sleep($this->sleep);
                	}
                } else {
                    $result = false;
                }
            }
			$this->locker->unlock($this->lock);
            $value = $this->getInfo($this->lock, 'all');
            $data = [
                'status' => 'stop',
                'info' => "任务已退出 \n".now(),
                'boot' => ($value['boot'] ?? 'offing') == 'offing' ? 'off' : $value['boot'],
            ];
            $this->setInfoArray($data);

            //获取下一次运行时间
            $nextRunAt = $this->getNextTime($this->config['cron']);
            $value['status'] = 'stop';
            if (!$this->mainTask) {
                if ($value['boot'] == 'offing') {
                    $value['next_run'] = $nextRunAt <= now() ? 'alwaysRun' : $nextRunAt;
                } else {
                    $value['next_run'] = $nextRunAt <= now() ? 'handing' : $nextRunAt;
                }
            }
            $value['boot'] = $value['boot'] == 'offing' ? 'off' : $value['boot'];
            $this->setInfo($this->lock, $value, 'all');
        }
	}

	public function getNextTime($cron)
    {
        $result=false;
        foreach ($cron as $val){
			$v=$this->getNextTimeByCron($val);
			if ($result) {
				if ($v<$result) {
					$result=$v;
				}
			} else {
                $result=$v;
			}
        }
        return $result;
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
    public function getNextTimeByCron($cornStr)
    {
        $cornStr=preg_replace('/\s+/', ' ', trim($cornStr));
        if ($cornStr=='* * * * *') {
            return now();
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
        return now(mktime($hour,$minute,0, $month,$day,$year));
    }

    private function cronUnitParse($unit, $allowRange)
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

    private function cronNextVal($range, $val)
    {
        reset($range);
        foreach ($range as $v){
            if($v>=$val){
                return $v;
            }
        }
        return -1;
    }

	protected function taskSleep($time)
	{
		return $this->setInfo('next_run_at', now(time() + $time));
	}

	protected function nextRunAt()
	{
		return $this->setInfo('next_run_at', $this->getNextTime());
	}

	abstract public function run();
}