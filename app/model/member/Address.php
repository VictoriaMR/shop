<?php

namespace app\model\member;
use app\model\Base;

class Address extends Base
{
	protected $_table = 'member_address';
	protected $_primaryKey = 'address_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['address_id', 'site_id', 'mem_id', 'zone_id', 'is_default', 'is_bill'];
}