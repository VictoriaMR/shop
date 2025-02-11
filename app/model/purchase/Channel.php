<?php

namespace app\model\purchase;
use app\model\Base;

class Channel extends Base
{
	protected $_table = 'purchase_channel';
	protected $_intFields = ['channel_id', 'status', 'sort'];
}