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

	public function autoload($concrete, $file)
	{
		if (isset($this->_building[$concrete])) {
			return $this->_building[$concrete];
		}
		require $file;
		return $this->build($concrete);
	}

	private function build($concrete)
	{
		if ($concrete instanceof Closure) {
			return $concrete($this);
		}
		$reflector = new \ReflectionClass($concrete);
		if (!$reflector->isInstantiable()) {
			return $concrete;
		}
		$object = $reflector->newInstance();
		$this->_building[$concrete] = $object;
		return $object;
	}
}