<?php

namespace app\model\attr;
use app\model\Base;

class Name extends Base
{
	protected $_table = 'attr_name';
	protected $_primaryKey = 'attrn_id';
	protected $_intFields = ['attrn_id'];
}