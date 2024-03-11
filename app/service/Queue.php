<?php

namespace app\service;

class Queue
{
	protected $key = 'frame:queue:list-data';
	protected $failedKey = 'frame:queue:failed-list-data';
	protected $node = 'frame:queue:list-info';

	public function push($data, $first=false)
	{
		if ($first) {
			cache(2)->rPush($this->key, $data);
		} else {
			cache(2)->lPush($this->key, $data);
		}
		make('frame/Task')->taskStart('Queue');
		return true;
	}

	public function count()
	{
		return cache(2)->lLen($this->key);
	}

	public function pop()
	{
		return cache(2)->rPop($this->key);
	}

	public function getInfo()
	{
		return cache(2)->lIndex($this->key, -1);
	}

	public function dealFalse($data)
	{
		return cache(2)->lPush($this->failedKey, $data);
	}
}
