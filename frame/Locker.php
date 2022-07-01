<?php

namespace frame;

class Locker
{
	const LOCKERPREFIX = 'frame-lock:';
	protected $lock = [];

	protected function cache()
	{
		return redis(2);
	}

	protected function getKey($name)
	{
		return self::LOCKERPREFIX.$name;
	}

	public function lock($name, $timeout=100)
	{
		$cas = randString(32);
		$lock = $this->cache()->set($this->getKey($name), $cas, ['nx', 'ex' => $timeout]);
		if ($lock) {
			$this->lock[$name] = $cas;
			return $cas;
		}
		return false;
	}

	public function holdLock($name)
	{
		$cas = $this->lock[$name] ?? false;
		if ($cas) {
			unset($this->lock[$name]);
		}
		return $cas;
	}

	public function getLock($name, $cas)
	{
		$lock = $this->cache()->get($this->getKey($name));
		if ($lock == $cas) {
			$this->lock[$name] = $cas;
			return true;
		}
		return false;
	}

	public function update($name, $timeout=10)
    {
    	if (!isset($this->lock[$name])) {
    		return false;
    	}
		$key = $this->getKey($name);
		$lock = $this->cache()->get($key);
		if ($lock == $this->lock[$name]) {
			return $this->cache()->expire($key, $timeout);
		}
		return false;
    }

    public function unlock($name)
    {
    	if (!isset($this->lock[$name])) {
    		return false;
    	}
		$lock = $this->cache()->get($this->getKey($name));
		if ($lock == $this->lock[$name]) {
			unset($this->lock[$name]);
			return $this->cache()->del($this->getKey($name));
		}
		return false;
    }

    public function existLock($name)
    {
    	return $this->cache()->exists($name);
    }
}