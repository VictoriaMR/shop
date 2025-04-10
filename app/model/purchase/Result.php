<?php

namespace app\model\purchase;
use app\model\Base;

class Result extends Base
{
	protected $_table = 'purchase_result';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_result_id', 'channel_id'];
}