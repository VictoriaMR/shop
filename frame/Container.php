<?php

namespace frame;

final class Container
{
	static private $_instance;
	private function __construct() {}
	private function __clone() {}

	public static function instance() 
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function autoload($concrete, $params=[])
	{
		if ($concrete instanceof Closure) return $concrete($this);
		$reflector = new \ReflectionClass($concrete);
		if ($reflector->isInstantiable()) {
			if (is_null($reflector->getConstructor())) {
				return $reflector->newInstance();
			} else {
				return $reflector->newInstance($params);
			}
		}
		throw new \Exception($concrete.' is not instantiable!', 1);
	}
}