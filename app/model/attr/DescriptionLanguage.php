<?php

namespace app\model\attr;
use app\model\Base;

class DescriptionLanguage extends Base
{
	protected $_table = 'description_language';
	protected $_intFields = ['item_id', 'desc_id'];
}