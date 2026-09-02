<?php

namespace frame;

class Locker
{
	const LOCKERPREFIX = 'frame:lock:';
	protected $lock = [];

	protected function cache()
	{
		return redis(2);
	}

	protected function getKey($name)
	{
		return self::LOCKERPREFIX . $name;
	}

	public function lock($name, $timeout = 100)
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
		if ($lock === $cas) {
			$this->lock[$name] = $cas;
			return true;
		}
		return false;
	}

	public function update($name, $timeout = 10)
	{
		if (!isset($this->lock[$name])) {
			return false;
		}
		$cas = $this->lock[$name];
		$lua = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('expire', KEYS[1], ARGV[2]) else return 0 end";
		return (bool)$this->cache()->eval($lua, [$this->getKey($name), $cas, $timeout], 1);
	}

	public function unlock($name)
	{
		if (!isset($this->lock[$name])) {
			return false;
		}
		$cas = $this->lock[$name];
		unset($this->lock[$name]);
		$lua = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";
		return (bool)$this->cache()->eval($lua, [$this->getKey($name), $cas], 1);
	}

	public function existLock($name)
	{
		return (bool)$this->cache()->exists($this->getKey($name));
	}
}