<?php

namespace app\model\member;
use app\model\Base;

class Address extends Base
{
	protected $_table = 'member_address';
	protected $_primaryKey = 'address_id';
}