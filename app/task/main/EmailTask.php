<?php

namespace app\task\main;
use app\task\TaskDriver;

class EmailTask extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = 'email';
		$this->config['cron'] = ['* * * * *']; //ayaways run
	}

	public function run()
	{
		$emailService = make('app/service/email/EmailService');
		$list = $emailService->getListData(['status'=>0], 'email_id');
		if (!empty($list)) {
			foreach ($list as $value) {
				$emailService->sendEmailById($value['email_id']);
				dd();
			}
		} else {
			$this->taskSleep(300);
		}
		return true;
	}

}