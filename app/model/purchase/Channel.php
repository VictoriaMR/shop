<?php

namespace app\model\purchase;
use app\model\Base;

class Channel extends Base
{
	const CHANNEL_TAOBAO = 6051;
    const CHANNEL_TMALL = 6052;
    const CHANNEL_1688 = 6053;
    const CHANNEL_FACTORY = 6054;
    const CHANNEL_STORE = 6055;

	protected $_table = 'purchase_channel';
	protected $_intFields = ['channel_id', 'status', 'sort'];
}