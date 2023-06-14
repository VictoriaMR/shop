<?php 

namespace frame;

class Task
{
	const TASKPREFIX ='frame:task:';
	
	public function start($taskClass, $lockTimeout=0, $cas='')
	{
		$taskClass = $this->getStandClassName($taskClass);
		$lockKey = $this->getKeyByClassName($taskClass);
		if ($lockTimeout < 1) {
			$lockTimeout = config('task', 'timeout');
		}
		if ($cas == '') {
			$cas = make('frame/Locker')->lock($lockKey, $lockTimeout);
			if (!$cas) {
				return false;
			}
		}
		$process = [
			'class' => $taskClass,
			'lock' => [$lockKey, $cas],
		];
		return $this->run($process);
	}

	public function run($process)
	{
		$param = [];
		$param[] = $process['class'];
		$param[] = 'start';
		$param[] = 'lock='.base64_encode(json_encode($process['lock'], JSON_UNESCAPED_UNICODE));
		return $this->localRunPhp(implode(' ', $param));
	}

	public function localRunPhp($param)
	{
		$phpBin = config('task', 'phpbin');
		$cmd = $phpBin.' -f '.ROOT_PATH.'command '.$param;
		if (isWin()) {
			pclose(popen('start /B '.$cmd.' 1>NUL 2>NUL', 'r'));
		} else {
			$out = [];
			$rstSign = '';
			exec($cmd.' > /dev/null 2>&1 &', $out, $rstSign);
		}
		make('frame/Debug')->runlog($cmd, 'task');
		return true;
	}

	protected function getStandClassName($classname)
	{
		return strtr($classname, '-', DS);
	}

	protected function getKeyByClassName($classname)
	{
		return str_replace(['\\', DS], '-', $classname);
	}

	public function taskStart($key)
	{
		$key = 'app-task-main-'.$key;
		$value = $this->cache()->hGet(self::TASKPREFIX.'all', $key);
		if (isset($value['next_run']) && $value['next_run'] == 'alwaysRun') {
			return true;
		}
		$value['next_run'] = 'alwaysRun';
		$this->cache()->hSet(self::TASKPREFIX.'all', $key, $value);
		return true;
	}

	public function getTaskList($main = false)
	{
		$files = scandir(APP_PATH.'task/main');
		$list = [];
		if ($main) {
			$list[] = 'app/task/MainTask';
		}
        foreach ($files as $value) {
            if ($value == '.' || $value == '..') continue;
            $list[] = 'app/task/main/'.str_replace('.php', '', $value);
        }
        $list = array_flip($list);
        foreach ($list as $key=>$value) {
        	$class = make($key, null, false);
        	$list[$key] = array_merge($class->config, $this->getInfo($key) ?: []);
        }
        return $list;
	}

	protected function getKey($key)
	{
		return self::TASKPREFIX.$this->getKeyByClassName($key);
	}

	protected function cache()
	{
		return redis(2);
	}

	public function setInfo($key, $field, $value)
	{
		return $this->cache()->hSet($this->getKey($key), $this->getKeyByClassName($field), $value);
	}

	public function delInfo($key)
	{
		return $this->cache()->del($this->getKey($key));
	}

	public function setInfoArray($key, array $data)
	{
		return $this->cache()->hMset($this->getKey($key), $data);
	}

	public function getInfo($key, $field='')
	{
		if ($field) {
			return $this->cache()->hGet($this->getKey($key), $this->getKeyByClassName($field));
		} else {
			return $this->cache()->hGetAll($this->getKey($key));
		}
	}

	public function countIncr($key, $field='count', $num=1)
	{
		return $this->cache()->hIncrBy($this->getKey($key), $field, $num);
	}

	public function loopCountIncr($key)
	{
		return $this->countIncr($key, 'loop_count');
	}

	public function noticeTask($key)
	{
		$value = $this->getInfo('all', $key);
		$value['next_run'] = time();
		$this->setInfo('all', $key, $value);
		$this->setInfo($key, 'next_run', $value['next_run']);
	}
}