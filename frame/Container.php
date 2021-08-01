<?php

namespace frame;

final class Container
{
	static private $_instance;
	private $_building = [];
	private function __construct() {}
	private function __clone() {}

	public static function instance() 
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function autoload($concrete, $file, $params=null)
	{
		if (isset($this->_building[$concrete])) {
			return $this->_building[$concrete];
		}
		require $file;
		return $this->build($concrete, $params);
	}

	private function build($concrete, $params)
	{
		if ($concrete instanceof Closure) {
			return $concrete($this);
		}
		$reflector = new \ReflectionClass($concrete);
		if (!$reflector->isInstantiable()) {
			return $concrete;
		}
		if (is_null($reflector->getConstructor()) || is_null($params)) {
			$object = $reflector->newInstance();
		} else {
			$object = $reflector->newInstance($params);
		}
		$this->_building[$concrete] = $object;
		return $object;
	}
}