<?php

namespace app\task;
use app\task\TaskDriver;

class MainTask extends TaskDriver
{
    protected $sleep = 10;
    protected $mainTask = true;
    public $config = [
        'name' => '系统核心队列任务',
        'cron' => ['* * * * *'],
    ];

    protected function before()
    {
        $className = 'app/task/MainTask';
        $keyName = nameFormat($className);
        $data = [];
        $data['boot'] = 'on';
        $data['status'] = 'stop';
        $data['name'] = $this->config['name'] ?? '';
        $data['sleep'] = $this->sleep;
        $data['class_name'] = $className;
        $data['next_run'] = 'alwaysRun';
        $this->delInfo('all');
        $this->setInfoArray($data, $keyName);

        $files = getDirFile(__DIR__.DS.'main');
        foreach ($files as $key => $value) {
            $className = str_replace('\\', DS, __NAMESPACE__).str_replace([__DIR__, '.php'], '', $value);
            //重新缓存配置
            $class = make($className);
            $keyName = nameFormat($className);
            $tempData = $this->getInfo('', $keyName);
            $data = [];
            $data['boot'] = $tempData['boot'] ?? 'off';
            $data['status'] = $tempData['status'] ?? 'stop';
            $config = $class->config;
            $data['name'] = $config['name'] ?? '';
            $data['sleep'] = $class->sleep;
            $data['class_name'] = $className;
            $nextRunAt = $this->getNextTime($config['cron']);
            $data['next_run'] = $nextRunAt <= now() ? 'alwaysRun' : $nextRunAt;
            $this->setInfo($keyName, ['boot'=>$data['boot'], 'next_run'=>$data['next_run'], 'status'=>$data['status']], 'all');
            $this->setInfoArray($data, $keyName);
        }
    }

    public function run()
    {
        $allTask = $this->getInfo('', 'all');
        foreach ($allTask as $key=>$value) {
            //循环检查进程状态
            if ($value['boot'] == 'on' || $value['boot'] == 'oning') {
                if ($value['status'] == 'stop'){
                    if ($value['next_run'] == 'alwaysRun' || $value['next_run'] <= now()) {
                        $this->startTask($key, $value);
                    }
                } else {
                    if (time()%$this->lockTimeout==0 && !$this->locker->existLock($key)) {
                        $this->startTask($key, $value);
                    }
                }
            }
        }
        return true;
    }

    protected function startTask($key, $value)
    {
        //启动任务
        $this->locker->unlock($key);
        $value['status'] = 'starting';
        $this->setInfo($key, $value, 'all');
        $this->tasker->start($key);
    }
}