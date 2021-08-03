<?php

namespace app\service;

class QueueService
{
	protected $key = 'frame-queue:list-data';
	protected $node = 'frame-queue:list-info';

	public function push($data, $first=false)
	{
		if ($first) {
			$result = redis(2)->rPush($this->key, $data);
		} else {
			$result = redis(2)->lPush($this->key, $data);
		}
		redis(2)->hSet($this->node, 'last_push', now());
		return $result;
	}
}
