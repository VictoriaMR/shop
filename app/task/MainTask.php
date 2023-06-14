<?php

namespace app\task;
use app\task\TaskDriver;

class MainTask extends TaskDriver
{
    protected $mainTask = true;
    public $config = [
        'name' => '系统核心队列任务',
        'cron' => ['* * * * *'],
        'sleep' => 60,
    ];

    protected function before()
    {
        $this->tasker->delInfo('all');
        //初始化
        $data = [
            'name' => $this->config['name'],
            'sleep' => $this->config['sleep'],
            'next_run' => $this->getNextTime($this->config['cron']),
        ];
        $this->tasker->setInfoArray($this->lock, $data);
        $list = $this->tasker->getTaskList();
        foreach ($list as $key => $data) {
            $data['next_run'] = $this->getNextTime($data['cron']);
            unset($data['cron']);
            $this->tasker->setInfo('all', $key, ['boot'=>$data['boot']??'off', 'next_run'=>$data['next_run'], 'status'=>$data['status']??'stop']);
            $this->tasker->setInfoArray($key, $data);
        }
    }

    public function run()
    {
        $allTask = $this->tasker->getInfo('all');
        foreach ($allTask as $key=>$value) {
            //循环检查进程状态
            if (isset($value['boot']) && $value['boot'] == 'on' && $value['status'] == 'stop' && $value['next_run'] <= time()) {
                echo $key.PHP_EOL;
                $this->startTask($key, $value);
            }
        }
        return true;
    }

    protected function startTask($key, $value)
    {
        $value['status'] = 'starting';
        $this->tasker->setInfoArray($key, $value);
        $this->tasker->setInfo('all', $key, $value);
        $this->tasker->start($key);
    }
}