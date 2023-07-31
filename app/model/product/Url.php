<?php

namespace app\model\product;
use app\model\Base;

class Url extends Base
{
	protected $_connect = 'static';
	protected $_table = 'product_url';
	protected $_primaryKey = 'product_url_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['product_url_id', 'channel_id'];
}