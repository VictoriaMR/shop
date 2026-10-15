<?php

namespace app\task\main;
use app\task\TaskDriver;

class Email extends TaskDriver
{
	public static $config = [
		'name' => '邮件发送任务',
		'type' => self::TYPE_OTHER,
		'cron' => [],
	];

	public function run()
	{
		$email = service('email/Email');
		// 分批拉取待发邮件, 避免一次性加载过多占用内存
		$list = $email->getListData(['status' => 0], 'email_id', 1, 50);
		if (empty($list)) {
			return false;
		}
		foreach ($list as $value) {
			$email->sendEmailById($value['email_id']);
			$this->updateLock();
		}
		return count($list) >= 50;
	}
}