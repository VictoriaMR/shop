<?php

namespace app\model\site;
use app\model\Base;

class StaticFile extends Base
{
	protected $_table = 'site_static_file';
	protected $_primaryKey = 'static_id';
}