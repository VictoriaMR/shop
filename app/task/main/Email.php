<?php

namespace app\task\main;
use app\task\TaskDriver;

class Email extends TaskDriver
{
	public $config = [
        'info' => '邮件发送任务',
        'cron' => ['* * * * *'],
    ];

	public function run()
	{
		$email = make('app/service/email/Email');
		$list = $email->getListData(['status'=>0], 'email_id');
		if (empty($list)) {
			return false;
		} else {
			foreach ($list as $value) {
				$email->sendEmailById($value['email_id']);
			}
		}
		return true;
	}
}