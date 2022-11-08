<?php

namespace app\model\desc;
use app\model\Base;

class Group extends Base
{
	protected $_table = 'desc_group';
	protected $_primaryKey = 'descg_id';
	protected $_intFields = ['descg_id', 'status'];
}