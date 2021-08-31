<?php

namespace app\service\message;
use app\service\Base;

class Message extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/message/Message');
	}

	public function createGroup($from, $to=0, $type=0)
	{
		$key = $this->getGroupKey($from, $to, $type);
		if ($this->isExistGroup($key)) return $key;
		$insert = [
			'group_key' => $key,
			'mem_id' => $from,
			'type' => $type,
			'add_time' => now(),
		];
		$this->start();
		make('app/model/message/Group')->insert($insert);
		//群组加人员
		$insert = [
			[
				'group_key' => $key,
				'mem_id' => $from,
				'add_time' => now(),
			],
			[
				'group_key' => $key,
				'mem_id' => $to,
				'add_time' => now(),
			],
		];
		make('app/model/message/Member')->insert($insert);
		$this->commit();
		$insert = 'Hi, Welcome to '.site()->getName().', what can I do for you?';
		$this->sendMessage($key, $insert, $to);
	}

	protected function sendMessage($key, $content, $memId)
	{
		if (!$this->isExistGroup($key)) return false;
		if (!$this->isExistMember($key, $memId)) return false;
		$insert = [
			'group_key' => $key,
			'mem_id' => $memId,
			'lan_id' => lanId(),
			'content' => substr(trim($content), 0, 250),
			'add_time' => now(),
		];
		$rst = $this->insert($insert);
		if ($rst) {
			$this->updateReadCount($key, $memId);
		}
		return $rst;
	}

	protected function updateReadCount($key, $memId)
	{
		make('app/model/message/Group')->where('group_key', $key)->increment('total');
		make('app/model/message/Member')->where('group_key', $key)->where('mem_id', '<>', $memId)->increment('unread');
		return true;
	}

	public function getSystemId()
	{
		return $this->getConst('SYSTEM_CONTACT_USER');
	}

	protected function getGroupKey($from, $to=0, $type=0)
	{
		$array = [$from, $to];
		sort($array);
		return md5(implode('_', $array).'_'.$type);
	}

	protected function isExistGroup($key)
	{
		return make('app/model/message/Group')->getCountData(['group_key'=>$key]);
	}

	protected function isExistMember($key, $memId)
	{
		return make('app/model/message/Member')->getCountData(['group_key'=>$key, 'mem_id'=>$memId]);
	}
}