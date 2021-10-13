<?php

namespace app\task\main;
use app\task\TaskDriver;

class Email extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '邮件发送任务';
		$this->config['cron'] = ['* * * * *']; //ayaways run
	}

	public function run()
	{
		$email = make('app/service/email/Email');
		$list = $email->getListData(['status'=>0], 'email_id');
		if (empty($list)) {
			$this->taskSleep(300);
			return false;
		} else {
			foreach ($list as $value) {
				$email->sendEmailById($value['email_id']);
			}
		}
		return true;
	}
}