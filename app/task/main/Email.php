<?php

namespace app\task\main;
use app\task\TaskDriver;

class Email extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		$this->config['info'] = '邮件发送任务';
	}

	public function run()
	{
		$email = make('app/service/email/Email');
		$list = $email->getListData(['status'=>0], 'email_id');
		if (empty($list)) {
			$this->taskSleep(300);
			return true;
		} else {
			foreach ($list as $value) {
				$email->sendEmailById($value['email_id']);
			}
		}
		return true;
	}
}