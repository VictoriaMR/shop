<?php

namespace app\model\site;
use app\model\Base;

class Domain extends Base
{
	protected $_table = 'site_domain';
	protected $_primaryKey = 'domain_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['domain_id', 'site_id', 'status'];
	const CACHE_KEY_PATH = 'domain_config_site_template';
	const CACHE_KEY_INFO = 'domain_config_site_info';
}