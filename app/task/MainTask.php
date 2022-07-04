<?php

namespace app\task;
use app\task\TaskDriver;

class MainTask extends TaskDriver
{
    public $config = [
        'info' => '系统核心队列任务',
        'cron' => ['* * * * *'],
    ];

    protected function before()
    {
        $files = getDirFile(__DIR__.DS.'main');
        foreach ($files as $key => $value) {
            $className = str_replace('\\', DS, __NAMESPACE__).str_replace([__DIR__, '.php'], '', $value);
            //重新缓存配置
            $class = make($className);
            $keyName = nameFormat($className);
            $data = [];
            $data['boot'] = 'off';
            $config = $class->config;
            $data['info'] = $config['info'] ?? '';
            $data['lock_time_out'] = $class->lockTimeout;
            $data['sleep'] = $class->sleep;
            $data['classname'] = $className;
            $data['next_run'] = $this->getNextTime($config['cron']);
            $this->cache()->hSet(self::TASKPREFIX.'all', $keyName, ['boot'=>$data['boot'], 'next_run'=>$data['next_run']]);
        }
    }

    public function run()
    {
        $allTaskCache = $this->cache()->hGetAll(self::TASKPREFIX.'all');
        foreach ($allTaskCache as $key=>$value) {
            //循环检查进程状态
            if ($value['boot'] == 'on') {
                //每个锁周期检查一次锁有效期,
                if (time()%$this->lockTimeout==0 && $value['next_run']<=now() && !$this->locker->existLock($key)) {
                    //重启进程
                    if (isset($value['process_pid'])) {
                        posix_kill($value['process_pid'], 9);
                    }
                    $this->tasker->start($key);
                }
            } elseif ($value['boot'] == 'waiting' && $value['next_run'] <= now() && !$this->locker->existLock($key)) {
                if (isset($value['process_pid'])) {
                    posix_kill($value['process_pid'], 9);
                }
                $this->tasker->start($key);
            }
        }
        return true;
    }
}