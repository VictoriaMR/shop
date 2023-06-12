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

    public function before()
    {
        //主进程任务
        $className = get_called_class();
        $this->delInfo('all');
        $this->setConfigInfo($className, $this->config);
        $data = [
            'boot' => 'on',
            'status' => 'stop',
            'name' => $this->config['name'],
            'sleep' => $this->sleep,
            'class_name' => $className,
            'next_run' => 'alwaysRun',
        ];
        $this->setInfoArray($data, nameFormat($className));
        $files = scandir(__DIR__.DS.'main');
        foreach ($files as $value) {
            if ($value == '.' || $value == '..') continue;
            $className = strtr(__NAMESPACE__, '\\', DS).DS.'main'.DS.str_replace('.php', '', $value);
            $class = make($className, null, false);
            $config = $class->config;
            $keyName = nameFormat($className);
            //获取缓存中的值合并
            $data = $this->getInfo('', $keyName);
            $data = [
                'boot' => $data['boot'] ?? 'on',
                'status' => $data['status'] ?? 'start',
                'name' => $config['name'],
                'sleep' => $class->sleep,
                'class_name' => $className,
                'next_run' => $this->getNextTime($config['cron']),
            ];
            $this->setInfo($keyName, ['boot'=>$data['boot'], 'next_run'=>$data['next_run'], 'status'=>$data['status']], 'all');
            $this->setInfoArray($data, $keyName);
        }
    }

    public function run()
    {
        $allTask = $this->getInfo('', 'all');
        foreach ($allTask as $key=>$value) {
            //循环检查进程状态
            if ($value['boot'] == 'on' &&
                (($value['status'] == 'stop' && ($value['next_run'] == 'alwaysRun' || $value['next_run'] <= now())) ||
                (time()%$this->lockTimeout==0 && !$this->locker->existLock($key)))
            ) {
                $this->startTask($key, $value);
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