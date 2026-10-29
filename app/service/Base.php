<?php

namespace app\service;

abstract class Base
{
	protected $baseModel = null;
	protected array $_error = [];
	protected $_model = null;

	private function __clone() {}

	protected function getModel()
	{
		if (!$this->_model) {
			$this->_model = str_replace('\\service\\', '\\model\\', static::class);
		}
		$this->baseModel = \App::make($this->_model);
	}

	public function __call($func, $arg)
	{
		if ($this->baseModel === null) {
			$this->getModel();
		}
		return $this->baseModel->$func(...$arg);
	}

	protected function setError($message): bool
	{
		$this->_error[] = $message;
		return false;
	}

	public function getError(): array
	{
		return $this->_error;
	}
}
