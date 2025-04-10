<?php

namespace app\task\main;
use app\task\TaskDriver;

class Email extends TaskDriver
{
	public $config = [
		'name' => '邮件发送任务',
		'cron' => ['* * * * *'],
	];

	public function run()
	{
		$email = service('email/Email');
		$list = $email->getListData(['status'=>0], 'email_id');
		if (empty($list)) {
			$this->taskMonitor();
			return false;
		} else {
			foreach ($list as $value) {
				$email->sendEmailById($value['email_id']);
			}
		}
		return true;
	}
}