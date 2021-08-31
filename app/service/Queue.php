<?php

namespace app\service;

class Queue
{
	protected $key = 'frame-queue:list-data';
	protected $failedKey = 'frame-queue:failed-list-data';
	protected $node = 'frame-queue:list-info';

	public function push($data, $first=false)
	{
		if ($first) {
			$result = redis(2)->rPush($this->key, $data);
		} else {
			$result = redis(2)->lPush($this->key, $data);
		}
		if ($result) {
			make('frame/Task')->taskStart('QueueTask');
		}
		return $result;
	}

	public function count()
	{
		return redis(2)->lLen($this->key);
	}

	public function pop()
	{
		return redis(2)->rPop($this->key);
	}

	public function getInfo()
	{
		return redis(2)->lIndex($this->key, -1);
	}

	public function dealFalse($data)
	{
		return redis(2)->lPush($this->failedKey, $data);
	}
}
