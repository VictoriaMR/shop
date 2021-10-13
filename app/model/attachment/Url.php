<?php

namespace app\model\attachment;
use app\model\Base;

class Url extends Base
{
	protected $_table = 'attachment_url';
	protected $_primaryKey = 'attach_id';
	protected $_intFields = ['attach_id'];
}