<?php

namespace app\model;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'language';
	protected $_primaryKey = 'code';
	protected $_withSiteId = false;
	protected $_intFields = ['lan_id'];
}