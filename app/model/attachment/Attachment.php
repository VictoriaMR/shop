<?php

namespace app\model\attachment;
use app\model\Base;

class Attachment extends Base
{
	protected $_table = 'attachment';
	protected $_primaryKey = 'attach_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['attach_id'];
}