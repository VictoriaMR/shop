<?php

namespace app\model;
use app\model\Base;

class Currency extends Base
{
	protected $_table = 'currency';
	protected $_primaryKey = 'code';
}