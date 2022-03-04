<?php

namespace frame;

class Locker
{
	const LOCKERPREFIX = 'frame-lock:';
	protected $lock = [];

	public function lock($name, $timeout=100)
	{
		$cas = randString(32);
		$lock = redis(2)->set(self::LOCKERPREFIX.$name, $cas, ['nx', 'ex' => $timeout]);
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
		$lock = redis(2)->get(self::LOCKERPREFIX.$name);
		if ($lock == $cas) {
			$this->lock[$name] = $cas;
			return true;
		}
		return false;
	}

	public function update($name, $timeout=10)
    {
		if ($timeout < 1) {
			$timeout = 10;
		}
		$cas = $this->lock[$name] ?? false;
		if (empty($cas)) { //当前无锁
			return false;
		}
		$key = self::LOCKERPREFIX.$name;
		$lock = redis(2)->get($key);
		if ($lock != $cas) {
			return false;
		}
		return redis(2)->expire($key, $timeout);
    }

    public function unlock($name)
    {
		$cas = $this->lock[$name] ?? '';
		if (empty($cas)) {
			return false;
		}
		$lock = redis(2)->get(self::LOCKERPREFIX.$name);
		if ($lock == $cas) {
			unset($this->lock[$name]);
			return redis(2)->del(self::LOCKERPREFIX.$name);
		}
		return false;
    }
}